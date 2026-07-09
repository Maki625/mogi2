<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\UserController;
use App\Models\User;

class UserController extends Controller
{
    public function index() {

    $users = User::where('admin_status', false)->get();

    return view('admin.attendance.user_list.user', compact('users'));

    }
}
