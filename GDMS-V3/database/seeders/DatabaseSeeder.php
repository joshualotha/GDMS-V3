<?php

namespace Database\Seeders;

use App\Models\CylinderType;
use App\Models\FuelStock;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gdms.com',
            'password' => 'password',
        ]);

        CylinderType::create(['name' => '6kg', 'size_kg' => 6, 'full_sale_cost' => 0, 'full_sale_price' => 0, 'refill_cost' => 0, 'refill_price' => 0]);
        CylinderType::create(['name' => '13kg', 'size_kg' => 13, 'full_sale_cost' => 0, 'full_sale_price' => 0, 'refill_cost' => 0, 'refill_price' => 0]);
        CylinderType::create(['name' => '50kg', 'size_kg' => 50, 'full_sale_cost' => 0, 'full_sale_price' => 0, 'refill_cost' => 0, 'refill_price' => 0]);

        FuelStock::create(['fuel_type' => 'diesel', 'litres' => 0]);
        FuelStock::create(['fuel_type' => 'petrol', 'litres' => 0]);
    }
}
