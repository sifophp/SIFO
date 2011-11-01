<?php
/**
 * This file should define all the properties regarding advertising modules.
 *
 * The key is used as 'module_name'.
 */

// Store the Google client ID, for repetitive usage:
$config['google_client'] = 'pub-1420060626565286';

// Link analytics with adsense:
$config['google_analytics_domain_name'] = \Sifo\Domains::getInstance()->getDomain();

$config['ads_google_skyscrapper'] = array(
		'google_analytics_domain_name' => $config['google_analytics_domain_name'],
		'client' => $config['google_client'],
		'comment' => 'Skyscrapper 120x600',
		'containter_class' => 'skyscrapper',
		'layout' => 'ads/google.tpl',
		'slot' => '7567220975',
		'width'	=> 120,
		'height' => 600,
		'type' => 'text_image',
		'colors' => array(
			'border' => 'FFFFFF',
			'bg'	=> 'FFFFFF',
			'link'	=> '085fd6',
			'url'	=> '008000',
			'text'	=> 'CCCCCC'
		)
	);

$config['ads_google_standard'] = array(
		'google_analytics_domain_name' => $config['google_analytics_domain_name'],
		'client' => $config['google_client'],
		'comment' => 'Gràfic i text 300x250',
		'containter_class' => 'standard_banner',
		'layout' => 'ads/google.tpl',
		'slot' => '8051831856',
		'width'	=> 300,
		'height' => 250,
		'type' => 'text_image',
		'colors' => array(
			'border' => 'FFFFFF',
			'bg'	=> 'FFFFFF',
			'link'	=> '085fd6',
			'url'	=> '008000',
			'text'	=> 'CCCCCC'
		)
	);