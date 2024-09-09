<?php

namespace src;

use src\Exceptions\CurlError;

class TelegramBot
{
    private string $tokenBot;
    const URL_API_TG = 'https://api.telegram.org/';

    // Путь к файлу, где будет храниться последний обработанный update_id
    const OFFSET_FILE = 'offset.txt';

    public function __construct(string $tokenBot)
    {
        $this->tokenBot = $tokenBot;
    }
    public function getUpdates(int $offset):array
    {
        $ch = curl_init(self::URL_API_TG . "bot$this->tokenBot/getUpdates?offset=$offset");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $res = curl_exec($ch);
        curl_close($ch);

        if ($res === false) {
            throw new CurlError(sprintf('CurlError Exception: %s', curl_error($ch)));
        } else {
           return json_decode($res, true);
        }
    }

    public function sendMessage(int $chatID,string $message): void
    {
        $data = [
            "chat_id" 	=> $chatID,
            "text"  	=> $message,
        ];
        $ch = curl_init(self::URL_API_TG . "bot$this->tokenBot/sendMessage?" . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $res = curl_exec($ch);
        curl_close($ch);

        if ($res === false) {
            throw new CurlError(sprintf('CurlError Exception: %s', curl_error($ch)));
        }
    }

    public function getLastOffset():int
    {
        // Проверяем, существует ли файл, и читаем его содержимое
        if (file_exists(self::OFFSET_FILE)) {

            // Проверяем, прошло ли больше недели (7 дней = 604800 секунд)
            $lastModified = filemtime(self::OFFSET_FILE);
            if (time() - $lastModified > 604800) {
                // Если прошло больше недели, сбрасываем update_id
                return 0; // Это приведет к выбору новых обновлений
            }

            return (int)file_get_contents(self::OFFSET_FILE); // Читаем и приводим к числу
        }
        return 0;
    }

    // Сохраняем последний обработанный update_id, увеличенный на 1
    public function saveLastOffset(int $updateId): void
    {
        file_put_contents(self::OFFSET_FILE, $updateId + 1);
    }
}