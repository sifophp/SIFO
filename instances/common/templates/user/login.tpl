<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$url.lang}">
{$modules.head nofilter}
<body>
<div id="wrapper" class="lang_{$url.lang}{if $links_new_window} new_window{/if}{if $rtl} rtl{/if}">
{	$modules.header nofilter}
<div id="contents" class="clearfix">
{if isset($errors)}
<div class="msg_ko">
{foreach from=$errors item=error}
	<p>{t}{$error}{/t}</p>
{/foreach}
</div>
{/if}

<div class="standard_form">
<form method="post" name="login" action="{$url.login}">
	<h2>{t}Login{/t}</h2>
	<fieldset>
		<p class="already"><a title="{t}Password reminder{/t}"
		href="{$url.reminder}">{t}Password reminder{/t}</a></p>
		<div class="fieldrow">
			<label for="email">{t}Your email{/t}</label>
			<input type="text" id="email" name="email" value="{$email}" />
		</div>
		<div class="fieldrow">
			<label for="pass">{t}Your password{/t}</label>
			<input type="password" id="pass" name="pass" />
		</div>
		<div class="submitrow">
		<input type="submit" value="{t}Login{/t}" />
{		if !$disable_register_text}
		<p class="signup">{t}or{/t} <a href="{$url.signup}" title="{t}signup for a free account{/t}">{t escape=off}signup for a <strong>free account</strong>{/t}</a></p>
{		/if}
		</div>
	</fieldset>
</form>
</div>

{if $logged}
	<a title="{t}Back to the dashboard{/t}" href="{$url.base}">{t}Back to the dashboard{/t}</a>
{/if}
</div>
{$modules.footer nofilter}
</div>
</body>
</html>
