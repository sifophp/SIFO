{if !empty( $css_generated )}
	{foreach from=$css_generated item=css_file key=css_type}
		<link rel="stylesheet" type="text/css" media="{$css_type}" href="{$url.static}/{$css_file}" />
	{/foreach}
{/if}

<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

{if !empty( $js_generated ) && !empty($js_generated.common) }
	<script type="text/javascript" src="{$url.static}/{$js_generated.common}"></script>
{/if}