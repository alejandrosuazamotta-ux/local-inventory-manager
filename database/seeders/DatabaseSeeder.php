<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
        * Seed the application's database.
        */
    public function run(): void
    {
        // Crear usuario administrador por defecto
        if (User::count() === 0) {
            User::factory()->create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Importar datos históricos desde los Excel exportados por el sistema
        $this->call([
            CustomerSeeder::class,
            ProductSeeder::class,
            SaleSeeder::class,
            PaymentSeeder::class,
            ExpenseSeeder::class,
            CarteraAntiguaSeeder::class,
            SupplierDebtSeeder::class,
        ]);
    }
}
