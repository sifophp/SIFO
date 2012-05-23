<?php
{if isset( $instance_parent )}
include_once ROOT_PATH . '/instances/{$instance_parent}/config/{$file_name}';
{/if}
{foreach from=$config item=c key=k}
{	if is_array( $c ) }
{		foreach from=$c item=path key=instance}
$config['{$k}']['{$instance}'] = '{$path}';
{		/foreach}
{	else}
$config['{$k}'] = '{$c}';
{	/if}
{/foreach}

{*$config = {$config|@var_export};*}