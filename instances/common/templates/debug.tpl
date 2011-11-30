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

#debug h1, #debug h2, #debug h3  {
	font-family:Arial, sans-serif;
	color:#444;
	display: block;
	position:relative;
	top:auto;
}

#debug h1 a, #debug h2 a, #debug h3 a {
	color:#fff;
	text-decoration: none;
}

#debug h1 a:after, #debug h2 a:after, #debug h3 a:after {
	content:" »";
}

#debug h1 {
	font-size: 24px;
	line-height: 36px;
	margin-top: 18px;
}

#debug h2 {
	font-size: 18px;
	line-height: 18px;
	margin-bottom: 18px;
}

#debug h3 {
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

#debug h2 {
	background:#555;
	color:#fff;
	padding:9px;
	margin:0;
	border-bottom:1px solid #333;
}

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

/* @group TIMER */
#debug_timers {
	position:fixed;
	top:0;
	right:0;
	background:#fff;
	border:1px solid #ccc;
	border-width:1px 1px 0;
	width:150px;
	cursor:move;
	z-index:10000;
}
#debug_timers dl {
	padding:0;
	margin:0;
}
#debug_timers dt, #debug_timers dd {
	margin:0;
	padding:2px;
	position:relative;
	border-bottom:1px solid #ccc;
	text-align:center;
}
#debug_timers dt {
	font-weight:bold;
	background:#efefef;
	text-align:left;
}
#debug_timers dt small {
	font-weight:normal;
	font-size:10px
}
#debug_timers a.slide {
	position:absolute;
	right:3px;
	top:3px;
	width:17px;
	height:17px;
	text-align:center;
	text-decoration:none;
	background:#ccc;
}
#debug_timers dd span {
	display:block;
	float:right;
	margin-top:-10px;
	background:#ccc;
	margin:-2px;
	padding:2px;
	font-size:10px;
}

#debug_timers a {
	color: #2E79D0;
}
/* @endgroup TIMER */

#debug .benchmark_contents { float: left; width: 70% }
#debug .benchmarks_legend { float:right; width:29% }
.rebuild_all_active {
	font-weight:bold;
	color:#FF0000;
}



</style>
<script type="text/javascript">
function waitingForScript(url, obj) {
	// doesn't work in Opera
	var callback = arguments.callee.caller;
	var args = arguments.callee.caller.arguments;
	var s, ok, timer, doc = document;

	// if the object/function doesn't exist and we've not tried to load it
	// then pull it in and fire the calling function once complete
	if ((typeof window[obj] == 'undefined') && !window['loading' + obj]) {
		window['loading' + obj] = true;

		if (!doc.getElementById('_' + obj)) {
			s = doc.createElement('script');
			s.src = url;
			s.id = '_' + obj;
			doc.body.appendChild(s);
		}

		timer = setInterval(function () {
			ok = false;
			try {
				ok = (typeof window[obj] != 'undefined');
			} catch (e) {}

			if (ok) {
				clearInterval(timer);
				callback.apply(this);
			}
		}, 10);

		// we're loading in the script now, so we're currently waiting
		return true;
	} else if (typeof window[obj] == 'undefined') {
		// object not defined yet, so we're still waiting
		return true;
	} else {
		// it's already loaded
		return false;
	}
}

