<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$url.lang}">
<head>
<style type="text/css">
{literal}
#redirect_info {
	font-family: Arial, sans-serif;
	color: #444;
	width: 50%;
	margin: 100px auto;
	background: #F1F1F1;
	padding: 10px;
	border: 1px solid #DDD;
	border-top: 10px solid #DDD;
	overflow: hidden;
}

a { color: blue;}
.button {
	display: block;
	background: #CDE;
	padding: 6px;
	font-family: "Trebuchet MS", Arial, sans-serif;
	font-size: 18px;
	font-weight: bold;
	color: #343434;
	border: none;
	width: 120px;
	-webkit-border-radius: 7px;
	-moz-border-radius: 7px;
	cursor: pointer;
	text-decoration: none;
	margin: 10px;
	text-align: center;
	float:right;
}
{/literal}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{literal}
<script type="text/javascript">
<!--
	steps = ["3[..........]",
			"3[=.........]",
			"3[==........]",
			"3[===.......]",
			"2[====......]",
			"2[=====.....]",
			"2[======....]",
			"2[=======...]",
			"1[========..]",
			"1[=========.]",
			"1[==========]"];
	var frame = 0;

	var paused = false;

	function init(){
		interval = setInterval( "rotate_icon()", 300 );
	}
	function rotate_icon(){
		frame++;
		if ( frame == 10 && !paused ){
			document.getElementById('pause_btn').innerHTML = 'Bungee!!';
			clearTimeout( interval );
			location.href = document.getElementById('destination').href;
		}
		else
		{
			document.getElementById('redirect').innerHTML = steps[frame];
		}
	}
	function pause_toggle()
	{
		if ( paused )
		{
			document.getElementById('pause_btn').innerHTML = 'Pause';
			paused = false;
			interval = setInterval( "rotate_icon()", 300 );
		}
		else
		{
			clearTimeout( interval );
			document.getElementById('pause_btn').innerHTML = 'Continue';
			paused = true;
		}
	}
//-->
</script>
{/literal}
</head>
{if isset( $error ) }
<body onload="init()">
	<div id="redirect_info">
		<h1>{$error.code} Redirect exception</h1>

		Redirecting to...<br /> <a id='destination'
			href='{$error.url_redirect}'>{$error.url_redirect}</a>
			<pre id='redirect'></pre>
			<a id='pause_btn' href="#" class="button"
			onclick="pause_toggle();">Pause</a>
	</div>
{/if}


</body>
</html>
