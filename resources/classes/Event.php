<?php
class Event
{

    public function __construct()
    {

    }

    public function listEvents($code)
    {
        $connect = new Connect();

        // custom query to get events
        $result = $connect->query(
            "SELECT * FROM `events` WHERE `date` > NOW() ORDER BY `date` LIMIT 3;"
        );

        // create a table for each event
        $table = [];
        while ($row = $result->fetch_assoc()) {
            $table[] = $row;
        }

        $connect->close();
        return $table;
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
