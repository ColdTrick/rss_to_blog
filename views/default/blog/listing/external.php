<?php
/**
 * List external blogs
 *
 * @uses $vars['entity'] (optional) limit to group
 */

$options = elgg_extract('options', $vars, []);

$entity = elgg_extract('entity', $vars);

if ($entity instanceof ElggGroup) {
	$options['container_guid'] = $entity->guid;
	$options['preload_containers'] = false;
}

$options['metadata_names'][] = 'rss_permalink';

$vars['options'] = $options;

echo elgg_view('blog/listing/all', $vars);
