<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managers = [
            ['name' => 'Grzegorz Galon', 'department' => 'Sales'],
            ['name' => 'Filip Jeżycki', 'department' => 'Sales'],
            ['name' => 'Konrad Czajkowski', 'department' => 'Sales'],
            ['name' => 'Kamil Kalicki', 'department' => 'Sales'],
            ['name' => 'Mikołaj Sobczak', 'department' => 'Sales'],
            ['name' => 'Katarzyna Kowalska', 'department' => 'Sales'],
            ['name' => 'Ewa Piechowiak', 'department' => 'Growth'],
            ['name' => 'Piotr Zaborski', 'department' => 'Growth'],
            ['name' => 'Wioleta Pawłowska', 'department' => 'Growth'],
            ['name' => 'Adam Grodziński', 'department' => 'Production'],
            ['name' => 'Łukasz Kwiatkowski', 'department' => 'Production'],
            ['name' => 'Mateusz Styś', 'department' => 'Production'],
            ['name' => 'Łukasz Kozak', 'department' => 'Production'],
            ['name' => 'Kacper Iwaniszyn', 'department' => 'Production'],
            ['name' => 'Paweł Grehl', 'department' => 'Production'],
            ['name' => 'Karolina Krawczyk', 'department' => 'Logistyka'],
            ['name' => 'Joanna Tonkowicz', 'department' => 'People & Culture'],
            ['name' => 'Karol Mach', 'department' => 'Production'],
        ];

        foreach ($managers as $manager) {
            $password = \Str::random(8); // Generowanie losowego hasła
            User::create([
                'name' => $manager['name'],
                'username' => $manager['username'],
                'email' => $manager['username'].'@example.com', // Tymczasowy email
                'password' => Hash::make($password),
                'role' => 'manager',
            ]);
            // Wyświetl login i hasło w konsoli
            echo "Manager: {$manager['name']}, Username: {$manager['username']}, Password: {$password}\n";
        }
    }
}
