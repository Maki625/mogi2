<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\WorkBreak;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index() {

        $user = Auth::user();
        $todayAttendance = Attendance::where('user_id', $user->id)
        ->where('work_date', now()->toDateString())->first();

        $isWorking = $todayAttendance && !$todayAttendance->clock_out;
        $onBreak = $todayAttendance && $todayAttendance->workBreaks()->whereNull('break_end')->exists();

        if (!$todayAttendance) {
            $status = '勤務外';
        } elseif ($todayAttendance->clock_out) {
            $status = '退勤済';
        } elseif ($onBreak) {
            $status = '休憩中';
        } elseif ($isWorking) {
            $status = '出勤中';
        } else {
            $status = '勤務外';
        }

        return view('user.attendance.create', compact('todayAttendance', 'isWorking', 'onBreak', 'status'));

    }

    public function start(Request $request) {
        $user = Auth::user();

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        return redirect('/attendance');
    }

    public function breakStart(Request $request) {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('work_date', now()->toDateString())
                                ->first();

        if ($attendance) {
            WorkBreak::create([
                'attendance_id' => $attendance->id,
                'break_start' => now()
            ]);
        }

        return redirect('/attendance');
    }

    public function breakEnd(Request $request) {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('work_date', now()->toDateString())
                                ->first();

        if ($attendance) {
        $workBreak = $attendance->workBreaks()->whereNull('break_end')->first();
        if ($workBreak) {
            $workBreak->update(['break_end' => now()]);
            }
        }
        return redirect('/attendance');
    }

    public function end(Request $request) {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
                            ->where('work_date', now()->toDateString())
                            ->first();

        if ($attendance) {
        $attendance->update(['clock_out' => now()]);
    }
    return redirect('/attendance');
    }

    public function show($id)
    {
        $user = Auth::user();

        $date = Carbon::parse($id);

        $attendance = Attendance::with('workbreaks', 'correctionRequests')
        ->where('user_id', $user->id)
        ->where('work_date', $date)
        ->first();

        $correction = $attendance
            ? $attendance->correctionRequests()->latest()->first()
            : null;

        $correctionBreaks = $correction
        ? $correction->correctionWorkBreaks
        : ($attendance ? $attendance->workbreaks : collect());

        $pending = $correction && $correction->status === 'pending';

        return view('user.attendance.show', compact('attendance', 'date', 'user', 'correction', 'correctionBreaks', 'pending', 'id'));
    }

    public function list(Request $request) {
        $month = $request->month
        ? Carbon::parse($request->month)
        : now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $dates = collect();

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $dates->push($date->copy());
        }

        $user = Auth::user();

        $attendances = Attendance::with('workBreaks')
                        ->where('user_id', $user->id)
                        ->whereBetween('work_date', [$start,$end])
                        ->orderBy('work_date', 'desc')
                        ->get();

        $attendanceMap = $attendances->keyBy(function ($attendance) {
        return $attendance->work_date->format('Y-m-d'); });

        foreach ($attendances as $attendance) {
        $totalMinutes = 0;
        foreach ($attendance->workBreaks as $break) {
            if ($break->break_start && $break->break_end) {

                $breakstart = Carbon::parse($break->break_start);
                $breakend   = Carbon::parse($break->break_end);
                $totalMinutes += $breakstart->diffInMinutes($breakend);
            }
        }

        $attendance->show_break_time =
        $totalMinutes > 0
        ? sprintf(
            '%02d:%02d ',
            intdiv($totalMinutes,60),
            $totalMinutes % 60
            )
            : null;

        if ($attendance->clock_in && $attendance->clock_out) {

        $workMinutes =
            $attendance->clock_in->diffInMinutes($attendance->clock_out)
            - $totalMinutes;

        $attendance->show_work_time = sprintf(
            '%02d : %02d',
            intdiv($workMinutes, 60),
            $workMinutes % 60
        );

        } else {
            $attendance->show_work_time = null;
        }
    }

    return view('user.attendance.index', compact('attendances', 'attendanceMap', 'dates', 'month'));
    }

    public function report()
    {
        $user = Auth::user();

        $start = now()->subMonths(5)->startOfMonth();
        $end = now()->endOfMonth();

        $attendances = Attendance::with('workBreaks')
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$start, $end])
            ->orderBy('work_date')
            ->get();

        $totalWorkMinutes = 0;
        $totalOvertimeMinutes = 0;

        foreach ($attendances as $attendance) {

        if (!$attendance->clock_in || !$attendance->clock_out) {
            continue;
        }

        // 休憩時間
        $breakMinutes = 0;

        foreach ($attendance->workBreaks as $break) {
            if ($break->break_start && $break->break_end) {
                $breakMinutes += Carbon::parse($break->break_start)
                    ->diffInMinutes(Carbon::parse($break->break_end));
            }
        }

        // 1日の実労働時間
        $workMinutes =
            $attendance->clock_in->diffInMinutes($attendance->clock_out)
            - $breakMinutes;

        $totalWorkMinutes += $workMinutes;

        // 8時間を超えた分を残業時間にする
        if ($workMinutes > 480) {
            $totalOvertimeMinutes += $workMinutes - 480;
        }
        }

        // 平均労働時間
        $workDays = $attendances->filter(function ($attendance) {
            return $attendance->clock_in && $attendance->clock_out;
        })->count();

        $averageWorkMinutes = $workDays > 0
            ? intdiv($totalWorkMinutes, $workDays)
            : 0;

        // 月次推移
        $monthlyReports = collect();

        for ($i = 5; $i >= 0; $i--) {

            $month = now()->subMonths($i);

            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthlyAttendances = $attendances->filter(function ($attendance) use ($monthStart, $monthEnd) {
            return $attendance->work_date->between($monthStart, $monthEnd);
            });

        $workMinutes = 0;
        $overtimeMinutes = 0;

        foreach ($monthlyAttendances as $attendance) {

        if (!$attendance->clock_in || !$attendance->clock_out) {
            continue;
        }

        $breakMinutes = 0;

        foreach ($attendance->workBreaks as $break) {
            if ($break->break_start && $break->break_end) {
                $breakMinutes += Carbon::parse($break->break_start)
                    ->diffInMinutes(
                        Carbon::parse($break->break_end)
                    );
            }
        }

        $dailyWorkMinutes =
            $attendance->clock_in->diffInMinutes($attendance->clock_out)
            - $breakMinutes;

        $workMinutes += $dailyWorkMinutes;

        if ($dailyWorkMinutes > 480) {
            $overtimeMinutes += $dailyWorkMinutes - 480;
        }
        }

        $monthlyReports->push([
            'month' => $month->format('Y-m'),
            'work_minutes' => $workMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ]);
        }

        //異常検知
        $lateCount = 0;
        $earlyLeaveCount = 0;
        $longWorkCount = 0;

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $currentMonthAttendances = $attendances->filter(function ($attendance) use ($currentMonthStart, $currentMonthEnd) {
            return $attendance->work_date->between($currentMonthStart, $currentMonthEnd);
        });

        foreach ($currentMonthAttendances as $attendance) {

            if (!$attendance->clock_in || !$attendance->clock_out) {
                continue;
            }

            // 遅刻：9:00を超えたらカウント
            if ($attendance->clock_in->format('H:i') > '09:00') {
                $lateCount++;
            }

            // 早退：18:00より前ならカウント
            if ($attendance->clock_out->format('H:i') < '18:00') {
                $earlyLeaveCount++;
            }

            // 休憩時間
            $breakMinutes = 0;

            foreach ($attendance->workBreaks as $break) {
                if ($break->break_start && $break->break_end) {
                    $breakMinutes += Carbon::parse($break->break_start)
                        ->diffInMinutes(Carbon::parse($break->break_end));
                }
            }

            // 実労働時間
            $workMinutes =
                $attendance->clock_in->diffInMinutes($attendance->clock_out)
                - $breakMinutes;

            // 10時間を超えたらカウント
            if ($workMinutes > 600) {
                $longWorkCount++;
            }
        }

        return view('user.attendance.report', compact(
            'attendances',
            'totalWorkMinutes',
            'totalOvertimeMinutes',
            'averageWorkMinutes',
            'monthlyReports',
            'lateCount',
            'earlyLeaveCount',
            'longWorkCount'
        ));
    }

}