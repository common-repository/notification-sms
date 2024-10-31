<?php
/**
 * Email notification
 *
 * @package notification
 */

namespace BracketSpace\Notification\Ilensoft\Notification;

use BracketSpace\Notification\Interfaces\Triggerable;
use BracketSpace\Notification\Abstracts;
use BracketSpace\Notification\Defaults\Field;

/**
 * Email notification
 */
class Sms extends Abstracts\Notification {

	/**
	 * Notification constructor
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		parent::__construct( 'sms', __( 'SMS', 'notification' ) );
	}

	/**
	 * Used to register notification form fields
	 * Uses $this->add_form_field();
     *
	 * @return void
	 */
	public function form_fields() {

		$this->add_form_field( new Field\TextareaField( array(
            'label'    => __( 'Body', 'notification' ),
            'name'     => 'body',
        ) ) );

		$this->add_form_field( new Field\RecipientsField( array(
			'notification' => $this->get_slug(),
		) ) );

	}

	/**
	 * Sends the notification
     *
	 * @param  Triggerable $trigger trigger object.
	 * @return void
	 */
	public function send( Triggerable $trigger ) {

		$data = $this->data;

        $enabled = notification_get_setting('notifications/sms/enable');

        if(!$enabled){
            return;
        }

		$recipients = apply_filters( 'notification/' . $this->get_slug() . '/recipients', $data['parsed_recipients'], $this, $trigger );

		$message = apply_filters( 'notification/' . $this->get_slug() . '/message/pre', $data['body'], $this, $trigger );

		$message = apply_filters( 'notification/' . $this->get_slug() . '/message', $message, $this, $trigger );

		// Fix for wp_mail not being processed with empty message.
		if ( empty( $message ) ) {
			$message = ' ';
		}


		$driver = notification_get_setting('notifications/sms/gateway');
		$sender_id = notification_get_setting('notifications/sms/sender_id');

        do_action('notification/sms/gateways/load');

        $recipients = array_unique($recipients);

		// Fire an email one by one.
		foreach ( $recipients as $to ) {
			$this->sendSms($to, $message, $sender_id, $driver);
		}
    }

    public function sendSms($to, $message, $sender_id, $driver){
	    // use a driver to send the sms
        do_action('notification/sms/send/'.$driver, $to, $message, $sender_id);
    }

}
