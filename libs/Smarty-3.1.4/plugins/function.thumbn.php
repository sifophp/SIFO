<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {fill} function plugin
 *
 * Type:     function<br>
 * Name:     fill<br>
 * Input:<br>
 *           - [any]      (required) - string
 *           - subject       (required) - string
 *           - delimiter  (optional, defaults to '%' ) - string
 * Purpose:  Fills the variables found in 'subject' with the paramaters passed. The variables are any word surrounded by two delimiters.
 *           
 *           Examples of usage:
 *           
 *           {fill subject="http://domain.com/profile/%username%" username='fred'}
 *           Output: http://domain.com/profile/fred
 *
 *           {fill subject="Hello %user%, welcome aboard!" user=Fred}
 *           Outputs: Hello Fred, welcome aboard
 *
 *           {fill subject="http://||subdomain||.domain.com/||page||/||action||" subdomain='www' page='my-first-post' action='vote' delimiter='||'}
 *           Outputs: http://www.domain.com/my-first-post/vote
 *
 * @link    http://www.harecoded.com/fill-smarty-php-plugin-311577
 * @author Albert Lombarte <alombarte at harecoded dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_thumbn($params, &$smarty)
{
    if (!isset($params['source'])) 
    {
        $smarty->trigger_error("fill: The attribute 'source' and at least one parameter is needed in function {url}", E_USER_NOTICE);
    }
    
    if (!isset($params['size']))
    {
    	$params['size'] = 'mini';
    }
    
    if ( !in_array($params['size'], array('mini','thumbnail','medium','transparent','full') ) )
    {
        $smarty->trigger_error("fill: The attribute 'size' has an invalid value {url}", E_USER_NOTICE);
    }
    
    if ( is_array( $params['source'] ) )
    {
    	$wine['name']		= ($params['source']['wine_name'])?$params['source']['wine_name']:$params['source']['name'];
    	$wine['type']		= ($params['source']['wine_type'])?$params['source']['wine_type']:$params['source']['type'];
    	$wine['image_url']	= $params['source']['image_url'];    	
    }
    else
    {
    	$wine['name']		= '';
    	$wine['image_url']	= $params['source'];
    	$wine['type']		= 'tinto';
    }

    unset( $params['source'] );
        
    return UtilsUvinum::getWineThumbnail( $wine, $params['size'] );

}

/* vim: set expandtab: */

?>
