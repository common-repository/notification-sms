<?php

namespace BracketSpace\Notification\Ilensoft\SmsSetting;

/**
 * Created by PhpStorm.
 * User: jimmy
 * Date: 9/22/18
 * Time: 5:40 PM
 */

class NotificationSmsSettings extends \BracketSpace\Notification\Admin\Settings {
    public function register_page(){
        //no need to register the page again
    }

    /**
     * @param \BracketSpace\Notification\Admin\Settings $settings
     * @throws \Exception
     */
    public function sms_settings($settings){
        /** @var \BracketSpace\Notification\Utils\Settings\Section $notifications */
        $notifications = $settings->get_section('notifications');
        /** @var \BracketSpace\Notification\Utils\Settings\Group $sms_group */
        $sms_group = $notifications->add_group( __( 'SMS', 'notification' ), 'sms' )
            ->add_field( array(
                'name'     => __( 'Enable', 'notification' ),
                'slug'     => 'enable',
                'default'  => 'true',
                'addons'   => array(
                    'label' => __( 'Enable SMS notification', 'notification' )
                ),
                'render'   => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Checkbox(), 'input' ),
                'sanitize' => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Checkbox(), 'sanitize' ),
            ) );

        do_action('notification/sms/gateways/load');

        $sms_gateway_options = array(
            'hubtel'  => __( 'Hubtel', 'notification' ),
        );

        $sms_gateway_options_installed = apply_filters('notification/sms/gateways', array());
        $sms_gateway_options_installed = array_merge($sms_gateway_options_installed, $sms_gateway_options);

        $sms_group->add_field( array(
            'name'     => __( 'SMS Gateway', 'notification' ),
            'slug'     => 'gateway',
            'default'  => 'hubtel',
            'addons'   => array(
                'options' => $sms_gateway_options_installed
            ),
            'render'   => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Select(), 'input' ),
            'sanitize' => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Select(), 'sanitize' ),
        ) );

        $sms_group->add_field( array(
            'name'        => __( 'SenderID', 'notification' ),
            'slug'        => 'sender_id',
            'default'     => '',
            'render'      => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Text(), 'input' ),
            'sanitize'    => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Text(), 'sanitize' ),
            'description' => __( 'Please enter maximum of 11 characters with no space or as prescribed by your SMS gateway', 'notification' ),
        ) );

        $sms_gateway_settings_section = $settings->add_section('SMS Gateway Settings', 'sms_gateway_settings');

        foreach($sms_gateway_options_installed as $sms_gateway_slug => $sms_gateway_name){
            $group_setting = $sms_gateway_settings_section->add_group($sms_gateway_name, $sms_gateway_slug);
            do_action('notification/sms/gateways/'.$sms_gateway_slug.'/settings', $group_setting);
        }
    }

    function catch_config()
    {

    }



    function save_settings()
    {

    }
}