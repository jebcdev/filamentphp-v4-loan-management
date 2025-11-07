<?php

namespace App\Models;

use App\Enums\InstallmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Installment
 *
 * Representa una cuota individual de un préstamo en el sistema.
 * Maneja toda la información financiera, pagos y estados de cada cuota.
 */
class Installment extends Model
{
    use HasFactory;

    /**
     * Campos que pueden ser asignados masivamente.
     *
     * Incluye todos los campos de la tabla excepto id, timestamps y campos calculados.
     */
    protected $fillable = [
        'loan_id',                      // ID del préstamo al que pertenece la cuota
        'installment_number',           // Número secuencial de la cuota
        'due_date',                     // Fecha de vencimiento de la cuota
        'original_due_date',            // Fecha de vencimiento original
        'principal_amount',             // Monto de capital de la cuota
        'interest_amount',              // Monto de interés de la cuota
        'other_charges_amount',         // Otros cargos (seguros, comisiones, etc.)
        'total_amount',                 // Monto total de la cuota
        'paid_amount',                  // Monto total pagado
        'principal_paid_amount',        // Monto de capital pagado
        'interest_paid_amount',         // Monto de interés pagado
        'other_charges_paid_amount',    // Otros cargos pagados
        'arrears_amount',               // Monto de mora acumulada
        'arrears_paid_amount',          // Monto de mora pagada
        'days_overdue',                 // Días de mora acumulados
        'status',                       // Estado actual de la cuota
        'paid_date',                    // Fecha de pago completo
        'was_rescheduled',              // Indica si fue reprogramada
        'original_installment_id',      // ID de la cuota original (si reprogramada)
        'notes',                        // Observaciones sobre la cuota
        'created_by',                   // Usuario que creó el registro
        'updated_by',                   // Usuario que actualizó el registro
    ];

    /**
     * Casts para los campos del modelo.
     *
     * Define cómo se deben convertir los valores al accederlos desde la base de datos.
     */
    protected $casts = [
        'due_date' => 'date',                           // Fecha de vencimiento
        'original_due_date' => 'date',                  // Fecha de vencimiento original
        'paid_date' => 'date',                          // Fecha de pago completo
        'principal_amount' => 'decimal:2',              // Monto de capital
        'interest_amount' => 'decimal:2',               // Monto de interés
        'other_charges_amount' => 'decimal:2',          // Otros cargos
        'total_amount' => 'decimal:2',                  // Monto total
        'paid_amount' => 'decimal:2',                   // Monto pagado total
        'principal_paid_amount' => 'decimal:2',         // Capital pagado
        'interest_paid_amount' => 'decimal:2',          // Interés pagado
        'other_charges_paid_amount' => 'decimal:2',     // Otros cargos pagados
        'arrears_amount' => 'decimal:2',                // Monto de mora
        'arrears_paid_amount' => 'decimal:2',           // Mora pagada
        'pending_balance' => 'decimal:2',               // Saldo pendiente (calculado)
        'days_overdue' => 'integer',                    // Días de mora
        'installment_number' => 'integer',              // Número de cuota
        'was_rescheduled' => 'boolean',                 // Indicador de reprogramación
        'status' => 'string',           // Enum para estado de la cuota
    ];

    /**
     * Relación con el préstamo al que pertenece la cuota.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Relación con el usuario que creó la cuota.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el usuario que actualizó la cuota por última vez.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación con la cuota original (en caso de reprogramación).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function originalInstallment()
    {
        return $this->belongsTo(Installment::class, 'original_installment_id');
    }

    /**
     * Relación con las cuotas reprogramadas a partir de esta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rescheduledInstallments()
    {
        return $this->hasMany(Installment::class, 'original_installment_id');
    }

    /**
     * Relación con los pagos aplicados a esta cuota.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }


    protected static function booted()
    {
        static::creating(function ($installment) {
            if (empty($installment->installment_number)) {
                $maxNumber = static::where('loan_id', $installment->loan_id)->max('installment_number') ?? 0;
                $installment->installment_number = $maxNumber + 1;
            }
        });
    }
}
