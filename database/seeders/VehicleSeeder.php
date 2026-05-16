<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        Vehicle::create([
            'license_plate' => 'D 1234 ABC',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'type' => 'MPV',
            'status' => 'active'
        ]);

        Vehicle::create([
            'license_plate' => 'B 9999 XYZ',
            'brand' => 'Mitsubishi',
            'model' => 'Pajero Sport',
            'type' => 'SUV',
            'status' => 'active'
        ]);

        Vehicle::create([
            'license_plate' => 'F 5555 DEF',
            'brand' => 'Honda',
            'model' => 'Civic Turbo',
            'type' => 'Sedan',
            'status' => 'maintenance'
        ]);
    }
}
