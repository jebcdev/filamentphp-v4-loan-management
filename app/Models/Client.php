<?php

namespace App\Models;

use App\Enums\ClientStatus;
use App\Enums\DocumentType;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'document_type',
        'document_number',
        'birth_date',
        /*  */
        'address',
        'city',
        'phone',
        'secondary_phone',
        'email',
        /*  */
        'gender',
        'occupation',
        'monthly_income',
        /*  */
        'max_credit_limit',
        'used_credit_limit',
        /*  */
        'status',
        /*  */
        'personal_references',
        'notes',
        /*  */
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_type' => "string",
        'gender' => "string",
        'status' => "string",
        'personal_references' => 'array',
        'birth_date' => 'date',
        'monthly_income' => 'decimal:2',
        'max_credit_limit' => 'decimal:2',
        'used_credit_limit' => 'decimal:2',
        'available_credit_limit' => 'decimal:2',
    ];

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
}
