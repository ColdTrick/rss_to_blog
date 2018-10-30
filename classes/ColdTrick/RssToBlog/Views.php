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
}
