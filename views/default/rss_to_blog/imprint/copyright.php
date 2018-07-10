<?php
/**
 * Show a copyright notice on imported blogs (if available)
 *
 * @uses $vars['entity'] the blog to show copyright info for
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggBlog) {
	return;
}

if (empty($entity->rss_copyright) && empty($entity->rss_source_title)) {
	// no copyright info available
	return;
}

$icon = 'copyright';
$text = $entity->rss_copyright;
if (empty($text)) {
	$icon = 'link';
	$text = $entity->rss_source_title;
}

$link = elgg_view('output/url', [
	'text' => $text,
	'href' => $entity->rss_permalink,
	'target' => '_blank',
]);

echo elgg_view('object/elements/imprint/element', [
	'icon_name' => $icon,
	'content' => $link,
]);
