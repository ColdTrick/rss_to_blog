<?php

use Elgg\Exceptions\Http\BadRequestException;

$lower = elgg_extract('lower', $vars);
$upper = elgg_extract('upper', $vars);
$filter = elgg_extract('filter', $vars);
if (!in_array($filter, ['internal', 'external'])) {
	throw new BadRequestException();
}

elgg_register_title_button('blog', 'add', 'object', 'blog');

elgg_push_collection_breadcrumbs('object', 'blog');

$title = elgg_echo("collection:object:blog:all:{$filter}");
if ($lower) {
	$title .= ': ' . elgg_echo('date:month:' . date('m', $lower), [date('Y', $lower)]);
}

$content = elgg_view("blog/listing/{$filter}", [
	'created_after' => $lower,
	'created_before' => $upper,
]);

$layout = elgg_view_layout('default', [
	'title' => $title,
	'content' => $content,
	'sidebar' => elgg_view('blog/sidebar', ['page' => $filter]),
	'filter_value' => $filter,
]);

echo elgg_view_page($title, $layout);
