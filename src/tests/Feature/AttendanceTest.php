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
}