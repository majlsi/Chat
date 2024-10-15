<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SectionsAppSeeder::class);
    }
}

class SectionsAppSeeder extends Seeder
{

    public function run()
    {

        $this->call(AppsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ModulesTableSeeder::class);
        $this->call(RightsTableSeeder::class);
        $this->call(MessageTypesTableSeeder::class);
    }
}