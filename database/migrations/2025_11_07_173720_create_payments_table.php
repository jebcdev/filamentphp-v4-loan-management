<?php

use App\Enums\PaymentClassification;
use App\Enums\PaymentMethod;
use App\Enums\PayStatus;
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
        Schema::create('payments', function (Blueprint $table) {
            // Primary Key
            $table->id(); // Identificador único autoincremental

            // Payment Identification
            $table->string('code', 50)->unique(); // Código único del pago (ej: PAG-2025-0001)

            // Relationships
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onUpdate('set null')->onDelete('set null'); // Cliente que realizó el pago
            $table->foreignId('loan_id')->nullable()->references('id')->on('loans')->onUpdate('set null')->onDelete('set null'); // Préstamo al cual se aplica el pago
            $table->foreignId('installment_id')->nullable()->references('id')->on('installments')->onUpdate('set null')->onDelete('set null'); // Cuota específica (si aplica a una cuota en particular)

            // Payment Date & Time
            $table->date('payment_date'); // Fecha en que se realizó el pago
            $table->time('payment_time'); // Hora en que se realizó el pago

            // Total Amount
            $table->decimal('total_amount', 15, 2); // Monto total del pago recibido

            // Amount Distribution
            $table->decimal('principal_applied_amount', 15, 2)->default(0); // Monto aplicado al capital del préstamo
            $table->decimal('interest_applied_amount', 15, 2)->default(0); // Monto aplicado a intereses
            $table->decimal('arrears_applied_amount', 15, 2)->default(0); // Monto aplicado a mora
            $table->decimal('other_charges_applied_amount', 15, 2)->default(0); // Monto aplicado a otros cargos

            // Payment Classification
            $table->enum('payment_type', PaymentClassification::values()); // Tipo de pago realizado

            // Payment Method
            $table->enum('payment_method', PaymentMethod::values()); // Método de pago utilizado

            // Bank Information
            $table->string('external_reference', 100)->nullable(); // Número de transacción, comprobante o referencia externa
            $table->string('bank', 100)->nullable(); // Nombre del banco (si aplica)
            $table->string('account', 50)->nullable(); // Número de cuenta (si aplica)

            // Documentation
            $table->string('receipt_url', 500)->nullable(); // URL o ruta del comprobante digital escaneado

            // Payment Status
            $table->enum('status', PayStatus::values())->default(PayStatus::confirmed); // Estado del pago

            // Reversal Information
            $table->timestamp('reversed_at')->nullable(); // Fecha y hora en que se reversó el pago
            $table->text('reversal_reason')->nullable(); // Motivo de la reversión o anulación
            $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete(); // Usuario que reversó el pago

            // Receipt Information
            $table->boolean('receipt_generated')->default(false); // Indica si se generó un recibo de pago
            $table->string('receipt_number', 50)->nullable(); // Número consecutivo del recibo generado

            // Additional Information
            $table->text('notes')->nullable(); // Observaciones sobre el pago
            $table->json('metadata')->nullable(); // Información adicional en formato JSON

            // Audit Fields
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->timestamps(); // created_at (fecha de registro) y updated_at (fecha de última modificación)

            // Indexes
            $table->index('client_id', 'idx_payment_client'); // Índice para búsquedas por cliente
            $table->index('loan_id', 'idx_payment_loan'); // Índice para búsquedas por préstamo
            $table->index('installment_id', 'idx_payment_installment'); // Índice para búsquedas por cuota
            $table->index('payment_date', 'idx_payment_date'); // Índice para búsquedas y reportes por fecha
            $table->index('status', 'idx_payment_status'); // Índice para búsquedas por estado
            $table->index(['client_id', 'payment_date'], 'idx_payment_client_date'); // Índice compuesto para historial de pagos por cliente
            $table->index(['loan_id', 'payment_date'], 'idx_payment_loan_date'); // Índice compuesto para historial de pagos por préstamo
            $table->index('payment_method', 'idx_payment_method'); // Índice para reportes por método de pago
            $table->index('receipt_number', 'idx_payment_receipt'); // Índice para búsqueda de recibos
            $table->index('code', 'idx_payment_code'); // Índice para búsquedas por código

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
