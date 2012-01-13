<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$url.lang}">
{$modules.head}
<body>
<div id="wrapper">
{	$modules.system_messages}
	<div id="contents" class="translation_page">
		<h1>Translator</h1>
{		if $isAdmin}
		<a href="#" id="rebuild" rel="{$url.translations_rebuild}">Rebuild translations</a>
		<a href="#" id="add-trans">Add translation</a>
		<div id="add-trans-form">
		<form action="{$url.translations-add}">
			<input type="text" value="" name="msgid" />
			<input type="text" value="" name="translation" />
			<input type="hidden" name="lang" value="{$curr_lang}" />
			<input type="submit" value="Add" class="add" />
		</form>
	</div>
{		/if}

{	if $translations}
	<a href="{$url.translate}" title="Translation status">Back to translations status</a>
	<table>
		<thead>
			<tr>
				<th width="30%">
				<h4>English string</h4>
				</th>
				<th>
				<h4>Translation</h4>
				</th>
				<th width="10%">When/who</th>
			</tr>
		</thead>
		<tbody>
{		foreach from=$translations key=key item=t}
			<tr {if $key % 2 == 0} class="odd" {/if}id="row{$t.id}">
				<td><label for="idmsg_{$t.id}" id="label_{$t.id}">{$t.message|escape:'html'}</label></td>
				<td>
				{if $can_edit}
				<form method="post" action="{$url.translations_save}" name="save_{$t.id}" onsubmit="save( this );return false;" accept-charset="UTF-8">
					{if $t.comment}
					Notes: <em>{$t.comment}</em><br/>
					{/if}
					<a href="#" class="copy" title="Copy original string" rel="{$t.id}">&gt;&gt;</a>
					<input type="text" value="{$t.translation|escape:html}"	id="idmsg_{$t.id}" name="translation" />
					<input type="hidden" name="id_message" value="{$t.id}" />
					<input type="hidden" name="lang" value="{$curr_lang}" />
					<input type="submit" value="Save" class="save" />
					<input type="hidden" name="save_url" url="{$url.translations_save}" />
				</form>
				{else}
					{$t.translation|escape:html}
				{/if}
				</td>
				<td><small>{$t.modified}<br />by {$t.author|truncate:10}</small></td>
			</tr>
{		/foreach}
		</tbody>
	</table>
{	else}
<div style="float:left" class="box">
	<h2>Current status of translations</h2>
	<p>Select a language to review its contents:</p>
	<table>
		<tr>
			<th>Language</th><th>Status</th><th>Completeness</th>
		</tr>
{		foreach from=$different_languages item=l key=key}
		<tr {if $key % 2 == 0} class="odd" {/if}id="row{$t.id}">
			<td><a href="{$url.translate}:{$l.lang}" title="Translate {$l.english_name}">{$l.english_name} ({$l.name})</a></td>
			<td>{if $l.missing == 0}Translation complete{else}{$l.missing} strings missing{/if}</td>
			<td class="fill_{if $l.percent >95}ok{elseif $l.percent > 70}warn{else}ko{/if}">{$l.percent}%</td></tr>
{		/foreach}
	</table>
	</div>
{/if}
{literal}
<style>
<!--
#contents{
	display:block;
	min-height:300px;
}
.box {
padding:25px;
}
#add-trans-form {
	display:none;
}

.save_ok {
	background: url(http://cdn.splitweet.com/images/msg_ok_bg.png) 9px 50% no-repeat #72C160;
	color: #fff;
}

.save_ko {
	background: url(http://cdn.splitweet.com/images/msg_ko_bg.png) 9px 50% no-repeat #C16060;
	color: #fff;
}
td,th {
	padding: 3px;
	border: 1px solid #333;
}

thead {
	color: black;
	font-weight: bold;
	background-color: #ccc
}

tbody input {
	width: 95%;
}

.fill_ok { background-color: #72C160; color: #fff; }
.fill_warn { background-color: #E5C452; color: #333; }
.fill_ko { background-color: #C16060; color: #fff; }
//
-->
</style>
<script type="text/javascript">
<!--

// Copy original string to translation text.
$('a.copy').click(function(){
	var id = $(this).attr('rel');
	var original_string = $('#label_'+id).text();
	var translation =  $('#idmsg_'+id).val();
	var cont = false;

	if ( translation.length > 0 )
	{
		cont = confirm( 'The following translation will be replaced by English string:\n\n'+translation + '\n\nAre you sure?' );
		if ( !cont )
		{
			return false;
		}
	}

	// Fill input box:
	$('#idmsg_'+id).val( original_string );

	return false;
});

$('#rebuild').click(function(){
	$.ajax({
		type: "POST",
		url: this.rel,
		dataType: 'json',
		success: function(txt)
		{
			alert( txt['msg'] );

		}
	});
});

$('#add-trans').click(function(){ $('#add-trans-form').toggle() });

function save( obj )
{
	var id_message = obj.id_message.value;
	var lang = obj.lang.value;
	var translation = obj.translation.value;
	var url_save = obj.action;
	$('#row'+id_message).removeClass();

	$.ajax({
		type: "POST",
		url: url_save,
		data: { id_message: id_message, lang: lang, translation: translation },
		dataType: 'json',
		success: function(txt)
		{
			if ( txt['status'] == 'OK' )
			{
				$('#row'+id_message).addClass( 'save_ok' );
			}
			else
			{
				$('#row'+id_message).addClass( 'save_ko' );
				alert(txt['msg']);
			}
		}
	});

}
//-->
</script>
{/literal}
</div>
{$modules.footer}
</div>
</body>
</html>