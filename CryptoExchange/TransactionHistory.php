<?php

namespace Reelz222z\CryptoExchange;

class TransactionHistory
{
    public static function addTransaction(int $userId, string $asset, float $amount, string $transactionType, float $price): void
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO transactions (
                user_id, asset, amount, transaction_type, date, price, total
            ) VALUES (
                :user_id, :asset, :amount, :transaction_type, :date, :price, :total
            )"
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':asset' => $asset,
            ':amount' => $amount,
            ':transactionType' => $transactionType,
            ':date' => date('Y-m-d H:i:s'),
            ':price' => $price,
            ':total' => $price * $amount
        ]);
    }

    public static function getTransactions(int $userId): array
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
