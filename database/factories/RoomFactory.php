<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $statuses = ['disponible', 'ocupada', 'mantenimiento'];

    return [
        'name'     => 'Sala ' . $this->faker->unique()->bothify('??-###'),
        'capacity' => $this->faker->numberBetween(1, 12),
        'status'   => $this->faker->randomElement($statuses),
    ];
}

}
