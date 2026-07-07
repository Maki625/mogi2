<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
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


}
