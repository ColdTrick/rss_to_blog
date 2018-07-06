<?php

namespace ColdTrick\RssToBlog;

class PageMenu {
	
	/**
	 * Add an admin menu item to the page menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:page'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function registerAdmin(\Elgg\Hook $hook) {
		
		if (!elgg_is_admin_logged_in() || !elgg_in_context('admin')) {
			return;
		}
		
		$return = $hook->getValue();
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'configure_utilities:rss_to_blog',
			'text' => elgg_echo('admin:configure_utilities:rss_to_blog'),
			'href' => 'admin/configure_utilities/rss_to_blog',
			'section' => 'configure',
			'parent_name' => 'configure_utilities',
		]);
		
		return $return;
	}
}
