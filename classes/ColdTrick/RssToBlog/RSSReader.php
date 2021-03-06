<?php

namespace ColdTrick\RssToBlog;

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
		$curl_options = [];
		
		$proxy_config = (array) elgg_get_config('proxy', []);
		$proxy_host = elgg_extract('host', $proxy_config);
		if (!empty($proxy_host)) {
			$curl_options[CURLOPT_PROXY] = $proxy_host;
		}
		$proxy_port = (int) elgg_extract('port', $proxy_config);
		if (!empty($proxy_port)) {
			$curl_options[CURLOPT_PROXYPORT] = $proxy_port;
		}
		$verify_ssl = (bool) elgg_extract('verify_ssl', $proxy_config, true);
		if (!$verify_ssl) {
			$curl_options[CURLOPT_SSL_VERIFYPEER] = false;
		}
		
		$username = elgg_extract('username', $proxy_config);
		$password = elgg_extract('password', $proxy_config);
		if (!empty($username) && !empty($password)) {
			$curl_options[CURLOPT_PROXYUSERPWD] = "{$username}:{$password}";
		}
		
		// set additional curl options
		$reader->set_curl_options($curl_options);
		
		return $reader;
	}
}
