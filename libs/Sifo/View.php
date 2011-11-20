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

/**
 * Templating engine. Compiles some smarty stuff for an easier management.
 */
class View
{
	/**
	 * The Smarty object.
	 *
	 * @var Smarty
	 */
	protected $view;

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
		include_once ROOT_PATH . '/libs/'. Config::getInstance()->getLibrary( 'smarty' ).'/Smarty.class.php';
		$this->view = new \Smarty();

		$this->instance = Bootstrap::$instance;

		// Paths definition:
		$templates_path = ROOT_PATH . '/instances/' . $this->instance . '/templates/';
		$this->view->setTemplateDir( ROOT_PATH . '/' );  // The templates are taken using the templates.config.php mappings, under the variable $_tpls.
		$this->view->setCompileDir( $templates_path . '_smarty/compile/' );
		$this->view->setConfigDir( $templates_path . '_smarty/configs/' );
		$this->view->setCacheDir( $templates_path . '_smarty/cache/' );
		$this->view->addPluginsDir( $templates_path . '_smarty/plugins' );

		// Settings:
		// Smarty tests to see if the current template has changed (different time stamp) since the last time it was compiled. If it has changed, it recompiles
		$this->view->compile_check = true;

		// This forces Smarty to (re)compile templates on every invocation. This setting overrides  $compile_check
		$this->view->force_compile = false;

		// This tells Smarty whether or not to cache the output of the templates to the  $cache_dir. 0=no caching, 1=use cache with $cache_lifetime, 2=different $cache_lifetime per template
		$this->view->caching = 0;

		//  This is the length of time in seconds that a template cache is valid. Once this time has expired, the cache will be regenerated.
		// Infinite=-1, N seconds=N,
		$this->view->cache_lifetime = 90;

		// Memcached caching:
		// $this->cache_handler_func = array( &$this, "smarty_memcache_handler" );

		// If set to TRUE, Smarty will respect the If-Modified-Since header sent from the client. If the cached file timestamp has not changed since the last visit, then a '304: Not Modified'  header will be sent instead of the content
		$this->view->cache_modified_check = true;

		// SMARTY 3 compatibility. Delete once Sifo doesn't support Smarty 2.
		if ( isset ( $this->view->auto_literal ) )
		{
			$this->view->auto_literal = false;
		}

		$this->view->debugging = 0;
	}

	/**
	 * Retrieves original Smarty properties not defined in this class.
	 *
	 * @param string $name The undefined property to be retrieved.
	 * @return mixed
	 */
	public function  __get( $name )
	{
		return $this->view->$name;
	}

	/**
	 * Sets original Smarty properties not defined in this class.
	 *
	 * @param string $name The undefined property to be setted.
	 * @return mixed
	 */
	public function  __set( $name, $value )
	{
		$this->view->$name = $value;
	}

	/**
	 * Retrieves original Smarty methods not defined in this class.
	 *
	 * @param string $name The undefined method to be retrieved.
	 * @return mixed
	 */
	public function  __call( $name, $arguments )
	{
		return call_user_func_array(array( $this->view, $name ), $arguments );
	}

}
?>