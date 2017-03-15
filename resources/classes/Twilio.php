<?php
require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
use Twilio\Rest\Client;

class Twilio
{
    private $client;

    public function __construct()
    {
        global $config;

        $client = new Client(
            $config['twilio']['sid'],
            $config['twilio']['token']
        );
    }

    public function getMMS($post)
    {

    }

    public function delMMS($url)
    {
        // get the message and media id
        $path = explode('/', parse_url($url, PHP_URL_PATH));
        $msg_sid = $path[5];
        $media_sid = $path[7];

        // trigger a delete of the media
        $client->messages($msg_sid)
            ->media($media_sid)
            ->delete();
    }

    public function sendSMS()
    {

    }
}
