<?php

$guid = (int) get_input('guid');
if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof RSSToBlog) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

// could take a while
set_time_limit(0);

$count = $entity->import();

if ($count === false) {
	return elgg_error_response(elgg_echo('rss_to_blog:action:import:error', [$entity->getDisplayName()]));
}

return elgg_ok_response('', elgg_echo('rss_to_blog:action:import:success', [$entity->getDisplayName(), $count]));
