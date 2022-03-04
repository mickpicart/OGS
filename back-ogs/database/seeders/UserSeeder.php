<?php

namespace Database\Seeders;

use App\Helpers\DataHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 12 characters randomly created password containing
        // (lower case char, upper case char, numbers, special_symbols)
        $passwrdGen = DataHelper::randomPassword(12, 1, "lower_case,upper_case,numbers,special_symbols");
        DB::table('users')->insert([
            'name' => 'Emilien',
            'email' => 'emilien.degert@gmail.com',
            'password' => Hash::make($passwrdGen),
        ]);
    }
}
