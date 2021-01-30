<?php


namespace App\Util\Validation;


interface ValidationUtilInterface
{
    public function validate(string $data, string $model): object;
}