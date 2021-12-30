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

use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use function array_key_exists;

/**
 * Handles the dependency injection.
 */
class DependencyInjector implements ContainerInterface
{
    /**
     * Singleton instance being used.
     *
     * @var ContainerInterface|DependencyInjector
     * @static
     */
    static protected $instance;
    /**
     * Already instantiated container services.
     *
     * @var array
     * @static
     */
    static protected $container_instances = [];
    /**
     * Defined services.
     *
     * @var array
     */
    protected $service_definitions;
    private static ?\Psr\Container\ContainerInterface $container = null;

    /**
     * Private constructor, use getInstance() instead to get an instance.
     */
    private function __construct($container = null)
    {
        if (null !== self::$container) {
            return;
        }

        self::$container = $container;
        if (null === $container) {
            $this->loadServiceDefinitions();
        }
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
     * @param null $instance_name
     * @param null|ContainerInterface $container
     * @return ContainerInterface|DependencyInjector Dependency injector instance.
     */
    public static function getInstance($instance_name = null, $container = null)
    {
        if (null == $instance_name) {
            $instance_name = Bootstrap::$instance;
        }

        if (!isset(self::$instance[$instance_name])) {
            self::$instance[$instance_name] = new self($container ?? self::$container);
        }

        return self::$instance[$instance_name];
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $service_key Identifier of the entry to look for.
     * @param bool $get_private_service
     * @return mixed
     * @throws Exception_DependencyInjector No entry was found for this identifier.
     */
    public function get($service_key, $get_private_service = false)
    {
        if (null !== self::$container) {
            return self::$container->get($service_key);
        }

        if (!array_key_exists($service_key, $this->service_definitions)) {
            throw new Exception_DependencyInjector('Undefined service "' . $service_key . '"');
        }

        if ($this->loadingAPrivateService($service_key) && !$get_private_service) {
            throw new Exception_DependencyInjector('Trying to get a private service "' . $service_key . '"');
        }

        $uses_the_container_scope = $this->usingTheContainerScope($service_key);

        if ($uses_the_container_scope && array_key_exists($service_key, self::$container_instances)) {
            return self::$container_instances[$service_key];
        }

        $service_instance = $this->service_definitions[$service_key];
        if (is_object($service_instance)) {
            $service_instance = $service_instance($this);
        }

        if ($uses_the_container_scope) {
            self::$container_instances[$service_key] = $service_instance;
        }

        return $service_instance;
    }

    private function loadingAPrivateService($service_key)
    {
        return
            array_key_exists('private_services', $this->service_definitions)
            && in_array($service_key, $this->service_definitions['private_services']);
    }

    private function loadServiceDefinitions()
    {
        try {
            $environment_suffix = Domains::getInstance()->getDevMode() ? '_dev' : '';
            $service_definitions = Config::getInstance()->getConfig('services/definition' . $environment_suffix);
        } catch (Exception_Configuration $e) {
            $service_definitions = Config::getInstance()->getConfig('services/definition');
        }

        $this->service_definitions = $service_definitions;
    }

    /**
     * Returns true if the container can return an entry for the given identifier, false otherwise.
     *
     * @param string $service Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($service)
    {
        if (null !== self::$container) {
            return self::$container->has($service);
        }

        return array_key_exists($service, $this->service_definitions);
    }

    /** @deprecated  */
    public function servicesWithTag($tag_name)
    {
        if (!isset($this->service_definitions['tags'][$tag_name])) {
            return [];
        }

        return $this->service_definitions['tags'][$tag_name];
    }

    private function usingTheContainerScope($service)
    {
        return
            array_key_exists('scopes', $this->service_definitions) &&
            array_key_exists($service, $this->service_definitions['scopes']) &&
            'container' == $this->service_definitions['scopes'][$service];
    }

    /**
     * Generates the dependencies declaration for the current instance branch.
     */
    public function generateDependenciesDeclaration()
    {
        $domains = Domains::getInstance();
        $instances = array_slice($domains->getInstanceInheritance(), 1);

        foreach ($instances as $index => $instance) {
            $parent_instance = $index > 0 ? $instances[$index - 1] : null;
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

        if (!file_exists($instance_yml_definitions_file)) {
            return;
        }

        $parsed_yaml_content = Yaml::parse(file_get_contents($instance_yml_definitions_file));
        $declared_services = self::getImportedServices($parsed_yaml_content, $instance_yml_definitions_file);

        $compiled_services = [];
        $scoped_definitions = [];
        $private_services = [];
        $tags_definition = [];

        foreach ($declared_services as $service_key => $declaration) {
            if ($this->isALiteralDeclaration($declaration)) {
                $compiled_services[$service_key] = "'" . $declaration . "';";
                continue;
            }

            if ($this->isAnAlias($declaration)) {
                $aliased_service = ltrim($declaration['alias'], '@');
                $container_return_string = "return \$container->get('" . $aliased_service . "', true);";
                $compiled_services[$service_key] = "function (\$container) {\n\x20\x20\x20\x20" . $container_return_string . "\n};";
                continue;
            }

            $class_name = '\\' . $declaration['class'];
            $arguments = array_key_exists('arguments', $declaration) ? $declaration['arguments'] : [];
            $compiled_arguments = $this->stringifyArguments($arguments, $compiled_services);

            if ($this->isASingleton($declaration)) {
                $class_instance_creation_statement = $class_name . "::" . $declaration['singleton'];
            } else {
                if ($this->isAFactory($declaration)) {
                    $factory_service = ltrim($declaration['factory'][0], '@');
                    $factory_method = $declaration['factory'][1];
                    $class_instance_creation_statement = "\$container->get('" . $factory_service . "', true)->" . $factory_method;
                } else {
                    $class_instance_creation_statement = "new " . $class_name;
                }
            }

            $class_instance_creation_statement .= "(\n" . implode(",\n", $compiled_arguments) . "\n\x20\x20\x20\x20)";
            $service_return = '$service_instance = ' . $class_instance_creation_statement . ';';

            if ($this->hasSetterInjections($declaration)) {
                $service_return .= $this->getSetterInjectionsCalls($declaration, $compiled_services);
            }

            if ($this->isAPrototypedDeclaration($declaration)) {
                $scoped_definitions[$service_key] = 'prototype';
            } else {
                $scoped_definitions[$service_key] = 'container';
            }

            if ($this->isAPrivateService($declaration)) {
                $private_services[] = $service_key;
            }

            $tags_definition = $this->addAllDefinitionTags($tags_definition, $service_key, $declaration);

            $service_return .= "\n\n\x20\x20\x20\x20" . 'return $service_instance;';
            $compiled_services[$service_key] = "function (\$container) {\n\x20\x20\x20\x20" . $service_return . "\n};";
        }

        $this->dumpConfigurationFile(
            $compiled_services,
            $scoped_definitions,
            $private_services,
            $tags_definition,
            $instance_php_definitions_file,
            $instance,
            $parent_instance,
            $files_suffix
        );
    }

    private function stringifyArguments(array $arguments, array $compiled_services, $depth = 2)
    {
        $compiled_arguments = [];

        foreach ($arguments as $argument) {
            if ($this->isAVariableArgument($argument)) {
                $compiled_arguments[] = $this->getVariableArgumentCompilation($argument);
            } elseif ($this->isALiteralArgument($argument)) {
                $compiled_arguments[] = $this->getLiteralArgumentCompilation($argument);
            } elseif ($this->isAnArray($argument)) {
                $compiled_argument = '';

                foreach ($argument as $argument_key => $argument_value) {
                    $stringified_value = $this->stringifyArguments([$argument_value], $compiled_services, $depth + 1)[0];
                    $compiled_argument .= str_repeat("\x20\x20\x20\x20", $depth + 1) . '\'' . $argument_key . '\' => ' . ltrim($stringified_value) . ",\n";
                }

                $compiled_arguments[] = str_repeat("\x20\x20\x20\x20", $depth) . "[\n" . $compiled_argument . str_repeat("\x20\x20\x20\x20", $depth) . ']';
            } else {
                $dependant_service = ltrim($argument, '@');
                $compiled_arguments[] = str_repeat("\x20\x20\x20\x20", $depth) . "\$container->get('" . $dependant_service . "', true)";
            }
        }

        return $compiled_arguments;
    }

    public static function getImportedServices($parsed_yaml_content, $parsed_yaml_path)
    {
        $imports = is_array($parsed_yaml_content)
            ? array_key_exists('imports', $parsed_yaml_content)
                ? $parsed_yaml_content['imports']
                : []
            : [];

        $services = is_array($parsed_yaml_content)
            ? array_key_exists('services', $parsed_yaml_content)
                ? $parsed_yaml_content['services']
                : []
            : [];

        $retrieved_services = [];

        if (self::hasGroupedServicesByInstances($imports)) {
            foreach ($imports as $instance => $files_to_import) {
                $config_files_path = ROOT_PATH . '/instances/' . $instance . '/config/services/';
                $retrieved_services = array_merge(
                    $retrieved_services,
                    self::getServicesFromFilesCollection($config_files_path, $files_to_import)
                );
            }

            return array_merge($retrieved_services, $services);
        }

        $config_files_path = dirname($parsed_yaml_path) . '/';
        $retrieved_services = self::getServicesFromFilesCollection($config_files_path, $imports);

        return array_merge($retrieved_services, $services);
    }

    private static function hasGroupedServicesByInstances($imports)
    {
        return count(array_filter(array_keys($imports), 'is_string')) > 0;
    }

    private static function getServicesFromFilesCollection($config_files_path, array $files_to_import)
    {
        $retrieved_services = [];

        foreach ($files_to_import as $file_to_import) {
            $imported_parsed_yaml_path = $config_files_path . $file_to_import;
            $imported_parsed_yaml_content = Yaml::parse(file_get_contents($imported_parsed_yaml_path));

            $retrieved_services = array_merge(
                $retrieved_services,
                self::getImportedServices($imported_parsed_yaml_content, $imported_parsed_yaml_path)
            );
        }

        return $retrieved_services;
    }

    private function isALiteralDeclaration($declaration)
    {
        return !is_array($declaration);
    }

    private function isAnAlias($declaration)
    {
        return array_key_exists('alias', $declaration);
    }

    private function isASingleton($declaration)
    {
        return array_key_exists('singleton', $declaration);
    }

    private function isAFactory($declaration)
    {
        return array_key_exists('factory', $declaration);
    }

    private function hasSetterInjections($declaration)
    {
        return array_key_exists('calls', $declaration);
    }

    private function isAPrototypedDeclaration($declaration)
    {
        if (array_key_exists('scope', $declaration) && $declaration['scope'] == 'prototype') {
            return true;
        }

        return false;
    }

    private function isAPrivateService($declaration)
    {
        return array_key_exists('public', $declaration) && !$declaration['public'];
    }

    private function addAllDefinitionTags($tags_definition, $service_key, $declaration)
    {
        if (!$this->hasTags($declaration)) {
            return $tags_definition;
        }

        foreach ($declaration["tags"] as $declaration_tag) {
            $tags_definition = $this->addDeclarationTagDefinition($tags_definition, $service_key, $declaration_tag);
        }

        return $tags_definition;
    }

    private function hasTags($declaration)
    {
        return array_key_exists('tags', $declaration);
    }

    public function addDeclarationTagDefinition($tags_definition, $service_key, $declaration_tag)
    {
        $tag_name = $declaration_tag['name'];

        unset($declaration_tag['name']);

        $tags_definition[$tag_name][$service_key] = $declaration_tag;

        return $tags_definition;
    }

    private function getSetterInjectionsCalls($declaration, $compiled_services)
    {
        $setter_injections_calls = "";

        foreach ($declaration['calls'] as $setter_injection) {
            $setter_injection_compiled_arguments = $this->stringifyArguments($setter_injection[1], $compiled_services);

            $class_instance_creation_statement = implode(",\n", $setter_injection_compiled_arguments);
            $setter_injections_calls .= "\n\x20\x20\x20\x20" . '$service_instance->' . $setter_injection[0] . "(\n" . $class_instance_creation_statement . "\n\x20\x20\x20\x20);";
        }

        return $setter_injections_calls;
    }

    private function isAVariableArgument($argument): bool
    {
        return !is_array($argument) && preg_match('/%([^%\s]+)%/', $argument);
    }

    private function isALiteralArgument($argument): bool
    {
        return !is_array($argument) && strpos($argument, '@') !== 0;
    }

    private function isAnArray($dependency)
    {
        return is_array($dependency);
    }

    private function getVariableArgumentCompilation($argument): string
    {
        $variable_name = trim($argument, '%');

        return $_ENV[$variable_name] ? "\x20\x20\x20\x20\x20\x20\x20\x20'" . $_ENV[$variable_name] . "'" : '';
    }

    private function getLiteralArgumentCompilation($argument)
    {
        return "\x20\x20\x20\x20\x20\x20\x20\x20'" . $argument . "'";
    }

    private function dumpConfigurationFile(
        $compiled_services,
        $scoped_definitions,
        $private_services,
        $tags_definition,
        $definitions_config_file,
        $instance,
        $parent_instance,
        $files_suffix
    ) {
        $dumped_configuration = "<?php\n\n";
        $production_dependencies_php_file = ROOT_PATH . '/instances/' . $instance . '/config/services/definition.config.php';
        $parent_dependencies_php_file = ROOT_PATH . '/instances/' . $parent_instance . '/config/services/definition' . $files_suffix . '.config.php';

        if ($files_suffix && file_exists($production_dependencies_php_file)) {
            $dumped_configuration .= "include ROOT_PATH . '/instances/" . $instance . "/config/services/definition.config.php';\n\n";
        }

        if (file_exists($parent_dependencies_php_file)) {
            $dumped_configuration .= "include ROOT_PATH . '/instances/" . $parent_instance . "/config/services/definition" . $files_suffix . ".config.php';\n\n";
        }

        foreach ($compiled_services as $service => $compilation) {
            $dumped_configuration .= "\$config['" . $service . "'] = " . $compilation . "\n\n";
        }

        $dumped_configuration .= $this->dumpScopedServices($scoped_definitions);
        $dumped_configuration .= $this->dumpPrivateServices($private_services);
        $dumped_configuration .= $this->dumpTagsDefinition($tags_definition);

        file_put_contents($definitions_config_file, $dumped_configuration);
    }

    private function dumpScopedServices(array $scoped_definitions)
    {
        $dumped_services = "";

        foreach ($scoped_definitions as $service => $type) {
            $dumped_services .= "\$config['scopes']['" . $service . "'] = '" . $type . "';\n";
        }

        return $dumped_services;
    }

    private function dumpPrivateServices(array $private_services)
    {
        $dumped_services = "\n";

        foreach ($private_services as $private_service) {
            $dumped_services .= "\$config['private_services'][] = '" . $private_service . "';\n";
        }

        return $dumped_services;
    }

    private function dumpTagsDefinition(array $tags_definition)
    {
        $dumped_services = "\n";
        foreach ($tags_definition as $tag_name => $service_tag_definition) {
            foreach ($service_tag_definition as $service_key => $all_tag_values) {
                if (empty($all_tag_values)) {
                    $dumped_services .= "\$config['tags']['" . $tag_name . "']['" . $service_key . "'] = [];\n";
                } else {
                    foreach ($all_tag_values as $tag_value_key => $tag_value_value) {
                        $dumped_services .= "\$config['tags']['" . $tag_name . "']['" . $service_key . "']['" . $tag_value_key . "'] = '" . $tag_value_value . "';\n";
                    }
                }
            }
        }

        return $dumped_services;
    }
}

/**
 * Exception for the process.
 */
class Exception_DependencyInjector extends \Exception
{
}
