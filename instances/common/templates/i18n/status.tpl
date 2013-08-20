<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{$instance|capitalize} translation tool</title>
	<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body style="margin-top:40px;">

<div class="container">

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
	{foreach from=$current_instance_inheritance item=instance_menu}
		<ul class="nav">
		{if $instance_menu != 'common'}
			<li {if $instance_menu==$instance}class="active"{/if}><a class="brand" href="{$url.translate|default:''}:{$instance_menu}">{$instance_menu|capitalize}</a></li>
		{/if}
		</ul>
	{/foreach}
		</div>
	</div>
</div>

{    $modules.system_messages nofilter}

<div class="page-header">
	<h1>Translation tool instance: {$instance|capitalize}</h1>
</div>

{        if $isAdmin}

<div class="btn-toolbar">
{	if $translations}
	<div class="btn-group">
		<a class="btn" href="{$url.translate|default:''}:{$instance}" title="Translation status"><i class="icon-chevron-left"></i> Back to languages list</a>
	</div>
{/if}
	<div class="btn-group">
		<a class="btn btn-warning" href="#" id="rebuild" rel="{$url.translations_rebuild|default:''}:{$instance}"><i class="icon-refresh icon-white"></i> Rebuild translations</a>
	</div>
	<div class="btn-group">
		<a class="btn btn-info" href="#" id="add-message"><i class="icon-plus icon-white"></i> Add message</a>
		{if !$is_parent_instance}<a class="btn btn-info" href="#" id="customize"><i class="icon-wrench icon-white"></i> Customize translation on {$instance|capitalize}</a>{/if}
	</div>
</div>

<div id="add-message-form" style="display:none">
	<form class="form-horizontal well" action="{$url.translations_actions|default:''}:a:addMessage:i:{$instance}" id="add_message" onsubmit="add( this );return false;">
		<div class="input-append">
			<input class="span7" type="text" value="" name="msgid" placeholder="New message to add to parent instance..." /><input class="add btn" type="submit" value="Add"/>
		</div>
	</form>
</div>

<div id="customize-form" style="display:none">
	<form class="form-horizontal well" action="{$url.translations_actions|default:''}:a:customizeTranslation:i:{$instance}" id="customize_translate" onsubmit="add( this );return false;">
		<div class="input-append">
			<input class="span7" type="text" value="" name="msgid" placeholder="Message to be customized on {$instance|capitalize}..." /><input class="add btn" type="submit" value="Customize"/>
		</div>
	</form>
</div>
{		/if}

{    if $translations}
<table class="table table-bordered table-striped">
	<thead>
	<tr>
		<th width="10">
			<h4>English string</h4>
		</th>
		<th>
			<h4>Translation</h4>
		</th>
		<th width="10%">When/who</th>
	</tr>
	</thead>
	<tbody>
		{        foreach from=$translations key=key item=t}
		<tr>
			<td>
				<label for="idmsg_{$t.id|default:''}" id="label_{$t.id|default:''}">{$t.message|escape:'html'|default:''|wordwrap:40:'<br />':true}</label>
			</td>
			<td>
				{if $can_edit}
					<form class="form-horizontal" method="post" action="{$url.translations_save|default:''}:{$instance}" name="save_{$t.id|default:''}" onsubmit="save( this );return false;" accept-charset="UTF-8">

						<input type="hidden" name="id_message" value="{$t.id|default:''}"/>
						<input type="hidden" name="lang" value="{$curr_lang|default:''}"/>
						<input type="hidden" name="save_url" url="{$url.translations_save|default:''}"/>

						<div class="input-append input-prepend">
							<a href="#" class="copy btn" title="Copy original string" rel="{$t.id|default:''}">&gt;&gt;</a><input class="span6" type="text" value="{$t.translation|escape:html|default:''}" id="idmsg_{$t.id|default:''}" name="translation"/><input class="btn" type="submit" value="Save"/>
						</div>
						{if $t.comment}
							<span class="help-block">Notes: <em>{$t.comment|default:''}</em></span>
						{/if}

					</form>
					{else}
					{$t.translation|escape:html|default:''}
				{/if}
			</td>
			<td>
				<small>{$t.modified|default:''}<br/>by {$t.author|truncate:10|default:''}</small>
			</td>
		</tr>
		{		/foreach}
	</tbody>
</table>
	{    else}
<h2>Current status of translations</h2>

<p>Select a language to review its contents:</p>
<table class="table table-bordered table-striped">
	<tr>
		<th>Language</th>
		<th>Status</th>
		<th>Completeness</th>
	</tr>
	{        foreach from=$different_languages item=l key=key}
		<tr>
			<td>
				<a href="{$url.translate|default:''}:{$instance}:{$l.lang|default:''}" title="Translate {$l.english_name|default:''}">{$l.english_name|default:''} ({$l.name|default:''})</a>
			</td>
			<td>{if $l.missing == 0}Translation complete{else}{$l.missing|default:''} strings missing{/if}</td>
			<td class="btn-{if $l.percent >95}success{elseif $l.percent > 70}warning{else}danger{/if}">{$l.percent|default:''}%</td>
		</tr>
	{		/foreach}
</table>
{/if}
{literal}
<script type="text/javascript">
	<!--

	// Copy original string to translation text.
	$( 'a.copy' ).click( function () {
		var id = $( this ).attr( 'rel' );
		var original_string = $( '#label_' + id ).text();
		var translation = $( '#idmsg_' + id ).val();
		var cont = false;

		if ( translation.length > 0 ) {
			cont = confirm( 'The following translation will be replaced by English string:\n\n' + translation + '\n\nAre you sure?' );
			if ( !cont ) {
				return false;
			}
		}

		// Fill input box:
		$( '#idmsg_' + id ).val( original_string );

		return false;
	} );

	$( '#rebuild' ).click( function () {
		$.ajax( {
			type:"POST",
			url:this.rel,
			dataType:'json',
			success:function ( txt ) {
				alert( txt['msg'] );

			}
		} );
	} );

	$( '#add-message' ).click( function () {
		$( '#customize-form' ).hide( 'fast' )
		$( '#add-message-form' ).toggle( 'fast' )
	} );

	$( '#customize' ).click( function () {
		$( '#add-message-form' ).hide( 'fast' )
		$( '#customize-form' ).toggle( 'fast' )
	} );

	function save( obj ) {
		var id_message = obj.id_message.value;
		var lang = obj.lang.value;
		var translation = obj.translation.value;
		var url_save = obj.action;
		$( '#row' + id_message ).removeClass();

		$.ajax( {
			type:"POST",
			url:url_save,
			data:{ id_message:id_message, lang:lang, translation:translation },
			dataType:'json',
			success:function ( txt ) {
				if ( txt['status'] == 'OK' ) {
					$( obj ).parents( 'td' ).addClass( 'btn-success' );
				}
				else {
					$( obj ).parents( 'td' ).addClass( 'btn-danger' );
					alert( txt['msg'] );
				}
			}
		} );
	}

	function add( obj )
	{
		var url_save = obj.action;
		var data = $('#' + obj.id ).serialize();

		$.ajax( {
			type:"POST",
			url:url_save,
			data: data,
			dataType:'json',
			success:function ( txt ) {
				alert( txt['msg'] );
			}
		} );
	}
	//-->
</script>
{/literal}

<footer class="footer" style="margin-top: 45px; padding: 35px 0 36px; border-top: 1px solid #E5E5E5;">
	<p>Powered by Sifo, 2009-{$smarty.now|date_format:"%Y"}</p>
</footer>

</div>
<!-- /container -->

</body>
</html>