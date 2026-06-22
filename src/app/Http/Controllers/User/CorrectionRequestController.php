<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionRequest;


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
    
}
