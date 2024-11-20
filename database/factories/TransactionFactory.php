<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer = Customer::inRandomOrder()->first();

        // Generate a created_at date within the current year
        $createdAt = $this->faker->dateTimeThisYear;

        return [
            'customer_id' => $customer->id,
            'particulars' => $this->faker->sentence, // Random particulars (optional)
            'amount' => $this->faker->randomFloat(2, 10, 1000), // Random amount between 10 and 1000
            'type' => $this->faker->randomElement(['debit', 'credit']),
            'datetime' => $this->faker->dateTimeBetween(
                $createdAt,
                Carbon::now() // Ensure datetime is after created_at
            ),
            'created_at' => $createdAt,
            'updated_at' => $this->faker->dateTimeBetween(
                $createdAt,
                Carbon::now() // Ensure updated_at is after created_at
            ),
            'deleted_at' => null, // Soft delete is null by default
        ];
    }
}