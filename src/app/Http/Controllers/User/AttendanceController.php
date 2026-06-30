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
            $status = '退勤済み';
        } elseif ($onBreak) {
            $status = '休憩中';
        } elseif ($isWorking) {
            $status = '出勤中';
        } else {
            $status = '勤務外';
        }

        return view('user.attendance.index', compact('todayAttendance', 'isWorking', 'onBreak', 'status'));

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

        $date = $id;

        $attendance = Attendance::with('workbreaks')
        ->where('user_id', $user->id)
        ->where('work_date', $date)
        ->first();

        return view('user.attendance.show', compact('attendance', 'date', 'user'));
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

        foreach ($attendances as $attendance) {
        $totalMinutes = 0;
        foreach ($attendance->workBreaks as $break) {
            if ($break->break_start && $break->break_end) {

                $start = Carbon::parse($break->break_start);
                $end   = Carbon::parse($break->break_end);
                $totalMinutes += $start->diffInMinutes($end);
            }
        }

        $attendance->show_break_time =
        $totalMinutes > 0
        ? sprintf(
            '%02d : %02d ',
            intdiv($totalMinutes,60),
            $totalMinutes % 60
            )
            : null;
        }

    return view('user.attendance.list', compact('attendances', 'dates', 'month'));
    }
}