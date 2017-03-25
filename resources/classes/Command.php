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
        $this->info = $account->getInfo($phone);
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
                $events = explode(', ', $this->info['events']);
                $events[] = $code;

                // sign into this event
                $this->account->updateEvents(
                    $code,
                    implode(', ', $events),
                    $result['points'] + $this->info['points']
                );

                $this->twilio->replySMS("You have sucessfully signed in! You now have " . $this->info['points'] . " points.");
            }
        }
    }

    public function commands()
    {

    }

    public function delete()
    {

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

    }

    public function privacy()
    {

    }

    public function profile()
    {

    }

    public function start()
    {

    }
}
