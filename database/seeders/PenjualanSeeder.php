<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penjualan = [
            ['id' => 1, 'transaction_number' => 'Alfandy', 'marketing_Id' => 1  ,'date' => '2023-05-22', 'cargo_fee' => '25000', 'total_balance' => '3000000', 'grand_total' => '3025000' ],
        ];

        foreach($penjualan as $penjualan){
            Penjualan::create($penjualan);
        }
    }
}
