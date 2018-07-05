<?php

$entity = elgg_extract('entity', $vars);
if ($entity instanceof RSSToBlog) {
	echo elgg_view_field([
		'#type' => 'hidden',
		'name' => 'guid',
		'value' => $entity->guid,
	]);
}

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('title'),
	'name' => 'title',
	'value' => elgg_extract('title', $vars),
]);

echo elgg_view_field([
	'#type' => 'url',
	'#label' => elgg_echo('rss_to_blog:edit:feed_url'),
	'name' => 'feed_url',
	'value' => elgg_extract('feed_url', $vars),
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'userpicker',
	'#label' => elgg_echo('rss_to_blog:edit:target_owner_guid'),
	'#help' => elgg_echo('rss_to_blog:edit:target_owner_guid:help'),
	'name' => 'target_owner_guid',
	'values' => elgg_extract('target_owner_guid', $vars),
	'limit' => 1,
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'grouppicker',
	'#label' => elgg_echo('rss_to_blog:edit:target_container_guid'),
	'#help' => elgg_echo('rss_to_blog:edit:target_container_guid:help'),
	'name' => 'target_container_guid',
	'values' => elgg_extract('target_container_guid', $vars),
	'limit' => 1,
]);

echo elgg_view_field([
	'#type' => 'tags',
	'#label' => elgg_echo('rss_to_blog:edit:target_tags'),
	'name' => 'target_tags',
	'value' => elgg_extract('target_tags', $vars),
]);

echo elgg_view_field([
	'#type' => 'access',
	'#label' => elgg_echo('rss_to_blog:edit:target_access_id'),
	'name' => 'target_access_id',
	'value' => elgg_extract('target_access_id', $vars),
	'options_values' => [
		RSS_TO_BLOG_ACCESS_GROUP => elgg_echo('rss_to_blog:edit:target_access_id:group'),
		ACCESS_LOGGED_IN => elgg_echo('access:label:logged_in'),
		ACCESS_PUBLIC => elgg_echo('access:label:public'),
	],
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('rss_to_blog:edit:refresh_interval'),
	'#help' => elgg_echo('rss_to_blog:edit:refresh_interval:help'),
	'name' => 'refresh_interval',
	'value' => elgg_extract('refresh_interval', $vars),
	'options_values' => [
		'fifteenmin' => elgg_echo('interval:fifteenmin'),
		'halfhour' => elgg_echo('interval:halfhour'),
		'hourly' => elgg_echo('interval:hourly'),
		'daily' => elgg_echo('interval:daily'),
	],
	'required' => true,
]);

// form footer
$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);

elgg_set_form_footer($footer);
