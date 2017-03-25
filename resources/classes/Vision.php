<?php

class Vision
{
    public function __construct()
    {

    }

    public function detectText($image_data)
    {
        global $config;

        // set up post date and provide image
        $post_data = http_build_query(
            [
                'requests' => [
                    'image' => $image_data,
                    'features' => [
                        'type' => 'DOCUMENT_TEXT_DETECTION'
                    ]
                ]
            ]
        );

        // set the post options
        $post_options = ['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            ]
        ];

        // send image to Google's Vision API
        $result = file_get_contents(
            'https://vision.googleapis.com/v1/images:annotate?key=' . $config['vision']['api_key'],
            false,
            stream_context_create($post_options)
        );

        return $result;
    }
}
