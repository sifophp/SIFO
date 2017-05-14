<?php

namespace Sifo\Controller\Tools;

use Sifo\Container\DependencyInjector;
use Sifo\Controller\Controller;
use Sifo\Exception\Http\NotFound;
use Sifo\Filesystem\Dir;
use Sifo\Http\Domains;

class DumpConfigFilesController extends Controller
{

    /**
     * Filenames where the configuration files will be stored.
     * @var string
     */
    protected $filenames = [
        'config' => 'configuration_files.config.php',
        'commands' => 'commands.config.php',
        'templates' => 'templates.config.php',
        'locale' => 'locale.config.php'
    ];

    /**
     * Saves files that couldn't be saved to disk.
     *
     * @var array
     */
    protected $failed_files = [];

    /** @var array */
    private $instance_inheritance = [];

    public function build()
    {
        if (true !== Domains::getInstance()->getDevMode()) {
            throw new NotFound('User tried to access the rebuild page, but he\'s not in development');
        }

        $this->getInstancesInheritance();

        // Calculate where the config files are taken from.
        $files_output = $this->rebuildFiles([
            'config' => ['config'],
            'commands' => ['src/Command'],
            'templates' => ['templates'],
            'locale' => ['locale']
        ]);

        // Reset the layout and paste the content in the empty template:
        $this->setLayout('manager/rebuild.tpl');

        // Disable debug on this page.
        Domains::getInstance()->setDebugMode(false);

        $this->assign('inheritance', $this->instance_inheritance);

        $this->assign('errors', $this->failed_files);
        $this->assign('filenames', $this->filenames);
        $this->assign('files_output', $files_output);
    }

    /**
     * Writes all the configuration files to disk.
     *
     * Input expected is:
     *
     * array( 'filename' => array( 'folder_to_parse1', 'folder_to_parse2', '...' ) )
     *
     * @param array $files
     * @return array Array of contents write to each file.
     */
    protected function rebuildFiles(array $files)
    {
        // Generate the dependencies declaration file.
        DependencyInjector::getInstance()->generateDependenciesDeclaration();

        $this->setLayout('manager/templates.tpl');

        $output = [];

        // For each instance in the inheritance it regenerates his configuration files.
        foreach ($this->instance_inheritance as $current_instance) {
            $this->assign('instance_parent', $this->getParentInstance($current_instance));

            foreach ($files as $file => $folders) {
                $config_file_name = $this->filenames[$file];
                $this->assign('file_name', $config_file_name);

                $current_config_file = $this->getCurrentConfigFilename($current_instance, $config_file_name);
                $parent_config_file = $this->getParentConfigFilename($current_instance, $config_file_name);

                $configs = [];
                foreach ($folders as $folder) {
                    $configs = array_merge($configs, $this->getAvailableFiles($folder, $current_instance));
                }

                if (empty($configs) && null === $parent_config_file) {
                    if (file_exists($current_config_file)) {
                        unlink($current_config_file);
                    }
                    continue;
                }

                $this->assign('config', $configs);

                $this->assign('parent_config_file', str_replace(ROOT_PATH, '', $parent_config_file));

                $configs_content = $this->grabHtml();


                $success = file_put_contents($current_config_file, $configs_content);
                if (!$success) {
                    $this->failed_files[] = $current_config_file;
                }
                $output[$current_instance][$file] = $configs_content;
            }
        }

        return $output;

    }

    protected function getRunningInstances()
    {
        $d = new Dir();
        $instances = $d->getDirs(ROOT_PATH . '/instances');

        return $instances;

    }

    protected function cleanStartingSlash($path)
    {
        if (0 === strpos($path, "/")) {
            // Remove starting slashes.
            return substr($path, 1);
        }
        return $path;

    }

    protected function getAvailableFiles($type, $current_instance)
    {
        $d = new Dir();
        $available_files = [];

        $all_files_list = $d->getFileListRecursive(ROOT_PATH . "/instances/{$current_instance}/{$type}") ?: [];

        foreach ($all_files_list as $file_info) {
            $relative_path = $this->getRelativePath($file_info);
            $absolute_path = $this->getAbsolutePath($file_info);

            if ($this->shouldIgnoreFile($type, $relative_path)) {
                continue;
            }

            $available_files[$relative_path] = $absolute_path;
        }

        ksort($available_files);
        return $available_files;
    }

    private function getRelativePath($file_info)
    {
        return preg_replace('/(?:\.(config|ctrl|model))?\.php$/', '', trim($file_info['relative'], '/'));
    }

    private function getAbsolutePath($file_info)
    {
        return str_replace(ROOT_PATH . '/', '', $file_info['absolute']);
    }

    private function getInstancesInheritance()
    {
        $this->instance_inheritance = array_reverse(array_unique(Domains::getInstance()->getInstanceInheritance()));
    }

    private function getParentInstance(string $instance)
    {
        $index = array_search($instance, $this->instance_inheritance);

        if (!isset($this->instance_inheritance[$index + 1])) {
            return null;
        }

        return $this->instance_inheritance[$index + 1];
    }

    private function getCurrentConfigFilename($current_instance, $config_file_name): string
    {
        $file_destination = ROOT_PATH . "/instances/" . $current_instance . "/config/" . $config_file_name;
        return $file_destination;
    }

    private function getParentConfigFilename($current_instance, $config_file_name)
    {
        if ($parent_instance = $this->getParentInstance($current_instance)) {
            $config_file_path = ROOT_PATH . "/instances/" . $parent_instance . "/config/" . $config_file_name;
            if (file_exists($config_file_path)) {
                return $config_file_path;
            }

            return null;
        }

        $sifo_config_file_path = ROOT_PATH . "/vendor/sifophp/sifo/config/" . $config_file_name;
        if (file_exists($sifo_config_file_path)) {
            return $sifo_config_file_path;
        }

        return null;
    }

    private function shouldIgnoreFile($type, $relative_path): bool
    {
        if ('templates' == $type) {
            return false;
        }

        if ('config' == $type && 'configuration_files' == $relative_path) {
            return true;
        }

        if (preg_match('/^\./',
            $relative_path)) {
            return true;
        }

        if (!empty(pathinfo($relative_path, PATHINFO_EXTENSION))) {
            return true;
        }

        return false;
    }
}
