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
        $media->addMedia($_POST['From'], $_POST["MediaUrl$i"]);
    }
}

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
            $account->updateProfile($_POST['From'], $media_sid);
            $twilio->replySMS("Your profile picture has now been updated.");
            break;
        case 'deactivate':
            $twilio->replySMS("You will no longer receive any messages.");
            break;
        case '?':
            $email = $account->getEmail($_POST['From']);

            $twilio->replySMS("List of commands:\n\n\"?\" - Display help prompt\n\"DELETE\" - Deactivate account and stop recieving messages\n\"PROFILE\" - Set profile picture to next image recieved\n");
            $twilio->replySMS("Your email is currently set to \"$email\", reply with another email address to update it.");
            break;
        default:
            $twilio->replySMS("Unknown command, reply ? for more info.");
    }
}

// send a response back
$twilio->postReply();
