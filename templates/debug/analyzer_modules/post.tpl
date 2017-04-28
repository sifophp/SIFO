{if is_array($debug.post) && !empty($debug.post)}
	<h2 id="post">{t}Post{/t}</h2>
	{foreach $debug.post as $post_key => $value}
		<h3 id="post_{$value@index}"><a class="debug_toggle_view" rel="post_content_{$value@index}" href="#">{$value@index+1}. {$post_key}</a></h3>
		<div id="post_content_{$value@index}" class="debug_contents">
			<pre>
				{$value|debug_print_var nofilter}
			</pre>
		</div>
	{/foreach}
{/if}