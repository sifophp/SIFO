<!doctype html>

<html lang="en">
<head>
	<meta charset="utf-8">

	<title>Sifo debug analyzer</title>
	<meta name="description" content="Sifo debug analyzer">
	<meta name="author" content="Sifo">

	{literal}
		<style type="text/css">
			/* @group DEBUG reset */
			#debug {
				text-align:left;
				border-top:1px solid #ccc;
				padding:18px;
				font-size:12px;
				line-height:18px;
				font-size:12px;
				font-family:Arial, sans-serif;
				background:#fff;
				color:#333;
			}

			#debug a {
				color:#2E79D0;
			}

			#debug h1, #debug h2, #debug h3, #debug h4  {
				font-family:Arial, sans-serif;
				color:#444;
				display: block;
				position:relative;
				top:auto;
			}

			#debug h1 a, #debug h2 a, #debug h3 a, #debug h4 a {
				color:#fff;
				text-decoration: none;
			}

			#debug h1 a { color:#333; }

			#debug h1 a:after, #debug h2 a:after, #debug h3 a:after, #debug h4 a:after {
				content:" »";
			}

			#debug h1 {
				font-size: 30px;
				line-height: 36px;
				margin-top: 18px;
			}

			#debug h2 {
				font-size: 24px;
				line-height: 36px;
				margin-top: 18px;
			}

			#debug h3 {
				font-size: 18px;
				line-height: 18px;
				margin-bottom: 18px;
				background:#555;
				color:#fff;
				padding:9px;
				margin:0;
				border-bottom:1px solid #333;
			}

			#debug h4 {
				background-color:#A69600;
				font-size:15px;
				padding:10px;
				border-bottom:1px solid #333;
			}

			#debug li {
				margin-left:18px;
				list-style-type:disc;
			}
			/* @endgroup DEBUG reset */

			#debug div.debug_contents {
				background:#efefef;
				padding:9px;
				display:none;
				overflow: auto;
			}

			#debug div.visible {
				display:block;
			}

			#debug div.debug_contents table {
				margin:9px 0;
			}

			#debug div.debug_contents table th, #debug div.debug_contents table td {
				border:1px solid #ccc;
				padding:3px;
				font-family:monospace;
			}

			#debug div.debug_contents table th {
				font-weight:bold;
				background:#ddd;
				cursor:default;
			}

			#debug .queries small {
				font-size:12px;
				font-weight: normal;
			}

			#debug .query_read {
				background-color: green;
			}

			#debug .query_write {
				background-color: darkblue;
			}

			#debug .query_error {
				background-color: red;
			}

			#debug .query_duplicated {
				background-color: orange;
			}

			#debug .query_slow small, #debug .query_very_slow small,
			#debug .slow {
				background:#eadaaf;
				color:red;
				padding:3px;
				font-weight: bold;
			}

			#debug .query_slow small:after {
				content:" --> Alert: Slow Query";
			}

			#debug .query_very_slow small:after {
				content:" --> Alert: VERY Slow Query";
			}

			#debug .query_very_slow small {
				background:red;
				color:#fff;
			}

			#debug .array strong {
				color: blue;
			}

			#debug .benchmark_contents { float: left; width: 70% }
			#debug .benchmarks_legend { float:right; width:29% }

			.debug_hidden {
			  display: none;
			  visibility: hidden;
			}
		</style>
	{/literal}
</head>

