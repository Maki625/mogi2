<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\CorrectionRequest;


class CorrectionRequestController extends Controller
{
    public function index (Request $request) {
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
        'admin.correction_request.request',
        compact('requests', 'tab')
    );
    }
}
