<?php


namespace App\Helper;


class TextHelper
{
    /**
     * @param string $text
     *
     * @return string
     */
    public static function makeSnakeCase(string $text): string
    {
        if (!trim($text)) {
            return $text;
        }

        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $text));
    }
}