<?php

class Change_WP_Cron_Settings_Page {

	/**
	 * Change_WP_Cron_Settings_Page constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'create_settings' ] );
		add_action( 'admin_init', [ $this, 'setup_sections' ] );
		add_action( 'admin_init', [ $this, 'setup_fields' ] );
	}

	public function create_settings() {
		$page_title = 'Change WP Cron Request URL';
		$menu_title = 'Change WP Cron Request URL';
		$capability = 'manage_options';
		$slug       = 'change-wp-cron';
		$callback   = [ $this, 'settings_content' ];
		add_options_page( $page_title, $menu_title, $capability, $slug, $callback );
	}

	public function settings_content() { ?>
		<div class="wrap">
			<h1>Change WP Cron Request URL</h1>
			<form method="POST" action="options.php">
				<?php
				settings_fields( 'change-wp-cron' );
				do_settings_sections( 'change-wp-cron' );
				submit_button();
				?>
			</form>
		</div> <?php
	}

	public function setup_sections() {
		add_settings_section( 'change-wp-cron_section', '', [], 'change-wp-cron' );
	}

	public function setup_fields() {
		$fields = [
			[
				'label'       => 'Domain Name',
				'id'          => Change_WP_Cron::CHANGE_DOMAIN,
				'type'        => 'url',
				'section'     => 'change-wp-cron_section',
				'placeholder' => 'http://yourdomain.com',
				'validator'   => 'domain'
			],
			[
				'label'       => 'Port',
				'id'          => Change_WP_Cron::CHANGE_PORT,
				'type'        => 'number',
				'section'     => 'change-wp-cron_section',
				'placeholder' => '',
				'validator'   => 'port'
			],
		];

		foreach ( $fields as $field ) {
			add_settings_field( $field['id'], $field['label'], [ $this, 'field_callback' ], 'change-wp-cron', $field['section'], $field );
			$class     = 'Cwc\Classes\\' . ucfirst( $field['validator'] ) . '_Validator';
			$validator = new $class( 'change-wp-cron' );

			register_setting( 'change-wp-cron', $field['id'], [ $validator, 'validate' ] );
		}
	}

	public function field_callback( $field ) {
		if ( $field['id'] === Change_WP_Cron::CHANGE_DOMAIN && defined( 'TARGET_CRON_DOMAIN' ) ) {
			?>
			<input name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" placeholder="<?php echo $field['placeholder'] ?>" value="<? echo esc_attr( TARGET_CRON_DOMAIN ); ?>" disabled/>
			<?php
		} elseif ( $field['id'] === Change_WP_Cron::CHANGE_PORT && defined( 'TARGET_CRON_PORT' ) ) {
			?>
			<input name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" placeholder="<?php echo $field['placeholder'] ?>" value="<? echo esc_attr( TARGET_CRON_PORT ); ?>" disabled/>
			<?php
		} else {
			?>
			<input name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" type="<?php echo $field['type']; ?>" placeholder="<?php echo $field['placeholder'] ?>" value="<? echo esc_attr( get_option( $field['id'] ) ); ?>"/>
			<?php
		}
	}
}

if ( is_admin() ) {
	new Change_WP_Cron_Settings_Page();
}
