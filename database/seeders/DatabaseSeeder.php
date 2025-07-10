<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rank;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Database\Seeders\EmployeeSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User mặc định
        User::create([
            'username' => 'admin',
            'password' => bcrypt('@0123321admin'),
            'role' => 'admin'
        ]);

        // Quân hàm
        $ranks = ['Hạ Sĩ', 'Trung Sĩ', 'Thượng Sĩ', 'Thiếu Úy', 'Trung Úy', 'Thượng Úy', 'Đại Úy', 'Thiếu Tá', 'Trung Tá', 'Thượng Tá', 'Đại Tá', 'Thiếu Tướng', 'Trung Tướng', 'Thượng Tướng', 'Đại Tướng'];
        foreach ($ranks as $rank) {
            Rank::create(['name_ranks' => $rank]);
        }

        // Chức vụ
        $positions = ['Thực Tập', 'Sĩ Quan Dự Bị', 'Cảnh Sát Viên', 'Đội Phó', 'Đội Trưởng', 'Thư Ký', 'Trợ Lý Cục Trưởng', 'Phó Cục Trưởng', 'Cục Trưởng'];
        foreach ($positions as $pos) {
            Position::create(['name_positions' => $pos]);
        }

        $this->call(EmployeesSeeder::class);
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
