<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use App\Models\WorkBreak;


class CorrectionRequestController extends Controller
{
    public function index (Request $request)
    {
    $tab = $request->query('tab', 'pending');

    $requests = CorrectionRequest::with([
        'user',
        'attendance'
    ])
    ->when(
        $tab === 'pending',
        fn($query) => $query->where('status', 'pending')
    )
    ->when(
        $tab === 'approved',
        fn($query) => $query->where('status', 'approved')
    )
    ->orderBy('created_at', 'desc')
    ->get();

    return view(
        'admin.request.index',
        compact('requests', 'tab')
    );
    }

    public function show($attendance_correct_request_id)
{
    $correction = CorrectionRequest::with([
        'user',
        'attendance',
        'correctionWorkBreaks'
    ])->findOrFail($attendance_correct_request_id);

    $user = $correction->user;
    $attendance = $correction->attendance;
    $date = $attendance->work_date;

    $pending = $correction->status === 'pending';
    $approved = $correction->status === 'approved';

    $correctionBreaks = $correction->correctionWorkBreaks;

    return view('admin.request.show', compact(
        'correction',
        'attendance',
        'user',
        'date',
        'pending',
        'approved',
        'correctionBreaks'
    ));
}

    public function approve($id)
    {
        $correction = CorrectionRequest::with(
            'correctionWorkBreaks'
        )
        ->findOrFail($id);

        $attendance = $correction->attendance;

        $attendance->update([
            'clock_in' => $correction->clock_in,
            'clock_out' => $correction->clock_out,
            'reason' => $correction->reason,
        ]);

        WorkBreak::where(
            'attendance_id',
            $attendance->id
            )->delete();

        foreach($correction->correctionWorkBreaks as $break){

            WorkBreak::create([
                'attendance_id' => $attendance->id,
                'break_start' => $break->break_start,
                'break_end' => $break->break_end,
            ]);
        }

        $correction->update([
            'status' => 'approved',
        ]);

        return back();
    }
}
