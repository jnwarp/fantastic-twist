<?php

class Command
{
    private $account;
    private $body;
    private $event;
    private $info;
    private $phone;
    private $twilio;

    public function __construct($body, $phone)
    {
        $this->twilio = new Twilio();
        $this->account = new Account();
        $this->event = new Event();
        $this->info = $this->account->getInfo($phone);
        $this->phone = $phone;
        $this->body = strtolower($body);
    }

    public function code($code)
    {
        // quit if user not logged in
        if ($this->info === []) return false;

        $result = $this->event->loadEvent($code);

        if ($result === []) {
            $this->twilio->replySMS("Error: Unknown code, please try again.");
        } else {
            $events = explode(', ', $this->info['events']);

            if (in_array($code, $events)) {
                $this->twilio->replySMS("Error: You already signed in to this event.");
            } else {
                // add a new event onto the list
                if ($this->info['events'] == "") {
                    $events = [$code];
                } else {
                    $events[] = $code;
                }

                // sign into this event
                $this->account->updateEvents(
                    $this->phone,
                    implode(', ', $events),
                    $result['points'] + $this->info['points']
                );

                $this->twilio->replySMS("You have sucessfully signed in! You now have " . ($result['points'] + $this->info['points']) . " points.");
            }
        }
    }

    public function commands()
    {
        $this->twilio->replySMS("A full list of commands is available here: https://git.io/vSki6");
    }

    public function delete()
    {
        $this->twilio->replySMS("Are you sure you want to delete your account? All point event logs will be cleared.\n\nReply \"DELETE CONFIRM\" if you are sure you want to do this.");
    }

    public function deleteconfirm()
    {
        $this->account->deleteAccount($this->phone);
        $this->twilio->replySMS("Your account has been deleted. Images and message logs still remain on the server. (WIP)");
    }

    public function email()
    {
        $regex = '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/';

        // check if there is an email in message
        if (preg_match($regex, $this->body, $temp)) {
            $this->account->updateEmail($this->phone, $temp[0]);
            $this->twilio->replySMS("Email updated to \"" . $temp[0] . "\", reply ? for more information");
        } else {
            $this->twilio->replySMS("Error: Wrong syntax, try \"EMAIL xyz123@psu.edu\"");
        }
    }

    public function events()
    {

    }

    public function github()
    {

    }

    public function points()
    {
        $this->twilio->replySMS("You have " . $this->info['points'] . " points.");
    }

    public function postReply()
    {
        $this->twilio->postReply();
    }

    public function privacy()
    {

    }

    public function profile()
    {

    }

    public function start()
    {
        $this->twilio->replySMS(
            "Welcome to Selfie Sign-In! Getting started is easy, just follow these steps:\n\n" .
            "1. Reply \"EMAIL zyz123@psu.edu\" to set your email\n" .
            "2. Reply \"PROFILE\" to set your profile (optional)\n" .
            "3. Take a picture of the sign in code to sign in\n"
        );
        $this->twilio->replySMS("For more information, try typing \"?\" or \"COMMANDS\".");
    }
}
