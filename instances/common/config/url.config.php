<?php
// Do not translate:
$config['base']      = $base = \Sifo\Urls::$base_url;
$config['domain']    = $domain = \Sifo\Domains::getInstance()->getDomain();
$config['subdomain'] = $subdomain = \Sifo\Domains::getInstance()->getSubdomain();
$config['lang']      = $subdomain = \Sifo\Domains::getInstance()->getLanguage();
$config['static']    = \Sifo\Domains::getInstance()->getStaticHost();

$config['mail-continue'] = $base . '/mail-continue';

// Sifo debug
$config['sifo_debug_analyzer'] = $base . '/sifo-debug-analyzer';
$config['sifo_debug_actions']  = $base . '/sifo-debug-actions';

// Translator
$config['translate']            = $base . '/translate';
$config['translations_save']    = $base . '/translation-save';
$config['translations_rebuild'] = $base . '/translation-rebuild';
$config['translations_add']     = $base . '/translations-add';
$config['translations_actions'] = $base . '/translation-actions';

$config["locales"]      = $base . '/locales';
$config["locales_save"] = $base . '/locales-save';

// Translate below: