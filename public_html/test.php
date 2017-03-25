<?php
include(dirname(__FILE__) . '/../resources/prepend.php');

$command = new Command('', '+');
echo json_encode($command->vision('58d6aac2a8bf9'));
