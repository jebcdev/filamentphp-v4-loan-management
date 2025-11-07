<?php

/**
 * Enum DocumentType
 *
 * Enumera los tipos de documentos de identificación aceptados en el sistema.
 *
 * Facilita la validación y registro de clientes con distintos documentos oficiales,
 * incluyendo extranjeros y casos especiales.
 *
 * Example:
 *   $doc = DocumentType::passport;
 *
 * Note:
 *   Mantener actualizado según normativas legales vigentes.
 */

namespace App\Enums;

enum DocumentType: string
{
    case citizenship_id_card = "citizenship_id_card"; // Cédula de ciudadanía
    case identity_card = "identity_card"; // Tarjeta de identidad
    case foreigner_id_card = "foreigner_id_card"; // Cédula de extranjería
    case passport = "passport"; // Pasaporte
    case tax_identification_number = "tax_identification_number"; // NIT (Número de Identificación Tributaria)
    case civil_registration = "civil_registration"; // Registro civil
    case unique_personal_identification_number = "unique_personal_identification_number"; // NUIP (Número Único de Identificación Personal)
    case national_identity_document = "national_identity_document"; // DNI (Documento Nacional de Identidad)
    case special_stay_permit = "special_stay_permit"; // Permiso especial de permanencia
    case temporary_protection_permit = "temporary_protection_permit"; // Permiso por protección temporal
    case other = "other"; // Otro

    public static function names(): array
    {
        return array_map(fn($case) => $case->name, self::cases());
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
