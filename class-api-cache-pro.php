<?php
/**
 * Plugin Name: API Cache Pro
 * Description: A simple plugin to cache WP Rest API Requests.
 * Author: Brandon Hubbard
 * Author URI: http://github.com/bhubbard
 * Version: 0.0.1
 * Plugin URI: https://github.com/bhubbard/blah
 * License: GPL3+
 *
 * @package wp-rest-api-cache
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'API_CACHE_PRO' ) ) {

	/**
	 * API_CACHE_PRO class.
	 */
	class API_CACHE_PRO {

		/**
		 * Constructor.
		 *
		 * @access public
		 */
		public function __construct() {

			// Include Our Customizer Settings.
			include_once 'class-api-cache-pro-customizer.php';

			$cache_options = get_option( 'rest_api_cache' ) ?? array();

			$disable_cache = $cache_options['disable'] ?? false;

			if ( ! is_admin() && false === $disable_cache ) {

				add_filter( 'rest_pre_dispatch', array( $this, 'cache_requests_headers' ), 10, 3 );

				add_filter( 'rest_request_after_callbacks', array( $this, 'cache_requests' ), 10, 3 );

			}

			// Delete All Cache on Deactivation.
			register_deactivation_hook( __FILE__, array( $this, 'delete_all_cache' ) );

		}

		/**
		 * Get Timeout.
		 *
		 * @access public
		 */
		public function get_timeout() {

			$cache_options = get_option( 'rest_api_cache' ) ?? array();

			$default_timout = $cache_options['default_timeout'] ?? 300;

			return $default_timout;

		}

		/**
		 * Cache Key.
		 *
		 * @access public
		 * @param mixed $request_uri Request URI.
		 */
		public function cache_key( $request_uri ) {

			if ( ! empty( $request_uri ) ) {
				$cache_key = apply_filters( 'rest_api_cache_key', 'rest_api_cache_' . md5( $request_uri ) );
			} else {
				return new WP_Error( 'missing_request_uri', __( 'Please provide the Request URI.', 'wp-rest-api-cache' ) );
			}

			return $cache_key;
		}

		/**
		 * Cache our Request.
		 *
		 * @access public
		 * @param mixed $response Response.
		 * @param mixed $handler Handler.
		 * @param mixed $request Request.
		 */
		public function cache_requests( $response, $handler, $request ) {

			// Get Request URI.
			$request_uri = esc_url( $_SERVER['REQUEST_URI'] ) ?? null;

			// Timeouts.
			$timeout = apply_filters( 'rest_api_cache_timeout', $this->get_timeout() );

			if ( null !== $request_uri ) {
				$cache_key = $this->cache_key( $request_uri ) ?? null;
			} else {
				$cache_key = null;
				return $response;
			}

			if ( null !== $cache_key ) {
				$cache_results = get_transient( $cache_key );

				// Check Transient.
				if ( false === $cache_results ) {

					$result = $response->get_data();

					// Set Transient.
					if ( ! empty( $result ) || null !== $result ) {
						$set_cache = set_transient( $cache_key, $result, $timeout );
					}

					return $response;

				} else {

					return $cache_results;

				}
			} else {
				return $response;
			}

		}

		/**
		 * Cache Request Headers.
		 *
		 * @access public
		 * @param mixed $response Response.
		 * @param mixed $server Server.
		 * @param mixed $request Request.
		 */
		public function cache_requests_headers( $response, $server, $request ) {

			// Get Request URI.
			$request_uri = esc_url( $_SERVER['REQUEST_URI'] ) ?? null;

			// Get Path & Method.
			$path   = $request->get_route() ?? null;
			$method = $request->get_method() ?? 'GET';

			$timeout = $this->get_timeout() ?? 300;

			// Set Cache Control Header.
			$max_age   = apply_filters( 'rest_cache_max_age', $timeout ) ?? null;
			$s_max_age = apply_filters( 'rest_cache_s_max_age', $timeout ) ?? null;

			// Send Cache Control Header.
			if ( null !== $max_age && null !== $s_max_age ) {
				$server->send_header( 'Cache-Control', 'public s-maxage=' . $s_max_age . ' max-age=' . $max_age . ' re-validate' );
			}

			// Get Cache Key.
			$cache_key = $this->cache_key( $request_uri ) ?? '';

			// Check for Cache from Transient.
			if ( ! empty( $cache_key ) || '' !== $cache_key || null !== $cache_key ) {
				$cache_results = get_transient( $cache_key );
			} else {
				$cache_results = false;
			}

			if ( false !== $cache_results || '' !== $cache_results || null !== $cache_results ) {

				// Send Header - Cached.
				$server->send_header( 'X-API-CACHE-PRO', esc_html( 'Cached', 'wp-rest-api-cache' ) );

				// Display Key Header.
				$display_cache_key  = apply_filters( 'api_cache_pro_key_header', true );

				if ( true === $display_cache_key ) {
					$server->send_header( 'X-API-CACHE-PRO-KEY', $cache_key );
				}

				// Get Transient Timeout.
				$cache_timeout = $this->get_cache_timeout( $cache_key ) ?? null;

				// Display Cache Timout.
				$display_cache_timeout = apply_filters( 'api_cache_pro_expires_header', true );

				if ( null !== $cache_timeout && true === $display_cache_timeout ) {

					// Get WordPress Time Zone Settings.
					$gmt_offset = get_option( 'gmt_offset' ) ?? 0;

					// Set Transient Timeout & Diff.
					$transient_timeout = date( 'F j, Y, g:i A T', current_time( $cache_timeout, $gmt_offset ) ) ?? null;
					$timeout_diff      = human_time_diff( current_time( $cache_timeout, $gmt_offset ), current_time( 'timestamp', $gmt_offset ) ) ?? null;

					// Send Cache Expires Header.
					if ( null !== $transient_timeout ) {
						$server->send_header( 'X-API-CACHE-PRO-EXPIRES', $transient_timeout );
					}

					// Send Cache Expires Diff Header.
					if ( null !== $timeout_diff ) {
						$server->send_header( 'X-API-CACHE-PRO-EXPIRES-DIFF', $timeout_diff );
					}
				}
			} else {
				// Send Header - Not Cached.
				$server->send_header( 'X-API-CACHE-PRO', esc_html( 'Not Cached', 'wp-rest-api-cache' ) );
			}

		}

		/**
		 * Delete Cache.
		 *
		 * @access public
		 * @param mixed $cache_key Cache Key.
		 */
		public function delete_cache( $cache_key ) {

			if ( ! empty( $cache_key ) ) {

				// Delete Transient.
				delete_transient( $cache_key );

				// Sometimes Transient are not in DB. So Flush.
				$flush_cache = wp_cache_flush();

			} else {
				return new WP_Error( 'missing_cache_key', __( 'Please provide the Cache Key (Transient Name).', 'wp-rest-api-cache' ) );
			}
		}

		/**
		 * Delete All Cache.
		 *
		 * @access public
		 */
		public function delete_all_cache() {

			global $wpdb;

			$results = $wpdb->query(
				$wpdb->prepare(
					"DELETE FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s",
					'_transient_rest_api_cache_%',
					'_transient_timeout_rest_api_cache_%',
					'_site_transient_rest_api_cache_%',
					'_site_transient_timeout_rest_api_cache_%'
				)
			);

			// Sometimes Transient are not in DB. So Flush.
			$flush_cache = wp_cache_flush();

			return $results;

		}

		/**
		 * Get Cache Timeout.
		 *
		 * @access public
		 * @param mixed $cache_key Cache Key.
		 */
		public function get_cache_timeout( $cache_key ) {

			if ( ! empty( $cache_key ) ) {

				global $wpdb;

				$timeout_key = '_transient_timeout_' . $cache_key;

				$cache_timeout = $wpdb->get_col( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name LIKE %s", $timeout_key ) );

				if ( ! empty( $cache_timeout ) ) {
					return $cache_timeout[0];
				} else {
					return null;
				}
			} else {
				return new WP_Error( 'missing_cache_key', __( 'Please provide the Cache Key (Transient Name).', 'wp-rest-api-cache' ) );
			}

		}

	} // End Class.

	new API_CACHE_PRO();

}
