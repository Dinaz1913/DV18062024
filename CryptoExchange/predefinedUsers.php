<?php

require __DIR__ . '/../vendor/autoload.php';

use Reelz222z\CryptoExchange\Database;
use Reelz222z\CryptoExchange\User;
use Reelz222z\CryptoExchange\Wallet;

class PredefinedUsers
{
    public static function insertPredefinedUsers(): void
    {
        $users = [
            [
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'wallet_balance' => 1000.0,
                'password' => 'password123'
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'wallet_balance' => 1500.0,
                'password' => 'password456'
            ],
            [
                'name' => 'Charlie',
                'email' => 'charlie@example.com',
                'wallet_balance' => 2000.0,
                'password' => 'password789'
            ]
        ];

        foreach ($users as $userData) {
            $wallet = new Wallet($userData['wallet_balance']);
            $hashedPassword = md5($userData['password']);
            $user = new User(
                $userData['name'],
                $wallet,
                $userData['email'],
                $hashedPassword
            );
            User::saveUser($user);
        }

        echo "Predefined users inserted successfully.\n";
    }
}

PredefinedUsers::insertPredefinedUsers();
