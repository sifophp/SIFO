{if $show_timers|default:true}
	{literal}
		<style type="text/css">
			pre.debug, .xdebug-error, .xe-notice{ z-index: 10000; font-family: 'courier new' monospaced; border: 1px solid #CCC; padding: 9px; background: #EFEFEF; position: relative; margin: 1px;}

			/* @group TIMER */
			#debug_timers {
				font-size:12px;
				line-height:18px;
				font-family:Arial, sans-serif;
				color:#333;
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
			#debug_timers dt small,#debug_timers dd small  {
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

			#debug_timers .rebuild_all_active {
				font-weight:bold;
				color:#FF0000;
			}

			#debug_timers .actions_options li{
				text-align: left;
				list-style-type: none;
				margin-left: 20px;
			}

			#debug_timers .query_read {
				background-color: green;
			}

			#debug_timers .query_write {
				background-color: darkblue;
			}

			#debug_timers .query_error {
				background-color: red;
			}

			#debug_timers .query_duplicated {
				background-color: orange;
			}
			/* @endgroup TIMER */
		</style>
	{/literal}

	<div id="debug_timers" data-sifo-parent-debug-execution-key="{$execution_key}" data-sifo-debug-actions-url="{$url.sifo_debug_actions}" data-sifo-debug-analyzer-url="{$url.sifo_debug_analyzer}">
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
				<dt>{t}DB queries{/t} <small>(<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}#db_queries" target="_blank">{t 1=$debug.elements.db_queries}%1 sql{/t}</a>)</small></dt>
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
				<dt>{t}Searches{/t} <small>(<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}#search_queries" target="_blank">{t 1=$debug.elements.search}%1 searches{/t}</a>)</small></dt>
				<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.search format="%.0f"}%</span>{$debug.times.search|time_format}</dd>
			{/if}
			{if $debug.times.sphinxql}
				<dt>{t}SphinxQL{/t} <small>(<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}#sphinxql_queries" target="_blank">{t 1=$debug.elements.sphinxql}%1 searches{/t}</a>)</small></dt>
				<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.sphinxql format="%.0f"}%</span>{$debug.times.sphinxql|time_format}</dd>
			{/if}
			{if $debug.sphinxql_errors|default:false}
				<dt class="query_error">{t}SphinxQL errors{/t}</dt>
				<dd class="query_error"><strong>{$debug.sphinxql_errors|@count}</strong></dd>
			{/if}
			{if $debug.times.cache}
				<dt>{t}Cache{/t} <small>(<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}#controllers" target="_blank">{t 1=$debug.elements.cache}%1 blocks{/t}</a>)</small></dt>
				<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.cache format="%.0f"}%</span>{$debug.times.cache|time_format}</dd>
			{/if}
			{if $debug.times.external}
				<dt>{t}External requests{/t} <small>(<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}#external" target="_blank">{t 1=$debug.elements.external}%1 calls{/t}</a>)</small></dt>
				<dd><span>{math equation="y / x * 100" x=$debug.times.total y=$debug.times.external format="%.0f"}%</span>{$debug.times.external|time_format}</dd>
			{/if}
			{if $debug.memory_usage != '0 bytes'}
				<dt>{t}Used memory{/t}</dt>
				<dd>{$debug.memory_usage}</dd>
			{/if}

			<dt class="ajax_calls">{t}AJAX{/t} <small>(<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}#ajax_debug_0" target="_blank"><span class="num_calls">{if isset( $children_executions )}{$children_executions|count}{else}0{/if}</span> calls</a>)</small></dt>
			<dd class="ajax_calls">
				{$ajax_calls_total_time = 0}
				{if isset( $children_executions )}
					{foreach $children_executions as $child_debug_data}
						{$ajax_calls_total_time = $ajax_calls_total_time + $child_debug_data.debug_content.times.total}
					{/foreach}
				{/if}
				{$ajax_calls_total_time|time_format}
			</dd>

			<dt>{t}Automatic rebuild{/t}</dt>
			{if $debug.rebuild_all }
				<dd>
					<input type="checkbox" name="rebuild_all" id="rebuild_all_active" checked onclick="window.location='?rebuild_nothing=1'">
					<label for="rebuild_all_active" class="rebuild_all_active">Active</label>
				</dd>
			{else}
				<dd>
					<input type="checkbox" name="rebuild_all" id="rebuild_all_inactive" onclick="window.location='?rebuild_all=1'">
					<label for="rebuild_all_inactive">Inactive</label>
				</dd>
			{/if}

			<dt>{t}ACTIONS{/t}</dt>
			<dd>
				<ol class="actions_options">
					<li>
						<a href="?kill_session=1">{t}Kill session{/t}</a>
					</li>
					<li>
						<a href="?rebuild=1">{t}Rebuild{/t}</a>
					</li>
					<li>
						<a href="?rebuild=1&clean_compile=1">{t}Clean compile{/t}</a>
					</li>
				</ol>
			</dd>

			<dt>{t}Debug{/t}</dt>
			<dd class="no-hide">
				<a href="{$url.sifo_debug_analyzer}?execution_key={$execution_key}" target="_blank">{t}Debug Analyzer{/t} &raquo;</a>
			</dd>

			{if !isset( $url.sifo_debug_analyzer ) || !isset( $url.sifo_debug_actions )}
				<dt class="query_error">{t}SIFO UPDATE{/t}</dt>
				<dd>
					You have to insert the sifo_debug_actions and sifo_debug_analyzer routes in your router and url.config files.
					<br><br>Check out the <a href="http://sifo.me/API/Debug">Sifo debug documentation</a>.
				</dd>
			{/if}
		</dl>
	</div>

	{literal}
		<script type="text/javascript">

			// Function used as a dummy callback by the Sifo debug linker JSONP call in order to be able to call it from different sub-domains.
			function foo() {}
			
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
				} else {
					// return if the object is already loaded (defined) or not (we're still waiting in that case)
					return typeof window[obj] == 'undefined';
				}
			}

			function LoadjQueryUI() {
				var domain_parts = document.domain.split('.');
				var cookie_domain = document.domain.replace( domain_parts[0], '');

				if ( typeof jQuery != 'function' && waitingForScript( (("https:" == document.location.protocol) ? "https" : "http") + '://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js', 'jQuery')  ) return;

				$(document).ready( function(){

					if (parseFloat(jQuery.fn.jquery) >= 1.8) {
						$.getScript('http://code.jquery.com/jquery-migrate-1.0.0.js' ).done( debugBehaviours );
					} else {
						debugBehaviours();
					}


					function debugBehaviours() {

						// If cookie plugin is not loaded, we declare it
						if ( typeof jQuery.cookie != 'function' )
						{
							jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1;}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000));}else{date=options.expires;}expires='; expires='+date.toUTCString();}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}return cookieValue;}};
						}

						(function($) {
								$.fn.drags = function(opt) {

									opt = $.extend({handle:"",cursor:"move"}, opt);

									if(opt.handle === "") {
										var $el = this;
									} else {
										var $el = this.find(opt.handle);
									}

									return $el.css('cursor', opt.cursor).bind("mousedown", function(e) {
										if(opt.handle === "") {
											var $drag = $(this).addClass('draggable');
										} else {
											var $drag = $(this).addClass('active-handle').parent().addClass('draggable');
										}
										var z_idx = $drag.css('z-index'),
												drg_h = $drag.outerHeight(),
												drg_w = $drag.outerWidth(),
												pos_y = $drag.offset().top + drg_h - e.pageY,
												pos_x = $drag.offset().left + drg_w - e.pageX;
										$drag.css('z-index', 1000).parents().bind("mousemove", function(e) {
											$('.draggable').offset({
												top:e.pageY + pos_y - drg_h,
												left:e.pageX + pos_x - drg_w
											}).bind("mouseup", function() {
														$(this).removeClass('draggable').css('z-index', z_idx);
													});
										});
										e.preventDefault(); // disable selection
									}).bind("mouseup", function() {
												if(opt.handle === "") {
													$(this).removeClass('draggable');
												} else {
													$(this).removeClass('active-handle').parent().removeClass('draggable');
												}
												$.cookie("DEBUG_timer_style", $('#debug_timers').attr('style'),{ expires:7, domain: cookie_domain });
											});

								}
							})(jQuery);

						$('#debug_timers').drags();

						$('#debug_timers a.slide').click( function() {
							if ( $('#debug_timers dt:last').is(':visible') )
							{
								$.cookie('DEBUG_hide_time', 'true', { expires:7, domain: cookie_domain } );
								$('#debug_timers dd:gt(0),#debug_timers dt:gt(0)').not('.no-hide').slideUp();
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

						if ( 'true' == $.cookie( 'DEBUG_hide_time' ) )
						{
							$('#debug_timers dd:gt(0),#debug_timers dt:gt(0)').not('.no-hide').hide();
						}
						if (sStyle = $.cookie("DEBUG_timer_style")){
							$('#debug_timers').attr('style', sStyle);
						}

					{/literal}

					{if $show_timers|default:true}

						{literal}
							var num_ajax_calls 	= 0;
							var total_time 		= 0;

							// Link a child execution to its parent
							$(document).ajaxComplete(function( e, xhr, settings )
							{
								try
								{
									var response = jQuery.parseJSON( xhr.responseText );
									var debug_timers = $('#debug_timers');

									if ( typeof response.debug_total_time != 'undefined' )
									{
										$('#debug_timers .ajax_calls').show();

										$.ajax({
											dataType: "jsonp",
											url: debug_timers.data('sifoDebugActionsUrl') + '?action=link&execution_key=' + debug_timers.data('sifoParentDebugExecutionKey') + '&child_execution_key=' + response.debug_execution_key + '&json_callback=foo'
										});

										num_ajax_calls++;
										$("#debug_timers dt.ajax_calls .num_calls" ).html( num_ajax_calls );

										// Timing.
										total_time = total_time + parseFloat( response.debug_total_time );

										// Format timing:
										var time = total_time * 1000;

										if ( time < 100 )
										{
											// Miliseconds.
											$formatted_time = time.toFixed(2) + ' milisec';
										}
										else
										{
											// Seconds.
											time = time / 1000;
											$formatted_time = time.toFixed(2) + ' sec';
										}

										$("#debug_timers dd.ajax_calls").html( $formatted_time + ' <small>(<a href="' + debug_timers.data('sifoDebugAnalyzerUrl') + '?execution_key=' + debug_timers.data('sifoParentDebugExecutionKey') + '#ajax_debug_' + ( num_ajax_calls - 1 ) + '">Go to last one</a>)</small>' );
									}
								}
								catch( e )
								{
									// Do nothing. Only supported for JSON responses.
								}
							});

							// Expand/collapse debug block info form Analyzer
							$('#debug a.debug_toggle_view,#ajax_debug a.debug_toggle_view').unbind('click').on( 'click', function() {
								dest_el = $(this).attr('rel');
								if ( $('#'+dest_el).is(':visible') )
									$('#'+dest_el).slideUp();
								else
									$('#'+dest_el).slideDown();
								return false;
							});

							// Un/Pin debug execution form Analyzer
							$('#pin_execution, #unpin_execution').on('click', function(e) {
								e.preventDefault();

								var clicked_element = $(this);

								$.ajax({
									dataType: "json",
									url: clicked_element.attr('href')
								} ).done( function(){
									clicked_element.addClass('debug_hidden');
									$('#' + clicked_element.data('counterpart') ).removeClass('debug_hidden');
								});
							});

						{/literal}
					{/if}

					{literal}
					}
				});
			}

			LoadjQueryUI();
		</script>
	{/literal}
{/if}