<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition()
    {
        $specializations = [
            'Batsman', 'Bowler', 'All-rounder', 'Wicket Keeper',
            'Fast Bowler', 'Spin Bowler', 'Opening Batsman', 'Middle Order Batsman'
        ];

        return [
            'name' => $this->faker->name,
            'unique_username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'specialization' => $this->faker->randomElement($specializations),
            'experience_years' => $this->faker->numberBetween(0, 20),
            'base_price' => $this->faker->randomFloat(2, 1000, 50000),
            'description' => $this->faker->paragraph,
            'avatar' => 'https://picsum.photos/seed/' . $this->faker->unique()->word . '/400/400.jpg',
            'is_active' => true,
        ];
    }
}
