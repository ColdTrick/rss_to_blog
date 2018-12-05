<?php

namespace ColdTrick\RssToBlog;

use Elgg\Database\QueryBuilder;
use Elgg\Menu\MenuItems;

class BlogArchivemenu {
	
	/**
	 * Register menu items to the blog archive menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:blog_archive'
	 *
	 * @return void|MenuItems
	 */
	public static function addArchive(\Elgg\Hook $hook) {
		
		$entity = $hook->getParam('entity', elgg_get_page_owner_entity());
		$page = $hook->getParam('page');
		if (!in_array($page, ['internal', 'external'])) {
			return;
		}
		
		$options = [
			'type' => 'object',
			'subtype' => 'blog',
		];
		
		$route_name = "collection:object:blog:all:{$page}";
		$route_params = [];
		
		if ($entity instanceof \ElggGroup) {
			$options['container_guid'] = $entity->guid;
			
			$route_name = "collection:object:blog:group:{$page}";
			$route_params['guid'] = $entity->guid;
		}
		
		switch ($page) {
			case 'internal':
				$options['wheres'][] = function(QueryBuilder $qb, $main_alias) {
					$external = $qb->subquery('metadata');
					$external->select('entity_guid')
						->where($qb->compare('name', '=', 'rss_permalink', ELGG_VALUE_STRING));
					
					return $qb->compare("{$main_alias}.guid", 'NOT IN', $external->getSQL());
				};
				break;
			case 'external':
				$options['metadata_names'][] = 'rss_permalink';
				break;
		}
		
		$dates = elgg_get_entity_dates($options);
		if (empty($dates)) {
			return;
		}
		
		$generate_url = function($lower = null, $upper = null) use ($route_name, $route_params) {
			$route_params['lower'] = $lower;
			$route_params['upper'] = $upper;
			
			return elgg_generate_url($route_name, $route_params);
		};
		
		$return = $hook->getValue();
		$years = [];
		
		$dates = array_reverse($dates);
		foreach ($dates as $date) {
			$timestamplow = mktime(0, 0, 0, substr($date, 4, 2), 1, substr($date, 0, 4));
			$timestamphigh = mktime(0, 0, 0, ((int) substr($date, 4, 2)) + 1, 1, substr($date, 0, 4));
	
			$year = substr($date, 0, 4);
			if (!in_array($year, $years)) {
				$return[] = \ElggMenuItem::factory([
					'name' => $year,
					'text' => $year,
					'href' => false,
					'child_menu' => [
						'display' => 'toggle',
					]
				]);
			}
	
			$month = trim(elgg_echo('date:month:' . substr($date, 4, 2), ['']));
	
			$return[] = \ElggMenuItem::factory([
				'name' => $date,
				'text' => $month,
				'href' => $generate_url($timestamplow, $timestamphigh),
				'parent_name' => $year,
			]);
		}
		
		return $return;
	}
}
