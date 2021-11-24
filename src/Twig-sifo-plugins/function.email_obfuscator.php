<?php

function twig_function_email_obfuscator()
{
    return new \Twig_Function(
        'email_obfuscator', function (array $args = []) {
        // email address to obfuscate.
        $email = isset($args[0]['email']) ? $args[0]['email'] : '';
        // optional text to show instead the email
        $linktext = isset($args[0]['text']) ? $args[0]['text'] : '';
        // style information via class.
        $style_class = isset($args[0]['class']) ? ' class=\"' . $args[0]['class'] . '\" ' : '';
        // style information via id.
        $style_id = isset($args[0]['id']) ? ' id=\"' . $args[0]['id'] . '\" ' : '';

        // Getting the extra params for the case of %1, %2, etc in the linktext. Using ; like separator.
        $extra_params = array();
        if (isset($args[0]['extra']))
        {
            $extra_params = explode(';', $args[0]['extra']);
        }

        // Translating linktext
        $textbefore = '';
        $textafter  = '';
        if (!empty($linktext))
        {
            $calling_class = get_called_class();
            $obj = new $calling_class();
            $temp = smarty_block_t($extra_params, $linktext, $obj);
            // If the email is inside the text string
            $email_position = strpos($temp, $email);
            if ($email_position)
            {
                // If the email is inside the string we make the link only in the email address
                $textbefore = substr($temp, 0, $email_position);
                $textafter  = substr($temp, strpos($temp, $email) + strlen($email));
                $linktext   = '';
            }
            else
            {
                // Else the link is all the string
                $linktext = $temp;
            }
        }

        $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
        $key           = str_shuffle($character_set);
        $cipher_text   = '';
        $id            = 'e' . rand(1, 999999999);
        for ($i = 0; $i < strlen($email); $i += 1)
        {
            $cipher_text .= $key[strpos($character_set, $email[$i])];
        }
        $script = 'var namex="' . $linktext . '";var a="' . $key . '";var b=a.split("").sort().join("");var c="' . $cipher_text . '";var d="";';
        $script .= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));var linktext=(namex.length == 0)?linktext=d:linktext=namex;var textbefore="'
            . $textbefore . '";var textafter="' . $textafter . '";';
        $script .= 'document.getElementById("' . $id . '").innerHTML=textbefore+"<a ' . $style_id . $style_class
            . ' href=\\"mailto:"+d+"\\">"+linktext+"<\/a>"+textafter';
        $script = "eval(\"" . str_replace(array("\\", '"'), array("\\\\", '\"'), $script) . "\")";
        $script = '<script type="text/javascript">/*<![CDATA[*/' . $script . '/*]]>*/</script>';

        return '<span id="' . $id . '">[javascript protected email address]</span>' . $script;
    }, ['is_variadic' => true]
    );
}
