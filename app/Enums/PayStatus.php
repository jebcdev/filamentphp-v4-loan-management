<?php

/**
 * Enum ClientType
 *
 * Define los diferentes tipos de clientes que pueden existir en el sistema.
 *
 * Permite segmentar clientes según su naturaleza (residencial, negocio, comercial, etc.)
 * y aplicar reglas o flujos personalizados para cada tipo.
 *
 * Example:
 *   $tipo = ClientType::business;
 *
 * Note:
 *   Útil para reportes y personalización de servicios.
 */

namespace App\Enums;

enum PayStatus : string
{
    /*
        'PENDING',     // Pendiente de confirmación
                'CONFIRMED',   // Confirmado y aplicado
                'REVERSED',    // Reversado
                'CANCELLED'    // Anulado
    */
    case pending = 'pending';           // Pendiente de confirmación
    case confirmed = 'confirmed';       // Confirmado y aplicado
    case reversed = 'reversed';         // Reversado
    case cancelled = 'cancelled';      // Anulado

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
