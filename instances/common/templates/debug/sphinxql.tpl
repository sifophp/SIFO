{if is_array($debug.sphinxql)}
	<h2 id="sphinxql_queries">{t}SphinxQl{/t}</h2>
{foreach name=search from=$debug.sphinxql item=value}
	<h3 class="queries query_read{if $value.error} query_error{/if}" id="sphinxql_{$smarty.foreach.search.index}"><a class="debug_toggle_view" rel="sphinxql_content_{$smarty.foreach.search.index}{$execution_key}" href="#">{$smarty.foreach.search.index+1}. [R] {$value.tag}</a> <small>({$value.time|time_format} - match: {$value.total_found|default:''} elements - return: {$value.returned_rows|default:''} elements )</small></h3>
	<div id="sphinxql_content_{$smarty.foreach.search.index}{$execution_key}" class="debug_contents">

		<table>
			<tr>
				<th>Host</th>
				<th>Port</th>
				{if !empty( $value.connection_data.weight )}<th>Weight</th>{/if}
				<th>Trace <a href="#" class="debug_toggle_view" rel="sphinxql_backtrace_{$smarty.foreach.search.index}{$execution_key}">(show/collapse full trace)</a></th>
			</tr>
			<tr>
				<td>{$value.connection_data.server|default:''}</td>
				<td>{$value.connection_data.port|default:''}</td>
				{if !empty( $value.connection_data.weight )}<td>{$value.connection_data.weight}</td>{/if}
				<td>
                    <div id="sphinxql_backtrace_{$smarty.foreach.search.index}{$execution_key}" class="debug_contents">
                    {foreach from=$value.backtrace item=step name=backtrace_iterator}
                        {if !$smarty.foreach.backtrace_iterator.last}{$step}<br />{/if}
                    {/foreach}
                    </div>
                    {$value.backtrace[$value.backtrace|count - 1]}
                </td>
			</tr>
		</table>

		{if !empty( $value.error )}
		<h4 class="query_error">{$value.error}</h4>
		{/if}

		{foreach name=sphinxql_query from=$value.queries item=query}
		{if count( $value.queries ) > 1}
			<h4 class="{if !empty( $query.error )}query_error{/if}">{$smarty.foreach.sphinxql_query.index}. {$query.tag} <small>({$query.time|time_format|default:''} - match: {$query.total_found|default:''} elements - return: {$query.returned_rows|default:''} elements )</small></h4>
		{/if}
		{if !empty( $query.error )}<p class="query_error"><b>{$query.error}</b></p>{/if}
		<pre>{$query.query|escape}</pre>

		<table>
			<tr>
{			foreach from=$query.resultset[0] key=attribute item=values}
				<th>{$attribute}</th>
{			/foreach}
			</tr>
{if isset( $query.resultset )}
{			foreach from=$query.resultset key=id item=match}
			<tr>
{				foreach from=$match key=attribute item=values}
				<td>{if is_array($values)}{$values|debug_print_var nofilter}{else}{$values}{/if}</td>
{				/foreach}
			</tr>
{			/foreach}
{/if}
		</table>
		{/foreach}
	</div>
{/foreach}
{/if}