<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => fake()->name(),
            'email'     => fake()->unique()->safeEmail(),
            'phone'     => fake()->regexify("[6-9]{1}[0-9]{9}"),
            'joining_date' => fake()->date(),
            'salary'       => fake()->randomFloat(2,10000.00,60000.00)
        ];
    }
}
