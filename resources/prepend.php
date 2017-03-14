<?php
/*
This file should be required in every php file.
require_once(dirname(__FILE__) . '/../../resources/prepend.php');
*/

// automatically load any classes
spl_autoload_register(function ($class_name) {
    $class_path = explode('_', $class_name);
    $file_path = 'classes/' . implode('/', $class_path) . '.php';
    require_once($file_path);
});

// return json output when a script ends
function return_json($json = false) {
    // if no arguements are passed, success is false
    if ($json === false) $json = ['success' => false];

    // if a true value is passed, success is true
    if ($json === true) $json = ['success' => true];

    // assume that success is false if not true
    if (!isset($json['success'])) $json['success'] = false;

    // output the script's total execution time
    $json['exec_time'] = number_format(microtime(true) -
        $_SERVER["REQUEST_TIME_FLOAT"], 4);

    // output the JSON code and stop the script
    exit(json_encode($json));
}

// used by the scripts to validate inputs
function require_valid($json) {
    foreach ($json as $key => $value) {
        // if any valid_ key is not true, exit
        if (substr($key, 0, 6) == 'valid_' && $value != true) return_json($json);
    }
}

// load the config file if it exists
if (file_exists(dirname(__FILE__) . '/config.php')) {
    require(dirname(__FILE__) . '/config.php');
} else {
    require(dirname(__FILE__) . '/config.default.php');
}
