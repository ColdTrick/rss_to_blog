<?php

namespace ColdTrick\RssToBlog;

use Elgg\DefaultPluginBootstrap;
use Symfony\Component\Routing\Route;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritDoc}
	 */
	public function init() {
		$this->rewriteRoutes();
	}
	
	/**
	 * Rewrite blog routes when internal and external blogs should be split
	 *
	 * @return void
	 */
	protected function rewriteRoutes(): void {
		
		if (!$this->plugin()->getSetting('split_blogs')) {
			return;
		}
		
		$route_collection = _elgg_services()->routeCollection;
		
		$internal = $route_collection->get('collection:object:blog:all:internal');
		
		// rewrite all (eg. /blog/all)
		$all = $route_collection->get('collection:object:blog:all');
		if ($all instanceof Route) {
			$all->setDefault('_resource', $internal->getDefault('_resource'));
			$all->setDefault('filter', $internal->getDefault('filter'));
			
			$route_collection->add('collection:object:blog:all', $all);
		}
		
		// rewrite default (eg. /blog)
		$default = $route_collection->get('default:object:blog');
		if ($default instanceof Route) {
			$default->setDefault('_resource', $internal->getDefault('_resource'));
			$default->setDefault('filter', $internal->getDefault('filter'));
			
			$route_collection->add('default:object:blog', $default);
		}
		
		// rewrite group (eg. /blog/group/{guid}
		$internal = $route_collection->get('collection:object:blog:group:internal');
		
		$group = $route_collection->get('collection:object:blog:group');
		if ($group instanceof Route) {
			$group->setDefault('_resource', $internal->getDefault('_resource'));
			$group->setDefault('filter', $internal->getDefault('filter'));
			$group->setDefault('subpage', $internal->getDefault('filter'));
			
			$route_collection->add('collection:object:blog:group', $group);
		}
	}
}
