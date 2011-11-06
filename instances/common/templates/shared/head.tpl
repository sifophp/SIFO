<head>
    <meta charset="utf-8">
    <title>{$metadata.title}</title>
	<meta http-equiv="Content-Language" content="{$url.lang}" />
    <meta name="title" content="{$metadata.title}" />
	<meta name="description" content="{$metadata.description}" />
	<meta name="keywords" content="{$metadata.keywords}" />

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    {literal}
	<style type="text/css">
      body {
        padding-top: 60px;
      }
    </style>
	{/literal}
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

	{* Styles and JS is loaded inside this module: *}
	{	$media_module}
  </head>
