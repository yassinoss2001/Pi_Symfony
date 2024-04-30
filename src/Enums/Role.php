<?php

namespace App\Enums;

use Doctrine\Common\Annotations\Annotation\Enum;

Enum Role
{
    public const GERANT = 'GERANT';
    public const LIVREUR = 'LIVREUR';
    public const CLIENT = 'CLIENT';

    public static function getChoices(): array
    {
        return [
            self::GERANT => 'Gerant',
            self::LIVREUR => 'Livreur',
            self::CLIENT => 'Client',
        ];
    }
}
