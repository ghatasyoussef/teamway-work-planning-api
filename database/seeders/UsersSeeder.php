<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Users
        for ($i = 0; $i < 10; $i++) {
            DB::table('users')->insert([
                'name' => "worker{$i}",
                'email' => "worker{$i}@example.com",
                'password' => Hash::make("worker{$i}@example.com"),
                'is_admin' => false,
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),

            ]);
        }

        // Admin
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin@example.com'),
            'is_admin' => true,
            'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),

        ]);
    }
}
