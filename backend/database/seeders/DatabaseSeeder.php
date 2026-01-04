<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Car;
use App\Models\Trip;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $driver1 = User::create([
            'name' => 'Иван Петров',
            'email' => 'driver1@company.com',
            'password' => Hash::make('password'),
            'position' => 'driver',
        ]);

        $driver2 = User::create([
            'name' => 'Сергей Иванов',
            'email' => 'driver2@company.com',
            'password' => Hash::make('password'),
            'position' => 'driver',
        ]);

        $director = User::create([
            'name' => 'Алексей Смирнов',
            'email' => 'director@company.com',
            'password' => Hash::make('password'),
            'position' => 'director',
            'allowed_categories' => json_encode(['first', 'second', 'third']),
        ]);

        $manager = User::create([
            'name' => 'Мария Кузнецова',
            'email' => 'manager@company.com',
            'password' => Hash::make('password'),
            'position' => 'manager',
            'allowed_categories' => json_encode(['second', 'third']),
        ]);

        $employee = User::create([
            'name' => 'Петр Сидоров',
            'email' => 'employee@company.com',
            'password' => Hash::make('password'),
            'position' => 'employee',
            'allowed_categories' => json_encode(['third']),
        ]);

        $cars = [
            [
                'model' => 'Toyota Camry',
                'comfort_category' => 'first',
                'driver_id' => $driver1->id,
            ],
            [
                'model' => 'Toyota Corolla',
                'comfort_category' => 'second',
                'driver_id' => $driver2->id,
            ],
            [
                'model' => 'Kia Rio',
                'comfort_category' => 'third',
                'driver_id' => $driver1->id,
            ],
            [
                'model' => 'Mercedes E-class',
                'comfort_category' => 'first',
                'driver_id' => $driver2->id,
            ],
            [
                'model' => 'Hyundai Solaris',
                'comfort_category' => 'third',
                'driver_id' => $driver1->id,
            ],
        ];

        foreach ($cars as $carData) {
            Car::create($carData);
        }

        $camry = Car::where('model', 'Toyota Camry')->first();
        $mercedes = Car::where('model', 'Mercedes E-class')->first();

        Trip::create([
            'car_id' => $camry->id,
            'user_id' => $director->id,
            'start_time' => now()->addDay()->setTime(9, 0),
            'end_time' => now()->addDay()->setTime(12, 0),
        ]);

        Trip::create([
            'car_id' => $mercedes->id,
            'user_id' => $manager->id,
            'start_time' => now()->setTime(14, 0),
            'end_time' => now()->setTime(18, 0),
        ]);

        $this->command->info('Директор: director@company.com / password');
        $this->command->info('Менеджер: manager@company.com / password');
        $this->command->info('Сотрудник: employee@company.com / password');
    }
}
