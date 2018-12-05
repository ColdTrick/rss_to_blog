<?php
/**
 * List internal blogs
 *
 * @uses $vars['entity'] (optional) limit to group
 */

use Elgg\Database\QueryBuilder;

$options = elgg_extract('options', $vars, []);

$entity = elgg_extract('entity', $vars);

if ($entity instanceof ElggGroup) {
	$options['container_guid'] = $entity->guid;
	$options['preload_containers'] = false;
}

$options['wheres'][] = function(QueryBuilder $qb, $main_alias) {
	$external = $qb->subquery('metadata');
	$external->select('entity_guid')
		->where($qb->compare('name', '=', 'rss_permalink', ELGG_VALUE_STRING));
	
	return $qb->compare("{$main_alias}.guid", 'NOT IN', $external->getSQL());
};

$vars['options'] = $options;

echo elgg_view('blog/listing/all', $vars);
