<?php
/**
 * Plugin Name: Change WP Cron Request URL
 * Plugin URI:
 * Description: Change the request url when wp-cron executed.
 * Version: 1.0
 * Author: PRESSMAN
 * Author URI: https://www.pressman.ne.jp/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Change_WP_Cron {

	private static $instance;
	const CHANGE_DOMAIN = 'change_wp_cron_domain';
	const CHANGE_PORT = 'change_wp_cron_port';

	/**
	 * Change_WP_Cron constructor.
	 */
	private function __construct() {

		require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-field-validator-interface.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-domain-validator.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-port-validator.php' );

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

		add_filter( 'cron_request', [ $this, 'change_cron_url' ], 9999 );
	}

	/**
	 * Get instance
	 *
	 * @return Change_WP_Cron
	 */
	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function activate() {

		$target = site_url();
		$point  = strpos( $target, ':' );

		if ( !get_option( self::CHANGE_DOMAIN ) ) {
			if ( $point ) {
				add_option( self::CHANGE_DOMAIN, substr( $target, 0, $point ) );
			} else {
				add_option( self::CHANGE_DOMAIN, $target );
			}
		}

		if ( !get_option( self::CHANGE_PORT ) ) {
			if ( $point ) {
				add_option( self::CHANGE_PORT, substr( $target, $point + 1 ) );
			} else {
				add_option( self::CHANGE_PORT, '' );
			}
		}
	}

	public function deactivate() {
		delete_option( self::CHANGE_DOMAIN );
		delete_option( self::CHANGE_PORT );
	}

	/**
	 * Change cron url
	 *
	 * @param $cron_request_array
	 *
	 * @return mixed
	 */
	function change_cron_url( $cron_request_array ) {

		if ( defined( 'TARGET_CRON_DOMAIN' ) ) {
			$domain = TARGET_CRON_DOMAIN;
		} else {
			$domain = get_option( self::CHANGE_DOMAIN );
		}

		if ( defined( 'TARGET_CRON_PORT' ) ) {
			$port = TARGET_CRON_PORT;
		} else {
			$port = get_option( self::CHANGE_PORT );
		}

		if ( $port ) {
			$target = $domain . ':' . $port;
		} else {
			$target = $domain;
		}

		/**
		 * Filter the wp-cron request url.
		 *
		 * @param $target
		 */
		$target = apply_filters( 'change_wp_cron_request_url', $target, $cron_request_array );

		// Verify domain and port
		if ( !preg_match( '/^https?:\/\/[A-Za-z0-9]+[A-Za-z0-9\.\-]+:[0-9]{0,5}$/', $target ) ) {
			return $cron_request_array;
		}

		$site_url                  = site_url();
		$cron_request_array['url'] = str_replace( $site_url, $target, $cron_request_array['url'] );

		return $cron_request_array;
	}
}

Change_WP_Cron::get_instance();
