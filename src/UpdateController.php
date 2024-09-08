<?php

namespace src;

class UpdateController
{
    private array $update;

    public  function __construct(array $update)
    {
        $this->update = $update;
    }
    public  function getUpdateID():int
    {
        return $this->update['update_id'];
    }

    public  function getMessage():string
    {
        return $this->update['message']['text'] ?? '';
    }

    public  function getChatID():int|string
    {
        return $this->update['message']['chat']['id'] ?? '';
    }
}