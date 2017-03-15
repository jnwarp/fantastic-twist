<?php
include(dirname(__FILE__) . '/../resources/prepend.php');

exit();
$url = '';
$phone = '+1';

$media = new Media();
$media->addMedia($phone, $url);
