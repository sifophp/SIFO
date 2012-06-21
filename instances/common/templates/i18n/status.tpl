<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>{$instance_name|capitalize} translation tool</title>
	<link rel="stylesheet" type="text/css" media="screen" href="http://twitter.github.com/bootstrap/assets/css/bootstrap.css" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body style="margin-top:40px;">

<div class="container">

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="#">{$instance_name|capitalize}</a>
		</div>
	</div>
</div>

{    $modules.system_messages}

<div class="page-header">
	<h1>Translation tool</h1>
</div>

{        if $isAdmin}

<div class="btn-toolbar">
{	if $translations}
	<div class="btn-group">
		<a class="btn" href="{$url.translate|default:''}" title="Translation status"><i class="icon-chevron-left"></i> Back to languages list</a>
	</div>
{/if}
	<div class="btn-group">
		<a class="btn btn-warning" href="#" id="rebuild" rel="{$url.translations_rebuild|default:''}"><i class="icon-refresh icon-white"></i> Rebuild translations</a>
	</div>
	<div class="btn-group">
		<a class="btn btn-info" href="#" id="add-trans"><i class="icon-plus icon-white"></i> Add message</a>
		<a class="btn btn-info" href="#" id="add-trans"><i class="icon-wrench icon-white"></i> Customize translation</a>
	</div>
</div>

<div id="add-trans-form" style="display:none">
	<form class="form-horizontal" action="{$url.translations_add|default:''}">
		<input type="text" value="" name="msgid"/>
		<input type="text" value="" name="translation"/>
		<input type="hidden" name="lang" value="{$curr_lang|default:''}"/>
		<input class="add btn" type="submit" value="Add"/>
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
					<form class="form-horizontal" method="post" action="{$url.translations_save|default:''}" name="save_{$t.id|default:''}" onsubmit="save( this );return false;" accept-charset="UTF-8">

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
				<a href="{$url.translate|default:''}:{$l.lang|default:''}" title="Translate {$l.english_name|default:''}">{$l.english_name|default:''} ({$l.name|default:''})</a>
			</td>
			<td>{if $l.missing == 0}Translation complete{else}{$l.missing|default:''} strings missing{/if}</td>
			<td class="fill_{if $l.percent >95}ok{elseif $l.percent > 70}warn{else}ko{/if}">{$l.percent|default:''}%</td>
		</tr>
	{		/foreach}
</table>
{/if}
{literal}
<style>
	<!--
	.footer { margin-top: 45px; padding: 35px 0 36px; border-top: 1px solid #E5E5E5;}
	#contents {
		display: block;
		min-height: 300px;
	}

	.save_ok {
		background:#72C160 !important;
		color: #fff !important;
	}

	.save_ko {
		background:#C16060 !important;
		color: #fff !important;
	}

	.fill_ok {
		background-color: #72C160 !important;
		color: #fff !important;
	}

	.fill_warn {
		background-color: #E5C452 !important;
		color: #333 !important;
	}

	.fill_ko {
		background-color: #C16060 !important;
		color: #fff !important;
	}

	/
	/
	-->
</style>
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

	$( '#add-trans' ).click( function () {
		$( '#add-trans-form' ).toggle( 'fast' )
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
					$( obj ).parents( 'td' ).addClass( 'save_ok' );
				}
				else {
					$( obj ).parents( 'td' ).addClass( 'save_ko' );
					alert( txt['msg'] );
				}
			}
		} );

	}
	//-->
</script>
{/literal}

<footer class="footer">
	<p>Powered by Sifo, 2009-{$smarty.now|date_format:"%Y"}</p>
</footer>

</div>
<!-- /container -->

</body>
</html>