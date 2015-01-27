<?php
/**
 * LICENSE
 *
 * Copyright 2015 Eric Lopez
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

namespace Sifo;

use Symfony\Component\Yaml\Yaml;

/**
 * Handles the dependency injection.
 */
class DependencyInjector
{
    /**
     * Singleton instance being used.
     *
     * @var object
     * @static
     */
    static protected $instance;

    /**
     * Private constructor, use getInstance() instead to get an instance.
     */
    private function __construct()
    {
    }

    /**
     * Private clone method, use getInstance() instead to get an instance.
     */
    private function __clone()
    {
    }

    /**
     * Gets an instance of the dependency injector class.
     *
     * @static
     * @return DependencyInjector Dependency injector instance.
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed
     * @throws Exception_DependencyInjector No entry was found for this identifier.
     */
    public function get($service)
    {
        try {
            $environment_suffix  = Domains::getInstance()->getDevMode() ? '_dev' : '';
            $service_definitions = Config::getInstance()->getConfig('services/definition' . $environment_suffix);
        }
        catch (Exception_Configuration $e) {
            $service_definitions = Config::getInstance()->getConfig('services/definition');
        }

        if (!array_key_exists($service, $service_definitions)) {
            throw new Exception_DependencyInjector('Undefined service ' . $service);
        }

        $service = $service_definitions[$service];
        if (is_object($service)) {
            $service = $service($service_definitions);
        }

        return $service;
    }

    /**
     * Returns true if the container can return an entry for the given identifier, false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($service)
    {
        $service_exists = true;

        try {
            Config::getInstance()->getConfig('services/definition', $service);
        }
        catch (Exception_Configuration $e) {
            $service_exists = false;
        }

        return $service_exists;
    }

    /**
     * Generates the dependencies declaration for the current instance branch.
     */
    public function generateDependenciesDeclaration()
    {
        $domains   = Domains::getInstance();
        $instances = array_slice($domains->getInstanceInheritance(), 1);

        foreach ($instances as $index => $instance) {
            $parent_instance    = $index > 0 ? $instances[$index - 1] : null;
            $this->generateDependenciesDeclarationForInstance($instance, $parent_instance, '');

            if ($domains->getDevMode()) {
                $this->generateDependenciesDeclarationForInstance($instance, $parent_instance, '_dev');
            }
        }
    }

    private function generateDependenciesDeclarationForInstance($instance, $parent_instance, $files_suffix)
    {
        $instance_yml_definitions_file = ROOT_PATH . '/instances/' . $instance . '/config/services/definition' . $files_suffix . '.yml';
        $instance_php_definitions_file = ROOT_PATH . '/instances/' . $instance . '/config/services/definition' . $files_suffix . '.config.php';

        if (!file_exists($instance_yml_definitions_file))
        {
            return;
        }

        $parsed_yaml_content = Yaml::parse(file_get_contents($instance_yml_definitions_file));
        $declared_services   = $this->getImportedServices($parsed_yaml_content);
        $compiled_services   = array();

        foreach ($declared_services as $service => $declaration) {
            if ($this->isALiteralDeclaration($declaration)) {
                $compiled_services[$service] = "'" . $declaration . "';";
                continue;
            }

            $class_name         = '\\' . $declaration['class'];
            $arguments          = array_key_exists('arguments', $declaration) ? $declaration['arguments'] : array();
            $compiled_arguments = array();

            foreach ($arguments as $argument) {
                if ($this->isALiteralArgument($argument)) {
                    $compiled_dependency = $this->getLiteralArgumentCompilation($argument);
                }
                elseif ($this->isALiteralDependency($compiled_services, $argument)) {
                    $compiled_dependency = $this->getLiteralDependencyCompilation($argument);
                }
                else {
                    $compiled_dependency = $this->getCallableDependencyCompilation($argument);
                }

                $compiled_arguments[] = $compiled_dependency;
            }

            $return_statement = "new " . $class_name;
            if ($this->isASingleton($declaration))
            {
                $return_statement = $class_name . "::" . $declaration['singleton'];
            }

            $service_return              = "return " . $return_statement . "(\n" . implode(",\n", $compiled_arguments) . "\n\t);";
            $compiled_services[$service] = "function (\$config) {\n\t" . $service_return . "\n};";
        }

        $this->dumpConfigurationFile($compiled_services, $instance_php_definitions_file, $instance, $parent_instance, $files_suffix);
    }

    private function getImportedServices($parsed_yaml_content)
    {
        $imports = is_array($parsed_yaml_content)
            ? array_key_exists('imports', $parsed_yaml_content)
                ? $parsed_yaml_content['imports']
                : array()
            : array();

        $services = is_array($parsed_yaml_content)
            ? array_key_exists('services', $parsed_yaml_content)
                ? $parsed_yaml_content['services']
                : array()
            : array();

        $retrieved_services = array();
        foreach ($imports as $instance => $files_to_import) {
            $config_files_path = ROOT_PATH . '/instances/' . $instance . '/config/services/';

            foreach ($files_to_import as $file_to_import) {
                $imported_parsed_yaml_content = Yaml::parse(file_get_contents($config_files_path . $file_to_import));
                $retrieved_services           = array_merge(
                    $retrieved_services,
                    $this->getImportedServices($imported_parsed_yaml_content)
                );
            }
        }

        return array_merge($retrieved_services, $services);
    }

    private function isALiteralDeclaration($declaration)
    {
        return !is_array($declaration);
    }

    private function isASingleton($declaration)
    {
        return array_key_exists('singleton', $declaration);
    }

    private function isALiteralArgument($argument)
    {
        return !is_array($argument) && substr($argument, 0, 1) != '@';
    }

    private function isALiteralDependency($compiled_dependencies, $dependency)
    {
        $sanitized_dependency = substr($dependency, 1);
        return
            array_key_exists($sanitized_dependency, $compiled_dependencies)
            && substr($compiled_dependencies[$sanitized_dependency], 0, 8) != 'function';
    }

    private function getLiteralArgumentCompilation($argument)
    {
        return "\t\t'" . $argument . "'";
    }

    private function getLiteralDependencyCompilation($argument)
    {
        return "\t\t\$config['" . substr($argument, 1) . "']";
    }

    private function getCallableDependencyCompilation($argument)
    {
        return "\t\t\$config['" . substr($argument, 1) . "'](\$config)";
    }

    private function dumpConfigurationFile($compiled_services, $definitions_config_file, $instance, $parent_instance, $files_suffix)
    {
        $dumped_configuration             = "<?php\n\n";
        $production_dependencies_php_file = ROOT_PATH . '/instances/' . $instance . '/config/services/definition.config.php';
        $parent_dependencies_php_file     = ROOT_PATH . '/instances/' . $parent_instance . '/config/services/definition' . $files_suffix . '.config.php';

        if ($files_suffix && file_exists($production_dependencies_php_file)) {
            $dumped_configuration .= "require_once ROOT_PATH . '/instances/" . $instance . "/config/services/definition.config.php';\n\n";
        }

        if (file_exists($parent_dependencies_php_file)) {
            $dumped_configuration .= "require_once ROOT_PATH . '/instances/" . $parent_instance . "/config/services/definition" . $files_suffix . ".config.php';\n\n";
        }

        foreach ($compiled_services as $service => $compilation) {
            $dumped_configuration .= "\$config['" . $service . "'] = " . $compilation . "\n\n";
        }

        file_put_contents($definitions_config_file, $dumped_configuration);
    }
}

/**
 * Exception for the process.
 */
class Exception_DependencyInjector extends \Exception
{
}
