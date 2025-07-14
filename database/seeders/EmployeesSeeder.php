<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Rank;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeesSeeder extends Seeder
{
    public function run(): void
    {
        /*
        // Lấy danh sách position và rank (giả định đã có sẵn)
        $positions = Position::pluck('id')->toArray();
        $ranks = Rank::pluck('id')->toArray();

        // Chạy vòng lặp tạo 30 nhân sự
        for ($i = 1; $i <= 30; $i++) {
            $username = 'user' . $i;

            $user = User::create([
                'username' => $username,
                'password' => Hash::make('password'), // mật khẩu mặc định
                'role' => 'sĩ quan', // hoặc map từ position nếu cần
            ]);

            Employee::create([
                'user_id' => $user->id,
                'name_ingame' => 'Officer ' . Str::upper(Str::random(5)),
                'position_id' => $positions[array_rand($positions)],
                'rank_id' => $ranks[array_rand($ranks)],
                'avatar' => null, // nếu cần gán ảnh giả thì dùng fake path hoặc null
                'created_by' => 1, // hoặc auth id nếu có user admin
            ]);
        }
            */
    }
}
