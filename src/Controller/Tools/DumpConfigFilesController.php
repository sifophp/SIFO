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
        'templates' => 'templates.config.php',
        'locale' => 'locale.config.php'
    ];

    /**
     * Saves files that couldn't be saved to disk.
     *
     * @var array
     */
    protected $failed_files = array();

    /**
     * Writes all the configurattion files to disk.
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

        $output = array();

        $instance_inheritance = array_unique(Domains::getInstance()->getInstanceInheritance());

        $instance_inheritance_reverse = array_reverse($instance_inheritance);

        $instances_configuration = [];
        // Build the instance configuration: instance name and his parent instance name is exists.
        foreach ($instance_inheritance_reverse as $key => $instance) {
            $instance_config['current'] = $instance;
            if (isset($instance_inheritance[$key + 1])) {
                $instance_config['parent'] = $instance_inheritance_reverse[$key + 1];
            }
            $instances_configuration[] = $instance_config;
            unset($instance_config);
        }

        // For each instance in the inheritance it regenerates his configuration files.
        foreach ($instances_configuration as $instance) {
            $current_instance = $instance['current'];

            $this->assign('instance_parent', null);
            if (isset($instance['parent'])) {
                $this->assign('instance_parent', $instance['parent']);
            }

            foreach ($files as $file => $folders) {
                $configs = [];
                foreach ($folders as $folder) {
                    $configs = array_merge($configs, $this->getAvailableFiles($folder, $current_instance));
                }

                $this->assign('config', $configs);
                $this->assign('file_name', $this->filenames[$file]);

                $configs_content = $this->grabHtml();

                $file_destination = ROOT_PATH . "/instances/" . $current_instance . "/config/" . $this->filenames[$file];

                if ($current_instance == 'common') {
                    $file_destination = ROOT_PATH . "/vendor/sifophp/sifo-common-instance/config/" . $this->filenames[$file];
                }

				$success = file_put_contents( $file_destination, $configs_content );
                if (!$success) {
                    $this->failed_files[] = $file_destination;
                }
                $output[$current_instance][$file] = $configs_content;
            }
        }

        return $output;

    }

    public function build()
    {
        if (true !== Domains::getInstance()->getDevMode()) {
            throw new NotFound('User tried to access the rebuild page, but he\'s not in development');
        }

        // Calculate where the config files are taken from.
        $files_output = $this->rebuildFiles([
            'config' => ['config'],
            'templates' => ['templates'],
            'locale' => ['locale'],
        ]);

        // Reset the layout and paste the content in the empty template:
        $this->setLayout('manager/rebuild.tpl');

        // Disable debug on this page.
        Domains::getInstance()->setDebugMode(false);

        $this->assign('inheritance', array_reverse(array_unique(Domains::getInstance()->getInstanceInheritance())));

        $this->assign('errors', $this->failed_files);
        $this->assign('filenames', $this->filenames);
        $this->assign('files_output', $files_output);
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
        $type_files = array();

        if ($type == 'core') {
            return [];
        }

        if ($current_instance == 'common') {
            $available_files = $d->getFileListRecursive(ROOT_PATH . "/vendor/sifophp/sifo-common-instance/" . "/$type");
            $path_files = "vendor/sifophp/sifo-common-instance";
        } else {
            $available_files = $d->getFileListRecursive(ROOT_PATH . "/instances/" . $current_instance . "/$type");
            $path_files = "instances/$current_instance";
        }

        if (is_array($available_files) === true && count($available_files) > 0) {
            foreach ($available_files as $k => $v) {
                // Allow only PHP extensions
                $desired_file_pattern = preg_match('/\.(php)$/i', $v["relative"]);
                if (($type != 'templates' && $desired_file_pattern) || $type == 'templates') {
                    $rel_path = $this->cleanStartingSlash($v["relative"]);

                    $path = str_replace('//', '/', $path_files . "/$type/$rel_path");

                    // Calculate the class name for the given file:
                    $rel_path = str_replace('.model.php', '', $rel_path);
                    $rel_path = str_replace('.ctrl.php', '', $rel_path);
                    $rel_path = str_replace('.config.php', '', $rel_path);
                    $rel_path = str_replace('.php', '', $rel_path); // Default

                    switch ($type) {
                        case 'config':
                            if ($rel_path == 'configuration_files') {
                                continue;
                            }
                        case 'templates':
                        default:
                            $type_files[$rel_path] = $path;
                    }
                }
            }
        }

        ksort($type_files);
        return $type_files;
    }

}
