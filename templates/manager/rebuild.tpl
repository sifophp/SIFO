<!DOCTYPE html>
<html lang="en">
<head>
    <title>Rebuild for {$inheritance.0|capitalize}</title>
</head>
<style>
{literal}
body{
    color: #333;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-size: 12px;
}
a { text-decoration: none; color: inherit; padding: 5px;}
h3 { padding: 10px;}
pre {
    -webkit-border-image: none;
    position: relative;
    background-color: whiteSmoke;
    border-color: #ddd;
    border-radius: 4px;
    border-style: solid;
    border-width: 1px;
    color: #333;
    display: block;
    font-family: Monaco, Menlo, Consolas, 'Courier New', monospace;
    font-size: 13px;
    line-height: 20px;
    white-space: pre-wrap;
    word-break: break-all;
    word-wrap: break-word;
    padding: 10px;;
}
#container { padding: 10px; }
#errors { padding: 10px; border: 1px solid #b43d3d; background-color: #FFCFD1; }
#errors p { font-weight: bold; }
.bgcolor1 { background-color: #cdf492; } .bgcolor1, .bordercolor1 { border: 1px solid #a2cb5b; border-left-width: 5px; }
.bgcolor2 { background-color: #d7eff4; } .bgcolor2, .bordercolor2 { border: 1px solid #73a7b4; border-left-width: 5px; }
.bgcolor3 { background-color: #ebd5f4; } .bgcolor3, .bordercolor3 { border: 1px solid #c963f4; border-left-width: 5px; }
.bgcolor4 { background-color: #f4e486; } .bgcolor4, .bordercolor4 { border: 1px solid #f4da43; border-left-width: 5px; }

{/literal}
</style>
<body>
<div id="container">
<h1>Rebuilding configuration files...</h1>
{if !empty($errors)}
<div id="errors">
    <p>Unable to write the following files, fix their permissions (copy/paste):</p>
    <ul>
{foreach $errors as $error}
        <li>chmod -R 777 {$error}</li>
{/foreach}
    </ul>
</div>
{/if}
<h2>Your instance inheritance is:
{foreach $inheritance as $instance}
<a href="#{$instance}" class="bgcolor{$instance@iteration}">{$instance}</a>
{if !$instance@last} > {/if}
{/foreach}
</h2>
{foreach $files_output as $instance_name => $files_instance }
<h3 id="{$instance_name}" class="bgcolor{$files_instance@iteration}">Instance <em>{$instance_name|ucfirst}</em></h3>
    {foreach  $files_instance as $file => $output}
    <h4>{$filenames.$file}</h4>
    <pre class="bordercolor{$files_instance@iteration}">{$output|escape}</pre>
    {/foreach}
{/foreach}
</div>
</body>
</html>



