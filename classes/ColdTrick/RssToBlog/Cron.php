<?php

namespace ColdTrick\RssToBlog;

class Cron {
	
	/**
	 * Proccess all rss feed imports
	 *
	 * @param \Elgg\Hook $hook 'cron', 'all'
	 *
	 * @return void
	 */
	public static function importBlogs(\Elgg\Hook $hook) {
		
		$allowed_intervals = [
			'fifteenmin',
			'halfhour',
			'hourly',
			'daily',
		];
		$interval = $hook->getType();
		if (!in_array($interval, $allowed_intervals)) {
			// different interval
			return;
		}
		
		echo "Starting RSS-to-Blog import: {$interval}" . PHP_EOL;
		elgg_log("Starting RSS-to-Blog import: {$interval}", 'NOTICE');
		
		// takes a long time
		set_time_limit(0);
		
		elgg_call(ELGG_IGNORE_ACCESS, function() use ($interval) {
			
			// prevent notifications
			elgg_register_plugin_hook_handler('enqueue', 'notification', __NAMESPACE__ . '\Notifications::preventBlogEnqueue');
			elgg_register_plugin_hook_handler('send:before', 'notifications', __NAMESPACE__ . '\Notifications::preventBlogSendBefore');
			
			// get feeds
			$feeds = elgg_get_entities([
				'type' => 'object',
				'subtype' => \RSSToBlog::SUBTYPE,
				'limit' => false,
				'metadata_name_value_pairs' => [
					[
						'name' => 'refresh_interval',
						'value' => $interval,
					],
				],
				'batch' => true,
			]);
			
			$count = 0;
			/* @var $feed \RssToBlog */
			foreach ($feeds as $feed) {
				$count += (int) $feed->import();
			}
			
			// restore notifications
			elgg_unregister_plugin_hook_handler('enqueue', 'notification', __NAMESPACE__ . '\Notifications::preventBlogEnqueue');
			elgg_unregister_plugin_hook_handler('send:before', 'notifications', __NAMESPACE__ . '\Notifications::preventBlogSendBefore');
			
			echo "RSS-to-Blog imported {$count} blogs" . PHP_EOL;
			elgg_log("RSS-to-Blog imported {$count} blogs", 'NOTICE');
		});
		
		echo "Done with RSS-to-Blog import: {$interval}" . PHP_EOL;
		elgg_log("Done with RSS-to-Blog import: {$interval}", 'NOTICE');
	}
}
