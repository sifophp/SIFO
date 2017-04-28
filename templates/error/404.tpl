<h1>Error 404 - Not found page</h1>

{if isset( $error )}
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