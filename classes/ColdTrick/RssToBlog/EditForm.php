<?php

namespace ColdTrick\RssToBlog;

class EditForm {
	
	/**
	 * @var \RSSToBlog entity being edited
	 */
	protected $entity;
	
	public function __construct(\RSSToBlog $entity = null) {
		$this->entity = $entity;
	}
	
	public function __invoke() {
		
		$result = [
			'title' => '',
			'feed_url' => '',
			'target_owner_guid' => 0,
			'target_container_guid' => 0,
			'target_access_id' => ACCESS_LOGGED_IN,
			'target_tags' => [],
			'refresh_interval' => 'hourly',
		];
		
		// edit
		if ($this->entity instanceof \RSSToBlog) {
			foreach ($result as $key => $value) {
				$result[$key] = $this->entity->$key;
			}
			
			$result['entity'] = $this->entity;
		}
		
		// sticky form
		$sticky = elgg_get_sticky_values('rss_to_blog/edit');
		if (!empty($sticky)) {
			foreach ($sticky as $key => $value) {
				$result[$key] = $value;
			}
			
			elgg_clear_sticky_form('rss_to_blog/edit');
		}
		
		return $result;
	}
}
