<?php

namespace Reelz222z\CryptoExchange;

use PDO;

class Login
{
    public static function authenticate(string $username, string $password): ?User
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name");
        $stmt->execute([':name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === md5($password)) {
            $wallet = new Wallet((float)$user['wallet_balance']);
            $userObj = new User($user['name'], $wallet, [], (int)$user['id']);
            $userObj->setEmail($user['email']);
            $userObj->setPassword($user['password']);
            return $userObj;
        }

        return null;
    }
}
