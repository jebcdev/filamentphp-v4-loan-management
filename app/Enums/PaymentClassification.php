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

enum PaymentClassification : string
{
    /*
'REGULAR_INSTALLMENT',    // Pago de cuota normal
                'PRINCIPAL_PAYMENT',      // Abono extraordinario a capital
                'INTEREST_PAYMENT',       // Pago solo de intereses
                'ARREARS_PAYMENT',        // Pago de mora
                'TOTAL_SETTLEMENT',       // Liquidación total del préstamo
                'EXTRAORDINARY_PAYMENT'   // Pago extraordinario
    */
    case regular_installment="regular_installment"; // Semanal
    case principal_payment="principal_payment"; // Abono extraordinario a capital
    case interest_payment="interest_payment"; // Pago solo de intereses
    case arrears_payment="arrears_payment"; // Pago de mora
    case total_settlement="total_settlement"; // Liquidación total del préstamo
    case extraordinary_payment="extraordinary_payment"; // Pago extraordinario

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
