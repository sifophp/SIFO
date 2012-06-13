{if is_array($debug.queries)}
	<h1 id="db_queries">{t}DB Queries{/t}</h1>
{foreach name=queries from=$debug.queries item=query}
	<h2 class="queries {if false !== $query.error}query_error{else}query_{$query.type}{/if} {if $query.duplicated}query_duplicated{/if}{if $query.time >= 0.5 && $query.time < 1} query_slow{elseif $query.time >= 1} query_very_slow{/if}" id="queries_{$smarty.foreach.queries.index}">
		<a class="debug_toggle_view" href="#" rel="queries_content_{$smarty.foreach.queries.index}">
		{$smarty.foreach.queries.index+1}. {if $query.type=='read'}[R]{else}[W]{/if} {$query.tag}</a> <small>({$query.time|time_format} - rows:{$query.rows_num})</small></h2>
	<div id="queries_content_{$smarty.foreach.queries.index}" class="debug_contents">
		<pre>{$query.sql|escape}</pre>
{		if false !== $query.error}
		<pre style="color:red">
--
{$query.error}
		</pre>
{		/if}
		<table>
			<tr>
				<th>Host</th>
				<th>Destination</th>
				<th>Database</th>
				<th>User</th>
				<th>Controller</th>
			</tr>
			<tr>
				<td>{$query.host}</td>
				<td>{if isset($query.destination)}{$query.destination|upper}{/if}</td>
				<td>{$query.database}</td>
				<td>{$query.user}</td>
				<td>{if isset($query.controller)}{$query.controller}{/if}</td>
			</tr>
		</table>
{		if $query.rows_num > 0 }
			<strong>{t}Resultset{/t}</strong>:
			<table>

{*		RESPONSE CONTAINS AN ARRAY WITH A SINGLE RECRODSET AND ITS PROPERTIES *}
{			if is_array($query.resultset) && !isset($query.resultset[0])}
			<tr>
{			foreach from=$query.resultset item=value key=field}
				<th>{$field}</th>
{			/foreach}
			</tr>
			<tr>
{			foreach from=$query.resultset item=value}
				<td title="{$value|escape}">{$value|truncate:50:"..."|escape}</td>
{			/foreach}
			</tr>
{			else}
{*		RESPONSE CONTAINS AN ARRAY WITH ALL THE ROWS *}
{			if is_array($query.resultset)}
			<tr>
{			foreach from=$query.resultset[0] item=value key=field}
				<th>{$field}</th>
{			/foreach}
			</tr>
{			foreach from=$query.resultset item=row}
			<tr>
{				foreach from=$row item=value}
					<td title="{$value|escape}">{$value|truncate:50:"..."|escape}</td>
	{				/foreach}
				</tr>
	{			/foreach}
		{else}
			{* STRANGE FORMAT OF DATA *}
			<tr><td><pre>{$query.resultset|@var_dump}</pre></td></tr>
		{/if}
{			/if}
			</table>
{		else}
			<strong>{t}Empty resultset{/t}</strong>
{		/if}
{if isset($query.trace) }<pre>{$query.trace}</pre>{/if}
	</div>
{/foreach}
{/if}