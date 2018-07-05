<?php

$guid = (int) get_input('guid');
if (empty($guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$entity = get_entity($guid);
if (!$entity instanceof RSSToBlog) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

$entity->import();
