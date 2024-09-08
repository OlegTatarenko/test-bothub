<?php
declare(strict_types=1);

use src\TelegramBot;
use src\UpdateController;
use src\User;

require_once 'src/TelegramBot.php';
require_once 'src/UpdateController.php';
require_once 'src/User.php';
$pdo = require_once 'db_connect.php';

$tokenBot ='7536828296:AAGJ-_hBntw2KW4ngSyskPoF5gYAp3PfKMU';

$bot = new TelegramBot($tokenBot);

function format(int|float $number):string
{
    return number_format($number, 2, '.', '');
}

while(true) {
    try {
        // Получаем последний offset(update_id)
        $offset = $bot->getLastOffset();

        $updates = $bot->getUpdates($offset);

        if ($updates['ok'] && !empty($updates['result'])) {
            // Обрабатываем каждое обновление
            foreach ($updates['result'] as $update) {
                $updateController = new UpdateController($update);

                // Сохраняем последний обработанный update_id
                $bot->saveLastOffset($updateController->getUpdateID());

                $message = $updateController->getMessage();
                $chatId = (int) $updateController->getChatID();

                $user = new User($message, $chatId, $pdo);
                $userBalance = $user->getUserBalance();

                $amount = floatval($user->msgIsNumber());

                if ($amount) {
                    if ($userBalance + $amount >= 0) {
                        $msg = "Ваш текущий баланс: " . format($user->getUserBalance()) . "$ \nВы указали: $amount \n";
                        $user->updateUserBalance($amount);
                        $msg .= "Теперь ваш баланс: " . format($user->getUserBalance()) . "$ \n";

                    } else {
                        $msg = 'Ваш текущий баланс: ' . format($user->getUserBalance()) .
                            '$. У вас недостаточно средств для списания суммы в размере ' . $amount . "$\n";

                    }
                } else {
                    $msg = 'Вы ввели 0 или не число, ваш баланс не изменился и равен ' . format($user->getUserBalance()) . "$\n";

                }
                $bot->sendMessage($chatId, $msg);
            }
        } else {
//            echo "Новых сообщений нет.\n";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }

sleep(1);
}








