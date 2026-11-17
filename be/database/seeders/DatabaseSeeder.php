<?php

namespace Database\Seeders;

use Database\Seeders\Admin\AccountAdminSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AccountAdminSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            AdministrativeDivisionSeeder::class,
            BankSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
