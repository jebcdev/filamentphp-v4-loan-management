<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
        ];
    }

    /**
     * Relación con los clientes creados por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdClients()
    {
        return $this->hasMany(Client::class, 'created_by');
    }

    /**
     * Relación con los préstamos creados por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdLoans()
    {
        return $this->hasMany(Loan::class, 'created_by');
    }

    /**
     * Relación con las cuotas creadas por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdInstallments()
    {
        return $this->hasMany(Installment::class, 'created_by');
    }

    /**
     * Relación con los pagos creados por el usuario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdPayments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }
}
