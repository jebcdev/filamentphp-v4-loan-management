<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Payment
 *
 * Representa un pago realizado en el sistema de gestión de préstamos.
 * Maneja toda la información financiera y de procesamiento de pagos.
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * Campos que pueden ser asignados masivamente.
     *
     * Incluye todos los campos de la tabla excepto id y timestamps.
     */
    protected $fillable = [
        'code',                             // Código único del pago
        'client_id',                        // ID del cliente que realizó el pago
        'loan_id',                          // ID del préstamo al cual se aplica el pago
        'installment_id',                   // ID de la cuota específica (si aplica)
        'payment_date',                     // Fecha en que se realizó el pago
        'payment_time',                     // Hora en que se realizó el pago
        'total_amount',                     // Monto total del pago recibido
        'principal_applied_amount',         // Monto aplicado al capital del préstamo
        'interest_applied_amount',          // Monto aplicado a intereses
        'arrears_applied_amount',           // Monto aplicado a mora
        'other_charges_applied_amount',     // Monto aplicado a otros cargos
        'payment_type',                     // Tipo de pago realizado
        'payment_method',                   // Método de pago utilizado
        'external_reference',               // Número de transacción o referencia externa
        'bank',                             // Nombre del banco (si aplica)
        'account',                          // Número de cuenta (si aplica)
        'receipt_url',                      // URL del comprobante digital
        'status',                           // Estado del pago
        'reversed_at',                      // Fecha y hora de reversión
        'reversal_reason',                  // Motivo de la reversión
        'reversed_by',                      // Usuario que reversó el pago
        'receipt_generated',                // Indica si se generó un recibo
        'receipt_number',                   // Número del recibo generado
        'notes',                            // Observaciones sobre el pago
        'metadata',                         // Información adicional en JSON
        'created_by',                       // Usuario que creó el registro
        'updated_by',                       // Usuario que actualizó el registro
    ];

    /**
     * Casts para los campos del modelo.
     *
     * Define cómo se deben convertir los valores al accederlos desde la base de datos.
     */
    protected $casts = [
        'payment_date' => 'date',                           // Fecha del pago
        'payment_time' => 'datetime',                       // Hora del pago (como datetime)
        'reversed_at' => 'datetime',                        // Fecha y hora de reversión
        'total_amount' => 'decimal:2',                      // Monto total
        'principal_applied_amount' => 'decimal:2',          // Capital aplicado
        'interest_applied_amount' => 'decimal:2',           // Intereses aplicados
        'arrears_applied_amount' => 'decimal:2',            // Mora aplicada
        'other_charges_applied_amount' => 'decimal:2',      // Otros cargos aplicados
        'payment_type' => 'string',                         // Tipo de pago (enum como string)
        'payment_method' => 'string',                       // Método de pago (enum como string)
        'status' => 'string',                               // Estado del pago (enum como string)
        'receipt_generated' => 'boolean',                   // Indicador de recibo generado
        'metadata' => 'array',                              // Metadatos en formato array
    ];

    /**
     * Relación con el cliente que realizó el pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con el préstamo al cual se aplica el pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Relación con la cuota específica a la que se aplica el pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    /**
     * Relación con el usuario que creó el pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó el pago por última vez.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación con el usuario que reversó el pago.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reverser()
    {
        return $this->belongsTo(User::class, 'reversed_by');
    }
}
