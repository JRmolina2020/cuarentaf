<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Money;

class MoneySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $monies = [
            ['name' => 'Nequi', 'status' => 1],
            ['name' => 'Bancolombia', 'status' => 1],
            ['name' => 'Daviplata', 'status' => 1],
            ['name' => 'Datafono', 'status' => 1],
        ];

        foreach ($monies as $money) {
            Money::create($money);
        }
    }
}
