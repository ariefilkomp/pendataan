<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Form>
 */
class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = Str::uuid();
        return [
            'id' => $id,
            'user_id' => User::first()->id,
            'table_name' => strtolower(Str::random(10)),
            'slug' => $id,
            'name' => "FORMKU ".fake()->name(),
            'description' => fake()->text(100),
        ];
    }
}
