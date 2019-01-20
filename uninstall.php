<?php
/**
 * Uninstaller for WP Rest API Cache.
 *
 * @package wp-rest-api-cache
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Delete Our Options.
delete_option( 'rest_api_cache' );

// Delete All Cache.
$rest_api_cache = new API_CACHE_PRO();
$rest_api_cache->delete_all_cache();
