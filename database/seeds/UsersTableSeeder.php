<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'name' => 'Admin',
            'firstname' => 'king',
            'lastname' => 'king',
            'gender' => 0,
            'country' => 'France',
            'countryCode' => 'FR',
            'new_country' => 'France',
            'new_countryCode' => 'FR',
            'email' => 'admin@material.com',
            'password' => Hash::make('secret'),
            'phonenumber' => '',
            'email_verified_at' => now(),
            'role' => 1,
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
