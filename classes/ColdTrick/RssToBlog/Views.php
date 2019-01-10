<?php

namespace ColdTrick\RssToBlog;

class Views {
	
	/**
	 * Add RSS info to blog imprint
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'object/elements/imprint/contents'
	 *
	 * @return void|array
	 */
	public static function addRssImprint(\Elgg\Hook $hook) {
		
		$vars = $hook->getValue();
		$entity = elgg_extract('entity', $vars);
		if (!$entity instanceof \ElggBlog) {
			return;
		}
		
		if (empty($entity->rss_copyright) && empty($entity->rss_source_title)) {
			// no copyright info available
			return;
		}
		
		$icon = 'copyright';
		$text = $entity->rss_copyright;
		if (empty($text)) {
			$icon = 'link';
			$text = $entity->rss_source_title;
		}
		
		$link = elgg_view('output/url', [
			'text' => $text,
			'href' => $entity->rss_permalink,
			'target' => '_blank',
		]);
		
		$imprints = elgg_extract('imprint', $vars, []);
		$imprints['rss_to_blog'] = [
			'icon_name' => $icon,
			'content' => $link,
		];
		
		$vars['imprint'] = $imprints;
		
		return $vars;
	}
	
	/**
	 * Make sure we want to view internal blogs for a group
	 *
	 * If group has no external blogs render default group listing
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'resources/blog/filtered/group'
	 *
	 * @return void|array
	 */
	public static function validateGroupInternalPage(\Elgg\Hook $hook) {
		$vars = $hook->getValue();
		
		$group_guid = (int) elgg_extract('guid', $vars, elgg_extract('group_guid', $vars)); // group_guid for BC
		$filter = elgg_extract('filter', $vars);
		$route = elgg_extract('_route', $vars);
		if (empty($group_guid) || $route !== 'collection:object:blog:group' || $filter !== 'internal') {
			return;
		}
		
		$group = get_entity($group_guid);
		if (!$group instanceof \ElggGroup) {
			return;
		}
		
		$count = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'blog',
			'container_guid' => $group->guid,
			'count' => true,
			'metadata_name' => 'rss_permalink',
		]);
		if (!empty($count)) {
			// need to split
			return;
		}
		
		// render default group listing
		$vars[\Elgg\ViewsService::OUTPUT_KEY] = elgg_view_resource('blog/group', $vars);
		
		return $vars;
	}
}
