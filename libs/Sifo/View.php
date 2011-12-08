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
	 * Constructor. Inherits all methods from Smarty.
	 */
	public function __construct()
	{
		parent::__construct();

		// Paths definition:
		$templates_path = ROOT_PATH . '/instances/' . Bootstrap::$instance . '/templates/';
		$this->setTemplateDir( ROOT_PATH . '/' );  // The templates are taken using the templates.config.php mappings, under the variable $_tpls.
		$this->setCompileDir( $templates_path . '_smarty/compile/' );
		$this->setConfigDir( $templates_path . '_smarty/configs/' );
		$this->setCacheDir( $templates_path . '_smarty/cache/' );
        $this->addPluginsDir( ROOT_PATH . '/libs/Smarty-sifo-plugins' );
        $this->addPluginsDir( $templates_path . '_smarty/plugins' );

		// Set this to false to avoid magical parsing of literal blocks without the {literal} tags.
		$this->auto_literal = false;
	}
}
?>