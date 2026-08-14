<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\WorkBreak;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 休憩ボタンが正しく機能する
     */
    public function test_休憩開始するとステータスが休憩中になる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // 出勤
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        // 休憩開始
        $response = $this->post('/attendance/break/start');

        $response->assertRedirect('/attendance');

        // 休憩が登録されている
        $this->assertDatabaseHas('workbreaks', [
            'attendance_id' => $attendance->id,
        ]);

        // 勤怠画面を確認
        $response = $this->get('/attendance');

        $response->assertSee('休憩中');
    }


    /**
     * 休憩は一日に何回でもできる
     */
    public function test_休憩を複数回できる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        // 1回目の休憩
        $this->post('/attendance/break/start');

        $this->post('/attendance/break/end');

        // 2回目の休憩
        $this->post('/attendance/break/start');

        $response = $this->get('/attendance');

        // 休憩戻ではなく休憩中になっている
        $response->assertSee('休憩中');

        // 休憩が2件登録されている
        $this->assertDatabaseCount('workbreaks', 2);
    }


    /**
     * 休憩戻ボタンが正しく機能する
     */
    public function test_休憩戻するとステータスが出勤中になる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        // 休憩開始
        $this->post('/attendance/break/start');

        // 休憩戻
        $response = $this->post('/attendance/break/end');

        $response->assertRedirect('/attendance');

        // 休憩終了時刻が登録されている
        $this->assertDatabaseHas('workbreaks', [
            'attendance_id' => $attendance->id,
        ]);

        // 勤怠画面を確認
        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }


    /**
     * 休憩戻は一日に何回でもできる
     */
    public function test_休憩戻を複数回できる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        // 1回目
        $this->post('/attendance/break/start');
        $this->post('/attendance/break/end');

        // 2回目
        $this->post('/attendance/break/start');
        $this->post('/attendance/break/end');

        // 2回分の休憩が登録されている
        $this->assertDatabaseCount('workbreaks', 2);

        // 最後は出勤中
        $response = $this->get('/attendance');

        $response->assertSee('出勤中');
    }


    /**
     * 休憩時刻が勤怠一覧画面で確認できる
     */
    public function test_休憩時刻が勤怠一覧画面で確認できる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        // 休憩開始
        $this->post('/attendance/break/start');

        // 休憩終了
        $this->post('/attendance/break/end');

        // 勤怠一覧画面
        $response = $this->get('/attendance/list');

        $response->assertStatus(200);

        // 休憩時間が表示されていることを確認
        $response->assertSee($attendance->work_date->format('m/d'));
    }

    // ゲストはレポートページにアクセスできない
        public function test_guest_cannot_access_attendance_report()
        {
            $response = $this->get('attendance/report');

            $response->assertRedirect('/login');

        }

    // 勤怠記録がないユーザーで安全に処理される
        public function test_user_with_no_attendance_records_can_access_report()
        {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('attendance/report');

            $response->assertStatus(200);

            $response->assertViewHas('totalWorkMinutes', 0);
            $response->assertViewHas('totalOvertimeMinutes', 0);
            $response->assertViewHas('averageWorkMinutes', 0);

            $response->assertViewHas('monthlyReports', function ($monthlyReports) {
                return $monthlyReports->every(function ($report) {
                    return $report['work_minutes'] === 0
                        && $report['overtime_minutes'] === 0;
                });
            });

            $response->assertViewHas('lateCount', 0);
            $response->assertViewHas('earlyLeaveCount', 0);
            $response->assertViewHas('longWorkCount', 0);
        }

        // 認証ユーザーの統計情報が正しく計算される
public function test_user_attendance_report_calculates_statistics_correctly()
{
    $user = User::factory()->create();

    // 1日目：9:00〜18:00、休憩1時間 → 実労働8時間
    $attendance1 = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->subDays(2)->format('Y-m-d'),
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    WorkBreak::create([
        'attendance_id' => $attendance1->id,
        'break_start' => '12:00:00',
        'break_end' => '13:00:00',
    ]);

    // 2日目：9:00〜19:00、休憩1時間 → 実労働9時間
    $attendance2 = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->subDays(1)->format('Y-m-d'),
        'clock_in' => '09:00:00',
        'clock_out' => '19:00:00',
    ]);

    WorkBreak::create([
        'attendance_id' => $attendance2->id,
        'break_start' => '12:00:00',
        'break_end' => '13:00:00',
    ]);

    $response = $this->actingAs($user)->get('/attendance/report');

    $response->assertStatus(200);

    // 総労働時間：8時間 + 9時間 = 17時間
    $response->assertViewHas('totalWorkMinutes', 1020);

    // 残業時間：1時間
    $response->assertViewHas('totalOvertimeMinutes', 60);

    // 平均労働時間：17時間 ÷ 2日 = 8時間30分
    $response->assertViewHas('averageWorkMinutes', 510);
}

}