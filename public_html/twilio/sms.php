<?php
include(dirname(__FILE__) . '/../../resources/prepend.php');

$log = new Log();

// log a failure and quit
if (isset($_GET['fail'])) {
    $log->logEvent('SMS_FAIL', json_encode($_POST));
    exit();
} else {
    // log a success
    $log->logEvent('SMS_SUCCESS', json_encode($_POST));
}

// check for images
if (isset($_POST['NumMedia']) && $_POST['NumMedia'] > 0) {
    $media = new Media();

    // add each image to database
    for ($i = 0; $i < intval($_POST['NumMedia']); $i++) {
        $img_id = $media->addMedia($_POST['From'], $_POST["MediaUrl$i"]);
    }
}

$command = new Command($_POST['Body'], $_POST['From']);
$body = $_POST['Body'];

// set a new email address
if (substr($body, 0, 5) == 'email') {
    $command->email();
} elseif (strtolower(substr($body, 0, 4)) == 'code') {
    $command->code(substr($body, 5));
} else {
    // command block
    switch (strtolower($body)) {
        case 'profile':
            if (isset($img_id)) {
                $account->updateProfile($_POST['From'], $img_id);
                $twilio->replySMS("Your profile picture has now been updated.");
            } else {
                $account->updateProfile($_POST['From'], '');
                $twilio->replySMS("The next image sent will be set as your profile picture.");
            }
            break;

        case 'deactivate':
            $twilio->replySMS("You will no longer receive any messages.");
            break;
        case '?':
            $email = $info['email'];

            $twilio->replySMS("List of commands:\n\n? - Display help prompt\nDELETE - Deactivate account and stop recieving messages\nPROFILE - Set a profile picture by sending an image\n");
            $twilio->replySMS("Your email is currently set to \"$email\", reply with another email address to update it.");
            break;
        case '':

            break;
        default:
            $twilio->replySMS("Unknown command, reply ? for more info.");

            if ($this->info['profile'] == '') {
                if (isset($img_id)) {
                    $account->updateProfile($_POST['From'], $img_id);
                    $twilio->replySMS("Your profile picture has now been updated.");
                }
            }
    }
}

// send a response back
$command->postReply();
