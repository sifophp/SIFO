{"<?php"}
{if !empty( $parent_config_file )}
$config = include ROOT_PATH . '{$parent_config_file}';
{/if}

{foreach from=$config item=c key=k}
{if is_array( $c ) }
{foreach from=$c item=path key=instance}
$config['{$instance}\\{$k}'] = '{$path}';
{/foreach}
{else}
$config['{$k}'] = '{$c}';
{/if}
{/foreach}

return $config;
