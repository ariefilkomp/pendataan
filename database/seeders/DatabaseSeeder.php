<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\Question;
use App\Models\Role;
use App\Models\Section;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Role::create([
            'name' => 'admin',
        ]);

        $user->assignRole('admin');

        
        // $form = Form::factory()->create([
        //     'user_id' => $user->id
        // ]);


        // $section = Section::factory()->create([
        //     'id' => Str::uuid(),
        //     'form_id' => $form->id,
        //     'name' => $form->name,
        //     'description' => $form->description
        // ]);

        // Question::factory(10)->create([
        //     'form_id' => $form->id,
        //     'section_id' => $section->id
        // ]);

    }
}
