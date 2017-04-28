{if is_array($debug.session)}
	<h2 id="session">{t}Session{/t}</h2>
	{foreach $debug.session as $session_key => $value}
		<h3 id="sess_{$value@index}"><a class="debug_toggle_view" rel="sess_content_{$value@index}{$execution_key}" href="#">{$value@index+1}. {$session_key}</a></h3>
		<div id="sess_content_{$value@index}{$execution_key}" class="debug_contents">
			<pre>
				{$value|debug_print_var nofilter}
			</pre>
		</div>
	{/foreach}
{/if}