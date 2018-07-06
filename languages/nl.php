<?php

return [
	
	'admin:configure_utilities:rss_to_blog' => "RSS naar Blog",
	'add:object:rss_to_blog' => "Nieuwe RSS naar Blog",
	'edit:object:rss_to_blog' => "Bewerk RSS naar Blog: %s",
	
	'rss_to_blog:last_refresh' => "Laatste synchronisatie",
	
	// settings
	'rss_to_blog:settings:proxy:title' => "RSS-feed lezer instellingen",
	'rss_to_blog:settings:proxy:host' => "Proxy host",
	'rss_to_blog:settings:proxy:port' => "Proxy poortnummer",
	'rss_to_blog:settings:proxy:disable_ssl_verify' => "Schakel SSL verificatie uit",
	'rss_to_blog:settings:proxy:disable_ssl_verify:help' => "Indien ingeschakeld zal er geen SSL verificatie plaatsvinden tijdens het inlezen van de RSS-feed",
	
	// edit form
	'rss_to_blog:edit:feed_url' => "RSS-feed URL",
	'rss_to_blog:edit:target_owner_guid' => "Blog eigenaar",
	'rss_to_blog:edit:target_owner_guid:help' => "Zoek naar een gebruiker welke de eigenaar zal worden van de geïmporteerde blogs",
	'rss_to_blog:edit:target_container_guid' => "Blog container",
	'rss_to_blog:edit:target_container_guid:help' => "Laat leeg om de eigenaar te gebruiker, of zoek naar een groep en selecteer deze uit de lijst",
	'rss_to_blog:edit:target_tags' => "Extra tags voor de blog",
	'rss_to_blog:edit:target_access_id' => "Blog toegangsniveau",
	'rss_to_blog:edit:target_access_id:group' => "Groepsleden (indien er een groep is geselecteerd)",
	'rss_to_blog:edit:refresh_interval' => "Verversingsinterval",
	'rss_to_blog:edit:refresh_interval:help' => "Hoe vaak moet de RSS-feed worden gecontroleerd op nieuwe content",
	
	// actions
	'rss_to_blog:action:import:error' => "Er is een fout opgetreden tijdens het importeren van %s",
	'rss_to_blog:action:import:success' => "%s heeft geleid tot %d nieuwe blogs",
];
