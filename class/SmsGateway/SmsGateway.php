<?php
/**
 * Created by IntelliJ IDEA.
 * User: mabel
 * Date: 28/08/2018
 * Time: 11:29 AM
 */

namespace BracketSpace\Notification\Ilensoft\SmsGateway;

use BracketSpace\Notification\Utils\Settings\Group;

abstract class SmsGateway
{

    protected $slug;
    protected $name;
    /**
     * SmsGateway constructor.
     */
    public function __construct($slug, $name, $avoid_registration = false)
    {
        $this->slug = $slug;
        $this->name = $name;
        if(!$avoid_registration){
            add_filter('notification/sms/gateways', array($this, 'register_sms_gateway'));
        }
        add_action('notification/sms/send/'.$slug, array($this, 'send'), 10, 3);
        add_action('notification/sms/gateways/'.$slug.'/settings', array($this, 'add_fields'), 10, 1);
    }

    public function register_sms_gateway($sms_gateway_options){
        $sms_gateway_options[$this->slug] = __($this->name, 'notification');
        return $sms_gateway_options;
    }

    public abstract function send($recipient, $message, $sender);

    public static function sendSms($sender, $message, $receiver, $api_url, $method = 'POST', $content_type = 'json', $data = array(), $headers = array()){
        $input = array_filter(array_merge(compact('sender', 'message', 'receiver'), $data));

        if($content_type == 'json'){
            $input = json_encode($input);
            $content_type = 'application/json';
        }else if($content_type == 'form'){
            $content_type = 'application/x-www-form-urlencoded';
            $input = http_build_query($input);
        }else if($content_type == 'xml'){
            $input = '';
            $content_type = 'text/xml';
        }

        // use key 'http' even if you send the request to https://...
        if(!is_array($headers)){
            $headers = array();
        }
        $headers['Content-type'] = $content_type;

        $header_string = '';
        foreach($headers as $key => $value){
            $header_string .= "$key: $value\r\n";
        }

        $options = array(
            'http' => array(
                'header'  => $header_string,
                'method'  => $method,
                'content' => $input
            )
        );

        $context  = stream_context_create($options);
        if($handle = fopen($api_url, 'r', false, $context)){
            $response = '';
            while(!feof($handle)){
                $response .= fgets($handle);
            }
            fclose($handle);
        }else{
            $response = null;
        }
        if ($handle === FALSE) { /* Handle error */ }

        return $response;
    }

    /**
     * @param Group $sms_group
     * @return mixed
     */
    public abstract function add_fields($sms_group);
}