<?php
class Log
{

    public function __construct()
    {
    }

    public function logEvent($event, $data)
    {
        $connect = new Connect();

        $connect->simpleInsert(
            'log',
            [
                'event' => $event,
                'data' => $data,
                'ip_address' => $_SERVER['REMOTE_ADDR']
            ]
        );

        $connect->close();
    }
}