<body>
	<h1>
		<a id="unpin_execution" title="Unpin this execution debug" href="{$url.sifo_debug_actions}?action=pin&execution_key={$debug_data.parent_execution.execution_key}&is_pinned=0"{if !$debug_data.parent_execution.is_pinned} class="debug_hidden"{/if} data-counterpart="pin_execution">★</a>
		<a id="pin_execution" title="Pin this execution debug" href="{$url.sifo_debug_actions}?action=pin&execution_key={$debug_data.parent_execution.execution_key}&is_pinned=1"{if $debug_data.parent_execution.is_pinned} class="debug_hidden"{/if} data-counterpart="unpin_execution">☆</a>
		Debug for execution key: <a href="{$url.sifo_debug_analyzer}?execution_key={$debug_data.parent_execution.execution_key}" title="permalink">{$debug_data.parent_execution.execution_key}</a>
	</h1>

	<h2>{if !$debug_data.parent_execution.is_json}Non {/if}JSON response served at {$debug_data.parent_execution.date_time}</h2>
	<h3>URL: <a href="{$debug_data.parent_execution.url}" title="URL executed">{$debug_data.parent_execution.url}</a></h3>
	<h4>{if $debug_data.parent_execution.parent_execution_key}Parent execution: <a href="{$url.sifo_debug_analyzer}?execution_key={$debug_data.parent_execution.parent_execution_key}" title="Parent execution Debug Analyzer">{$debug_data.parent_execution.parent_execution_key}</a>.{else}No parent execution.{/if}</h4>

	{if isset( $debug_data.children_executions )}
		<h4>{$debug_data.children_executions|count} children executions:</h4>

		<ul>
			{foreach $debug_data.children_executions as $child_debug_data}
				<li>{$child_debug_data@iteration}.- <a title="Link to the debug of the child execution number {$child_debug_data@iteration}" href="#ajax_debug_{$child_debug_data@index}">{$child_debug_data.execution_key}</a> - <small><a href="{$child_debug_data.url}">{$child_debug_data.url}</a></small></li>
			{/foreach}
		</ul>
	{/if}

	<div id="debug">

		{* Mini debug *}
		{include file="debug/mini_debug.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key children_executions=$debug_data.children_executions|default:null}

		{* Traces *}
		{include file="debug/analyzer_modules/traces.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Log messages *}
		{include file="debug/analyzer_modules/log_messages.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Sent headers*}
		{include file="debug/analyzer_modules/headers.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Smarty Error: Compilation and runtime smarty errors*}
		{include file="debug/analyzer_modules/smarty_errors.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Basic debug: Benchmarks and controllers *}
		{include file="debug/analyzer_modules/basic_debug.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* SphinxQl and other search-related queries *}
		{include file="debug/analyzer_modules/sphinxql.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Sphinx and other search-related queries *}
		{include file="debug/analyzer_modules/search.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Database queries *}
		{include file="debug/analyzer_modules/database.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Redis callstack *}
		{include file="debug/analyzer_modules/redis.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Post *}
		{include file="debug/analyzer_modules/post.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Session *}
		{include file="debug/analyzer_modules/session.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{* Cookies *}
		{include file="debug/analyzer_modules/cookies.tpl"|custom_tpl debug=$debug_data.parent_execution.debug_content execution_key=$debug_data.parent_execution.execution_key}

		{if isset( $debug_data.children_executions )}
			<div id="ajax_debug">
				{foreach $debug_data.children_executions as $child_debug_data}
					<h1 class="ajax_title">
						<a class="debug_toggle_view" rel="ajax_debug_{$child_debug_data@index}" href="#">{$child_debug_data@iteration}.- AJAX call: {$child_debug_data.url}</a>
					</h1>

					<div id="ajax_debug_{$child_debug_data@index}" {if $child_debug_data@index % 2 == 0}style="background-color: rgba(209, 209, 209, 0.84);"{/if}>

						{* Traces *}
						{include file="debug/analyzer_modules/traces.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Log messages *}
						{include file="debug/analyzer_modules/log_messages.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Sent headers*}
						{include file="debug/analyzer_modules/headers.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Smarty Error: Compilation and runtime smarty errors*}
						{include file="debug/analyzer_modules/smarty_errors.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Basic debug: Benchmarks and controllers *}
						{include file="debug/analyzer_modules/basic_debug.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* SphinxQl and other search-related queries *}
						{include file="debug/analyzer_modules/sphinxql.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Sphinx and other search-related queries *}
						{include file="debug/analyzer_modules/search.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Database queries *}
						{include file="debug/analyzer_modules/database.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Redis callstack *}
						{include file="debug/analyzer_modules/redis.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Post *}
						{include file="debug/analyzer_modules/post.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Session *}
						{include file="debug/analyzer_modules/session.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}

						{* Cookies *}
						{include file="debug/analyzer_modules/cookies.tpl"|custom_tpl debug=$child_debug_data.debug_content execution_key=$child_debug_data.execution_key}
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
</body>
</html>