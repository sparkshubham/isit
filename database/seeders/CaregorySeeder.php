<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CaregorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'name' => 'Singing/vocalist',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Keyboardist',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Drummer',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'String Player',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Dancer',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Percussionist',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Horn Player',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Motivational/Inspirational Speaker',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Photographer/Videographer/Graphic Designer',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => 'Other',
            'status' => 1,
            'type' => 0,
        ]);
        Category::create([
            'name' => "It's Spam",
            'status' => 1,
            'type' => 1,
        ]);
        Category::create([
            'name' => "It's Inappropiate",
            'status' => 1,
            'type' => 1,
        ]);
        Category::create([
            'name' => "It' Abusive",
            'status' => 1,
            'type' => 1,
        ]);
        Category::create([
            'name' => "Other",
            'status' => 1,
            'type' => 1,
        ]);
        Category::create([
            'name' => "Not able to Post.",
            'status' => 1,
            'type' => 2,
        ]);
        Category::create([
            'name' => "My visbility not working.",
            'status' => 1,
            'type' => 2,
        ]);
        Category::create([
            'name' => "Other",
            'status' => 1,
            'type' => 2,
        ]);
        Category::create([
            'name' => "Automotive and Transport",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Business and Finance",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Chemicals and Materials",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Consumer Goods and Services",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Food and Beverage",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Government and Public Sector",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Healthcare",
            'status' => 1,
            'type' => 3,
        ]);
        Category::create([
            'name' => "Manufacturing and Construction",
            'status' => 1,
            'type' => 3,
        ]);

    }
}
