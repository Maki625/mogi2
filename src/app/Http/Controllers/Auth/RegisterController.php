<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Actions\Fortify\CreateNewUser;


class RegisterController
{
    public function store(Request $request, CreateNewUser $creator)
    {
        // ユーザー作成
        $user = $creator->create($request->all());

        // ログイン状態にする
        Auth::login($user);

        // プロフィール設定画面にリダイレクト
        return redirect('/mypage/profile');
    }
}