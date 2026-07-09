<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
        public function index(Request $request) {

        $date = $request->filled('date')
                ? Carbon::parse($request->date)
                : today();

        $attendances = Attendance::with('user')
                ->whereDate('work_date', $date)
                ->whereHas('user', function ($query) {
                        $query->where('admin_status', false); })
                ->orderBy('user_id')
                ->get();

        return view ('admin.attendance.index', compact('date', 'attendances'));
        }

        public function list(Request $request, $id)
        {
        $month = $request->month
                ? Carbon::parse($request->month)
                : now();

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $dates = collect();

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $dates->push($date->copy());
        }

        $user = User::findOrFail($id);

        $attendances = Attendance::with('workBreaks')
                ->where('user_id', $id)
                ->whereBetween('work_date', [$start, $end])
                ->orderBy('work_date', 'desc')
                ->get();

        $attendanceMap = $attendances->keyBy(function ($attendance) {
                return $attendance->work_date->format('Y-m-d');
        });

        foreach ($attendances as $attendance) {

                $totalMinutes = 0;

                foreach ($attendance->workBreaks as $break) {

                if ($break->break_start && $break->break_end) {

                        $start = Carbon::parse($break->break_start);
                        $end = Carbon::parse($break->break_end);

                        $totalMinutes += $start->diffInMinutes($end);
                }
                }

                $attendance->show_break_time =
                $totalMinutes > 0
                ? sprintf(
                        '%02d : %02d',
                        intdiv($totalMinutes, 60),
                        $totalMinutes % 60
                )
                : null;
        }

        return view('admin.attendance.user_list.show', compact(
                'attendances',
                'attendanceMap',
                'dates',
                'month',
                'user',
                'id'
        ));
        }

        public function show($user_id, $date)
        {
                $date = Carbon::parse($date);

                $user = User::findOrFail($user_id);

                $attendance = Attendance::with('workbreaks', 'correctionRequests')
                ->where('user_id', $user->id)
                ->where('work_date', $date)
                ->first();

                $correction = $attendance
                ? $attendance->correctionRequests()->latest()->first()
                : null;

                $correctionBreaks = $correction
                ? $correction->correctionWorkBreaks
                : $attendance->workbreaks;

                $pending = $correction && $correction->status === 'pending';

                return view('admin.attendance.show', compact('attendance', 'date', 'user', 'correction', 'correctionBreaks', 'pending'));
        }


}
