<?php

class SMS
{

    public function __construct()
    {

    }

    public function addSMS($phone, $body)
    {
        // add the metadata into the database
        $connect = new Connect();
        $connect->simpleInsert(
            'sms',
            [
                'phone' => $phone,
                'body' => $body
            ]
        );
    }
}
