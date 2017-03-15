<?php

class Media
{

    public function __construct()
    {

    }

    public function addMedia($phone, $url)
    {
        // generate a unique id for the image
        $img_id = uniqid();
        $path = dirname(__FILE__) . "/../uploads/$img_id";

        // download the image to the local server
        file_put_contents($path, file_get_contents($url));

        // get the mime type of the image
        $type = mime_content_type($path);

        // add the metadata into the database
        $connect = new Connect();
        $connect->simpleInsert(
            'media',
            [
                'img_id' => $img_id,
                'phone' => $phone,
                'type' => $type
            ]
        );
    }
}