function LoadjQueryUI()
{
	var domain_parts = document.domain.split('.');
	var cookie_domain = document.domain.replace( domain_parts[0], '');

	if ( typeof jQuery != 'function' && waitingForScript( (("https:" == document.location.protocol) ? "https" : "http") + '://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js', 'jQuery') ) return;

	// If cookie plugin is not loaded, we declare it
	if ( typeof jQuery.cookie != 'function' )
	{
		jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1;}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000));}else{date=options.expires;}expires='; expires='+date.toUTCString();}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}return cookieValue;}};
	}

	$.getScript( (("https:" == document.location.protocol) ? "https" : "http") + '://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js', function() {
		$(document).ready( function(){
			$('#debug_timers').draggable({ containment: 'body' });
			$('#debug_timers a.slide').click( function() {
				if ( $('#debug_timers dd:last').is(':visible') )
				{
					$.cookie('DEBUG_hide_time', 'true', { expires:7, domain: cookie_domain } );
					$('#debug_timers dd:gt(0),#debug_timers dt:gt(0)').slideUp();
					$(this).html('&darr;');
				}
				else
				{
					$.cookie('DEBUG_hide_time', 'false', { expires:7, domain: cookie_domain } );
					$('#debug_timers dd:gt(0),#debug_timers dt:gt(0)').slideDown();
					$(this).html('&uarr;');
				}
				return false;
			});

			$('#debug a.debug_toggle_view').unbind('click').click( function() {
				dest_el = $(this).attr('rel');
				if ( $('#'+dest_el).is(':visible') )
					$('#'+dest_el).slideUp();
				else
					$('#'+dest_el).slideDown();
				return false;
			});


			if ( 'true' == $.cookie( 'DEBUG_hide_time' ) )
			{
				$('#debug_timers dd:gt(0),#debug_timers dt:gt(0)').hide();
			}
		});
	});
}

LoadjQueryUI();

</script>
{/literal}

<div id="debug">
	<div id="debug_timers">
		<dl>
			<dt>{t}Total time{/t}<a class="slide" href="#">&uarr;</a></dt>
			<dd>{$debug.times.total|time_format}</dd>
{if $debug.times.scripts}
			<dt>{t}Scripts{/t}</dt>
			<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.scripts format="%.0f"}%</span>{$debug.times.scripts|time_format}</dd>
{/if}
{if $debug.times.db_connections}
			<dt>{t}DB connects{/t} <small>({t 1=$debug.elements.db_connections}%1 connects{/t})</small></dt>
			<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.db_connections format="%.0f"}%</span>{$debug.times.db_connections|time_format}</dd>
{/if}
{if $debug.times.db_queries}
			<dt>{t}DB queries{/t} <small>(<a href="#db_queries">{t 1=$debug.elements.db_queries}%1 sql{/t}</a>)</small></dt>
			<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.db_queries format="%.0f"}%</span>{$debug.times.db_queries|time_format}</dd>
{/if}
{if $debug.queries_errors}
			<dt class="query_error">{t}DB errors{/t}</dt>
			<dd class="query_error"><strong>{$debug.queries_errors|@count}</strong></dd>
{/if}
{if $debug.times.search}
			<dt>{t}Searches{/t} <small>(<a href="#search_queries">{t 1=$debug.elements.search}%1 searches{/t}</a>)</small></dt>
			<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.search format="%.0f"}%</span>{$debug.times.search|time_format}</dd>
{/if}
{if $debug.times.cache}
			<dt>{t}Cache{/t} <small>(<a href="#controllers">{t 1=$debug.elements.cache}%1 blocks{/t}</a>)</small></dt>
			<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.cache format="%.0f"}%</span>{$debug.times.cache|time_format}</dd>
{/if}
{if $debug.times.external}
			<dt>{t}External requests{/t} <small>(<a href="#external">{t 1=$debug.elements.external}%1 calls{/t}</a>)</small></dt>
			<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.external format="%.0f"}%</span>{$debug.times.external|time_format}</dd>
{/if}
{if $debug.memory_usage != '0 bytes'}
			<dt>{t}Used memory{/t}</dt>
			<dd>{$debug.memory_usage}</dd>
{/if}
			<dt>{t}Automatic rebuild{/t}</dt>
{if $debug.rebuild_all }
			<dd><input type="checkbox" name="rebuild_all" id="rebuild_all_active" checked onclick="window.location='?rebuild_nothing=1'">
				<label for="rebuild_all_active" class="rebuild_all_active">Active</label></dd>
{else}
			<dd><input type="checkbox" name="rebuild_all" id="rebuild_all_inactive" onclick="window.location='?rebuild_all=1'">
				<label for="rebuild_all_inactive">Inactive</label></dd>
{/if}
			<dt>{t}ACTIONS{/t}</dt>
			<dd><a href="?kill_session=1">{t}Kill session{/t}</a> -
				<a href="?rebuild=1">{t}Rebuild{/t}</a><br/><a href="#debug">{t}Debug{/t}&raquo;</a></dd>
		</dl>
	</div>

