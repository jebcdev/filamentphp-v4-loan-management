<?php

use App\Enums\PaymentFrequency;
use App\Enums\PaymentStatus;
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
        Schema::create('loans', function (Blueprint $table) {
            // Primary Key
            $table->id(); // Identificador único autoincremental

            // Client Relationship
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onUpdate('set null')->onDelete('set null'); // Referencia al cliente que recibe el préstamo

            // Loan Identification
            $table->string('code', 50)->unique(); // Código único del préstamo (ej: PRE-2025-0001)

            // Principal Amount
            $table->decimal('original_amount', 15, 2); // Monto original del préstamo otorgado
            $table->string('currency', 3)->default('COP'); // Código de moneda ISO 4217 (COP, USD, etc.)

            // Interest Configuration
            $table->decimal('interest_rate_percentage', 8, 4); // Tasa de interés expresada en porcentaje (ej: 2.5 = 2.5%)

            // Payment Structure
            $table->integer('total_installments'); // Número total de cuotas del préstamo
            $table->enum('payment_frequency', PaymentFrequency::values()); // Frecuencia de pago de las cuotas

            // Important Dates
            $table->date('disbursement_date'); // Fecha en que se entregó el dinero al cliente
            $table->date('first_due_date'); // Fecha de vencimiento de la primera cuota

            // Balance Tracking
            $table->decimal('principal_balance', 15, 2); // Saldo pendiente de capital
            $table->decimal('interest_balance', 15, 2)->default(0); // Saldo pendiente de intereses
            $table->decimal('arrears_balance', 15, 2)->default(0); // Saldo pendiente de mora
            $table->decimal('total_balance', 15, 2); // Saldo total pendiente (capital + intereses + mora)
            $table->decimal('total_paid_amount', 15, 2)->default(0); // Total pagado acumulado hasta la fecha

            // Loan Status
            $table->enum('status', PaymentStatus::values())->default(PaymentStatus::draft); // Estado actual del préstamo

            // Arrears Configuration
            $table->integer('grace_days')->default(0); // Días de gracia antes de aplicar intereses de mora
            $table->decimal('arrears_rate_percentage', 8, 4)->default(0); // Tasa de mora diaria o mensual aplicable

            // Early Payment Options
            $table->boolean('allows_principal_payment')->default(true); // Permite pagos extraordinarios a capital
            $table->boolean('allows_early_settlement')->default(true); // Permite liquidación anticipada total
            $table->decimal('early_settlement_discount', 8, 4)->nullable(); // Porcentaje de descuento por liquidación anticipada

            // Settlement Information
            $table->date('settlement_date')->nullable(); // Fecha en que se liquidó completamente el préstamo

            // Restructuring Reference
            $table->foreignId('original_loan_id')->nullable()->references('id')->on('loans')->onUpdate('set null')->onDelete('set null'); // Referencia al préstamo original si este es una reestructuración
            // Additional Information
            $table->text('notes')->nullable(); // Observaciones o notas sobre el préstamo
            $table->json('metadata')->nullable(); // Información adicional en formato JSON (datos extra, configuraciones especiales)

            // Audit Fields
            // Foreign Keys (asumiendo tabla users para administradores)
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onUpdate('set null')->onDelete('set null');
            $table->timestamps(); // created_at (fecha de creación) y updated_at (fecha de última modificación)

            // Indexes
            $table->index('client_id', 'idx_loan_client'); // Índice para búsquedas por cliente
            $table->index('status', 'idx_loan_status'); // Índice para búsquedas por estado
            $table->index('disbursement_date', 'idx_loan_disbursement'); // Índice para búsquedas por fecha de desembolso
            $table->index(['status', 'client_id'], 'idx_loan_status_client'); // Índice compuesto para reportes de cartera por cliente
            $table->index('first_due_date', 'idx_loan_first_due'); // Índice para búsquedas por fecha de vencimiento
            $table->index('code', 'idx_loan_code'); // Índice para búsquedas por código (además del unique)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
