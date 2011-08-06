<h1>Error {$error_code} - {$error_code_msg}</h1>

{if $error}
<hr />
Message:
<pre>
{$error.msg}
</pre>
Trace:
Messages:
<pre>
{$error.trace}
</pre>
{/if}