<?php
// Do not translate:
$config['base'] = $base = \Sifo\Urls::$base_url;
$config['domain'] = $domain = \Sifo\Domains::getInstance()->getDomain();
$config['subdomain'] = $subdomain = \Sifo\Domains::getInstance()->getSubdomain();
$config['lang'] = $subdomain = \Sifo\Domains::getInstance()->getLanguage();
$config['static'] = \Sifo\Domains::getInstance()->getStaticHost();

$config['mail-continue'] = $base . '/mail-continue';

// Translator
$config['translate']			= $base . '/translate';
$config['translations_save']	= $base . '/translation-save';
$config['translations_rebuild']	= $base . '/translation-rebuild';

$config["locales"]				= $base . '/locales';
$config["locales_save"]			= $base . '/locales-save';

// Translate below: