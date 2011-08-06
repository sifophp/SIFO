{if isset( $mail_sent )}
	<H2>Mail sent!</H2><BR/>
	Result: {$result|@debug_print_var}
	<BR/><BR/>
	{if isset( $return_page )}
		<a href="{$return_page}">Return...</a>
	{/if}
{else}
	<H2>The app is trying to send an email with this data:</H2><BR/>
	{foreach from=$mail_data item=info key = var }
	<table>
	<tr>
		<td>
			<b>{$var}</b>:
		</td>
		<td>
			{$info}
		</td>
	</tr>
	</table>
	<hr>
	{/foreach}
	<BR/><BR/>
	<h3>For continue with mail sending:</h3><a href="{$continue_sending}">Accept and continue sending...</a><BR/>
	{if isset( $return_page )}
		<h3>If don't need to continue sending click here:</h3><a href="{$return_page}">Cancel...</a><BR/>
	{/if}
{/if}