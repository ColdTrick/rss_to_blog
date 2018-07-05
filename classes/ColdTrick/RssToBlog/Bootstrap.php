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
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogEditLink');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\PageMenu::registerAdmin');
	}
}
