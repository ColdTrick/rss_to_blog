<?php

namespace ColdTrick\RssToBlog;

use ColdTrick\RssToBlog\SimplePie\File;
use Elgg\Values;
use Elgg\Project\Paths;

class RSSReader {
	
	/**
	 * Create a SimplePie instance with all to correct settings
	 *
	 * @param array $params feed params
	 *
	 * @return \SimplePie
	 * @throws \InvalidArgumentException
	 */
	public static function factory($params = []) {
		
		$defaults = [
			'timeout' => 5,
			'cache_location' => elgg_get_cache_path() . 'simplepie' . DIRECTORY_SEPARATOR,
			'enable_cache' => true,
		];
		$params = array_merge($defaults, $params);
		
		if (empty($params['feed_url'])) {
			throw new \InvalidArgumentException('feed_url is a required param');
		}
		
		$reader = new \SimplePie();
		
		// set all the (supported) SimplePie settings
		foreach ($params as $key => $value) {
			switch ($key) {
				case 'feed_url':
					$reader->set_feed_url($value);
					break;
				case 'timeout':
					$reader->set_timeout((int) $value);
					break;
				case 'cache_location':
					
					$value = Paths::sanitize($value);
					if (empty($value)) {
						break;
					}
					if (!is_dir($value)) {
						// make sure cache dir is availble
						if (!mkdir($value, 0755, true)) {
							// unable to create dir
							break;
						}
					}
					
					$reader->set_cache_location($value);
					break;
				case 'enable_cache':
					$reader->enable_cache($value);
					break;
			}
		}
		
		// check for proxy settings
		$proxy_host = elgg_get_plugin_setting('proxy_host', 'rss_to_blog');
		$disable_ssl = (bool) elgg_get_plugin_setting('proxy_disable_ssl_verify', 'rss_to_blog');
		if (!empty($proxy_host) || $disable_ssl) {
			$reader->set_file_class(File::class);
		}
		
		return $reader;
	}
}
