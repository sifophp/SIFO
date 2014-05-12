{* HTML messages *}
{if isset( $debug.log_messages.html )}
	<h2 id="benchmarks">{t}Log Messages{/t}</h2>
	{foreach from=$debug.log_messages.html item=value name=html_log}
	<h3 id="html_log_{$smarty.foreach.html_log.index}" class="{if $value.type=='error'}query_error{/if}{if $value.type=='warn'}query_duplicated{/if}">
		<a class="debug_toggle_view" rel="html_log_content_{$smarty.foreach.html_log.index}" href="#">
			{$smarty.foreach.html_log.index+1}. Message {$value.type}
		</a>
	</h3>
	<div id="html_log_content_{$smarty.foreach.html_log.index}" class="debug_contents">
			<pre>
	{			$value.message|debug_print_var nofilter}
			</pre>
		</div>

	{/foreach}
{/if}

{* Console messages *}
{if isset( $debug.log_messages.browser_console )}
<script>
	{literal}
	// JavaScript debug this is for IE and other browsers w/o console
	if (!window.console) console = {};
	console.log = console.log || function(){};
	console.warn = console.warn || function(){};
	console.error = console.error || function(){};
	console.info = console.info || function(){};
	console.debug = console.debug || function(){};
	{/literal}

	{foreach from=$debug.log_messages.browser_console item=debug_message name=debug_messages_iteration}
		{if $debug_message.is_object}
	var object_{$smarty.foreach.debug_messages_iteration.iteration} = {$debug_message.message};
	var val_{$smarty.foreach.debug_messages_iteration.iteration} = eval("(" + object_{$smarty.foreach.debug_messages_iteration.iteration} + ")" );
		{else}
	var val_{$smarty.foreach.debug_messages_iteration.iteration} = {$debug_message.message};
		{/if}
	console.{$debug_message.type}( val_{$smarty.foreach.debug_messages_iteration.iteration} );
	{/foreach}
</script>
{/if}

{* Alert messages *}
{if isset( $debug.log_messages.alert )}
<script>
	{foreach from=$debug.log_messages.alert item=debug_message name=debug_messages_iteration}
		alert( {$debug_message.message} );
	{/foreach}
</script>
{/if}