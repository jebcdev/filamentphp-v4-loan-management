<?php

use App\Enums\InstallmentStatus;
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
        Schema::create('installments', function (Blueprint $table) {
            // Primary Key
            $table->id(); // Identificador único autoincremental

            // Loan Relationship
            $table->foreignId('loan_id')->nullable()->references('id')->on('loans')->onUpdate('set null')->onDelete('set null'); // Referencia al préstamo al que pertenece esta cuota

            // Installment Identification
            $table->integer('installment_number'); // Número secuencial de la cuota (1, 2, 3... N)

            // Due Dates
            $table->date('due_date'); // Fecha de vencimiento de la cuota
            $table->date('original_due_date'); // Fecha de vencimiento original (se mantiene si se reprograma)

            // Installment Breakdown - Original Amounts
            $table->decimal('principal_amount', 15, 2); // Monto de capital correspondiente a esta cuota
            $table->decimal('interest_amount', 15, 2); // Monto de interés correspondiente a esta cuota
            $table->decimal('other_charges_amount', 15, 2)->default(0); // Otros cargos (seguros, comisiones, gastos administrativos)
            $table->decimal('total_amount', 15, 2); // Monto total de la cuota (capital + interés + otros cargos)

            // Payment Tracking - Paid Amounts
            $table->decimal('paid_amount', 15, 2)->default(0); // Monto total abonado a esta cuota
            $table->decimal('principal_paid_amount', 15, 2)->default(0); // Monto de capital pagado
            $table->decimal('interest_paid_amount', 15, 2)->default(0); // Monto de interés pagado
            $table->decimal('other_charges_paid_amount', 15, 2)->default(0); // Otros cargos pagados

            // Arrears Tracking
            $table->decimal('arrears_amount', 15, 2)->default(0); // Monto de mora acumulada por pago tardío
            $table->decimal('arrears_paid_amount', 15, 2)->default(0); // Monto de mora pagada
            $table->integer('days_overdue', false, true)->default(0); // Días de mora acumulados (unsigned integer)

            // Balance
            $table->decimal('pending_balance', 15, 2)->storedAs('total_amount + arrears_amount - paid_amount'); // Saldo pendiente de la cuota (calculado automáticamente)

            // Installment Status
            $table->enum('status', InstallmentStatus::values())->default(InstallmentStatus::pending->value); // Estado actual de la cuota

            // Payment Completion
            $table->date('paid_date')->nullable(); // Fecha en que se completó el pago de esta cuota

            // Rescheduling Information
            $table->boolean('was_rescheduled')->default(false); // Indica si esta cuota fue reprogramada
            $table->foreignId('original_installment_id')->nullable()->constrained('installments')->nullOnDelete(); // Referencia a la cuota original si fue reprogramada

            // Additional Information
            $table->text('notes')->nullable(); // Observaciones sobre esta cuota específica

            // Audit Fields
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->timestamps(); // created_at (fecha de creación) y updated_at (fecha de última modificación)

            // Indexes
            $table->unique(['loan_id', 'installment_number'], 'idx_installment_loan_number'); // Índice único compuesto para evitar duplicados
            $table->index('loan_id', 'idx_installment_loan'); // Índice para búsquedas por préstamo
            $table->index('due_date', 'idx_installment_due_date'); // Índice para búsquedas por fecha de vencimiento
            $table->index('status', 'idx_installment_status'); // Índice para búsquedas por estado
            $table->index(['status', 'due_date'], 'idx_installment_status_due'); // Índice compuesto para reportes de vencimientos
            $table->index('paid_date', 'idx_installment_paid_date'); // Índice para reportes de pagos realizados
            $table->index(['loan_id', 'status'], 'idx_installment_loan_status'); // Índice compuesto para estado de cuotas por préstamo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
