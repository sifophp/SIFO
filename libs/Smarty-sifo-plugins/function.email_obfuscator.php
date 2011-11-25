<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

include_once('block.t.php');

/**
 * Smarty email obfuscator plugin
 *
 * Type:     function.
 * Name:     email_obfuscator.
 * Purpose:  email obfuscation for protecting emails.
 * 
 * @author   Nino Dafonte <nino.dafonte@gmail.com>.
 * @param	string 	Email to obfuscate (received automatically).
 * @param 	string 	Text to show instead of the email.
 * @return 	string 	JavaScript to show the obfuscated email.
*/
function smarty_function_email_obfuscator($params, &$smarty)
{

	// email address to obfuscate.
	$email = ( isset( $params['email'] ) ) ? $params['email'] : '';
	// optional text to show instead the email
	$linktext = ( isset( $params['text'] ) ) ? $params['text'] : '';
	// style information via class.
	$style_class = ( isset( $params['class'] ) ) ? ' class=\"' . $params['class'] . '\" ' : '';
	// style information via id.
	$style_id = ( isset( $params['id'] ) ) ? ' id=\"' . $params['id'] . '\" ' : '';

	// Getting the extra params for the case of %1, %2, etc in the linktext. Using ; like separator.
	$extra_params = array();
	if ( isset( $params['extra'] ) ) {
		$extra_params = explode( ';', $params['extra'] );
	}

	// Translating linktext
	$textbefore = '';
	$textafter = '';
	if ( !empty( $linktext ) )
	{
		$temp = smarty_block_t( $extra_params, $linktext, $this, null);
		// If the email is inside the text string
		$email_position = strpos( $temp, $email );
		if ( $email_position )
		{
			// If the email is inside the string we make the link only in the email address
			$textbefore = substr( $temp, 0, $email_position );
			$textafter = substr( $temp, strpos( $temp, $email ) + strlen( $email ) );
			$linktext = '';
		}
		else
		{
			// Else the link is all the string
			$linktext = $temp;
		}
	}

	$character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
	$key = str_shuffle( $character_set );
	$cipher_text = '';
	$id = 'e'.rand( 1, 999999999 );
	for ( $i = 0; $i < strlen( $email ); $i += 1 )
	{
		$cipher_text.= $key[ strpos( $character_set, $email[ $i ] ) ];
	}
	$script = 'var namex="' . $linktext . '";var a="' . $key . '";var b=a.split("").sort().join("");var c="' . $cipher_text . '";var d="";';
	$script .= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));var linktext=(namex.length == 0)?linktext=d:linktext=namex;var textbefore="' . $textbefore . '";var textafter="' . $textafter . '";';
	$script .= 'document.getElementById("' . $id . '").innerHTML=textbefore+"<a ' . $style_id . $style_class . ' href=\\"mailto:"+d+"\\">"+linktext+"<\/a>"+textafter';
	$script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")";
	$script = '<script type="text/javascript">/*<![CDATA[*/' . $script . '/*]]>*/</script>';
	return '<span id="' . $id . '">[javascript protected email address]</span>' . $script;
}
?>