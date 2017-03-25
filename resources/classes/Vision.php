<?php

class Vision
{
    public function __construct()
    {

    }

    public function detectText($image_data)
    {
        global $config;

        // create the request body
        $request = <<<EOT
{
 "requests": [
  {
   "features": [
    {
     "type": "DOCUMENT_TEXT_DETECTION"
    }
   ],
   "image": {
    "content": "$image_data"
   }
  }
 ]
}
EOT;

        // create the url request
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,
            'https://vision.googleapis.com/v1/images:annotate?key=' .
            $config['vision']['api_key']
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
        ["Content-type: application/json"]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        // send the request
        $result = json_decode(curl_exec($curl), true);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $result;
    }
}
