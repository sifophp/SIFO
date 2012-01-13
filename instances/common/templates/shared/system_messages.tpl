{	if $ko_messages}
<div class="msg_ko">
	<ul>
{		foreach from=$ko_messages item=msg}
		<li>{t escape=off}{$msg}{/t}</li>
{		/foreach}
	</ul>
</div>
{	/if}
{	if $ok_messages}
<div class="msg_ok">
	<ul>
{		foreach from=$ok_messages item=msg}
		<li>{t escape=off}{$msg}{/t}</li>
{		/foreach}
	</ul>
</div>
{	/if}
{	if $info_messages}
<div class="msg_info">
	<ul>
{		foreach from=$info_messages item=msg}
		<li>{t escape=off}{$msg}{/t}</li>
{		/foreach}
	</ul>
</div>
{	/if}
{	if $warning_messages}
<div class="msg_info">
	<ul>
{		foreach from=$warning_messages item=msg}
		<li>{t escape=off}{$msg}{/t}</li>
{		/foreach}
	</ul>
</div>
{	/if}