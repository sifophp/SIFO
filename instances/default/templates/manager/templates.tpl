<?php
{foreach from=$config item=c key=k}
{if is_array($c) }
$config['{$k}'] = array( {foreach from=$c item=v}'{$v}',{/foreach} );
{else}
$config['{$k}'] = '{$c}';
{/if}
{/foreach}
?>