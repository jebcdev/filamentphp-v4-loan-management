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

enum PaymentMethod : string
{
    /*
          'CASH',           // Efectivo
                'TRANSFER',       // Transferencia bancaria
                'CHECK',          // Cheque
                'CARD',           // Tarjeta de débito/crédito
                'DEPOSIT',        // Consignación bancaria
                'MOBILE_PAYMENT', // Pago móvil (PSE, Nequi, Daviplata, etc.)
                'OTHER'           // Otro método
    */
    case cash="cash"; // Efectivo
    case transfer="transfer"; // Transferencia bancaria
    case check="check"; // Cheque
    case card="card"; // Tarjeta de débito/crédito
    case deposit="deposit"; // Consignación bancaria
    case mobile_payment="mobile_payment"; // Pago móvil (PSE, Nequi, Daviplata, etc.)
    case other="other"; // Otro método

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
