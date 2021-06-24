<?php

namespace ColdTrick\RssToBlog\Menus;

use Elgg\Menu\MenuItems;

class Filter {
	
	/**
	 * Add menu items to the blog tabs
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:filter:filter'
	 *
	 * @return void|MenuItems
	 */
	public static function addTabs(\Elgg\Hook $hook) {
		
		if (!elgg_in_context('blog') || !elgg_get_plugin_setting('split_blogs', 'rss_to_blog')) {
			return;
		}
		
		/* @var $result MenuItems */
		$result = $hook->getValue();
		$page_owner = elgg_get_page_owner_entity();
		$selected = $hook->getParam('filter_value', $hook->getParam('selected'));
		
		$section = 'all';
		$route_params = [];
		if ($page_owner instanceof \ElggGroup) {
			// check if the group has external blogs
			$count = elgg_get_entities([
				'type' => 'object',
				'subtype' => 'blog',
				'container_guid' => $page_owner->guid,
				'count' => true,
				'metadata_name' => 'rss_permalink',
			]);
			if (empty($count)) {
				// no need to split
				return;
			}
			
			$section = 'group';
			$route_params['guid'] = $page_owner->guid;
		}
		
		// remove 'all' tab
		$result->remove('all');
		$result->remove('collection:object:blog:group'); // added by blog_tools
		
		$result[] = \ElggMenuItem::factory([
			'name' => 'internal',
			'text' => elgg_echo('rss_to_blog:menu:filter:internal'),
			'href' => elgg_generate_url("collection:object:blog:{$section}:internal", $route_params),
			'priority' => 210,
			'selected' => $selected === 'internal',
		]);
		$result[] = \ElggMenuItem::factory([
			'name' => 'external',
			'text' => elgg_echo('rss_to_blog:menu:filter:external'),
			'href' => elgg_generate_url("collection:object:blog:{$section}:external", $route_params),
			'priority' => 220,
			'selected' => $selected === 'external',
		]);
		
		return $result;
	}
}
