<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class DiscordController extends Controller
{
    public function connect()
    {
        $query = http_build_query([
            'client_id' => config('services.discord.client_id'),
            'redirect_uri' => config('services.discord.redirect'),
            'response_type' => 'code',
            'scope' => 'identify guilds.join',
        ]);

        return redirect("https://discord.com/api/oauth2/authorize?" . $query);
    }

    public function callback(Request $request)
    {
        // 1. Lấy code từ callback
        $code = $request->get('code');

        // 2. Đổi code lấy access_token
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post('https://discord.com/api/oauth2/token', [
                    'client_id' => config('services.discord.client_id'),
                    'client_secret' => config('services.discord.client_secret'),
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => config('services.discord.redirect'),
                ]);

        $data = $response->json();
        if (!isset($data['access_token'])) {
            return redirect()->route('profile')->with('error', 'Liên kết Discord thất bại!');
        }

        $accessToken = $data['access_token'];

        // 3. Lấy thông tin user Discord
        $discordUser = Http::withToken($accessToken)->get('https://discord.com/api/users/@me')->json();

        // Kiểm tra discord_id đã được liên kết với employee khác chưa
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return redirect()->route('profile')->with('error', 'Không tìm thấy thông tin nhân viên!');
        }

        $exists = \App\Models\Employee::where('discord_id', $discordUser['id'])
            ->where('id', '!=', $user->employee->id) // dùng id employee, không phải user_id
            ->exists();

        if ($exists) {
            return redirect()->route('profile')->with('warning', 'Tài khoản Discord này đã được liên kết với account khác!');
        }

        // 4. Lưu vào employee
        $employee = auth()->user()->employee;
        $employee->discord_id = $discordUser['id'];
        $employee->discord_username = $discordUser['username'] . '#' . $discordUser['discriminator'];
        $employee->discord_avatar = "https://cdn.discordapp.com/avatars/{$discordUser['id']}/{$discordUser['avatar']}.png";
        $employee->save();

        // 5. Thêm user vào Guild (Server) bằng Bot Token
        Http::withToken(env('DISCORD_BOT_TOKEN'))->put(
            "https://discord.com/api/guilds/" . env('DISCORD_GUILD_ID') . "/members/" . $discordUser['id'],
            [
                "access_token" => $accessToken
            ]
        );

        // 6. Ghi log hoạt động
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logsCustom',
            'target' => $user->username,
            'detail' => 'Liên kết Discord thành công: ' . $employee->discord_username
        ]);

        return redirect()->route('profile')->with('success', 'Liên kết Discord thành công!');
    }
    public function unlink(Request $request)
    {
        $user = Auth::user();

        if ($user->employee) {
            $employee = $user->employee;
            $employee->discord_id = null;
            $employee->discord_username = null;
            $employee->discord_avatar = null;
            $employee->save();
        }

        $user = Auth::user();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logsCustom',
            'target' => $user->username,
            'detail' => 'Hủy liên kết Discord'
        ]);

        return back()->with('success', 'Đã hủy liên kết Discord thành công!');
    }
}
