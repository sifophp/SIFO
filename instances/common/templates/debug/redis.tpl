{if is_array($debug.redis)}
<h1 id="redis_callstack">{t}Redis callstack{/t}</h1>
	{foreach name=call from=$debug.redis item=value}
	<h3 class="queries query_read" id="redis_{$smarty.foreach.call.index}"><a class="debug_toggle_view" rel="redis_content_{$smarty.foreach.call.index}{$execution_key}" href="#">{$smarty.foreach.call.index+1}. {$value.method}</a> <small>({$value.connection.database}@{$value.connection.host}:{$value.connection.port} - return: {$value.results|count} elements )</small></h3>
	<div id="redis_content_{$smarty.foreach.call.index}{$execution_key}" class="debug_contents">
		<table>
			<tr>
				<th>Host</th>
				<th>Port</th>
				<th>Database</th>
				<th>Arguments</th>
				<th>Results</th>
				<th>Trace</th>
			</tr>
			<tr>
				<td>{$value.connection.host}</td>
				<td>{$value.connection.port}</td>
				<td>{$value.connection.database}</td>
				<td>
					{if !empty($value.args)}
					<strong>Array</strong>
					{foreach from=$value.args item=arr key=k}
					<ul>
						<li><strong>{$k}</strong>: {if is_array($arr)}<pre>{$arr|debug_print_var}</pre>{else}<code>{$arr|escape}</code>{/if}</li>
					</ul>
					{/foreach}
					{else}
					None
					{/if}
				</td>
				<td>
					{if is_array($value.results)}
					<strong>Array</strong>
					{foreach from=$value.results item=arr key=k}
						<ul>
							<li><strong>{$k}</strong>: {if is_array($arr)}<pre>{$arr|debug_print_var}</pre>{else}<code>{$arr|escape}</code>{/if}</li>
						</ul>
					{/foreach}
					{else}
					{$value.results|debug_print_var}
					{/if}
				</td>
				<td>{$value.controller}</td>
			</tr>
		</table>
	</div>
	{/foreach}
{/if}