<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class CorrectionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = User::where('email', 'user1@example.com')->first();
        $user2 = User::where('email', 'user2@example.com')->first();

        $attendance1 = Attendance::where('user_id', $user1->id)->first();

        CorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'clock_in' => $attendance1->clock_in,
            'clock_out' => Carbon::parse($attendance1->clock_out)->addHour(),
            'reason' => '退勤打刻漏れ',
            'status' => 'pending',
        ]);

        $attendance2 = Attendance::where('user_id', $user1->id)->skip(10)->first();

        CorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance2->id,
            'clock_in' => Carbon::parse($attendance2->clock_in)->subMinutes(30),
            'clock_out' => $attendance2->clock_out,
            'reason' => '出勤時刻修正',
            'status' => 'pending',
        ]);

        $attendance3 = Attendance::where('user_id', $user1->id)->skip(20)->first();

        CorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance3->id,
            'clock_in' => $attendance3->clock_in,
            'clock_out' => Carbon::parse($attendance3->clock_out)->addMinutes(30),
            'reason' => '業務対応による残業',
            'status' => 'pending',
        ]);

        $attendance4 = Attendance::where('user_id', $user2->id)->first();

        CorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance4->id,
            'clock_in' => $attendance4->clock_in,
            'clock_out' => Carbon::parse($attendance4->clock_out)->addHour(),
            'reason' => '退勤打刻漏れ',
            'status' => 'pending',
        ]);

        $attendance5 = Attendance::where('user_id', $user2->id)->skip(10)->first();

        CorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance5->id,
            'clock_in' => Carbon::parse($attendance5->clock_in)->subMinutes(30),
            'clock_out' => $attendance5->clock_out,
            'reason' => '出勤時刻修正',
            'status' => 'pending',
        ]);
    }
}
