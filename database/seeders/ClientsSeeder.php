<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Enums\ClientStatus;
use App\Enums\DocumentType;
use App\Enums\Gender;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $maxCreditLimit = fake()->numberBetween(5000, 50000);
            $usedCreditLimit = fake()->numberBetween(0, $maxCreditLimit);

            Client::create([
                'full_name' => fake()->name(),
                'document_type' => fake()->randomElement(DocumentType::values()),
                'document_number' => fake()->unique()->numerify('##########'),
                'birth_date' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
                'address' => fake()->address(),
                'city' => fake()->city(),
                'phone' => fake()->unique()->phoneNumber(),
                'secondary_phone' => fake()->optional()->phoneNumber(),
                'email' => fake()->unique()->safeEmail(),
                'gender' => fake()->randomElement(Gender::values()),
                'occupation' => fake()->jobTitle(),
                'monthly_income' => fake()->numberBetween(1000000, 10000000), // Ajustado para pesos colombianos, por ejemplo
                'max_credit_limit' => $maxCreditLimit,
                'used_credit_limit' => $usedCreditLimit,
                'status' => fake()->randomElement(ClientStatus::values()),
                'personal_references' => json_encode([
                    [
                        'name' => fake()->name(),
                        'phone' => fake()->phoneNumber(),
                        'relationship' => fake()->randomElement(['amigo', 'familiar', 'colega'])
                    ],
                    [
                        'name' => fake()->name(),
                        'phone' => fake()->phoneNumber(),
                        'relationship' => fake()->randomElement(['amigo', 'familiar', 'colega'])
                    ]
                ]),
                'notes' => fake()->optional()->sentence(),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
