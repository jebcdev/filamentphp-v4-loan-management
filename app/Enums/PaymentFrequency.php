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

enum PaymentFrequency : string
{
    /*
       'WEEKLY',      // Semanal (cada 7 días)
                'BIWEEKLY',    // Quincenal (cada 15 días)
                'MONTHLY',     // Mensual (cada mes)
                'BIMONTHLY'    // Bimensual (cada 2 meses)
    */
    case weekly="weekly"; // Semanal
    case biweekly="biweekly"; // Quincenal
    case monthly="monthly"; // Mensual
    case bimonthly="bimonthly"; // Bimensual
    case trimesterly="trimesterly"; // Trimestral
    case semiannual="semiannual"; // Semestral
    case annual="annual"; // Anual

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
