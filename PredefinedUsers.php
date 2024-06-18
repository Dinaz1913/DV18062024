<?php


require __DIR__ . '/../vendor/autoload.php';

use Reelz222z\CryptoExchange\User;
use Reelz222z\CryptoExchange\Wallet;

class PredefinedUsers
{
    public static function insertPredefinedUsers(): void
    {
        $users = [
            [
                'name' => 'Alice',
                'wallet_balance' => 1000.0,
                'email' => 'alice@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
            ],
            [
                'name' => 'Bob',
                'wallet_balance' => 1500.0,
                'email' => 'bob@example.com',
                'password' => password_hash('password456', PASSWORD_DEFAULT),
            ],
            [
                'name' => 'Charlie',
                'wallet_balance' => 2000.0,
                'email' => 'charlie@example.com',
                'password' => password_hash('password789', PASSWORD_DEFAULT),
            ]
        ];

        foreach ($users as $userData) {
            $wallet = new Wallet($userData['wallet_balance']);
            $user = new User(
                $userData['name'],
                $wallet,
                [],
                0,
                $userData['email'],
                $userData['password']
            );
            User::saveUser($user);
        }

        echo "Predefined users inserted successfully.\n";
    }
}

// Insert predefined users
PredefinedUsers::insertPredefinedUsers();