{if is_array($debug.traces)}
	<h2 id="traces_title"><a class="debug_toggle_view" rel="traces_content" href="#">{t}Show traces{/t}</a></h2>
	<div id="traces_content" class="debug_contents">
	<ul>
{		foreach from=$debug.traces item=trace}
		<li>
{			if is_array($trace)}
{				$trace|debug_print_var}
{			else}
"{				$trace}"
{			/if}
		</li>
{		/foreach}
	</ul>
	</div>
{/if}

<h1 id="benchmarks">{t}Benchmarks{/t}</h1>
<h2 id="bench"><a class="debug_toggle_view" rel="benchmarks_content" href="#">Times of execution</a></h2>
<div id="benchmarks_content" class="debug_contents">
	<table class="benchmark_contents">
{ if isset($debug.benchmarks)}
{		foreach name=bench from=$debug.benchmarks item=bench key=label}
		<tr {if $bench>0.1} class="slow"{/if}><td>{$label}</td><td>{$bench|number_format:4} secs.</td></tr>
{		/foreach}
{ /if}
	</table>
	<div class="benchmarks_legend">
		<p>Order of execution:
		<ul>
		<li><strong>Parent dispatch:</strong>
				<ul>
					<li>Parent preDispatch</li>
					<li>executeNestedModules [<strong>foreach module</strong>]
						<ul>
							<li>preDispatch</li>
							<li>execute</li>
							<li>postDispatch</li>
						</ul>
					</li>
					<li>Parent execute
						<ul>
							<li>preDispatch</li>
							<li>execute</li>
							<li>postDispatch</li>
						</ul>
					</li>
					<li>Grab HTML</li>
					<li>Realtimereplacement [<strong>foreach RTR found</strong>]
						<ul>
							<li>preDispatch</li>
							<li>execute</li>
							<li>postDispatch</li>
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</div>

</div>
	<h1 id="controllers">{t}Controllers{/t}</h1>
{foreach name=controllers from=$debug.controllers item=controller key=controller_name}
	<h2 id="cont_{$smarty.foreach.controllers.index}"><a class="debug_toggle_view" rel="cont_content_{$smarty.foreach.controllers.index}" href="#">{$smarty.foreach.controllers.index+1}. {$controller_name}</a></h2>
	<div id="cont_content_{$smarty.foreach.controllers.index}" class="debug_contents">
{	foreach from=$controller item=content key=key name=controllerparams}
{		if $key == "CONTROLLER"}
	<h3><a class="debug_toggle_view" rel="params_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}" href="#">Controller parameters</a></h3>
	<div id="params_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}" class="debug_contents">
	<ul>
{		foreach from=$content.parameters item=content1 key=key1}
{			if is_array($content1)}
			<li class="array"><strong>{$key1}: Array</strong>
{				foreach from=$content1 item=arr key=k}
				<ul>
					<li><strong>{$k}</strong>: {if is_array($arr)}<pre>{$arr|debug_print_var}</pre>{else}<code>{$arr|escape}</code>{/if}</li>
				</ul>
{				/foreach}
			</li>
{			else}
			<li><strong>{$key1}</strong>: "{$content1|escape}"</li>
{			/if}
{		/foreach}
	</ul>
	</div>
{		else}

{*	TEMPLATES AND OTHER FUTURE ELEMENTS *}
	<h3><a class="debug_toggle_view" rel="assigns_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}" href="#">
{			if $key == "assigns"}Template assigns{else}{$key}{/if}
		</a></h3>
	<div  id="assigns_cont_content_{$smarty.foreach.controllers.index}_{$smarty.foreach.controllerparams.index}" class="debug_contents">
	<ul>
{		foreach from=$content item=content1 key=key1}
{			if is_array($content1)}
			<li class="array"><strong>{$key1}: Array</strong>
{				foreach from=$content1 item=arr key=k}
				<ul>
					<li><strong>{$k}</strong>: {if is_array($arr)}<pre>{$arr|debug_print_var}</pre>{else}<code>{$arr|escape}</code>{/if}</li>
				</ul>
{				/foreach}
			</li>
{			else}
			<li><strong>{$key1}</strong>: "{$content1|escape}"</li>
{			/if}
{		/foreach}
	</ul>
	</div>
{		/if}
{	/foreach}
	</div>
{/foreach}

