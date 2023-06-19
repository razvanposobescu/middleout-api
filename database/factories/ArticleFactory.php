<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Fake some User Data
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user_ids = User::all('id')
            ->pluck('id')
            ->toArray();

        return [
            'user_id' => $this->faker->randomElement($user_ids),
            'title' => $this->faker->words(5, true),
            'body' => $this->faker->sentence(45),
            'published_at' => $this->faker->dateTime(),
        ];
    }
}
