<html>
<head>
	<title>I18N finder and extractor</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$selected_charset}" />

	<link rel="stylesheet" type="text/css" href="http://static.seoframework.local/css/main.css?id=" /></head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="{$url.static}/js/jquery.translate-core.min.js"></script>
	{literal}
	<script type="text/javascript">
	$(document).ready( function()
		{$('#instance').change( function()
			{selected_instance = $(this).val();
			$('#locale').html( $('#locale_' + selected_instance).html() );
		});

		$('a.toggle').unbind('click').click( function() 
			{dest_el = $(this).attr('rel');
			if ( $('#'+dest_el).is(':visible') )
				$('#'+dest_el).slideUp();
			else
				$('#'+dest_el).slideDown();
			return false;
		});

		$('#translations p').each( function() 
			{var original_container = $('span.original',this);
			var destination_container = $('span.translated',this);

			var original_text = original_container.html();
			original_text = original_text.replace('%1','111');
			original_text = original_text.replace('%2','222');
			original_text = original_text.replace('%3','333');
			original_text = original_text.replace('%4','333');
			original_text = original_text.replace(/(<([^>]+)>)/ig,"");

			var destination_language = '{/literal}{$language|default:'en'}{literal}';

			$.translate( original_text, destination_language, 
				{complete: function(translation)
					{translation = translation.replace('111','%1');
					translation = translation.replace('222','%2');
					translation = translation.replace('333','%3');
					translation = translation.replace('444','%4');
					translation = translation.replace(/(\\ ')/ig,'\\\'');
					//console.log(translation);
					destination_container.html(translation);
				}
			});
		});
	});
	</script>
	<style>
		body {font-family: Arial }
		.missing {color: red }
		.error {background-color: #C16060; color:#fff; padding:15px; font-size:16px; margin:18px 0; -webkit-border-radius:7px; -moz-border-radius:7px; min-height:21px; }
		#translations  {margin:18px; }
		#translations p  {margin:0;font-family: Courier, monospace; color:#666; }
		#translations p span.original {display:none; }
		#translations p span.original_visible {color:#333; }
		#translations p span.translated {color:green; font-weight:bold; }
	</style>
	{/literal}
<body>
	<h1>i18n finder</h1>
	{if isset( $error ) }
		<p class="error">{$error}</p>
	{/if}
	<form method="POST">
		<p>
			<label for="instance">Instance:</label>
			<select id="instance" name="instance">
				{foreach $available_instances as $instance}
					<option value="{$instance}"{if $selected_instance == $instance} selected="selected"{/if}>{$instance}</option>
				{/foreach}
			</select>

			<label for="locale">Compare against:</label>
			{foreach $available_locales as $locale_instance => $locale_files}
				<select id="locale_{$locale_instance}" style="display:none">
					{foreach $locale_files as $file}
						<option value="{$file}"{if isset($selected_locale) && $selected_locale == $file} selected="selected"{/if}>{$file}</option>
					{/foreach}
				</select>
			{/foreach}

			<select id="locale" name="locale">
				{foreach $available_locales.$selected_instance as $file}
					<option value="{$file}"{if isset($selected_locale) && $selected_locale == $file} selected="selected"{/if}>{$file}</option>
				{/foreach}
			</select>
			<label for="charset">Using charset:</label>
			<select id="charset" name="charset">
				<option value="utf-8"{if $selected_charset == 'utf-8'} selected="selected"{/if}>utf-8</option>
				<option value="iso-8859-1"{if $selected_charset == 'iso-8859-1'} selected="selected"{/if}>iso-8859-1</option>
			</select>

			<input type="submit" />
		</p>
	</form>

	{if isset($literals)}
		<h2>Results</h2>
		<p>{$literals|@count} strings were found and <span class="missing">{$missing_messages|@count}</span> missing</p>
		<h3>Literals (Total {$literals|@count})</h3>
		<p><a href="#literal_queries" class="toggle" rel="literal_queries">View SQL queries</a> &darr;</p>
		<pre id="literal_queries" style="display:none;font-size:11px;">
{foreach $literals as $literal => $path}
INSERT IGNORE INTO i18n_messages SET message='{$literal|replace:"'":"\'"|escape:'html'}', comment='{$path}';
{/foreach}
		</pre>

		<h3>Leftover strings in "messages" (Total {$leftover_messages|@count})</h3>
		<p><a href="#leftover_queries" class="toggle" rel="leftover_queries">View SQL queries</a> &darr;</p>
		<pre id="leftover_queries" style="display:none;font-size:11px;">
{foreach $leftover_messages as $literal => $path}
DELETE it FROM i18n_messages im LEFT JOIN i18n_translations it ON im.id = it.id_message WHERE im.message = '{$literal|replace:"'":"\'"|escape:'html'}';
DELETE FROM i18n_messages WHERE message = '{$literal|replace:"'":"\'"|escape:'html'}';
{/foreach}
		</pre>

		<h3>Missing strings in "messages" (Total {$missing_messages|@count})</h3>
		<p><a href="#missing_queries" class="toggle" rel="missing_queries">View SQL queries</a> &darr;</p>
		<pre id="missing_queries" style="display:none;font-size:11px;">
{foreach $missing_messages as $literal => $path}
INSERT INTO i18n_messages values ( null, '{$literal|replace:"'":"\'"|escape:'html'}', '{$path}' );
{/foreach}
		</pre>

		<h3>Suggested (automatic) translations for missing strings in "messages" (Total {$missing_messages|@count})</h3>
		<div id="translations">
			{foreach $missing_messages as $literal => $path}
				<p>
					<span class="original">{$literal|replace:"__":""|replace:"'":"\'"}</span>
					$translations["<span class="original_visible">{$literal|replace:"__":""|replace:"'":"\'"|escape:'html'}</span>"] =
					"<span class="translated">...</span>";
				</p>
			{/foreach}
		</div>
	{/if}
</body>
</html>