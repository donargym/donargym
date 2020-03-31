<?php

declare(strict_types=1);

namespace App\Domain;

final class PasswordGenerator
{
    public static function generatePassword(): string
    {
        $password  = "";
        $possible  = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
        for ($index = 0; $index < 16; $index++) {
            $password .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
        }
        return $password;
    }
}
