<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'title' => fake()->randomElement(['Mr.', 'Mrs.']),
            'birth_date' => fake()->date('Y-m-d', 'now'),
            'age' => fake()->numberBetween(26, 65),
            'address' => fake()->address(),
            'nationality' => fake()->randomElement(['Filipino', 'American', 'Chinese', 'Japanese']),
            'gender' => fake()->randomElement(['Male', 'Female']),
            'civil_status' => fake()->randomElement(['Single', 'Married']),
            'occupation' => fake()->jobTitle(),
            'email' => fake()->unique()->safeEmail(),
            'mobile_number_one' => fake()->phoneNumber(),
            'company_name' => fake()->company(),
            'combined_monthly_income' => fake()->randomElement(['Php 40k Below', 'Php 40k - 49,999', 'Php 50k Above']),
            'internet_connection' => fake()->randomElement(['Postpaid', 'Prepaid']),
            'owned_gadgets' => fake()->randomElement(['Desktop', 'Laptop', 'Tablet']),
            'spouse_occupation' => fake()->jobTitle(),
            'nature_of_business' => fake()->bs(),
            'property_id' => 1,
            'created_by' => 1,
            'presentation_date' => fake()->dateTimeBetween(now(), "+5 days"),
            'presentation_time' => fake()->time('H:i'),
            'exhibit_code' => fake()->randomElement(['Sales Deck', 'Palawan', 'Bora']),
            'venue_id' => fake()->numberBetween(1, 3),
            'source_prefix' => fake()->randomElement(['LSR', 'ALM', 'PRJ', 'ROI']),
            'source' => fake()->randomElement(['AA', 'BB', 'CC', 'DD', 'EE'])
        ];
    }
}
