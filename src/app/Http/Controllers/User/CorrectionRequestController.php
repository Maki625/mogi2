<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use App\Models\CorrectionWorkBreak;
use Carbon\Carbon;


class CorrectionRequestController extends Controller
{
    public function index() {

        $user = Auth::user();
        
        $requests = CorrectionRequest::with([
        'user',
        'attendance'
        ])
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('user.correction_request.request', compact('requests'));
    }
    
    public function store(Request $request, $id) {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('id', $id)
        ->firstOrFail();

        $baseDate = $attendance->work_date->format('Y-m-d');

        $request->validate([
            'clock_in' => ['required'],
            'clock_out' => ['required'],
            'break1_start' => ['nullable'],
            'break1_end' => ['nullable'],
            'break2_start' => ['nullable'],
            'break2_end' => ['nullable'],
            'reason' => ['required'],
        ]);

        $correctionRequest = CorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        if ($request->filled('break1_start') || $request->filled('break1_end')) {
            CorrectionWorkBreak::create([
                'correction_request_id' => $correctionRequest->id,
                'break_start' => $request->break1_start
                ? $baseDate . ' ' . $request->break1_start . ':00'
                : null,

                'break_end' => $request->break1_end
                ? $baseDate . ' ' . $request->break1_end . ':00'
                : null,
            ]);
        }

        if ($request->filled('break2_start') || $request->filled('break2_end')) {
            CorrectionWorkBreak::create([
                'correction_request_id' => $correctionRequest->id,
                'break_start' => $request->break2_start
                ? $baseDate . ' ' . $request->break2_start . ':00'
                : null,

                'break_end' => $request->break2_end
                ? $baseDate . ' ' . $request->break2_end . ':00'
                : null,
            ]);
        }

        return redirect('/attendance/detail/' . $attendance->work_date);
    }

    }

