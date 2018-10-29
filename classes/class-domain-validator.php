<?php

namespace Cwc\Classes;

class Domain_Validator implements Field_Validator_Interface {

	private $setting;

	public function __construct( $setting ) {
		$this->setting = $setting;
	}

	public function validate( $input ) {

		$compare = get_option( 'change_wp_cron_domain' );
		if ( preg_match( '/^https?:\/\/[A-Za-z0-9]+[A-Za-z0-9\.\-]+$/', $input ) ) {
			return $input;
		} else {
			$this->add_error( 'invalid-domain', 'You must provide a valid domain name.' );
			return $compare;
		}
	}

	private function add_error( $key, $message ) {
		add_settings_error(
			$this->setting,
			$key,
			$message
		);
	}
}