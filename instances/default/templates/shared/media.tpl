{if !empty( $css_groups )}
{	foreach from=$css_groups item=css_group}
		<link rel="stylesheet" type="text/css" media="{$media.$css_group.media}" href="{$url.static}/css/generated/{$css_group}.css?rev={$static_rev|default:'unset'}" />
{	/foreach}
{/if}

{if !empty( $js_groups ) }
{	foreach from=$js_groups item=group}
		<script type="text/javascript" src="{$url.static}/js/generated/{$group}.js?rev={$static_rev|default:'unset'}"></script>
{	/foreach}
{/if}