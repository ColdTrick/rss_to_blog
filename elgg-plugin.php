<?php

use ColdTrick\RssToBlog\Bootstrap;
use Elgg\Router\Middleware\AjaxGatekeeper;
use Elgg\Router\Middleware\AdminGatekeeper;

if (!defined('RSS_TO_BLOG_ACCESS_GROUP')) {
	define('RSS_TO_BLOG_ACCESS_GROUP', -20);
}

return [
	'plugin' => [
		'version' => '2.0.1',
		'dependencies' => [
			'blog' => [
				'position' => 'after',
			],
		],
	],
	'bootstrap' => Bootstrap::class,
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'rss_to_blog',
			'class' => RSSToBlog::class,
		],
	],
	'actions' => [
		'rss_to_blog/edit' => [
			'access' => 'admin',
		],
		'rss_to_blog/import' => [
			'access' => 'admin',
		],
	],
	'hooks' => [
		'cron' => [
			'all' => [
				'\ColdTrick\RssToBlog\Cron::importBlogs' => [],
			],
		],
		'register' => [
			'menu:blog_archive' => [
				'\ColdTrick\RssToBlog\Menus\BlogArchive::addArchive' => [],
			],
			'menu:entity' => [
				'\ColdTrick\RssToBlog\Menus\Entity::rssToBlogEditLink' => [],
				'\ColdTrick\RssToBlog\Menus\Entity::rssToBlogImportNow' => [],
				'\ColdTrick\RssToBlog\Menus\Entity::rssToBlogRawData' => [],
			],
			'menu:filter:filter' => [
				'\ColdTrick\RssToBlog\Menus\Filter::addTabs' => [],
			],
			'menu:filter:blog/group' => [
				'\ColdTrick\RssToBlog\Menus\Filter::addTabs' => [],
			],
			'menu:page' => [
				'\ColdTrick\RssToBlog\Menus\Page::registerAdmin' => [],
			],
		],
		'view_vars' => [
			'object/elements/imprint/contents' => [
				'\ColdTrick\RssToBlog\Views::addRssImprint' => [],
			],
			'resources/blog/filtered/group' => [
				'\ColdTrick\RssToBlog\Views::validateGroupInternalPage' => [],
			],
		],
	],
	'routes' => [
		'add:object:rss_to_blog' => [
			'path' => 'rss_to_blog/add/{guid?}',
			'resource' => 'rss_to_blog/add',
			'middleware' => [
				AjaxGatekeeper::class,
				AdminGatekeeper::class,
			],
		],
		'edit:object:rss_to_blog' => [
			'path' => 'rss_to_blog/edit/{guid}',
			'resource' => 'rss_to_blog/edit',
			'middleware' => [
				AjaxGatekeeper::class,
				AdminGatekeeper::class,
			],
		],
		'inspect:object:rss_to_blog' => [
			'path' => 'rss_to_blog/inspect/{guid}',
			'resource' => 'rss_to_blog/inspect',
			'middleware' => [
				AjaxGatekeeper::class,
				AdminGatekeeper::class,
			],
		],
		'collection:object:blog:all:internal' => [
			'path' => 'blog/internal/{lower?}/{upper?}',
			'resource' => 'blog/filtered/all',
			'defaults' => [
				'filter' => 'internal',
			],
			'requirements' => [
				'lower' => '\d+',
				'upper' => '\d+',
			],
		],
		'collection:object:blog:all:external' => [
			'path' => 'blog/external/{lower?}/{upper?}',
			'resource' => 'blog/filtered/all',
			'defaults' => [
				'filter' => 'external',
			],
			'requirements' => [
				'lower' => '\d+',
				'upper' => '\d+',
			],
		],
		'collection:object:blog:group:internal' => [
			'path' => 'blog/group/{guid}/internal/{lower?}/{upper?}',
			'resource' => 'blog/filtered/group',
			'defaults' => [
				'filter' => 'internal',
			],
			'requirements' => [
				'lower' => '\d+',
				'upper' => '\d+',
			],
		],
		'collection:object:blog:group:external' => [
			'path' => 'blog/group/{guid}/external/{lower?}/{upper?}',
			'resource' => 'blog/filtered/group',
			'defaults' => [
				'filter' => 'external',
			],
			'requirements' => [
				'lower' => '\d+',
				'upper' => '\d+',
			],
		],
	],
	'settings' => [
		'split_blogs' => 0,
	],
];
