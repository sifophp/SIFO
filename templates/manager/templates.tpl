{"<?php"}
{if !empty( $parent_config_file )}
include ROOT_PATH . '{$parent_config_file}';
{/if}

{foreach from=$config item=c key=k}
{if is_array( $c ) }
{foreach from=$c item=path key=instance}
$config['{$k}']['{$instance}'] = '{$path}';
{/foreach}
{else}
$config['{$k}'] = '{$c}';
{/if}
{/foreach}
