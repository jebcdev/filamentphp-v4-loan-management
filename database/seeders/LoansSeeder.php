<?php

namespace Database\Seeders;

use App\Enums\InstallmentStatus;
use App\Enums\PaymentClassification;
use App\Enums\PaymentFrequency;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PayStatus;
use App\Models\Client;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener clientes existentes
        $clients = Client::all();

        if ($clients->isEmpty()) {
            $this->command->info('No clients found. Please run ClientsSeeder first.');

            return;
        }

        // Para cada cliente, crear algunos préstamos
        foreach ($clients as $client) {
            $numLoans = fake()->numberBetween(1, 3); // 1-3 préstamos por cliente

            for ($i = 0; $i < $numLoans; $i++) {
                $this->createLoanForClient($client);
            }
        }
    }

    private function createLoanForClient(Client $client)
    {
        $originalAmount = fake()->numberBetween(1000000, 10000000); // 1M a 10M COP
        $interestRate = fake()->randomFloat(2, 1, 5); // 1% a 5%
        $totalInstallments = fake()->numberBetween(6, 24); // 6 a 24 cuotas
        $paymentFrequency = fake()->randomElement(PaymentFrequency::values());
        $disbursementDate = Carbon::now()->subDays(fake()->numberBetween(0, 365));
        $firstDueDate = $this->calculateFirstDueDate($disbursementDate, $paymentFrequency);

        $loan = Loan::create([
            'client_id' => $client->id,
            'code' => 'PRE-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'original_amount' => $originalAmount,
            'currency' => 'COP',
            'interest_rate_percentage' => $interestRate,
            'total_installments' => $totalInstallments,
            'payment_frequency' => $paymentFrequency,
            'disbursement_date' => $disbursementDate->format('Y-m-d'),
            'first_due_date' => $firstDueDate->format('Y-m-d'),
            'principal_balance' => $originalAmount, // Inicialmente todo pendiente
            'total_balance' => $originalAmount, // Simplificado, sin intereses calculados aún
            'status' => fake()->randomElement(PaymentStatus::values()), // Más activos
            'grace_days' => fake()->numberBetween(0, 5),
            'arrears_rate_percentage' => fake()->randomFloat(2, 0.5, 2),
            'allows_principal_payment' => fake()->boolean(80), // 80% permite
            'allows_early_settlement' => fake()->boolean(70),
            'early_settlement_discount' => fake()->boolean(30) ? fake()->randomFloat(2, 1, 5) : null, // 30% chance
            'notes' => fake()->optional()->sentence(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // Crear cuotas para el préstamo
        $this->createInstallmentsForLoan($loan);

        // Actualizar used_credit_limit del cliente
        $client->increment('used_credit_limit', $originalAmount);
    }

    private function calculateFirstDueDate(Carbon $disbursementDate, PaymentFrequency|string $frequency): Carbon
    {
        return match ($frequency) {

            PaymentFrequency::weekly, PaymentFrequency::weekly->value => $disbursementDate->copy()->addWeek(),
            PaymentFrequency::biweekly, PaymentFrequency::biweekly->value => $disbursementDate->copy()->addWeeks(2),
            PaymentFrequency::monthly, PaymentFrequency::monthly->value => $disbursementDate->copy()->addMonth(),
            PaymentFrequency::trimesterly, PaymentFrequency::trimesterly->value => $disbursementDate->copy()->addMonths(3),
            PaymentFrequency::semiannual, PaymentFrequency::semiannual->value => $disbursementDate->copy()->addMonths(6),
            PaymentFrequency::annual, PaymentFrequency::annual->value => $disbursementDate->copy()->addYear(),
            default => $disbursementDate->copy()->addMonth(),
        };
    }

    private function createInstallmentsForLoan(Loan $loan)
    {
        $principalPerInstallment = $loan->original_amount / $loan->total_installments;
        $interestPerInstallment = ($loan->original_amount * $loan->interest_rate_percentage / 100) / $loan->total_installments;
        $totalPerInstallment = $principalPerInstallment + $interestPerInstallment;

        $dueDate = Carbon::parse($loan->first_due_date);

        for ($i = 1; $i <= $loan->total_installments; $i++) {
            $installment = Installment::create([
                'loan_id' => $loan->id,
                'installment_number' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'original_due_date' => $dueDate->format('Y-m-d'),
                'principal_amount' => round($principalPerInstallment, 2),
                'interest_amount' => round($interestPerInstallment, 2),
                'total_amount' => round($totalPerInstallment, 2),
                'status' => InstallmentStatus::pending,
                'created_by' => 1,
                'updated_by' => 1,
            ]);

            // Avanzar la fecha de vencimiento
            $dueDate = $this->advanceDueDate($dueDate, $loan->payment_frequency);

            // Para algunas cuotas, simular pagos
            if (fake()->boolean(70)) { // 70% chance de pago
                $this->createPaymentForInstallment($installment, $loan);
            }
        }
    }

    private function advanceDueDate(Carbon $date, PaymentFrequency|string $frequency): Carbon
    {
        return match ($frequency) {

            PaymentFrequency::weekly, PaymentFrequency::weekly->value => $date->copy()->addWeek(),
            PaymentFrequency::biweekly, PaymentFrequency::biweekly->value => $date->copy()->addWeeks(2),
            PaymentFrequency::monthly, PaymentFrequency::monthly->value => $date->copy()->addMonth(),
            PaymentFrequency::trimesterly, PaymentFrequency::trimesterly->value => $date->copy()->addMonths(3),
            PaymentFrequency::semiannual, PaymentFrequency::semiannual->value => $date->copy()->addMonths(6),
            PaymentFrequency::annual, PaymentFrequency::annual->value => $date->copy()->addYear(),
            default => $date->copy()->addMonth(),
        };
    }

    private function createPaymentForInstallment(Installment $installment, Loan $loan)
    {
        $paymentAmount = $installment->total_amount;
        $isFullPayment = fake()->boolean(90); // 90% paga completo

        if (! $isFullPayment) {
            $paymentAmount = fake()->randomFloat(2, $paymentAmount * 0.5, $paymentAmount);
        }

        $paymentDate = Carbon::parse($installment->due_date)->subDays(fake()->numberBetween(0, 10));

        $payment = Payment::create([
            'code' => 'PAG-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'client_id' => $loan->client_id,
            'loan_id' => $loan->id,
            'installment_id' => $installment->id,
            'payment_date' => $paymentDate->format('Y-m-d'),
            'payment_time' => fake()->time('H:i:s'),
            'total_amount' => $paymentAmount,
            'principal_applied_amount' => round($paymentAmount * 0.7, 2), // 70% a capital
            'interest_applied_amount' => round($paymentAmount * 0.3, 2), // 30% a intereses
            'payment_type' => fake()->randomElement(PaymentClassification::values()),
            'payment_method' => fake()->randomElement(PaymentMethod::values()),
            'external_reference' => fake()->optional()->uuid(),
            'bank' => fake()->optional()->company(),
            'status' => PayStatus::confirmed,
            'receipt_generated' => fake()->boolean(),
            'receipt_number' => fake()->optional()->numerify('REC-#####'),
            'notes' => fake()->optional()->sentence(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        // Actualizar la cuota como pagada si fue completo
        if ($isFullPayment) {
            $installment->update([
                'paid_amount' => $paymentAmount,
                'principal_paid_amount' => $payment->principal_applied_amount,
                'interest_paid_amount' => $payment->interest_applied_amount,
                'status' => InstallmentStatus::paid,
                'paid_date' => $paymentDate->format('Y-m-d'),
            ]);

            // Actualizar balances del préstamo
            $loan->decrement('principal_balance', $payment->principal_applied_amount);
            $loan->decrement('total_balance', $paymentAmount);
            $loan->increment('total_paid_amount', $paymentAmount);
        }
    }
}
