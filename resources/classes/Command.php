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
        } elseif (
            date_add(
                date_create($result['date']),
                date_interval_create_from_date_string(
                    $result['valid_for'] .' minutes'
                )
            ) < date_create('now')
        ) {
            $this->twilio->replySMS("Error: This event is already over, sorry.");
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
        $result = $this->event->listEvents();

        if ($result !== []) {
            $temp = "Upcoming events:\n\n";
            foreach ($result as $row) {
                $temp = $temp . '[' . substr($row['date'], 0, 16) . ']' .
                    '  ' . $row['name'] . "\n";
            }
            $this->twilio->replySMS($temp);
        } else {
            $this->twilio->replySMS("There are no upcoming events scheduled, check back later!");
        }
    }

    public function github()
    {
        $this->twilio->replySMS("Selfie Sign-In is licensed under the MIT License, view or download the code:\nhttps://github.com/jnwarp/fantastic-twist");
    }

    public function points()
    {
        $this->twilio->replySMS("You have " . $this->info['points'] . " points.");
    }

    public function postReply()
    {
        $this->twilio->postReply();
    }

    public function profile($img_id, $req_blank = false)
    {
        // exit if blank is required
        if ($req_blank && $this->info['profile'] != '') return false;

        $this->account->updateProfile($this->phone, $img_id);

        // display a different message if profile is cleared
        if ($img_id == '') {
            $twilio->replySMS("The next image sent will be set as your profile picture.");
        } else {
            $this->twilio->replySMS("Your profile picture has been updated.");
        }

        return true;
    }

    public function start()
    {
        $this->twilio->replySMS(
            "Welcome to Selfie Sign-In! Getting started is easy, just follow these steps:\n\n" .
            "1. Reply \"EMAIL xyz5000@psu.edu\" to set your email\n" .
            "2. Reply \"PROFILE\" to set your profile (optional)\n" .
            "3. Take a picture of the sign in code to sign in\n"
        );
        $this->twilio->replySMS("For more information, try typing \"?\" or \"COMMANDS\".");
    }

    public function vision($img_id)
    {
        // load the image data as base64
        $media = new Media();
        $image_data = $media->getMedia($img_id);

        // send image to vision api
        $vision = new Vision();
        $result = $vision->detectText($image_data);
        $text = $result['responses'][0]['fullTextAnnotation']['text'];

        // check for a valid code
        $keyword = explode(' ', $text);

        foreach ($keyword as $code) {
            $event = $this->event->loadEvent($code);

            if ($event !== []) {
                $this->code($code);
            }
        }

        return $result['responses'][0]['fullTextAnnotation']['text'];
    }
}
