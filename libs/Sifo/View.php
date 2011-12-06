<?php
/**
 * LICENSE
 *
 * Copyright 2010 Albert Lombarte
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

include_once ROOT_PATH . '/libs/'. Config::getInstance()->getLibrary( 'smarty' ).'/Smarty.class.php';

/**
 * Templating engine. Compiles some smarty stuff for an easier management.
 */
class View extends \Smarty
{
	/**
	 * The current instance being executed by the framework.
	 *
	 * @var string
	 */
	private $instance;

	/**
	 * Constructor. Inherits all methods from Smarty.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->instance = Bootstrap::$instance;

		// Paths definition:
		$templates_path = ROOT_PATH . '/instances/' . $this->instance . '/templates/';
		$this->setTemplateDir( ROOT_PATH . '/' );  // The templates are taken using the templates.config.php mappings, under the variable $_tpls.
		$this->setCompileDir( $templates_path . '_smarty/compile/' );
		$this->setConfigDir( $templates_path . '_smarty/configs/' );
		$this->setCacheDir( $templates_path . '_smarty/cache/' );
        $this->addPluginsDir( ROOT_PATH . '/libs/Smarty-sifo-plugins' );
        $this->addPluginsDir( $templates_path . '_smarty/plugins' );

        // Settings:
		// Smarty tests to see if the current template has changed (different time stamp) since the last time it was compiled. If it has changed, it recompiles
		$this->compile_check = true;

		// This forces Smarty to (re)compile templates on every invocation. This setting overrides  $compile_check
		$this->force_compile = false;

		// This tells Smarty whether or not to cache the output of the templates to the  $cache_dir. 0=no caching, 1=use cache with $cache_lifetime, 2=different $cache_lifetime per template
		$this->caching = 0;

		//  This is the length of time in seconds that a template cache is valid. Once this time has expired, the cache will be regenerated.
		// Infinite=-1, N seconds=N,
		$this->cache_lifetime = 90;

		// Memcached caching:
		// $this->cache_handler_func = array( &$this, "smarty_memcache_handler" );

		// If set to TRUE, Smarty will respect the If-Modified-Since header sent from the client. If the cached file timestamp has not changed since the last visit, then a '304: Not Modified'  header will be sent instead of the content
		$this->cache_modified_check = true;

		$this->debugging = 0;
	}
}
?>