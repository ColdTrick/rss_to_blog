<?php

use ColdTrick\RssToBlog\RSSReader;

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', RSSToBlog::SUBTYPE);

/* @var $entity RSSToBlog */
$entity = get_entity($guid);

$title = elgg_echo('inspect:object:rss_to_blog', [$entity->getDisplayName()]);

try {
	$reader = RSSReader::factory([
		'feed_url' => $entity->feed_url,
		'enable_cache' => false,
	]);
	
	$reader->init();
	
	$content = $reader->get_raw_data();
	if ($content === false) {
		// see if there was an error
		$content = elgg_view_message('error', $reader->error());
	} elseif (!empty($content)) {
		$content = htmlentities($content, ENT_QUOTES & ENT_XML1, 'UTF-8');
		if (!empty($content)) {
			$content = elgg_format_element('pre', ['lang' => 'xml'], $content, ['encode_text' => false]);
		}
	}
	
	if (empty($content)) {
		$content  = elgg_view_message('warning', elgg_echo('rss_to_blog:inspect:unable_to_parse'));
	}
	
} catch (InvalidArgumentException $e) {
	$content = elgg_view_message('error', $e->getMessage());
}

echo elgg_view_module('info', $title, $content);
