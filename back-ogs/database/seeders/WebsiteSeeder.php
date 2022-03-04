<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $websites = [
            ['url' => 'https://www.ambroiseprince.fr'],
            ['url' => 'http://www.aucoeurdesjumeaux.fr'],
            ['url' => 'http://starter.agencebcd.fr'],
            ['url' => 'https://www.agencebcd.fr'],
            ['url' => 'https://www.assistante-sociale.re']
        ];

        DB::table('websites')->insert($websites);
    }
}
