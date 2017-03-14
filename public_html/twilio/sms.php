<?php
include(dirname(__FILE__) . '/../../resources/prepend.php');

$log = new Log();

if (isset($_GET['fail'])) {
  $log->logEvent('SMS_FAIL', json_encode($_POST));
} else {
  $log->logEvent('SMS_SUCCESS', json_encode($_POST));
}

return_json(true);
