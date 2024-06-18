<?php

namespace Reelz222z\CryptoExchange;

use PDO;

class User
{
    private string $name;
    private Wallet $wallet;
    private string $email;
    private string $password;
    private int $id;

    public function __construct(
        string $name,
        Wallet $wallet,
        string $email,
        string $password,
        int $id = 0
    ) {
        $this->name = $name;
        $this->wallet = $wallet;
        $this->email = $email;
        $this->password = $password;
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function saveUser(self $user): void
    {
        $pdo = Database::getInstance()->getConnection();
        if ($user->getId() === 0) {
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, wallet_balance, password) 
                VALUES (:name, :email, :wallet_balance, :password)"
            );
            $stmt->execute([
                ':name' => $user->getName(),
                ':email' => $user->getEmail(),
                ':wallet_balance' => $user->getWallet()->getBalance(),
                ':password' => $user->getPassword()
            ]);
            $user->id = (int)$pdo->lastInsertId();
        } else {
            $stmt = $pdo->prepare(
                "UPDATE users SET wallet_balance = :wallet_balance WHERE id = :id"
            );
            $stmt->execute([
                ':wallet_balance' => $user->getWallet()->getBalance(),
                ':id' => $user->getId()
            ]);
        }
    }
}
