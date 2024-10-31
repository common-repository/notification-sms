<?php
/**
 * Plugin Name: Notification : SMS
 * Description: Notification-SMS makes it possible for you to send notifications via SMS. It depends on the Notification plugin.
 * Version: 1.0.2
 * Author: Jimmy Ilenloa
 * Author URI: https://www.facebook.com/jilenloa
 * License: GPLv2+
 * Text Domain: notification-sms
 * Domain Path: /languages
*/

/**
 * Plugin's autoload function
 *
 * @param  string $class class name.
 * @return mixed         false if not plugin's class or void
 */
function notification_sms_autoload( $class ) {

    $parts = explode( '\\', $class );

    if ( array_shift( $parts ) != 'BracketSpace' ) {
        return false;
    }

    if ( array_shift( $parts ) != 'Notification' ) {
        return false;
    }

    if ( array_shift( $parts ) != 'Ilensoft' ) {
        return false;
    }

    $file = trailingslashit( dirname( __FILE__ ) ) . trailingslashit( 'class' ) . implode( '/', $parts ) . '.php';

    if ( file_exists( $file ) ) {
        require_once $file;
    }

}

spl_autoload_register( 'notification_sms_autoload' );

add_action('not_fs_loaded', function(){
    register_recipient('sms', new \BracketSpace\Notification\Ilensoft\Recipient\PhoneNumber());
    register_notification( new \BracketSpace\Notification\Ilensoft\Notification\Sms() );

    static $notification_sms_settings;
    if(!$notification_sms_settings){
        $notification_sms_settings = new BracketSpace\Notification\Ilensoft\SmsSetting\NotificationSmsSettings();
    }

    notification_register_settings( array( $notification_sms_settings, 'sms_settings'), 40 );
});

// load the default sms gateways/drivers
add_action('notification/sms/gateways/load', function(){
    new BracketSpace\Notification\Ilensoft\SmsGateway\HubtelSms();
    new BracketSpace\Notification\Ilensoft\SmsGateway\MNotify();
    new BracketSpace\Notification\Ilensoft\SmsGateway\CustomGateway();
});