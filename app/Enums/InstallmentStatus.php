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

enum InstallmentStatus : string
{
    /*
 'PENDING',           // Pendiente de pago
                'PARTIALLY_PAID',    // Pagada parcialmente
                'PAID',              // Pagada completamente
                'OVERDUE',           // Vencida (con mora)
                'RESCHEDULED',       // Reprogramada
                'FORGIVEN'           // Condonada
    */
    case pending="pending"; // Pendiente de pago
    case partially_paid="partially_paid"; // Pagada parcialmente
    case paid="paid"; // Pagada completamente
    case overdue="overdue"; // Vencida (con mora)
    case rescheduled="rescheduled"; // Reprogramada
    case forgiven="forgiven"; // Condonada

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
