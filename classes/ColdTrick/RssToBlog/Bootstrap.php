<?php

namespace ColdTrick\RssToBlog;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		
		$this->rewriteRoutes();
		
		// plugin hooks
		$hooks = $this->elgg()->hooks;
		$hooks->registerHandler('cron', 'all', __NAMESPACE__ . '\Cron::importBlogs');
		$hooks->registerHandler('filter_tabs', 'blog', __NAMESPACE__ . '\FilterMenu::addTabs');
		$hooks->registerHandler('register', 'menu:blog_archive', __NAMESPACE__ . '\BlogArchiveMenu::addArchive');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogEditLink');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogImportNow');
		$hooks->registerHandler('register', 'menu:entity', __NAMESPACE__ . '\EntityMenu::rssToBlogRawData');
		$hooks->registerHandler('register', 'menu:filter:blog/group', __NAMESPACE__ . '\FilterMenu::addTabs');
		$hooks->registerHandler('register', 'menu:page', __NAMESPACE__ . '\PageMenu::registerAdmin');
		$hooks->registerHandler('view_vars', 'object/elements/imprint/contents', __NAMESPACE__ . '\Views::addRssImprint');
		$hooks->registerHandler('view_vars', 'resources/blog/filtered/group', __NAMESPACE__ . '\Views::validateGroupInternalPage');
	}
	
	protected function rewriteRoutes() {
		
		if (!$this->plugin()->getSetting('split_blogs')) {
			return;
		}
		
		$route_collection = _elgg_services()->routeCollection;
		
		$internal = $route_collection->get('collection:object:blog:all:internal');
		
		// rewrite all (eg. /blog/all)
		$all = $route_collection->get('collection:object:blog:all');
		$all->setDefault('_resource', $internal->getDefault('_resource'));
		$all->setDefault('filter', $internal->getDefault('filter'));
		
		$route_collection->add('collection:object:blog:all', $all);
		
		// rewrite default (eg. /blog)
		$default = $route_collection->get('default:object:blog');
		$default->setDefault('_resource', $internal->getDefault('_resource'));
		$default->setDefault('filter', $internal->getDefault('filter'));
		
		$route_collection->add('default:object:blog', $default);
		
		// rewrite group (eg. /blog/group/{guid}
		$internal = $route_collection->get('collection:object:blog:group:internal');
		
		$group = $route_collection->get('collection:object:blog:group');
		$group->setDefault('_resource', $internal->getDefault('_resource'));
		$group->setDefault('filter', $internal->getDefault('filter'));
		$group->setDefault('subpage', $internal->getDefault('filter'));
		
		$route_collection->add('collection:object:blog:group', $group);
	}
}
