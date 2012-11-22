{if isset($command_line_mode) && $command_line_mode}<body>{/if}
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
{/literal}
</script>

<div id="debug">
	<div id="debug_timers">
		<dl>
			<dt>{t}Total time{/t}<a class="slide" href="#">&uarr;</a></dt>
			<dd>{$debug.times.total|time_format}</dd>
{if $debug.times.scripts}
			<dt>{t}Scripts{/t}</dt>
			<dd><span>{if isset( $debug.times.total) && $debug.times.total > 0 }{math equation="y / x * 100" x=$debug.times.total y=$debug.times.scripts format="%.0f"}%{/if}</span>{$debug.times.scripts|time_format}</dd>
{/if}
{if $debug.times.db_connections}
			<dt>{t}DB connects{/t} <small>({t 1=$debug.elements.db_connections}%1 connects{/t})</small></dt>
			<dd><span>{if isset( $debug.times.total) && $debug.times.total > 0}{math equation="y / x * 100" x=$debug.times.total y=$debug.times.db_connections format="%.0f"}%{/if}</span>{$debug.times.db_connections|time_format}</dd>
{/if}
{if $debug.times.db_queries}
			<dt>{t}DB queries{/t} <small>(<a href="#db_queries">{t 1=$debug.elements.db_queries}%1 sql{/t}</a>)</small></dt>
			<dd><span>{if isset( $debug.times.total) && $debug.times.total > 0}{math equation="y / x * 100" x=$debug.times.total y=$debug.times.db_queries format="%.0f"}%{/if}</span>{$debug.times.db_queries|time_format}</dd>
{/if}
{if $debug.queries_errors}
			<dt class="query_error">{t}DB errors{/t}</dt>
			<dd class="query_error"><strong>{$debug.queries_errors|@count}</strong></dd>
{/if}
{if $debug.queries_duplicated}
			<dt class="query_duplicated">{t}Duplicated Queries{/t}</dt>
			<dd class="query_duplicated"><strong>{$debug.queries_duplicated|@count}</strong></dd>
{/if}
{if $debug.smarty_errors}
			<dt class="query_duplicated">{t}Smarty errors{/t}</dt>
			<dd class="query_duplicated"><strong>{$debug.smarty_errors|@count}</strong></dd>
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
{if !isset($command_line_mode) || !$command_line_mode}
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
				<a href="?rebuild=1">{t}Rebuild{/t}</a><br/><a href="#debug">{t}Debug{/t}&raquo;</a>
			</dd>
{/if}
		</dl>
	</div>

{if isset($debug.traces) && is_array($debug.traces)}
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

{* Sent headers*}
{$debug_modules.headers}

{* Smarty Error: Compilation and runtime smarty errors*}
{$debug_modules.smarty_errors}

{* Basic debug: Benchmarks and controllers *}
{$debug_modules.basic_debug}

{* Sphinx and other search-related queries *}
{$debug_modules.search}

{* Database queries *}
{$debug_modules.database}

{* Log messages *}
{$debug_modules.log_messages}

{* Post *}
{if is_array($debug.post)}
	<h1 id="post">{t}Post{/t}</h1>
{foreach name=post from=$debug.post item=value key=post_key}
	<h2 id="post_{$smarty.foreach.session.index}"><a class="debug_toggle_view" rel="post_content_{$smarty.foreach.post.index}" href="#">{$smarty.foreach.post.index+1}. {$post_key}</a></h2>
	<div id="post_content_{$smarty.foreach.post.index}" class="debug_contents">
		<pre>
{			$value|debug_print_var}
		</pre>
	</div>
{/foreach}
{/if}


{* Sessions and Cookies *}
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
{if isset($command_line_mode) && $command_line_mode}</body>{/if}