<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\WorkBreak;


class CorrectionRequestController extends Controller
{
    public function index() {

        $user = Auth::user();
    
        $attendances = Attendance::where('user_id', $user->id)
                                ->orderBy('work_date', 'desc')
                                ->get();
    
        return view('user.correction_request.request', compact('attendances'));
    }
    
}
