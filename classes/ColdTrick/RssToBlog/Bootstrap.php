<?php

namespace ColdTrick\RssToBlog;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		
		// views
		elgg_extend_view('object/elements/imprint/contents', 'rss_to_blog/imprint/copyright');
		
		// plugin hooks
		$hooks = $this->elgg()->hooks;
		$hooks->registerHandler('cron', 'all', __NAMESPACE__ . '\Cron::importBlogs');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogEditLink');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogImportNow');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogRawData');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\PageMenu::registerAdmin');
	}
}
