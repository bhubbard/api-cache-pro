<?php
/**
 * Create section for settings in customizer.
 *
 * @package wp-rest-api-cache
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_REST_API_Cache_Customizer' ) ) {

	// Require Customizer Control.
	require_once ABSPATH . WPINC . '/class-wp-customize-control.php';

	/**
	 * WP_REST_API_Cache_Customizer
	 */
	class WP_REST_API_Cache_Customizer {

		/**
		 * Constructing a customizing running lemming.
		 *
		 * @access public
		 */
		public function __construct() {
				add_action( 'customize_register', array( $this, 'register' ) );
		}

		/**
		 * Register.
		 *
		 * @access public
		 * @param mixed $wp_customize Support Customizer.
		 */
		public function register( $wp_customize ) {

			// Rest API Cache Panel.
			$wp_customize->add_panel(
				'rest_api_cache_panel',
				array(
					'priority'       => 500,
					'capability'     => 'manage_options',
					'theme_supports' => '',
					'title'          => __( 'Rest API Cache', 'wp-rest-api-cache' ),
					'description'    => __( 'Set Rest API Cache.', 'wp-rest-api-cache' ),
				)
			);

			// Settings Section.
			$wp_customize->add_section(
				'rest_api_cache_settings_section',
				array(
					'title'       => __( 'Settings', 'wp-rest-api-cache' ),
					'description' => __( 'Fill out the following cache settings for rest api.', 'wp-rest-api-cache' ),
					'priority'    => 500,
					'panel'       => 'rest_api_cache_panel',
				)
			);

			// Disable Settings.
			$wp_customize->add_setting(
				'rest_api_cache[disable]',
				array(
					'default'   => false,
					'type'      => 'option',
					'transport' => 'refresh',
				)
			);

			// Disable Controls.
			$wp_customize->add_control(
				'rest_api_cache_disable',
				array(
					'label'       => __( 'Disable Cache', 'wp-rest-api-cache' ),
					'description' => __( 'Check this box if you wish to disable the WP Rest API Cache.', 'wp-rest-api-cache' ),
					'type'        => 'checkbox',
					'section'     => 'rest_api_cache_settings_section',
					'settings'    => 'rest_api_cache[disable]',
				)
			);

			// Timeout Settings.
			$wp_customize->add_setting(
				'rest_api_cache[default_timeout]',
				array(
					'default'           => 300,
					'type'              => 'option',
					'transport'         => 'refresh',
					'sanitize_callback' => array( $this, 'sanitize_default_timeout' ),
				)
			);

			// Timeout Controls.
			$wp_customize->add_control(
				'rest_api_cache_default_timeout',
				array(
					'label'       => __( 'Default Timeout', 'wp-rest-api-cache' ),
					'description' => __( 'Set the default timeout in seconds. <br/> Default: 300 (5 Minutes) <br/> Max: 604800 (7 Days)', 'wp-rest-api-cache' ),
					'type'        => 'number',
					'section'     => 'rest_api_cache_settings_section',
					'settings'    => 'rest_api_cache[default_timeout]',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 604800, // Max of 7 Days in Seconds.
						'step' => 1,
					),
				)
			);

		}

		/**
		 * Sanitize Default Timeout.
		 *
		 * @access public
		 * @param mixed $default_timeout Default Timeout.
		 */
		public function sanitize_default_timeout( $default_timeout ) {

			if ( is_numeric( $default_timeout ) && $default_timeout <= 604800 ) {
				return $default_timeout;
			} else {
				return new WP_Error( 'invalid', __( 'You must supply a number no greater than the max default timeout allowed.', 'wp-rest-api-cache' ) );
			}

		}
	}

	new WP_REST_API_Cache_Customizer();

}
