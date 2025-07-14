<?php

namespace Database\Seeders;

use App\Models\Marketing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marketing = [
            ['id' => 1, 'name' => 'Alfandy'],
            ['id' => 2, 'name' => 'Merry'],
            ['id' => 3, 'name' => 'Danang'],
        ];

        foreach($marketing as $marketing){
            Marketing::create($marketing);
        }
    }
}
