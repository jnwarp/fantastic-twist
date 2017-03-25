<?php
class Event
{

    public function __construct()
    {

    }

    public function loadEvent($code)
    {
        $connect = new Connect();

        $result = $connect->simpleSelect(
            'events',
            'code',
            $code
        );

        $connect->close();
        return $result;
    }
}
