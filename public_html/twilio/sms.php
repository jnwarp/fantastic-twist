<?php
include(dirname(__FILE__) . '/../../resources/prepend.php');

$log = new Log();

// log a failure and quit
if (isset($_GET['fail'])) {
    $log->logEvent('SMS_FAIL', json_encode($_POST));
    exit();
}

// log a success
$log->logEvent('SMS_SUCCESS', json_encode($_POST));

// save the image
if (isset($_POST['NumMedia']) && $_POST['NumMedia'] > 0) {
    $media = new Media();

    // add each image to database
    for ($i = 0; $i < intval($_POST['NumMedia']); $i++) {
        $media->addMedia($_POST['From'], $_POST["MediaUrl$i"]);
    }
}
