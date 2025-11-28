<?php

namespace App\Models;

use App\Enums\ClientStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'document_type',
        'document_number',
        'birth_date',
        /* */
        'address',
        'city',
        'phone',
        'secondary_phone',
        'email',
        /* */
        'gender',
        'occupation',
        'monthly_income',
        /* */
        'max_credit_limit',
        'used_credit_limit',
        /* */
        'status',
        /* */
        'personal_references',
        'notes',
        /* */
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_type' => 'string',
        'gender' => 'string',
        'status' => 'string',
        'personal_references' => 'array',
        'birth_date' => 'date',
        'monthly_income' => 'decimal:2',
        'max_credit_limit' => 'decimal:2',
        'used_credit_limit' => 'decimal:2',
        'available_credit_limit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relación con los préstamos del cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Relación con los pagos realizados por el cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope para clientes activos.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', ClientStatus::active->value);
    }

    /**
     * Scope para clientes inactivos.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', ClientStatus::inactive->value);
    }

    /**
     * Scope para clientes bloqueados.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', ClientStatus::blocked->value);
    }

    /**
     * Obtiene estadísticas de clientes agrupados por status.
     *
     * Este método utiliza los scopes definidos para contar y obtener
     * clientes activos, inactivos y bloqueados. Es útil para reportes
     * o dashboards que requieren una visión general del estado de los clientes.
     *
     * @return array{
     *     active_count: int,
     *     inactive_clients: \Illuminate\Database\Eloquent\Collection,
     *     blocked_clients: \Illuminate\Database\Eloquent\Collection
     * }
     */
    public static function getClientStatusStats(): array
    {
        return [
            'active_count' => self::active()->count(),
            'inactive_clients' => self::inactive()->get(),
            'blocked_clients' => self::blocked()->get(),
        ];
    }

    /* **************************************** */

    /**
     * Obtiene el estado del cliente como enum.
     */
    public function getStatus(): ClientStatus
    {
        return ClientStatus::from($this->status);
    }

    /**
     * Verifica si el cliente está activo.
     *
     * Un cliente activo puede participar en operaciones como solicitar préstamos.
     * Este método es útil para validaciones rápidas en lógica de negocio.
     *
     * @return bool True si el cliente tiene status 'active', false en caso contrario.
     */
    public function isActive(): bool
    {
        return $this->getStatus() === ClientStatus::active;
    }

    /**
     * Verifica si el cliente está inactivo.
     *
     * Un cliente inactivo no puede realizar ciertas operaciones hasta que sea reactivado.
     * Este método ayuda a implementar restricciones basadas en el status.
     *
     * @return bool True si el cliente tiene status 'inactive', false en caso contrario.
     */
    public function isInactive(): bool
    {
        return $this->getStatus() === ClientStatus::inactive;
    }

    /**
     * Verifica si el cliente está bloqueado.
     *
     * Un cliente bloqueado tiene restricciones severas y no puede acceder a servicios.
     * Útil para rechazar automáticamente solicitudes de clientes con problemas previos.
     *
     * @return bool True si el cliente tiene status 'blocked', false en caso contrario.
     */
    public function isBlocked(): bool
    {
        return $this->getStatus() === ClientStatus::blocked;
    }

    /* Calcular edad */

    /**
     * Verifica si el cliente es mayor de edad en Colombia.
     *
     * En Colombia, la mayoría de edad se alcanza a los 18 años.
     * Este método calcula la edad basada en la fecha de nacimiento
     * y compara con 18 años. Es útil para validaciones legales
     * en procesos como solicitudes de préstamos.
     *
     * @return bool True si el cliente tiene 18 años o más, false en caso contrario.
     */
    public function isAdult(): bool
    {
        return $this->birth_date && $this->birth_date->age >= 18;
    }
}
