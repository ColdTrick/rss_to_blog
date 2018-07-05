<?php

use ColdTrick\RssToBlog\Bootstrap;
use Elgg\Router\Middleware\AjaxGatekeeper;
use Elgg\Router\Middleware\AdminGatekeeper;

define('RSS_TO_BLOG_ACCESS_GROUP', -20);

return [
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
	],
];
