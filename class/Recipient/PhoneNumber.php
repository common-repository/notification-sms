<?php
/**
 * Email recipient
 *
 * @package notification
 */

namespace BracketSpace\Notification\Ilensoft\Recipient;

use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Email recipient
 */
class PhoneNumber extends Abstracts\Recipient {

	/**
	 * Recipient constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( array(
			'slug'          => 'phone_number',
			'name'          => __( 'Phone Number / Merge tag', 'notification' ),
			'default_value' => '',
		) );
	}

	/**
	 * Parses saved value something understood by notification
	 * Must be defined in the child class
	 *
	 * @param  string $value raw value saved by the user.
	 * @return array         array of resolved values
	 */
	public function parse_value( $value = '' ) {

		if ( empty( $value ) ) {
			$value = $this->get_default_value();
		}

		$parsed_phone_numbers = array();
		$phone_numbers        = explode( ',', $value );

		foreach ( $phone_numbers as $phone_number ) {
			$parsed_phone_numbers[] = $this->sanitizePhoneNumber( $phone_number );
		}

		return $parsed_phone_numbers;

	}

	private function sanitizePhoneNumber($input){
	    return preg_replace('/\D+/', '', $input);
    }

	/**
	 * Returns input object
	 *
	 * @return object
	 */
	public function input() {

		return new Field\InputField( array(
			'label'       => __( 'Recipient', 'notification' ),       // don't edit this!
			'name'        => 'recipient',       // don't edit this!
			'css_class'   => 'recipient-value', // don't edit this!
			'placeholder' => __( '+233245667942 or {phone_number}', 'notification' ),
			'description' => __( 'You can use any valid phone_number merge tag.', 'notification' ),
			'resolvable'  => true
		) );

	}

}
