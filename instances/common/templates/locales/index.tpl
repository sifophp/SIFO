<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$url.lang}">
{$modules.head}
<body>
{literal}
<script type="text/javascript" charset="utf-8">
	function validateAll() {
		var ok = true;
		$('.validate').each(function(index)
		{
			ok = validate(this);
			if (ok == false)
			{
				$('#'+this.id).focus();
			}
			return ok;
		});

		if (ok == true)
		{
			$('#save_ctrl').val('Save');
			$('#translation-form').submit();
		}
	}
	
	function validate(input)
	{
		var ok = true;
		var id_parts = input.id.split('_');
		if ( /%/.test($('#keyword_'+id_parts[1]).val()) == true && input.value != '')
		{
			var percents_keyword = $('#keyword_'+id_parts[1]).val().match(/%/g);
			var percents_translation = input.value.match(/%/g);
			if (percents_translation == null)
			{
				ok = false;
				$('#'+input.id).after("<span style='color:red;font-weight:bold' id='error_"+id_parts[1]+"'><br/>The translation doesn't contain the same number of parameter placeholders (%n)</span>");
			}
			else if (percents_keyword.length != percents_translation.length)
			{
				ok = false;
				$('#'+input.id).after("<span style='color:red;font-weight:bold' id='error_"+id_parts[1]+"'><br/>The translation doesn't contain the same number of parameter placeholders (%n)</span>");
			}
			else if (percents_keyword.length == percents_translation.length)
			{
				$('#error_'+id_parts[1]).hide();
			}
		}

		return ok;
	}

	function addNewLanguage()
	{
		if ($('#new-language').val() != '')
		{
			document.location.href='{/literal}{$url.locales_save}{literal}:i:'+$('#instance').val()+':n:'+$('#new-language').val();
		}
		else
		{
			$('#new-language').focus();
		}
	}

	function suggestTranslation(button)
	{
		var id_parts = button.id.split('_');
		var original_text = $('#keyword_'+id_parts[2]).val();
		original_text = original_text.replace('%1','111');
		original_text = original_text.replace('%2','222');
		original_text = original_text.replace('%3','333');
		original_text = original_text.replace('%4','333');
		original_text = original_text.replace(/(<([^>]+)>)/ig,"");

		$.translate( original_text, $('#lang').val(), {
			complete: function(translation)
			{
				translation = translation.replace('111','%1');
				translation = translation.replace('222','%2');
				translation = translation.replace('333','%3');
				translation = translation.replace('444','%4');
				translation = translation.replace('% 1','%1');
				translation = translation.replace('% 2','%2');
				translation = translation.replace('% 3','%3');
				translation = translation.replace('% 4','%4');
				translation = translation.replace(/(\\ ')/ig,'\\\'');
				//console.log(translation);
				$('#translation_'+id_parts[2]).val(translation);
			}
		});
	}

</script>
{/literal}
<div id="wrapper">
{	$modules.system_messages}
	<div id="contents" class="translation_page">
		<div id="header" class="header" style="background-color:#999999">
			<h1>Translator {if $current_instance && $current_language}| Translating {$current_language} of instance '{$current_instance}' {/if}</h1>
		</div>

{		if $current_instance && $current_language}
			<form id="translation-form" name="translation-form" method="POST" action="{fill subject=$url.locales_save instance=$current_instance language=$current_language}">
			<input type="hidden" id="instance" name="instance" value="{$current_instance}"/>
			<input type="hidden" id="language" name="language" value="{$current_language}"/>
			<input type="hidden" id="lang" name="lang" value="{$lang}"/>
			<input type="hidden" id="save_ctrl" name="save" value=""/>
			<div id="translator-interface" class="main-content" style="margin-top:10px;padding:5px;">
				<p><input type="button" style="border:1px solid #aaa;padding:5px;cursor:pointer" value="Go back" onclick="document.location.href='{$url.locales}:i:{$current_instance}'"/>&nbsp;
					<input type="submit" onclick="validateAll();return false;" id="save_top" name="save" value="Save" style="border:1px solid #aaa;padding:5px;background-color:greenyellow;cursor:pointer"/>&nbsp;
					<input type="submit" style="border:1px solid #aaa;padding:5px;cursor:pointer;background-color:yellow" id="save_unvalidated" name="save" value="Save without validation"/>&nbsp;</p>
				<table style="border:0">
					<tr style="background-color:#bbb"><td style="border:none" colspan="2"><h3>Untranslated keys</h3></td></tr>
{					foreach name=translations item=key from=$translation_keys}
{						if $languages.$current_language.translations.$key == ''}
							<tr style="text-align:left;background-color:{cycle values="#fff,#ccc"};">
								<td style="width:40%;border:none">
									<input type="hidden" id="keyword_{$smarty.foreach.translations.iteration}" name="translations[{$smarty.foreach.translations.iteration}][key]" value="{$key|escape}"/>{$key|escape}
								</td>
								<td style="width:60%;border:none">
{									assign var="color" value="red"}
									<input class="validate" size="85%" type="text" style="border:1px solid {$color};padding:2px;width:85%" name="translations[{$smarty.foreach.translations.iteration}][translation]" id="translation_{$smarty.foreach.translations.iteration}" value="{$languages.$current_language.translations.$key|escape}" onchange="validate(this)" />
									<input type="button" id="suggest_translation_{$smarty.foreach.translations.iteration}" name="suggest_translation{$smarty.foreach.translations.iteration}" value="Suggest Translation" style="border:1px solid #999;padding:2px;cursor:pointer" onclick="suggestTranslation(this)"/>

								</td>
							</tr>
{						/if}
{					/foreach}
					<tr><td colspan="2" style="border:none;">&nbsp;</td></tr>
					<tr style="background-color:#bbb"><td style="border:none" colspan="2"><h3>Translated keys</h3></td></tr>
{					foreach name=translations item=key from=$translation_keys}
{						if $languages.$current_language.translations.$key != ''}
							<tr style="text-align:left;background-color:{cycle values="#fff,#ccc"};">
								<td style="width:40%;border:none">
									<input type="hidden" id="keyword_{$smarty.foreach.translations.iteration}" name="translations[{$smarty.foreach.translations.iteration}][key]" value="{$key|escape}"/>{$key|escape}
								</td>
								<td style="width:60%;border:none">
{									assign var="color" value="#bbb"}
									<input class="validate" size="99%" type="text" style="border:1px solid {$color};padding:2px;width:99%" name="translations[{$smarty.foreach.translations.iteration}][translation]" id="translation_{$smarty.foreach.translations.iteration}" value="{$languages.$current_language.translations.$key|escape}" onchange="validate(this)" />
								</td>
							</tr>
{						/if}
{					/foreach}
				</table>
				<p><input type="submit" id="save_bottom" onclick="validateAll();return false;" name="save" value="Save" style="border:1px solid #aaa;padding:5px;background-color:greenyellow;cursor:pointer"/></p>
			</div>
			</form>
{		/if}

{	if $current_instance == false || $current_language == false}
		<label for="instance">Instance:</label>
		<select name="instance" id="instance" style="cursor:pointer; border:1px solid #aaa;padding:2px;margin-top:5px" onchange="document.location.href='{$url.locales}:i:'+$('#instance').val()">
{			foreach item=instance from=$instances}
				<option value="{$instance.id}"{if $current_instance == $instance.id} selected{/if}>{$instance.name}</option>
{			/foreach}
		</select>

{		if $languages}
		<ul>
{			foreach item=language key=lang from=$languages}
				<li style="list-style-type:none;margin-bottom: 5px">{$lang} {$languages.$lang.total_keys}/{$languages.$lang.translated_keys} ({$languages.$lang.translated_percentage})&nbsp;
					<input type="button" name="edit-{$current_instance}-{$lang}" id="edit-{$current_instance}-{$lang}" style="cursor:pointer; border:1px solid #aaa;padding:2px;" value="{t}Edit{/t}" onclick="document.location.href='{$url.locales}:i:'+$('#instance').val()+':l:{$lang}'" />
				</li>
{			/foreach}
		</ul>
{		/if}
{		if $current_instance}
			<input type="button" id="add" name="add" value="Add new language" style="cursor:pointer; border:1px solid #aaa;padding:2px;margin-bottom:5px" onclick="addNewLanguage()"/>&nbsp;&nbsp;<input type="text" id="new-language" name="new_language" size="30" style="padding:2px; border: 1px solid #999" />
{		/if}
{	/if}
</div>
{$modules.footer}
</div>
</body>
</html>