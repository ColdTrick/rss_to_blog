<?php

namespace ColdTrick\RssToBlog;

use ColdTrick\TagTools\MenuItems;

class FilterMenu {
	
	/**
	 * Add menu items to the blog tabs
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:filter:blog'
	 *
	 * @return void|MenuItems
	 */
	public static function addTabs(\Elgg\Hook $hook) {
		
		if (!elgg_get_plugin_setting('split_blogs', 'rss_to_blog')) {
			return;
		}
		
		/* @var $result MenuItems */
		$result = $hook->getValue();
		$page_owner = elgg_get_page_owner_entity();
		
		$section = 'all';
		$route_params = [];
		if ($page_owner instanceof \ElggGroup) {
			$section = 'group';
			$route_params['guid'] = $page_owner->guid;
			
			// blog tools adds potential all link
			if (!$result->has('collection:object:blog:group')) {
				// add link to all blogs
				$result[] = \ElggMenuItem::factory([
					'name' => 'collection:object:blog:group',
					'text' => elgg_echo('collection:object:blog'),
					'href' => elgg_generate_url('collection:object:blog:group', [
						'guid' => $page_owner->guid,
					]),
					'priority' => 200,
				]);
			}
		}
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'internal',
			'text' => elgg_echo('rss_to_blog:menu:filter:internal'),
			'href' => elgg_generate_url("collection:object:blog:{$section}:internal", $route_params),
			'priority' => 210,
		]);
		$result[] = \ElggMenuItem::factory([
			'name' => 'external',
			'text' => elgg_echo('rss_to_blog:menu:filter:external'),
			'href' => elgg_generate_url("collection:object:blog:{$section}:external", $route_params),
			'priority' => 220,
		]);
		
		return $result;
	}
}
