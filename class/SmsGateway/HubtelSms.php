<?php
/**
 * Created by IntelliJ IDEA.
 * User: mabel
 * Date: 28/08/2018
 * Time: 11:39 AM
 */

namespace BracketSpace\Notification\Ilensoft\SmsGateway;


use BracketSpace\Notification\Utils\Settings\CoreFields;
use BracketSpace\Notification\Utils\Settings\Group;

class HubtelSms extends SmsGateway
{

    /**
     * HubtelSms constructor.
     */
    public function __construct()
    {
        parent::__construct('hubtel', 'Hubtel', true);
    }

    public function send($recipient, $message, $sender)
    {
        $key = notification_get_setting('sms_gateway_settings/hubtel/client_key');
        $secret = notification_get_setting('sms_gateway_settings/hubtel/client_secret');

        $headers = array(
            'Authorization' => 'Basic '.base64_encode("{$key}:{$secret}")
        );

       $data = array('From' => $sender, 'To'=>$recipient, 'Content' => $message);

       $result = self::sendSms(null, null, null,
           "https://api.smsgh.com/v3/messages", "POST", 'json', $data, $headers);
       return $result;
    }

    /**
     * @param Group $group
     * @return mixed
     * @throws \Exception
     */
    public function add_fields($group)
    {
        $group->add_field(array(
            'name'        => __( 'Client Key', 'notification' ),
            'slug'        => 'client_key',
            'default'     => '',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'This setting can be retrieved from your hubtel account', 'notification' ),
        ) );

        $group->add_field(array(
            'name'        => __( 'Client Secret', 'notification' ),
            'slug'        => 'client_secret',
            'default'     => '',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'This setting can be retrieved from your hubtel account', 'notification' ),
        ) );
    }
}