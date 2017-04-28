<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$url.lang}">
{if isset($modules.head)}{$modules.head nofilter}{/if}
{literal}
<style>
#error500{
	text-align:center;
}
</style>
{/literal}
<body>
<div id="error500">
	<p style="width:600px;margin:auto;" class="msg_ko">{t}Internal Server Error{/t}</p>
	{if isset($error)}
<hr />
Message:
<pre>
{$error.msg}
</pre>
Trace:
Message:
<pre>
{$error.trace}
</pre>
	{/if}
</div>
</body>
</html>
