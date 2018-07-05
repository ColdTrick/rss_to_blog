<?php

namespace ColdTrick\RssToBlog;

class EntityMenu {
	
	/**
	 * Change the edit link to a lighbox
	 *
	 * @param \Elgg\Hook $hook 'register' 'menu:entity'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function rssToBlogEditLink(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \RSSToBlog || !$entity->canEdit()) {
			return;
		}
		
		/* @var \Elgg\Menu\MenuItems */
		$return = $hook->getValue();
		
		$edit = $return->get('edit');
		if (!$edit instanceof \ElggMenuItem) {
			return;
		}
		
		$edit->addLinkClass('elgg-lightbox');
		
		$edit->{'data-colorbox-opts'} = json_encode([
			'width' => '1024px',
		]);
		
		$return->add($edit);
		
		return $return;
	}
	
	/**
	 * Add sync now link
	 *
	 * @param \Elgg\Hook $hook 'register' 'menu:entity'
	 *
	 * @return void|\ElggMenuItem[]
	 */
	public static function rssToBlogImportNow(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \RSSToBlog) {
			return;
		}
		
		/* @var \Elgg\Menu\MenuItems */
		$return = $hook->getValue();
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'import',
			'icon' => 'play',
			'text' => elgg_echo('import'),
			'href' => elgg_generate_action_url('rss_to_blog/import', [
				'guid' => $entity->guid,
			]),
		]);
		
		return $return;
	}
}
