<?php
use Sifo\Config;

/**
 * Allow using {extends} and {include} Smarty functions taking into account the instance
 * inheritance. Given template will be translated to full path tempalte file for
 * active instance.
 *
 * @param $template
 * @return string
 */
function smarty_modifier_custom_tpl( $template )
{
	if ( !isset( $template ) )
	{
		trigger_error( "custom_tpl: The attribute 'template' are not set", E_USER_ERROR );
	}

	$instance_templates = Config::getInstance()->getConfig( 'templates' );
	if ( isset( $instance_templates[$template] ) )
	{
		$selected_template = $instance_templates[$template];
	}
	else
	{
		trigger_error( "The template '{$template}' has not been found in the templates folder.", E_USER_ERROR );
		return false;
	}

	return ( ROOT_PATH . "/$selected_template" );
}
