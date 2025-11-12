<?php

use App\Enums\ClientStatus;
use App\Enums\DocumentType;
use App\Enums\Gender;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            // Primary Key
            $table->id(); // Identificador único autoincremental

            // Basic Information
            $table->string('full_name', 200); // Nombre completo del cliente
            $table->enum('document_type', DocumentType::values())->default(DocumentType::citizenship_id_card); // Tipo de documento de identidad
            $table->string('document_number', 50); // Número de documento de identidad
            $table->date('birth_date')->nullable(); // Fecha de nacimiento

            // Contact Information
            $table->string('address', 255)->nullable(); // Dirección de residencia
            $table->string('city', 100)->nullable(); // Ciudad de residencia
            $table->string('phone', 20)->nullable()->unique(); // Teléfono principal de contacto
            $table->string('secondary_phone', 20)->nullable(); // Teléfono alternativo de contacto
            $table->string('email', 100)->nullable()->unique(); // Correo electrónico

            // Personal Information
            $table->enum('gender', Gender::values())->nullable(); // Género del cliente
            $table->string('occupation', 100)->nullable(); // Ocupación o profesión
            $table->decimal('monthly_income', 15, 2)->nullable(); // Ingreso mensual estimado

            // Credit Limit Information
            $table->decimal('max_credit_limit', 15, 2)->default(0); // Cupo máximo autorizado para préstamos
            $table->decimal('used_credit_limit', 15, 2)->default(0); // Cupo actualmente en uso (suma de préstamos activos)
            $table->decimal('available_credit_limit', 15, 2)->storedAs('max_credit_limit - used_credit_limit'); // Cupo disponible (calculado automáticamente)

            // Status
            $table->enum('status', ClientStatus::values())->default(ClientStatus::active); // Estado del cliente en el sistema

            // Additional Information
            $table->text('personal_references')->nullable(); // Referencias personales (text: nombre, teléfono, parentesco)
            $table->text('notes')->nullable(); // Observaciones o notas adicionales sobre el cliente

            // Audit Fields
            $table->timestamps(); // created_at (fecha de creación) y updated_at (fecha de última modificación)
            $table->softDeletes(); // deleted_at (fecha de eliminación suave)

            // Indexes
            $table->unique(['document_type', 'document_number'], 'idx_client_document'); // Índice único para tipo y número de documento
            $table->index('status', 'idx_client_status'); // Índice para búsquedas por estado
            $table->index('full_name', 'idx_client_name'); // Índice para búsquedas por nombre
            $table->index('email', 'idx_client_email'); // Índice para búsquedas por email

            // Foreign Keys (asumiendo tabla users para administradores)
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
