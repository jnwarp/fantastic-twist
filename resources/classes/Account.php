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
            echo 'simple update';
            $connect->simpleUpdate(
                'account',
                'email',
                $email,
                'phone',
                $phone
            );
        } else {
            echo 'simple insert';
            $connect->simpleInsert(
                'account',
                [
                    'phone' => $phone,
                    'email' => $email,
                    'profile' => ''
                ]
            );
        }
    }
}
