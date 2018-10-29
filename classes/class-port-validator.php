<?php
namespace Cwc\Classes;

class Port_Validator implements Field_Validator_Interface {

	private $setting;

	public function __construct( $setting ) {
		$this->setting = $setting;
	}

	public function validate( $input ) {

		$compare = get_option( 'change_wp_cron_port' );
		if ( preg_match( '/^[0-9]{0,5}$/', $input ) ) {
			return $input;
		} else {
			$this->add_error( 'invalid-port', 'You must provide a valid port number.' );
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