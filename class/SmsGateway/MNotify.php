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

class MNotify extends SmsGateway
{

    /**
     * MNotify constructor.
     */
    public function __construct()
    {
        parent::__construct('mnotify', 'mNotify');
    }

    public function send($recipient, $message, $sender)
    {
        $key = notification_get_setting('sms_gateway_settings/mnotify/api_key');

        $parameters = compact('key');

        //prepare url
        $url = "https://api.mnotify.com/api/sms/quick"."?".http_build_query($parameters);

        $data = compact('recipient');

        $result = self::sendSms($sender, $message, null,
            $url, "POST", 'json', $data, array());
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
            'name'        => __( 'API Key', 'notification' ),
            'slug'        => 'api_key',
            'default'     => '',
            'render'      => array( new CoreFields\Text(), 'input' ),
            'sanitize'    => array( new CoreFields\Text(), 'sanitize' ),
            'description' => __( 'This setting can be retrieved from your mNotify account', 'notification' ),
        ) );
    }
}