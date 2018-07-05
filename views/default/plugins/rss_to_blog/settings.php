<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

// proxy settings
$proxy = elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('rss_to_blog:settings:proxy:host'),
	'name' => 'params[proxy_host]',
	'value' => $plugin->proxy_host,
]);

$proxy .= elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('rss_to_blog:settings:proxy:port'),
	'name' => 'params[proxy_port]',
	'value' => $plugin->proxy_port,
	'min' => 0,
	'max' => 65535,
]);

$proxy .= elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('rss_to_blog:settings:proxy:disable_ssl_verify'),
	'#help' => elgg_echo('rss_to_blog:settings:proxy:disable_ssl_verify:help'),
	'name' => 'params[proxy_disable_ssl_verify]',
	'default' => 0,
	'value' => 1,
	'checked' => !empty($plugin->proxy_disable_ssl_verify),
	'switch' => true,
]);

echo elgg_view_module('info', elgg_echo('rss_to_blog:settings:proxy:title'), $proxy);
