<?php
// Do not translate:
$config['base'] = $base = UrlParser::$base_url;
$config['domain'] = $domain = Domains::getInstance()->getDomain();
$config['subdomain'] = $subdomain = Domains::getInstance()->getSubdomain();
$config['lang'] = $subdomain = Domains::getInstance()->getLanguage();
$config['static'] = Domains::getInstance()->getStaticHost();

$config['mail-continue'] = $base . '/mail-continue';

// Translator
$config['translate']			= $base . '/translate';
$config['translations_save']	= $base . '/translation-save';
$config['translations_rebuild']	= $base . '/translation-rebuild';

$config["locales"]				= $base . '/locales';
$config["locales_save"]			= $base . '/locales-save';

// Translate below: