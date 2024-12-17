<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function PHPSTORM_META\type;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_id' => fake()->uuid(),
            'question' => fake()->text(100),
            'type' => fake()->randomElement(['short_answer', 'paragraph', 'multiple_choice', 'checkboxes','dropdown','file', 'date', 'time']),
        ];
    }
}
