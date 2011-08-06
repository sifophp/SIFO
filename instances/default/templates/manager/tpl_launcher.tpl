<html>
<head>
	<title>Templates Test Launcher</title>
	<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
	
	<link rel="stylesheet" type="text/css" href="http://static.seoframework.local/css/main.css?id=" /></head>	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script type="text/javascript" src="{$url.static}/js/jquery.translate-core.min.js"></script>	
	{literal}
	<script type="text/javascript">
	$(document).ready( function(){
	} );
	</script>
	{/literal}
<body>
	<h1>Templates Test Launcher</h1>
{	if isset( $error ) }
	<p class="error">{$error}</p>
{	/if}
	<form method="POST">
	<p>
	<label for="instance">Select the template to simulate:</label>
	<select id="select_template" name="select_template" onchange="document.location.href='template-launcher?template='+this.options[this.options.selectedIndex].value">
{	foreach from=$available_templates item=i}
		<option value="{$i}"{if isset( $selected_template ) && $selected_template == $i} selected="selected"{/if}>{$i}</option>
{	/foreach}
	</select>
	</form>
{	if isset( $used_vars )}
	<h2>Please, fill the required vars. Remind: Probably are not required all the vars.</h2>
	<h3>For array use php codification. Array( 'key1'=>'value1', 'key2'->'value2',...,'key3'->'value3')</h3>
	<form action="template-launcher?template={$selected_template}" method="post">
{	foreach from=$used_vars item=var_name}
		<label for="{$var_name}">{$var_name}:</label><input type="text" id="{$var_name}" name="{$var_name}"><br/>
{	/foreach}
		<br/><input type="submit" value="Submit" />
	</form>
{	/if}
</body>
</html>