<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\WorkBreak;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private function createAttendance($userId, $date, $clockIn, $clockOut)
    {
        $attendance = Attendance::create([
            'user_id' => $userId,
            'work_date' => $date,
            'clock_in' => "{$date} {$clockIn}",
            'clock_out' => "{$date} {$clockOut}",
        ]);

        WorkBreak::create([
            'attendance_id' => $attendance->id,
            'break_start' => "{$date} 12:00:00",
            'break_end' => "{$date} 13:00:00",
        ]);
    }

    public function run()
    {
        //ユーザー1
        $user1 = User::where('email', 'user1@example.com')->first();

        // 過去5ヶ月 各月平日15日
        for ($month = 1; $month <= 5; $month++) {

            $targetMonth = Carbon::now()->subMonths($month);

            $date = $targetMonth->copy()->startOfMonth();

            $count = 0;

            while ($count < 15) {

                if ($date->isWeekday()) {

                    $this->createAttendance(
                        $user1->id,
                        $date->format('Y-m-d'),
                        '09:00:00',
                        '18:00:00'
                    );

                    $count++;
                }

                $date->addDay();
            }

        }

        // 当月17日分
        $patterns = [
            // 通常10日
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],
            ['09:00:00', '18:00:00'],

            // 残業3日
            ['09:00:00', '20:00:00'],
            ['09:00:00', '20:00:00'],
            ['09:00:00', '20:00:00'],

            // 遅刻2日
            ['09:30:00', '18:00:00'],
            ['09:30:00', '18:00:00'],

            // 早退1日
            ['09:00:00', '17:00:00'],

            // 長時間労働1日
            ['08:00:00', '21:00:00'],
        ];

        $currentMonth = Carbon::now()->startOfMonth();

        $dateCount = 0;

        foreach ($patterns as $pattern) {

            $workDate = $currentMonth
                ->copy()
                ->addDays($dateCount)
                ->format('Y-m-d');

            $this->createAttendance(
                $user1->id,
                $workDate,
                $pattern[0],
                $pattern[1]
            );

            $dateCount++;
        }

        //ユーザー2
        $user2 = User::where('email', 'user2@example.com')->first();

        for ($month = 0; $month < 6; $month++) {

        $targetMonth = Carbon::now()->subMonths($month);

        $date = $targetMonth->copy()->startOfMonth();

        $count = 0;

        while ($count < 18) {

            if ($date->isWeekday()) {

                $clockOut = '18:00:00';

                if ($count % 7 === 0) {
                    $clockOut = '19:00:00';
                }

                if ($count % 11 === 0) {
                    $clockOut = '17:00:00';
                }

                $this->createAttendance(
                    $user2->id,
                    $date->format('Y-m-d'),
                    '09:00:00',
                    $clockOut
                );

                $count++;
            }

            $date->addDay();
            }
        }

        //ユーザー3
        $user3 = User::where('email', 'user3@example.com')->first();

        for ($month = 0; $month < 6; $month++) {

            $targetMonth = Carbon::now()->subMonths($month);

            $date = $targetMonth->copy()->startOfMonth();

            $count = 0;

            while ($count < 20) {

                if ($date->isWeekday()) {

                    $clockIn = '08:30:00';
                    $clockOut = '18:30:00';

                    if ($count % 8 === 0) {
                        $clockOut = '20:00:00';
                    }

                    if ($count % 15 === 0) {
                        $clockIn = '08:00:00';
                        $clockOut = '21:00:00';
                    }

                    $this->createAttendance(
                        $user3->id,
                        $date->format('Y-m-d'),
                        $clockIn,
                        $clockOut
                    );

                    $count++;
                }

                $date->addDay();
            }
        }
    }
}