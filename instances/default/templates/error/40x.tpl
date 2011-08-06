{* 401 and 403 errors *}
<h1>Forbidden</h1>
<p>You are not authorized to see the page. Maybe you need to login first</p>
{if $error}
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