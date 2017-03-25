<?php

class Account
{

    public function __construct()
    {

    }

    public function deleteAccount($phone)
    {
        $connect = new Connect();
        $connect->simpleDelete(
            'account',
            'phone',
            $phone
        );
        $connect->close();
    }

    public function updateEmail($phone, $email)
    {
        $connect = new Connect();
        if ($connect->simpleSelectCount('account', 'phone', $phone)) {
            $connect->simpleUpdate(
                'account',
                'email',
                $email,
                'phone',
                $phone
            );
        } else {
            $connect->simpleInsert(
                'account',
                [
                    'phone' => $phone,
                    'email' => $email,
                    'events' => '',
                    'profile' => '*',
                    'points' => 0
                ]
            );
        }
        $connect->close();
    }

    public function updateEvents($phone, $events, $points)
    {
        $connect = new Connect();
        $connect->simpleUpdate(
            'account',
            'events',
            $events,
            'phone',
            $phone
        );
        $connect->simpleUpdate(
            'account',
            'points',
            $points,
            'phone',
            $phone
        );
        $connect->close();
    }

    public function updateProfile($phone, $img_id)
    {
        $connect = new Connect();
        if ($connect->simpleSelectCount('account', 'phone', $phone)) {
            $connect->simpleUpdate(
                'account',
                'profile',
                $img_id,
                'phone',
                $phone
            );
        } else {
            $connect->simpleInsert(
                'account',
                [
                    'phone' => $phone,
                    'email' => '',
                    'profile' => $img_id
                ]
            );
        }
        $connect->close();
    }

    public function getInfo($phone)
    {
        $connect = new Connect();
        $results = $connect->simpleSelect(
            'account',
            'phone',
            $phone
        );
        $connect->close();

        return $results;
    }
}
