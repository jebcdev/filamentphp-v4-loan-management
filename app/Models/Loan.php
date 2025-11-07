<?php

namespace App\Models;

use App\Enums\PaymentFrequency;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Loan
 *
 * Representa un préstamo en el sistema de gestión de préstamos.
 * Maneja toda la información financiera, estados y relaciones del préstamo.
 */
class Loan extends Model
{
    use HasFactory;

    /**
     * Campos que pueden ser asignados masivamente.
     *
     * Incluye todos los campos de la tabla excepto id, timestamps y campos calculados.
     */
    protected $fillable = [
        'client_id',                    // ID del cliente que recibe el préstamo
        'code',                         // Código único del préstamo
        'original_amount',              // Monto original del préstamo
        'currency',                     // Código de moneda ISO 4217
        'interest_rate_percentage',     // Tasa de interés en porcentaje
        'total_installments',           // Número total de cuotas
        'payment_frequency',            // Frecuencia de pago de las cuotas
        'disbursement_date',            // Fecha de desembolso
        'first_due_date',               // Fecha de vencimiento de la primera cuota
        'principal_balance',            // Saldo pendiente de capital
        'interest_balance',             // Saldo pendiente de intereses
        'arrears_balance',              // Saldo pendiente de mora
        'total_balance',                // Saldo total pendiente
        'total_paid_amount',            // Total pagado acumulado
        'status',                       // Estado actual del préstamo
        'grace_days',                   // Días de gracia antes de mora
        'arrears_rate_percentage',      // Tasa de mora aplicable
        'allows_principal_payment',     // Permite pagos extraordinarios a capital
        'allows_early_settlement',      // Permite liquidación anticipada total
        'early_settlement_discount',    // Porcentaje de descuento por liquidación anticipada
        'settlement_date',              // Fecha de liquidación completa
        'original_loan_id',             // ID del préstamo original (para reestructuraciones)
        'notes',                        // Observaciones sobre el préstamo
        'metadata',                     // Información adicional en JSON
        'created_by',                   // Usuario que creó el registro
        'updated_by',                   // Usuario que actualizó el registro
    ];

    /**
     * Casts para los campos del modelo.
     *
     * Define cómo se deben convertir los valores al accederlos desde la base de datos.
     */
    protected $casts = [
        'payment_frequency' => 'string',    // Enum para frecuencia de pago
        'status' => 'string',                  // Enum para estado del préstamo
        'disbursement_date' => 'date',                     // Fecha de desembolso
        'first_due_date' => 'date',                        // Fecha de primera cuota
        'settlement_date' => 'date',                       // Fecha de liquidación
        'original_amount' => 'decimal:2',                  // Monto original con 2 decimales
        'interest_rate_percentage' => 'decimal:4',         // Tasa de interés con 4 decimales
        'principal_balance' => 'decimal:2',                // Saldo de capital
        'interest_balance' => 'decimal:2',                 // Saldo de intereses
        'arrears_balance' => 'decimal:2',                  // Saldo de mora
        'total_balance' => 'decimal:2',                    // Saldo total
        'total_paid_amount' => 'decimal:2',                // Total pagado
        'arrears_rate_percentage' => 'decimal:4',          // Tasa de mora
        'early_settlement_discount' => 'decimal:4',        // Descuento por liquidación anticipada
        'grace_days' => 'integer',                         // Días de gracia
        'total_installments' => 'integer',                  // Número de cuotas
        'allows_principal_payment' => 'boolean',           // Permite pagos a capital
        'allows_early_settlement' => 'boolean',            // Permite liquidación anticipada
        'metadata' => 'array',                             // Metadatos en formato array
    ];

    /**
     * Relación con el cliente que recibe el préstamo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con el usuario que creó el préstamo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el préstamo por última vez.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación con el préstamo original (en caso de reestructuración).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function originalLoan()
    {
        return $this->belongsTo(Loan::class, 'original_loan_id');
    }

    /**
     * Relación con los préstamos reestructurados a partir de este.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function restructuredLoans()
    {
        return $this->hasMany(Loan::class, 'original_loan_id');
    }

    /**
     * Relación con las cuotas del préstamo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * Relación con los pagos aplicados al préstamo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // TODO: Revisar el funcionamiento de este método
    protected static function booted()
    {
        static::creating(function ($loan) {
            if (empty($loan->code)) {
                $loan->code = 'PRE-' . date('Y') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
