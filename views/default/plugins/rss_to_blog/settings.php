<?php

/* @var $plugin ElggPlugin */
$plugin = elgg_extract('entity', $vars);

echo elgg_view_field([
	'#type' => 'checkbox',
	'#label' => elgg_echo('rss_to_blog:settings:split_blogs'),
	'#help' => elgg_echo('rss_to_blog:settings:split_blogs:help'),
	'name' => 'params[split_blogs]',
	'value' => 1,
	'checked' => (bool) $plugin->split_blogs,
	'switch' => true,
]);
