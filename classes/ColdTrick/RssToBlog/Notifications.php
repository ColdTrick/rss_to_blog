<?php

namespace ColdTrick\RssToBlog;

class Notifications {
	
	/**
	 * Prevent blogs from entering the notification queue
	 *
	 * @param \Elgg\Hook $hook 'enqueue', 'notification'
	 *
	 * @return void|false
	 */
	public static function preventBlogEnqueue(\Elgg\Hook $hook) {
		
		if (!$hook->getValue()) {
			// already prevented
			return;
		}
		
		$object = $hook->getParam('object');
		if (!$object instanceof \ElggBlog) {
			return;
		}
		
		return false;
	}
	
	/**
	 * Prevent default notification sending for blogs
	 *
	 * @param \Elgg\Hook $hook 'send:before', 'notifications'
	 *
	 * @return void|false
	 */
	public static function preventBlogSendBefore(\Elgg\Hook $hook) {
		
		if (!$hook->getValue()) {
			// already prevented
			return;
		}
		
		$object = $hook->getParam('object');
		if (!$object instanceof \ElggBlog) {
			return;
		}
		
		return false;
	}
}
