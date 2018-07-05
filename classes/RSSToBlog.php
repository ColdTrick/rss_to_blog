<?php

use ColdTrick\RssToBlog\RSSReader;
use Elgg\Database\QueryBuilder;

/**
 * @property string   feed_url              url to the RSS feed
 * @property int      target_owner_guid     the owner guid of the blog to be created
 * @property int      target_container_guid the container guid of the blog to be created
 * @property int      target_access_id      the access_id of the blog to be created
 * @property string[] target_tags           tags for the blog to be created
 * @property string   refresh_interval      how ofter to check for new content (cron interval)
 */
class RSSToBlog extends ElggObject {
	
	/**
	 * @var string subtype of this entity
	 */
	const SUBTYPE = 'rss_to_blog';
	
	/**
	 * @var int a valid container guid for creating blogs
	 */
	protected $validated_container_guid;
	
	/**
	 * {@inheritDoc}
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();
		
		$site = elgg_get_site_entity();
		
		$this->attributes['subtype'] = self::SUBTYPE;
		$this->attributes['access_id'] = ACCESS_LOGGED_IN;
		$this->attributes['owner_guid'] = $site->guid;
		$this->attributes['container_guid'] = $site->guid;
	}
	
	/**
	 * Import new RSS-feed items into blogs
	 *
	 * @return false|int
	 */
	public function import() {
		
		try {
			$reader = RSSReader::factory([
				'feed_url' => $this->feed_url,
			]);
		} catch (InvalidArgumentException $e) {
			elgg_log($e->getMessage(), 'WARNING');
			return false;
		}
		
		$reader->init();
		
		$items = $reader->get_items();
		if (empty($items)) {
			return 0;
		}
		
		$permalinks = [];
		
		/* @var $item SimplePie_Item */
		foreach ($items as $index => $item) {
			$permalinks[$index] = $item->get_permalink();
		}
		$permalinks = array_filter($permalinks);
		
		$permalinks = $this->findNewPermalinks($permalinks);
		if (empty($permalinks)) {
			// everythink already imported
			return 0;
		}
		
		// fake login
		$user = get_user($this->target_owner_guid);
		if (!$user instanceof ElggUser) {
			return false;
		}
		$session = elgg_get_session();
		$backup_user = $session->getLoggedInUser();
		$session->setLoggedInUser($user);
		
		// import items
		$count = 0;
		foreach ($permalinks as $index => $permalink) {
			$item = $reader->get_item($index);
			if (empty($item)) {
				continue;
			}
			
			if ($this->createBlog($item)) {
				$count++;
			}
		}
		
		// restore login
		$session->setLoggedInUser($backup_user);
		
		return $count;
	}
	
	/**
	 * Filter out the permalink which have already been imported
	 *
	 * @param string[] $feed_permalinks permalinks from RSS-feed
	 *
	 * @return string[]
	 */
	protected function findNewPermalinks(array $feed_permalinks) {
		
		$already_imported = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'blog',
			'owner_guid' => $this->target_owner_guid,
			'container_guid' => $this->target_container_guid ?: $this->target_owner_guid,
			'metadata_name_value_pairs' => [
				[
					'name' => 'rss_permalink',
					'value' => $feed_permalinks,
				],
			],
			'selects' => [
				function (QueryBuilder $qb, $main_alias) {
					$join_alias = $qb->joinMetadataTable($main_alias, 'guid', 'rss_permalink', 'inner', 'feed');
					return "{$join_alias}.value AS permalink";
				},
			],
			'limit' => false,
			'callback' => function ($row) {
				return [(int) $row->guid, $row->permalink];
			}
		]);
		
		if (empty($already_imported)) {
			return $feed_permalinks;
		}
		
		foreach ($already_imported as $row) {
			$key = array_search($row['permalink'], $feed_permalinks);
			if ($key === false) {
				continue;
			}
			
			unset($feed_permalinks[$key]);
		}
		
		return $feed_permalinks;
	}
	
	/**
	 * Create a blog based on the RSS-feed item
	 *
	 * @param SimplePie_Item $item RSS-feed item
	 *
	 * @return bool
	 */
	protected function createBlog(SimplePie_Item $item) {
		
		$blog = new ElggBlog();
		$blog->owner_guid = $this->target_owner_guid;
		$blog->container_guid = $this->getTargetContainerGuid();
		$blog->access_id = ACCESS_PUBLIC;
		
		$blog->title = $item->get_title();
		$blog->excerpt = $item->get_description(true);
		$blog->description = $item->get_content();
		
		$blog->rss_permalink = $item->get_permalink();
		
		return (bool) $blog->save();
	}
	
	/**
	 * Get a validated container guid for creating blogs in
	 *
	 * @return int
	 */
	protected function getTargetContainerGuid() {
		
		if (isset($this->validated_container_guid)) {
			return $this->validated_container_guid;
		}
		
		if (!empty($this->target_container_guid)) {
			$entity = get_entity($this->target_container_guid);
			if ($entity instanceof ElggGroup) {
				$this->validated_container_guid = $entity->guid;
			}
		}
		
		if (!isset($this->validated_container_guid)) {
			// not set, so owner
			$this->validated_container_guid = $this->target_owner_guid;
		}
		
		return $this->validated_container_guid;
	}
}
