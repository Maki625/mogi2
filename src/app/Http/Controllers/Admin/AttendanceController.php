<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCorrectionRequest;


class AttendanceController extends Controller
{
        public function index(Request $request) {

        $date = $request->filled('date')
                ? Carbon::parse($request->date)
                : today();

        $attendances = Attendance::with('user', 'workBreaks')
                ->whereDate('work_date', $date)
                ->whereHas('user', function ($query) {
                        $query->where('admin_status', false); })
                ->orderBy('user_id')
                ->get();

        foreach ($attendances as $attendance) {

        $totalMinutes = 0;

        foreach ($attendance->workBreaks as $break) {
                if ($break->break_start && $break->break_end) {

                $breakStart = Carbon::parse($break->break_start);
                $breakEnd = Carbon::parse($break->break_end);

                $totalMinutes += $breakStart->diffInMinutes($breakEnd);
                }
        }

        $attendance->show_break_time =
                $totalMinutes > 0
                ? sprintf(
                '%02d:%02d',
                intdiv($totalMinutes, 60),
                $totalMinutes % 60
                )
                : null;

        if ($attendance->clock_in && $attendance->clock_out) {

                $workMinutes =
                $attendance->clock_in->diffInMinutes($attendance->clock_out)
                - $totalMinutes;

                $attendance->show_work_time = sprintf(
                '%02d:%02d',
                intdiv($workMinutes, 60),
                $workMinutes % 60
                );

        } else {
                $attendance->show_work_time = null;
                }
        }
        return view ('admin.attendance.index', compact('date', 'attendances'));
        }

        //リスト
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

        if ($attendance->clock_in && $attendance->clock_out) {

        $workMinutes =
        $attendance->clock_in->diffInMinutes($attendance->clock_out)
        - $totalMinutes;

        $attendance->show_work_time = sprintf(
        '%02d:%02d',
        intdiv($workMinutes, 60),
        $workMinutes % 60
        );

        } else {

        $attendance->show_work_time = null;

                }
        }

        return view('admin.staff.attendance.index', compact(
                'attendances',
                'attendanceMap',
                'dates',
                'month',
                'user',
                'id'
        ));
        }

        //CSV出力
        public function csv(Request $request, $id)
        {
        $month = $request->month
                ? Carbon::parse($request->month)
                : now();

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $user = User::findOrFail($id);

        $attendances = Attendance::with('workBreaks')
                ->where('user_id', $id)
                ->whereBetween('work_date', [$start, $end])
                ->orderBy('work_date', 'desc')
                ->get();

        return response()->streamDownload(function () use ($attendances) {

                $handle = fopen('php://output', 'w');

                // Excelで日本語が文字化けしないようにBOMを付ける
                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, [
                '日付',
                '出勤',
                '退勤',
                '休憩',
                '合計',
                ]);

                foreach ($attendances as $attendance) {

                $totalMinutes = 0;

                foreach ($attendance->workBreaks as $break) {

                        if ($break->break_start && $break->break_end) {

                        $breakStart = Carbon::parse($break->break_start);
                        $breakEnd = Carbon::parse($break->break_end);

                        $totalMinutes += $breakStart->diffInMinutes($breakEnd);
                        }
                }

                $breakTime = $totalMinutes > 0
                        ? sprintf(
                        '%02d:%02d',
                        intdiv($totalMinutes, 60),
                        $totalMinutes % 60
                        )
                        : '';

                if ($attendance->clock_in && $attendance->clock_out) {

                        $workMinutes =
                        $attendance->clock_in->diffInMinutes($attendance->clock_out)
                        - $totalMinutes;

                        $workTime = sprintf(
                        '%02d:%02d',
                        intdiv($workMinutes, 60),
                        $workMinutes % 60
                        );

                } else {

                        $workTime = '';
                }

                fputcsv($handle, [
                        $attendance->work_date->format('Y/m/d'),
                        $attendance->clock_in?->format('H:i'),
                        $attendance->clock_out?->format('H:i'),
                        $breakTime,
                        $workTime,
                ]);
                }

                fclose($handle);

        }, $user->name . '_勤怠_' . $month->format('Y-m') . '.csv');
        }

        //詳細
        public function show($user_id, $date)
        {
                $date = Carbon::parse($date);

                $user = User::findOrFail($user_id);

                $attendance = Attendance::with('workBreaks', 'correctionRequests')
                ->where('user_id', $user->id)
                ->where('work_date', $date)
                ->first();

                $correction = $attendance
                ? $attendance->correctionRequests()->latest()->first()
                : null;

                $pending = $correction && $correction->status === 'pending';

                $approved =
                $correction &&
                $correction->status === 'approved';

                $correctionBreaks = $pending
                ? $correction->correctionWorkBreaks
                : ($attendance
                ? $attendance->workBreaks
                : collect());

                return view('admin.attendance.show', compact('attendance', 'date', 'user', 'correction', 'pending', 'approved',  'correctionBreaks'));
        }

        //修正
        public function update(StoreCorrectionRequest $request, $user_id, $date)
        {
        $user = User::findOrFail($user_id);

        $attendance = Attendance::firstOrCreate(
        [
        'user_id' => $user->id,
        'work_date' => $date,
        ],
        [
        'clock_in' => null,
        'clock_out' => null,
        'reason' => null,
        ]
        );

        $attendance->update([
        'clock_in' => $request->clock_in,
        'clock_out' => $request->clock_out,
        'reason' => $request->reason,
        ]);

        $attendance->workBreaks()->delete();

        if ($request->break1_start && $request->break1_end) {

                $attendance->workBreaks()->create([
                'break_start' => $request->break1_start,
                'break_end' => $request->break1_end,
                ]);
        }

        if ($request->break2_start && $request->break2_end) {

                $attendance->workBreaks()->create([
                'break_start' => $request->break2_start,
                'break_end' => $request->break2_end,
                ]);
        }

        return redirect()
                ->back()
                ->with('success', '修正しました。');
        }

}
