<?php

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
}
