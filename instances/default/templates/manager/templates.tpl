<?php
{foreach from=$config item=c key=k}
{if is_array($c) }
$config['{$k}'] = {$c|var_export} );
{else}
$config['{$k}'] = '{$c}';
{/if}
{/foreach}
?>