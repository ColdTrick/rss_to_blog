<?php

namespace ColdTrick\RssToBlog;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		
		// plugin hooks
		$hooks = $this->elgg()->hooks;
		$hooks->registerHandler('cron', 'all', __NAMESPACE__ . '\Cron::importBlogs');
		$hooks->registerHandler('filter_tabs', 'blog', __NAMESPACE__ . '\FilterMenu::addTabs');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogEditLink');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogImportNow');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogRawData');
		$hooks->registerHandler('register', 'menu:filter:blog/group', __NAMESPACE__ . '\FilterMenu::addTabs');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\PageMenu::registerAdmin');
		$hooks->registerHandler('view_vars', 'object/elements/imprint/contents', __NAMESPACE__ . '\Views::addRssImprint');
	}
}
