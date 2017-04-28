{if isset( $debug.traces ) && is_array( $debug.traces )}
	<h3 id="sess_{$smarty.foreach.session.index}"><a class="debug_toggle_view" rel="traces_content" href="#">{t}Show traces{/t}</a><h3 id="sess_{$smarty.foreach.session.index}">
	<div id="traces_content" class="debug_contents">
		<ul>
			{foreach $debug.traces as $trace}
				<li>
					{if is_array($trace)}
						{$trace|debug_print_var nofilter}
					{else}
						"{$trace}"
					{/if}
				</li>
			{/foreach}
		</ul>
	</div>
{/if}