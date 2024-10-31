<?php
/**
 * Created by IntelliJ IDEA.
 * User: mabel
 * Date: 28/08/2018
 * Time: 11:44 AM
 */

namespace BracketSpace\Notification\Ilensoft\SmsGateway;


use BracketSpace\Notification\Utils\Settings\Group;
use BracketSpace\Notification\Utils\Settings\CoreFields;

class CustomGateway extends SmsGateway
{

    /**
     * MNotify constructor.
     */
    public function __construct()
    {
        parent::__construct('custom', 'Custom SMS Gateway');
    }

    private static function getContentTypeOptions()
    {
        return array('application/json' =>'json', 'application/x-www-form-urlencoded'=>'form (url encoded)');
    }

    private static function getRequestMethodOptions()
    {
        return array('POST' =>'POST', 'GET'=>'GET');
    }

    public function send($recipient, $message, $sender)
    {
        $url = notification_get_setting('sms_gateway_settings/custom/url');
        $content_type = notification_get_setting('sms_gateway_settings/custom/content_type');
        $method = notification_get_setting('sms_gateway_settings/custom/method');
        $sender_parameter = trim(notification_get_setting('sms_gateway_settings/custom/sender_parameter'));
        $message_parameter = trim(notification_get_setting('sms_gateway_settings/custom/message_parameter'));
        $recipient_parameter = trim(notification_get_setting('sms_gateway_settings/custom/recipient_parameter'));
        $authorization_header = trim(notification_get_setting('sms_gateway_settings/custom/authorization_header'));

        $append_url_parameters_to_url = notification_get_setting('sms_gateway_settings/custom/append_url_parameters_to_url');

        $data = array($sender_parameter => $sender, $message_parameter => $message, $recipient_parameter=> $recipient);

        if($append_url_parameters_to_url || strtolower($method) == 'get'){
            if(strpos($url, '?') !== false){
                $url .= "&".http_build_query($data);
            }else{
                $url .= "?".http_build_query($data);
            }
        }

        if(strtolower($method) == 'get'){
            $data = array();
        }

        $headers = array();

        if($authorization_header){
            $headers['Authorization'] = $authorization_header;
        }

        $result = self::sendSms(null, null, null,
            $url, $method, $content_type, $data, $headers);
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
            'name'        => __( 'API URL', 'notification' ),
            'slug'        => 'url',
            'default'     => '',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'The URL to the API you wish to integrate with.', 'notification' ),
        ) );

        $group->add_field(array(
            'name'     => __( 'Content Type', 'notification' ),
            'slug'     => 'content_type',
            'default'  => 'application/json',
            'addons'   => array(
                'options' => self::getContentTypeOptions()
            ),
            'render'   => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Select(), 'input' ),
            'sanitize' => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Select(), 'sanitize' ),
        ) );

        $group->add_field(array(
            'name'     => __( 'Method', 'notification' ),
            'slug'     => 'method',
            'default'  => 'POST',
            'addons'   => array(
                'options' => self::getRequestMethodOptions()
            ),
            'render'   => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Select(), 'input' ),
            'sanitize' => array( new \BracketSpace\Notification\Utils\Settings\CoreFields\Select(), 'sanitize' ),
        ) );

        $group->add_field(array(
            'name'        => __( 'Sender Parameter', 'notification' ),
            'slug'        => 'sender_parameter',
            'default'     => 'from',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'Parameter name for sender', 'notification' ),
        ) );

        $group->add_field(array(
            'name'        => __( 'Message Parameter', 'notification' ),
            'slug'        => 'message_parameter',
            'default'     => 'content',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'Parameter name for sms body', 'notification' ),
        ) );

        $group->add_field(array(
            'name'        => __( 'Recipient Parameter', 'notification' ),
            'slug'        => 'recipient_parameter',
            'default'     => 'to',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'Parameter name for recipient', 'notification' ),
        ) );

        $group->add_field(array(
            'name'        => __( 'Authorization Header', 'notification' ),
            'slug'        => 'authorization_header',
            'default'     => '',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'Example: Basic Zm9vOmJhcg==', 'notification' ),
        ) );

        $group->add_field(array(
            'name'        => __( 'Append Parameters to URL', 'notification' ),
            'slug'        => 'append_url_parameters_to_url',
            'default'     => '',
            'render'      => array( new CoreFields\Checkbox(), 'input' ),
            'sanitize'    => array( new CoreFields\Checkbox(), 'sanitize' ),
            'description' => __( 'Check this if you want to add the parameters to the URL before sending the request.', 'notification' ),
        ) );
    }
}