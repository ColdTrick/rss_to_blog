<?php
/**
 * Edit a RSS-to-Blog entity
 *
 * Used in a lightbox
 */

use ColdTrick\RssToBlog\EditForm;

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', RSSToBlog::SUBTYPE);

/* @var $entity RSSToBlog */
$entity = get_entity($guid);

$title = elgg_echo('edit:object:rss_to_blog', [$entity->getDisplayName()]);

$form = new EditForm($entity);

$content = elgg_view_form('rss_to_blog/edit', [], $form());

echo elgg_view_module('info', $title, $content);
