<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionRequest;
use App\Models\Attendance;
use App\Models\CorrectionWorkBreak;
use Carbon\Carbon;
use App\Http\Requests\StoreCorrectionRequest;


class CorrectionRequestController extends Controller
{
    public function index(Request $request) {

        $user = Auth::user();

        $tab = $request->query('tab', 'pending');

        $requests = CorrectionRequest::with([
        'user',
        'attendance'
        ])
        ->where('user_id', $user->id)
        ->when($tab === 'pending', fn($q) => $q->where('status', 'pending'))
        ->when($tab === 'approved', fn($q) => $q->where('status', 'approved'))
        ->orderBy('created_at', 'desc')
        ->get();

        return view('user.correction_request.index', compact('requests', 'tab'));
    }

    public function store(StoreCorrectionRequest $request, $id) {
        $attendance = Attendance::where('user_id', Auth::id())
        ->where('id', $id)
        ->firstOrFail();

        $baseDate = $attendance->work_date->format('Y-m-d');

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

