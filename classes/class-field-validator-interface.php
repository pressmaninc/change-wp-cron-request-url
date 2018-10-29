<?php
namespace Cwc\Classes;

interface Field_Validator_Interface {

	public function __construct( $setting );

	public function validate( $input );
}