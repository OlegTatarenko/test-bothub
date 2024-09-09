<?php

namespace src;

use PDO;

class User
{
    private string $message;
    private int $chatId;
    public PDO $pdo;

    public function __construct(string $message, int $chatId, PDO $pdo)
    {
        $this->message = $message;
        $this->chatId = $chatId;
        $this->pdo = $pdo;
    }

    public function getUserBalance():float
    {
        $res = $this->pdo->prepare('SELECT * FROM users WHERE chat_id=:chat_id');
        $res->execute(['chat_id' => $this->chatId]);
        $user = $res->fetch();

        if ($user == null) {
            $res = $this->pdo->prepare('INSERT INTO users SET  chat_id=:chat_id, balance=:balance');
            $res->execute([
                'chat_id' => $this->chatId,
                'balance' => 0.00,
            ]);

            $res = $this->pdo->prepare('SELECT * FROM users WHERE chat_id=:chat_id');
            $res->execute(['chat_id' => $this->chatId]);
            $user = $res->fetch();
        }
        return $user['balance'];
    }

    public function updateUserBalance(float $amount): void
    {
        $res = $this->pdo->prepare('UPDATE users SET balance=:balance WHERE chat_id=:chat_id');
        $balance = $this->getUserBalance();
        $balance += $amount;
        $res->execute([
            'chat_id' => $this->chatId,
            'balance' => $balance,
        ]);
    }

    public function msgIsNumber():float|false
    {
        $userMessage = str_replace(',', '.', $this->message);
        if (is_numeric($userMessage)) {
            $number = floatval($userMessage);
            return number_format($number, 2, '.', '');
        } else {
            return false;
        }
    }
}