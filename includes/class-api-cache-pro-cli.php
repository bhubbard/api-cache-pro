<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	exit;
}

if ( ! class_exists( 'API_Cache_Pro_CLI' ) ) {

	/**
	 * API_Cache_Pro_CLI class.
	 */
	class API_Cache_Pro_CLI {

		/**
		 * api_cache_pro_cli function.
		 *
		 * @access public
		 * @param mixed $args
		 * @param mixed $assoc_args
		 * @return void
		 */
		public function clear_cache() {
		   WP_CLI::success( "The script has run!" );
		};


	}

	WP_CLI::add_command( 'api-cache-pro', 'API_Cache_Pro_CLI' );

}
