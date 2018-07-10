<?php

use ColdTrick\RssToBlog\RSSReader;
use Elgg\Database\QueryBuilder;
use Elgg\Export\AccessCollection;
use Elgg\Values;

/**
 * @property string   feed_url              url to the RSS feed
 * @property int      target_owner_guid     the owner guid of the blog to be created
 * @property int      target_container_guid the container guid of the blog to be created
 * @property int      target_access_id      the access_id of the blog to be created
 * @property string[] target_tags           tags for the blog to be created
 * @property string   refresh_interval      how ofter to check for new content (cron interval)
 *
 * @property-read int last_refresh          when was the last refresh from the RSS-feed
 */
class RSSToBlog extends ElggObject {
	
	/**
	 * @var string subtype of this entity
	 */
	const SUBTYPE = 'rss_to_blog';
	
	/**
	 * @var string subtype of this entity
	 */
	const IMPORTED_RELATIONSHIP = 'imported_from';
	
	/**
	 * @var int a valid container guid for creating blogs
	 */
	protected $validated_container_guid;
	
	/**
	 * @var int a valid access_id for creating blogs
	 */
	protected $validated_access_id;
	
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
	
	public function getDisplayName() {
		$title = parent::getDisplayName();
		if (!empty($title)) {
			return $title;
		}
		return $this->feed_url;
	}
	
	/**
	 * Import new RSS-feed items into blogs
	 *
	 * @return false|int
	 */
	public function import() {
		
		$user = get_user($this->target_owner_guid);
		if (!$user instanceof ElggUser) {
			return false;
		}
		
		try {
			$reader = RSSReader::factory([
				'feed_url' => $this->feed_url,
			]);
		} catch (InvalidArgumentException $e) {
			elgg_log($e->getMessage(), 'WARNING');
			return false;
		}
		
		// log last refresh time
		$this->last_refresh = time();
		
		// init feed
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
		if ($backup_user instanceof ElggUser) {
			$session->setLoggedInUser($backup_user);
		} else {
			$session->removeLoggedInUser();
		}
		
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
		
		$owner_guid = $this->target_owner_guid;
		$container_guid = $this->getTargetContainerGuid();
		
		$already_imported = elgg_call(ELGG_IGNORE_ACCESS, function() use ($feed_permalinks, $owner_guid, $container_guid) {
			return elgg_get_entities([
				'type' => 'object',
				'subtype' => 'blog',
				'owner_guid' => $owner_guid,
				'container_guid' => $container_guid,
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
					return [
						'guid' => (int) $row->guid,
						'permalink' => $row->permalink,
					];
				}
			]);
		});
		
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
		$blog->access_id = $this->getTargetAccessId();
		
		$created = $item->get_date('U'); // unix time
		if (!empty($created)) {
			$blog->time_created = Values::normalizeTimestamp($created);
		}
		
		$blog->title = filter_tags($item->get_title());
		$blog->excerpt = elgg_get_excerpt($item->get_description(true));
		$blog->description = filter_tags($item->get_content());
		
		$blog->status = 'published';
		
		// tags
		$tags = [];
		$categories = $item->get_categories();
		if (!empty($categories)) {
			/* @var $cat SimplePie_Category */
			foreach ($categories as $cat) {
				$tags[] = filter_tags($cat->get_label());
			}
		}
		if ($this->target_tags) {
			$tags = array_merge($tags, (array) $this->target_tags);
		}
		$blog->tags = $tags;
		
		// rss specific values
		// permalink
		$blog->rss_permalink = filter_tags($item->get_permalink());
		
		// copyrights
		$copyright = $item->get_copyright();
		if (empty($copyright)) {
			$copyright = $item->get_feed()->get_copyright();
		}
		if (!empty($copyright)) {
			$blog->rss_copyright = filter_tags($copyright);
		}
		
		// source title
		$source_title = false;
		
		$source = $item->get_source();
		if ($source instanceof \SimplePie_Source) {
			$source_title = $source->get_title();
		}
		
		if (empty($source_title)) {
			$source_title = $item->get_feed()->get_title();
		}
		
		if (!empty($source_title)) {
			$blog->rss_source_title = $source_title;
		}
		
		$authors = [];
		/* @var $author SimplePie_Author */
		foreach ($item->get_authors() as $author) {
			$authors[] = filter_tags($author->get_name());
		}
		$blog->rss_authors = $authors;
		
		// save blog
		$result = (bool) $blog->save();
		if (!$result) {
			return $result;
		}
		
		// icon, only after save, need a guid for icon location
		$enclosures = $item->get_enclosures();
		if (!empty($enclosures)) {
			$images = [];
			/* @var $enclosure SimplePie_Enclosure */
			foreach ($enclosures as $enclosure) {
				if (stripos($enclosure->get_type(), 'image') === false) {
					continue;
				}
				
				$images[] = [
					'length' => (int) $enclosure->get_length(),
					'url' => $enclosure->get_link(),
				];
			}
			
			$max_length = -1;
			$image_url = false;
			foreach ($images as $image) {
				if ($image['length'] < $max_length) {
					continue;
				}
				$max_length = $image['length'];
				$image_url = $image['url'];
			}
			
			if (!empty($image_url)) {
				$image_content = @file_get_contents($image_url);
				if (!empty($image_content)) {
					$temp = new ElggTempFile();
					$temp->open('write');
					$temp->write($image_content);
					$temp->close();
					
					$blog->saveIconFromElggFile($temp);
				}
			}
		}
		
		// link items
		$blog->addRelationship($this->guid, self::IMPORTED_RELATIONSHIP);
		
		// let others know about the import
		elgg_trigger_event('import', 'rss_to_blog', [
			'rss_item' => $item,
			'entity' => $blog,
		]);
		
		return $result;
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
			$container_guid = $this->target_container_guid;
			$entity = elgg_call(ELGG_IGNORE_ACCESS, function () use ($container_guid) {
				return get_entity($container_guid);
			});
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
	
	/**
	 * Get a validated access_id for creating blogs
	 *
	 * @return int
	 */
	protected function getTargetAccessId() {
		
		if (isset($this->validated_access_id)) {
			return $this->validated_access_id;
		}
		
		$access_id = $this->target_access_id;
		if ($access_id === RSS_TO_BLOG_ACCESS_GROUP) {
			$container_guid = $this->getTargetContainerGuid();
			$access_id = elgg_call(ELGG_IGNORE_ACCESS, function () use ($container_guid) {
				$container = get_entity($container_guid);
				if ($container instanceof ElggGroup) {
					$acl = $container->getOwnedAccessCollection('group_acl');
					if ($acl instanceof AccessCollection) {
						return $acl->id;
					}
				}
				return ACCESS_LOGGED_IN;
			});
		}
		
		$this->validated_access_id = $access_id;
		
		return $this->validated_access_id;
	}
}
