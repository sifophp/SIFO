{if is_array($debug.cookies)}
	<h2 id="cookies">{t}Cookies{/t}</h2>
	{foreach $debug.cookies as $cookies_key => $value}
		<h3 id="cook_{$value@index}"><a class="debug_toggle_view" rel="cook_content_{$value@index}{$execution_key}" href="#">{$value@index+1}. {$cookies_key}</a></h3>
		<div id="cook_content_{$value@index}{$execution_key}" class="debug_contents">
			<pre>
				{$value|debug_print_var nofilter}
			</pre>
		</div>
	{/foreach}
{/if}