<?php

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof RSSToBlog) {
	return;
}

if (elgg_extract('full_view', $vars, false)) {
	// @todo
} else {
	
	$params = [
		'entity' => $entity,
		'icon' => false,
		'byline' => false,
		'access' => false,
		'time' => false,
		'imprint' => [
			[
				'icon_name' => 'sync-alt',
				'content' => elgg_echo("interval:{$entity->refresh_interval}"),
			],
		],
	];
	
	$last_refresh = strtolower(elgg_echo('never'));
	if ($entity->last_refresh) {
		$last_refresh = elgg_view_friendly_time($entity->last_refresh);
	}
	$params['imprint'][] = [
		'icon_name' => 'history',
		'content' => elgg_echo('rss_to_blog:last_refresh') . ': ' . $last_refresh,
	];
	
	$params = $params + $vars;
	echo elgg_view('object/elements/summary', $params);
}
