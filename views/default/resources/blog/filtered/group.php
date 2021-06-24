<?php

use Elgg\Exceptions\Http\BadRequestException;

$group_guid = elgg_extract('guid', $vars, elgg_extract('group_guid', $vars)); // group_guid for BC
$lower = elgg_extract('lower', $vars);
$upper = elgg_extract('upper', $vars);
$filter = elgg_extract('filter', $vars);
if (!in_array($filter, ['internal', 'external'])) {
	throw new BadRequestException();
}

elgg_entity_gatekeeper($group_guid, 'group');
elgg_group_tool_gatekeeper('blog', $group_guid);

/* @var $group \ElggGroup */
$group = get_entity($group_guid);

elgg_register_title_button('blog', 'add', 'object', 'blog');

elgg_push_collection_breadcrumbs('object', 'blog', $group);

$title = elgg_echo("collection:object:blog:group:{$filter}");
if ($lower) {
	$title .= ': ' . elgg_echo('date:month:' . date('m', $lower), [date('Y', $lower)]);
}

$content = elgg_view("blog/listing/{$filter}", [
	'entity' => $group,
	'created_after' => $lower,
	'created_before' => $upper,
]);

$layout = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'sidebar' => elgg_view('blog/sidebar', [
		'page' => $filter,
		'entity' => $group,
	]),
	'filter_id' => 'blog/group',
	'filter_value' => $filter,
]);

echo elgg_view_page($title, $layout);
