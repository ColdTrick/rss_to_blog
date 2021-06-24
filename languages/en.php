<?php

return [
	
	'item:object:rss_to_blog' => "RSS to Blog config",
	'collection:object:rss_to_blog' => "RSS to Blog configs",
	
	'collection:object:blog:all:internal' => "Internal blogs",
	'collection:object:blog:all:external' => "External blogs",
	'collection:object:blog:group:internal' => "Internal group blogs",
	'collection:object:blog:group:external' => "External group blogs",
	
	'admin:configure_utilities:rss_to_blog' => "RSS to Blog",
	'add:object:rss_to_blog' => "New RSS to Blog",
	'edit:object:rss_to_blog' => "Edit RSS to Blog: %s",
	'inspect:object:rss_to_blog' => "Inspect RSS to Blog: %s",
	
	'rss_to_blog:last_refresh' => "Last sync",
	'rss_to_blog:inspect' => "Inspect RSS-feed",
	'rss_to_blog:inspect:unable_to_parse' => "Unable to display RSS-feed output (may not be a problem during import)",
	
	// menu
	'rss_to_blog:menu:filter:internal' => "Internal",
	'rss_to_blog:menu:filter:external' => "External",
	
	// settings
	'rss_to_blog:settings:split_blogs' => "Add tabs to blog listings to only show internal / external blogs",
	'rss_to_blog:settings:split_blogs:help' => "If enabled two tabs will be added to the blog listing pages to be able to only show internal or external blogs.",
	
	// edit form
	'rss_to_blog:edit:feed_url' => "RSS-feed URL",
	'rss_to_blog:edit:target_owner_guid' => "Blog owner",
	'rss_to_blog:edit:target_owner_guid:help' => "Search for a user who will own the imported blogs",
	'rss_to_blog:edit:target_container_guid' => "Blog container",
	'rss_to_blog:edit:target_container_guid:help' => "Leave empty to use the blog owner, or find a group and select it from the list",
	'rss_to_blog:edit:target_tags' => "Additional tags for the blog",
	'rss_to_blog:edit:target_access_id' => "Blog access level",
	'rss_to_blog:edit:target_access_id:group' => "Group members (if group selected)",
	'rss_to_blog:edit:target_comments_on' => "Allow comments on the blog",
	'rss_to_blog:edit:refresh_interval' => "Refresh interval",
	'rss_to_blog:edit:refresh_interval:help' => "How often to check the RSS-feed for new content",
	
	// actions
	'rss_to_blog:action:import:error' => "An error occured whil importing %s",
	'rss_to_blog:action:import:success' => "%s resulted in %d new blogs",
];
