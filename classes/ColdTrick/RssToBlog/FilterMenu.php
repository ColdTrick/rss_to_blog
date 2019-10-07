<?php

namespace ColdTrick\RssToBlog;

use Elgg\Menu\MenuItems;

class FilterMenu {
	
	/**
	 * Add menu items to the blog tabs
	 *
	 * @param \Elgg\Hook $hook 'filter_tabs', 'blog'
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
			
			// blog tools adds potential all link
			$result->remove('collection:object:blog:group');
		} elseif(is_array($result)) {
			// @var $menu_item \ElggMenuItem */
			foreach ($result as $index => $menu_item) {
				if ($menu_item->getName() !== 'all') {
					continue;
				}
				
				unset($result[$index]);
				break;
			}
		}
		
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
