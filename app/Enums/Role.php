<?php


namespace App\Enums;

enum Role: string
{
    case admin = "admin"; // Administrador

    case user = "user"; // Usuario estándar

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}

?>
