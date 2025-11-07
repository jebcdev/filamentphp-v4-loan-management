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

enum PaymentStatus : string
{
    /*
        'DRAFT',                // Borrador (aún no desembolsado)
                'ACTIVE',               // Activo (vigente y al día)
                'PARTIALLY_PAID',       // Parcialmente pagado (con pagos pero saldo pendiente)
                'PAID',                 // Pagado completamente
                'OVERDUE',              // Vencido (con cuotas en mora)
                'RESTRUCTURED',         // Reestructurado
                'WRITTEN_OFF'           // Castigado (pérdida)
    */
    case draft = 'draft';           // Borrador (aún no desembolsado)
    case active = 'active';         // Activo (vigente y al día)
    case partially_paid = 'partially_paid'; // Parcialmente pagado (con pagos pero saldo pendiente)
    case paid = 'paid';             // Pagado completamente
    case overdue = 'overdue';       // Vencido (con cuotas en mora)
    case restructured = 'restructured'; // Reestructurado
    case written_off = 'written_off';   // Castigado (pérdida)

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
