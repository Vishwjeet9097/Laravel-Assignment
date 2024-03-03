<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;


class ProjectNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::insert([
        ['name' => 'UI & UX'],
        ['name' => 'Backend Development'],
        ['name' => 'Mobile App Development'],
    ]);


    }
}