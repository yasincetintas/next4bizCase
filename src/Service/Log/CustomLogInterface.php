<?php


namespace App\Service\Log;


interface CustomLogInterface
{
    public function add($level, $message);
}