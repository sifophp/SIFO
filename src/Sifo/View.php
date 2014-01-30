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
	protected $template;

	/**
	 * Constructor. Inherits all methods from Smarty.
	 */
	public function __construct()
	{
		parent::__construct();

		// Get the instances inheritance.
		$instance_inheritance = Domains::getInstance()->getInstanceInheritance();

		// If there is inheritance.
		if ( is_array( $instance_inheritance ) )
		{
			// First the child instance, last the parent instance.
			$instance_inheritance = array_reverse( $instance_inheritance );
			foreach ( $instance_inheritance as $current_instance )
			{
				$this->addPluginsDir( ROOT_PATH . '/instances/' . $current_instance . '/templates/' . '_smarty/plugins' );
			}
		}
		else
		{
			$this->addPluginsDir( $templates_path . '_smarty/plugins' );
		}
		// Last path is the default smarty plugins directory.
		$this->addPluginsDir( ROOT_PATH . '/libs/Smarty-sifo-plugins' );

		$this->setTemplateDir( ROOT_PATH . '/' );  // The templates are taken using the templates.config.php mappings, under the variable $_tpls.

		// Paths definition:
		$templates_path = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/';
		$this->setCompileDir( $templates_path . '_smarty/compile/' );
		$this->setConfigDir( $templates_path . '_smarty/configs/' );
		$this->setCacheDir( $templates_path . '_smarty/cache/' );

		if ( ( $view_setting = Config::getInstance()->getConfig('views') ) && ( isset( $view_setting['smarty'] ) ) )
		{
			$smarty_settings = $view_setting['smarty'];

			if ( isset( $smarty_settings['custom_plugins_dir'] ) )
			{
				// If is set, this path will be the default smarty plugins directory.
				$this->addPluginsDir( $smarty_settings['custom_plugins_dir'] );
			}
			// Set this to false to avoid magical parsing of literal blocks without the {literal} tags.
			$this->auto_literal = $smarty_settings['auto_literal'];
			$this->escape_html = $smarty_settings['escape_html'];
		}
	}

	public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
	{
		$this->template = $template;

		set_error_handler( array( $this, "customErrorHandler" ) );
		self::muteExpectedErrors();
		$result = parent::fetch( $template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars );

		// The current method launch an set_error_handler but inside self::muteExpectedErrors() ther is one more.
		// We need launch two restores to turn back to the preview expected behaviour.
		restore_error_handler();
		restore_error_handler();

		return $result;
	}

	protected function customErrorHandler( $errno, $errstr, $errfile, $errline )
	{
		$error_friendly = Debug::friendlyErrorType( $errno );
		$error_string = "[{$error_friendly}] {$errstr} in {$errfile}:{$errline}";

		if( Domains::getInstance()->getDebugMode() )
		{
			Debug::subSet( 'smarty_errors',$this->template, '<pre>'.$error_string.'</pre>', true);
		}

		// Smarty only write PHP USER errors to log:
		if ( ( $raw_url = Urls::$actual_url ) )
		{
			error_log( "URL '{$raw_url}' launched the following Smarty error: {$error_string} fetching {$this->template}" );
			return true;
		}

		// Follow the error handling flow:
		return false;
	}

}
