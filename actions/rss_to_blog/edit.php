<?php

elgg_make_sticky_form('rss_to_blog/edit');

$guid = (int) get_input('guid');
$title = elgg_get_title_input();
$feed_url = get_input('feed_url');
$target_owner_guid = (array) get_input('target_owner_guid');
$target_container_guid = (array) get_input('target_container_guid');
$target_tags = string_to_tag_array(get_input('target_tags'));
$target_access_id = (int) get_input('target_access_id');
$comments_on = get_input('target_comments_on');
$refresh_interval = get_input('refresh_interval');

if (empty($feed_url) || empty($target_owner_guid) || empty($target_access_id) || empty($refresh_interval)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

if (!empty($guid)) {
	$entity = get_entity($guid);
	if (!$entity instanceof RSSToBlog || !$entity->canEdit()) {
		return elgg_error_response(elgg_echo('actionunauthorized'));
	}
} else {
	$entity = new RSSToBlog();
}

$entity->title = $title;
$entity->feed_url = $feed_url;
$entity->target_owner_guid = $target_owner_guid[0];
if (!empty($target_container_guid)) {
	$entity->target_container_guid = $target_container_guid[0];
} else {
	unset($entity->target_container_guid);
}
$entity->target_tags = $target_tags;
$entity->target_access_id = $target_access_id;
$entity->target_comments_on = $comments_on;
$entity->refresh_interval = $refresh_interval;

if (!$entity->save()) {
	return elgg_error_response(elgg_echo('save:fail'));
}

elgg_clear_sticky_form('rss_to_blog/edit');

return elgg_ok_response('', elgg_echo('save:success'));
