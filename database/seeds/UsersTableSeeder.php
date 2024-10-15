<?php

use Illuminate\Database\Seeder;
use Helpers\SecurityHelper;

class UsersTableSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
             DB::table('users')->insert([
            ['name' => "App Admin", 'email' => 'admin@app.com','username' => "admin@app.com", 'password' => SecurityHelper::getHashedPassword('123456'),'is_verified' => 1, 'role_id' => config('roles.admin')],
        ]);
    }
}
