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
	];
	$params = $params + $vars;
	echo elgg_view('object/elements/summary', $params);
}
