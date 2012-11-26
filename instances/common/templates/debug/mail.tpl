{if isset( $mail_sent )}
	<h3>Mail sent!</h3><BR/>
	Result: {$result|@debug_print_var}
	<BR/><BR/>
	{if isset( $return_page )}
		<a href="{$return_page}">Return...</a>
	{/if}
{else}
	<h3>The app is trying to send an email with this data:</h3><BR/>
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
	<h4>For continue with mail sending:</h4><a href="{$continue_sending}">Accept and continue sending...</a><BR/>
	{if isset( $return_page )}
		<h4>If don't need to continue sending click here:</h4><a href="{$return_page}">Cancel...</a><BR/>
	{/if}
{/if}