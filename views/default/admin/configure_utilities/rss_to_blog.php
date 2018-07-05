<?php

elgg_register_menu_item('title', [
	'name' => 'add',
	'icon' => 'plus',
	'text' => elgg_echo('add:object:rss_to_blog'),
	'href' => elgg_generate_url('add:object:rss_to_blog'),
	'link_class' => [
		'elgg-button',
		'elgg-button-action',
		'elgg-lightbox',
	],
	'data-colorbox-opts' => json_encode([
		'width' => '1024px',
	]),
]);

echo elgg_list_entities([
	'type' => 'object',
	'subtype' => RSSToBlog::SUBTYPE,
	'no_results' => true,
]);
