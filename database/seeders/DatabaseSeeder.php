<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seeding a specific user without a factory
        // User::create([
        //     'name' => 'Satish Kumar Sharma',
        //     'email' => 'abcc0abc5@gmail.com',
        //     'password' => Hash::make('S@tish555@sk'), // Be sure to hash the password
        //     // other fields as needed...
        // ]);


        // DB::table('users')->insert([
        //     [
        //         'name' => 'User 1',
        //         'email' => 'user1@example.com',
        //         'password' => Hash::make('password123'),
        //     ],
        //     [
        //         'name' => 'User 2',
        //         'email' => 'user2@example.com',
        //         'password' => Hash::make('password456'),
        //     ],
        //     // Add more users as needed
        // ]);

        // Customer::factory(5)->create();

        // Transaction::factory(50)->create();

        Customer::factory(50)->create()->each(function ($customer) {
            // Each customer gets between 5-10 transactions
            Transaction::factory(rand(5, 10))->create([
                'customer_id' => $customer->id,
            ]);
        });

        $this->command->info('Database seeding completed successfully.');
    }
}