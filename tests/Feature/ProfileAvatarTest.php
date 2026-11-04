<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Position;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_to_a_custom_avatar(): void
    {
        Storage::fake('public');
        [$user, $employee] = $this->createEmployee();
        $this->actingAs($user);
        $csrfToken = 'avatar-test-token';
        $this->withSession(['_token' => $csrfToken]);

        $this->put(route('profile.update'), [
            '_token' => $csrfToken,
            'avatar_source' => 'custom',
            'avatar' => $this->makePngFile('portrait.png'),
        ])->assertRedirect();

        $employee->refresh();
        $this->assertSame('custom', $employee->avatar_source);
        $this->assertNotEmpty($employee->avatar);
        Storage::disk('public')->assertExists($employee->avatar);
        $this->assertStringContainsString('/storage/avatars/', $employee->display_avatar);
    }

    public function test_user_can_switch_back_to_the_linked_discord_avatar(): void
    {
        [$user, $employee] = $this->createEmployee([
            'avatar' => 'avatars/custom.png',
            'avatar_source' => 'custom',
        ]);
        $this->actingAs($user);
        $csrfToken = 'avatar-test-token';
        $this->withSession(['_token' => $csrfToken]);

        $this->put(route('profile.update'), [
            '_token' => $csrfToken,
            'avatar_source' => 'discord',
        ])->assertRedirect();

        $employee->refresh();
        $this->assertSame('discord', $employee->avatar_source);
        $this->assertSame('https://cdn.discordapp.com/avatar.png', $employee->display_avatar);
    }

    public function test_oversized_avatar_upload_is_rejected_with_a_validation_error(): void
    {
        Storage::fake('public');
        [$user] = $this->createEmployee();
        $this->actingAs($user);
        $csrfToken = 'avatar-test-token';
        $this->withSession(['_token' => $csrfToken]);

        $this->from(route('profile'))
            ->put(route('profile.update'), [
                '_token' => $csrfToken,
                'avatar_source' => 'custom',
                'avatar' => $this->makePngFile('large.png', 5 * 1024 * 1024),
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('avatar');
    }

    private function createEmployee(array $employeeAttributes = []): array
    {
        $user = User::create([
            'username' => 'avatar-user',
            'password' => Hash::make('password'),
            'role' => 'thành viên',
        ]);
        $position = Position::forceCreate(['name_positions' => 'Cảnh Sát Viên']);
        $rank = Rank::forceCreate(['name_ranks' => 'Sĩ Quan']);
        $employee = Employee::forceCreate(array_merge([
            'user_id' => $user->id,
            'name_ingame' => 'Test Officer',
            'position_id' => $position->id,
            'rank_id' => $rank->id,
            'discord_id' => '123456789',
            'discord_username' => 'avatar-user',
            'discord_avatar' => 'https://cdn.discordapp.com/avatar.png',
            'avatar_source' => 'discord',
        ], $employeeAttributes));

        return [$user, $employee];
    }

    private function makePngFile(string $name, int $paddingBytes = 0): UploadedFile
    {
        $pngBytes = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/rckAAAAASUVORK5CYII=',
            true
        );
        $sizeInKilobytes = 1 + intdiv($paddingBytes + 1023, 1024);
        $file = UploadedFile::fake()->create($name, $sizeInKilobytes, 'image/png');
        file_put_contents($file->getPathname(), $pngBytes . str_repeat('0', $paddingBytes));

        return $file;
    }
}
