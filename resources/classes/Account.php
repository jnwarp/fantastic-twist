<?php

class Account
{

    public function __construct()
    {

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
                    'email' => $email
                ]
            );
        }
    }
}
