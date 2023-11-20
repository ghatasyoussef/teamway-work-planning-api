<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Shifts

        $users = [];
        for ($i = 0; $i < 3; $i++) {
            $user = User::where('name', 'like', "%worker{$i}%")->first();
            array_push($users, $user->id);
        }

        $days = [];
        for ($i = 0; $i < 5; $i++) {
            array_push($days, Carbon::now()->addDays(10 - $i)->format('Y-m-d'));
        }
        // For the first 3 days ==> add for one shift (same # as day): 3 users
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                DB::table('user_shifts')->insert([
                    'worker_id' => $users[$j],
                    'day' => $days[$i],
                    'shift_number' => $i,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),

                ]);
            }
        }
        // For the first 3 days ==> add for one shift (same # as day): 2 users

        for ($i = 3; $i < 5; $i++) {
            for ($j = 0; $j < 2; $j++) {
                DB::table('user_shifts')->insert([
                    'worker_id' => $users[$j],
                    'day' => $days[$i],
                    'shift_number' => $i - 3,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),

                ]);
            }
        }
    }
}
