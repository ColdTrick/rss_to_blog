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

// prevent notifications
elgg_register_plugin_hook_handler('enqueue', 'notification', 'ColdTrick\RssToBlog\Notifications::preventBlogEnqueue');
elgg_register_plugin_hook_handler('send:before', 'notifications', 'ColdTrick\RssToBlog\Notifications::preventBlogSendBefore');

// import
$count = $entity->import();

// restore notifications
elgg_unregister_plugin_hook_handler('enqueue', 'notification', 'ColdTrick\RssToBlog\Notifications::preventBlogEnqueue');
elgg_unregister_plugin_hook_handler('send:before', 'notifications', 'ColdTrick\RssToBlog\Notifications::preventBlogSendBefore');

if ($count === false) {
	return elgg_error_response(elgg_echo('rss_to_blog:action:import:error', [$entity->getDisplayName()]));
}

return elgg_ok_response('', elgg_echo('rss_to_blog:action:import:success', [$entity->getDisplayName(), $count]));
