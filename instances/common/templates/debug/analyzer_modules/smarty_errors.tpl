{if is_array($debug.smarty_errors)}
	<h2 id="smarty_erorrs_title">{t}Smarty Errors{/t}</h2>
{foreach name=smerrors from=$debug.smarty_errors item=error key=affected_file}
	<h3 class="sm_errors query_error" id="smarty_errors_{$smarty.foreach.smerrors.index}">
		<a class="debug_toggle_view" href="#" rel="smarty_errors_content_{$smarty.foreach.smerrors.index}">
		{$smarty.foreach.smerrors.index+1}. {$affected_file}</a> </h3>
	<div id="smarty_errors_content_{$smarty.foreach.smerrors.index}" class="debug_contents">
			{$error}
	</div>
{/foreach}
{/if}