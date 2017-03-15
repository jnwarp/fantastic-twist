<?php
include(dirname(__FILE__) . '/../../resources/prepend.php');

$log = new Log();
$twilio = new Twilio();
$account = new Account();

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
        $img_id = $media->addMedia($_POST['From'], $_POST["MediaUrl$i"]);
    }
}

$info = $account->getInfo($_POST['From']);
$body = strtolower($_POST['Body']);
$regex = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/';

// check if body contains email
if (preg_match($regex, $body, $email)) {
    // update the email account
    $email = $email[0];
    $account->updateEmail($_POST['From'], $email);

    $twilio->replySMS("Email updated to \"$email\", reply ? for more information");
} else {
    switch(strtolower($body)) {
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
    }
}

if ($info['profile'] == '' && $body != 'profile') {
    if (isset($img_id)) {
        $account->updateProfile($_POST['From'], $img_id);
        $twilio->replySMS("Your profile picture has now been updated.");
    } else {
        $twilio->replySMS("You do not have a profile picture set, send a picture to set it.");
    }
}

// send a response back
$twilio->postReply();
