<?php
/**
 * Create a new RSS-to-Blog entity
 *
 * Used in a lightbox
 */

use ColdTrick\RssToBlog\EditForm;

$title = elgg_echo('add:object:rss_to_blog');

$form = new EditForm();

$content = elgg_view_form('rss_to_blog/edit', [], $form());

echo elgg_view_module('info', $title, $content);
