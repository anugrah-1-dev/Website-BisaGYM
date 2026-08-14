<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $penjagaRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'penjaga']);
        $developerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'developer']);
        $kasirRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'kasir']);

        // 2. Users
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@bisagym.com'],
            [
                'name' => 'Budi Santoso (Admin)',
                'username' => 'admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);

        $penjaga = \App\Models\User::firstOrCreate(
            ['email' => 'kasir@bisagym.com'],
            [
                'name' => 'Andi Pratama (Kasir)',
                'username' => 'kasir',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'penjaga',
                'is_active' => true,
            ]
        );
        $penjaga->assignRole($penjagaRole);
        $penjaga->assignRole($kasirRole);

        $developer = \App\Models\User::firstOrCreate(
            ['email' => 'developer@bisagym.com'],
            [
                'name' => 'Tejo (Developer)',
                'username' => 'developer',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'developer',
                'is_active' => true,
            ]
        );
        $developer->assignRole($developerRole);

        // 3. Gym Packages
        \App\Models\GymPackage::insert([
            ['name' => 'Non Member', 'duration' => 1, 'duration_unit' => 'hari', 'price' => 50000, 'max_members' => 1, 'admin_fee' => 0, 'category' => 'non-member', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Basic Plan', 'duration' => 1, 'duration_unit' => 'bulan', 'price' => 250000, 'max_members' => 1, 'admin_fee' => 30000, 'category' => 'member', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pro Plan', 'duration' => 3, 'duration_unit' => 'bulan', 'price' => 675000, 'max_members' => 1, 'admin_fee' => 30000, 'category' => 'member', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elite Plan', 'duration' => 6, 'duration_unit' => 'bulan', 'price' => 1200000, 'max_members' => 1, 'admin_fee' => 30000, 'category' => 'member', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Power Couple', 'duration' => 1, 'duration_unit' => 'bulan', 'price' => 450000, 'max_members' => 2, 'admin_fee' => 30000, 'category' => 'couple', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Member VVIP', 'duration' => 1, 'duration_unit' => 'tahun', 'price' => 2500000, 'max_members' => 1, 'admin_fee' => 30000, 'category' => 'member', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Snacks
        \App\Models\Snack::insert([
            ['snack_code' => 'SNK-001', 'name' => 'Whey Protein Isolate', 'category' => 'Suplemen', 'stock' => 50, 'capital_price' => 15000, 'selling_price' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['snack_code' => 'SNK-002', 'name' => 'Air Mineral 600ml', 'category' => 'Minuman', 'stock' => 100, 'capital_price' => 2500, 'selling_price' => 5000, 'created_at' => now(), 'updated_at' => now()],
            ['snack_code' => 'SNK-003', 'name' => 'Pocari Sweat 500ml', 'category' => 'Minuman', 'stock' => 60, 'capital_price' => 5500, 'selling_price' => 8000, 'created_at' => now(), 'updated_at' => now()],
            ['snack_code' => 'SNK-004', 'name' => 'Fitbar Multigrain', 'category' => 'Makanan', 'stock' => 80, 'capital_price' => 4000, 'selling_price' => 7000, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 5. Members (dari gym.data_members.json)
        $this->call(MemberSeeder::class);
    }
}