{* Sphinx and other search-related queries *}
{if is_array($debug.searches)}
	<h1 id="search_queries">{t}Searches{/t}</h1>
{foreach name=search from=$debug.searches item=search}
{foreach name=match from=$search item=value}
	<h2 class="queries query_read" id="search_{$smarty.foreach.match.index}"><a class="debug_toggle_view" rel="search_content_{$smarty.foreach.match.index}" href="#">{$smarty.foreach.match.index+1}. [R]</a> <small>({$value.time|time_format} - match: {$value.total_found} elements - return: {if isset($value.matches)}{$value.matches|@count}{else}0{/if} elements )</small></h2>
	<div id="search_content_{$smarty.foreach.match.index}" class="debug_contents">
		<table>
			<tr>
				<th>Filter</th>
				<th>Order</th>
				<th>GroupBy</th>
				<th>Indexs</th>
			</tr>
			<tr>
				<td>{if isset($value.filter)}{$value.filter}{/if}</td>
				<td>{if isset($value.order)}{$value.order}{/if}</td>
				<td>{if isset($value.groupby)}{$value.groupby}{/if}</td>
				<td>{if isset($value.indexs)}{$value.indexs}{/if}</td>
			</tr>
		</table>
		<table>
			<tr>
				<th>ID</th>
				<th>WEIGHT</th>
{			foreach name=weights from=$value.attrs item=values}
				<th>{$values@key}</th>
{			/foreach}
			</tr>
{if isset($value.matches)}
{			foreach from=$value.matches item=match}
			<tr>
				<td>{$match@key}</td>
				<td>{$match.weight}</td>
{				foreach from=$match.attrs key=attribute item=values}
				<td>{if is_array($values)}{$values|debug_print_var}{else}{$values}{/if}</td>
{				/foreach}
			</tr>
{			/foreach}
{/if}
		</table>
		<pre>
{*			$value|debug_print_var*}
		</pre>
	</div>
{/foreach}
{/foreach}
{/if}

{if is_array($debug.queries)}
	<h1 id="db_queries">{t}DB Queries{/t}</h1>
{foreach name=queries from=$debug.queries item=query}
	<h2 class="queries {if false !== $query.error}query_error{else}query_{$query.type}{/if}{if $query.time >= 0.5 && $query.time < 1} query_slow{elseif $query.time >= 1} query_very_slow{/if}" id="queries_{$smarty.foreach.queries.index}">
		<a class="debug_toggle_view" href="#" rel="queries_content_{$smarty.foreach.queries.index}">
		{$smarty.foreach.queries.index+1}. {if $query.type=='read'}[R]{else}[W]{/if} {$query.tag}</a> <small>({$query.time|time_format} - rows:{$query.rows_num})</small></h2>
	<div id="queries_content_{$smarty.foreach.queries.index}" class="debug_contents">
		<pre>{$query.sql}</pre>
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

{if is_array($debug.session)}
	<h1 id="session">{t}Session{/t}</h1>
{foreach name=session from=$debug.session item=value key=session_key}
	<h2 id="sess_{$smarty.foreach.session.index}"><a class="debug_toggle_view" rel="sess_content_{$smarty.foreach.session.index}" href="#">{$smarty.foreach.session.index+1}. {$session_key}</a></h2>
	<div id="sess_content_{$smarty.foreach.session.index}" class="debug_contents">
		<pre>
{			$value|debug_print_var}
		</pre>
	</div>
{/foreach}
{/if}

{if is_array($debug.cookies)}
	<h1 id="cookies">{t}Cookies{/t}</h1>
{foreach name=cookies from=$debug.cookies item=value key=cookies_key}
	<h2 id="cook_{$smarty.foreach.cookies.index}"><a class="debug_toggle_view" rel="cook_content_{$smarty.foreach.cookies.index}" href="#">{$smarty.foreach.cookies.index+1}. {$cookies_key}</a></h2>
	<div id="cook_content_{$smarty.foreach.cookies.index}" class="debug_contents">
		<pre>
{			$value|debug_print_var}
		</pre>
	</div>
{/foreach}
{/if}

</div>