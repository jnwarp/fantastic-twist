<?php
require_once(dirname(__FILE__) . '/../../vendor/autoload.php');
use Twilio\Rest\Client;

class Twilio
{
    private $client;
    private $replies;

    public function __construct()
    {
        global $config;

        $this->client = new Client(
            $config['twilio']['sid'],
            $config['twilio']['token']
        );
    }

    public function delMMS($url)
    {
        // get the message and media id
        $path = explode('/', parse_url($url, PHP_URL_PATH));
        $msg_sid = $path[5];
        $media_sid = $path[7];

        // trigger a delete of the media
        $this->client
            ->messages($msg_sid)
            ->media($media_sid)
            ->delete();
    }

    public function replySMS($message)
    {
        $this->replies[] = $message;
    }

    public function postReply($message = false)
    {
        // add an optional final message
        if ($message !== false) {
            $this->replySMS($message);
        }

        // create an XML body
        $body = '<?xml version="1.0" encoding="UTF-8"?>' . "\n<Response>\n";
        foreach ($message in $this->replies) {
            $body = $body . "    <Message>$message</Message>\n";
        }
        $body = $body . "</Response>\n";

        // print out the result
        echo $body;
    }
}
