<?php

namespace App\Http\Controllers;


use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkHourConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Log;
use Throwable;

class OnDutyController extends Controller
{
    public function index()
    {
        $onDutyList = Attendance::with(['user.employee.position.rank'])
            ->where('status', 'Đang On-Duty')

            ->get();

        return view('partials.ondutyList', compact('onDutyList'));
    }
    public function indexLive()
    {
        return view('partials.onduty_live');
    }

    public function getOnDuty()
    {
        // Lấy toàn bộ và + quân hàm chức vụ
        // $list = Attendance::with([
        //     'user.employee.position',
        //     'user.employee.rank'
        // ])
        //     ->where('status', 'Đang On-Duty')
        //     ->get();
        $list = Attendance::query()
            ->select([
                'id',
                'user_id',
                'status',
                'check_in',
                'check_out',
            ])  
            ->where('status', 'Đang On-Duty')
            ->get();

        // Cache trong 1 request để tránh gọi lặp cùng Discord ID
        $discordCache = [];

        foreach ($list as $item) {

            $discordId = trim((string) ($item->user?->employee?->discord_id ?? ''));

            // Mặc định trả về mảng activities
            $item->discord = [];

            // Bỏ qua nếu chưa liên kết Discord
            if (empty($discordId) || $discordId === '0') {
                continue;
            }

            // Nếu đã lấy rồi trong request hiện tại
            if (isset($discordCache[$discordId])) {
                $item->discord = $discordCache[$discordId];
                continue;
            }

            try {

                $response = Cache::remember(
                    "discord_$discordId",
                    now()->addSeconds(20),
                    function () use ($discordId) {

                        $http = Http::acceptJson()
                            ->timeout(2)
                            ->retry(2, 150)
                            ->get("https://api.lanyard.rest/v1/users/{$discordId}");

                        if (!$http->successful()) {
                            return [];
                        }

                        return $http->json();
                    }
                );

                $activities = data_get($response, 'data.activities', []);

            } catch (Throwable $e) {

                report($e);

                $activities = [];
            }

            $discordCache[$discordId] = $activities;
            $item->discord = $activities;
        }

        return response()->json($list);
    }
}
