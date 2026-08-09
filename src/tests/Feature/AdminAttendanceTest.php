<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_出勤時間が退勤時間より後の場合エラーになる()
    {
        // 管理者ユーザー
        $admin = User::factory()->create([
            'admin_status' => true,
        ]);

        // 一般ユーザー
        $user = User::factory()->create([
            'admin_status' => false,
        ]);

        // 勤怠データ
        Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-08-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'reason' => '通常勤務',
        ]);

        // 管理者としてログイン
        $this->actingAs($admin);

        // 出勤18:00、退勤17:00で保存
        $response = $this->put(
            "/admin/attendance/fix/{$user->id}/2026-08-01",
            [
                'clock_in' => '18:00',
                'clock_out' => '17:00',
                'reason' => '修正理由',
            ]
        );

        // バリデーションエラーを確認
        $response->assertSessionHasErrors([
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    public function test_休憩開始時間が退勤時間より後の場合エラーになる()
{
    $admin = User::factory()->create([
        'admin_status' => true,
    ]);

    $user = User::factory()->create([
        'admin_status' => false,
    ]);

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => '2026-08-01',
        'clock_in' => '09:00',
        'clock_out' => '18:00',
        'reason' => '通常勤務',
    ]);

    $this->actingAs($admin);

    $response = $this->put(
        "/admin/attendance/fix/{$user->id}/2026-08-01",
        [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break1_start' => '19:00',
            'break1_end' => '19:30',
            'reason' => '修正理由',
        ]
    );

    $response->assertSessionHasErrors([
        'break1_start' => '休憩時間が不適切な値です',
    ]);
}


public function test_休憩終了時間が退勤時間より後の場合エラーになる()
{
    $admin = User::factory()->create([
        'admin_status' => true,
    ]);

    $user = User::factory()->create([
        'admin_status' => false,
    ]);

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => '2026-08-01',
        'clock_in' => '09:00',
        'clock_out' => '18:00',
        'reason' => '通常勤務',
    ]);

    $this->actingAs($admin);

    $response = $this->put(
        "/admin/attendance/fix/{$user->id}/2026-08-01",
        [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break1_start' => '17:00',
            'break1_end' => '19:00',
            'reason' => '修正理由',
        ]
    );

    $response->assertSessionHasErrors([
        'break1_end' => '休憩時間もしくは退勤時間が不適切な値です',
    ]);
}


public function test_備考欄が未入力の場合エラーになる()
{
    $admin = User::factory()->create([
        'admin_status' => true,
    ]);

    $user = User::factory()->create([
        'admin_status' => false,
    ]);

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => '2026-08-01',
        'clock_in' => '09:00',
        'clock_out' => '18:00',
        'reason' => '通常勤務',
    ]);

    $this->actingAs($admin);

    $response = $this->put(
        "/admin/attendance/fix/{$user->id}/2026-08-01",
        [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'reason' => '',
        ]
    );

    $response->assertSessionHasErrors([
        'reason' => '備考を記入してください',
    ]);
}

}
