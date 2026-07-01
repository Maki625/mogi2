<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //管理者ユーザーログイン
        Fortify::authenticateUsing(function (Request $request) {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return null;
        }

        if ($request->login_type === 'admin' && ! $user->admin_status) {
            return null;
        }

        if ($request->login_type !== 'admin' && $user->admin_status) {
            return null;
        }

        return $user;
        });

        // ユーザー作成の処理
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録画面
        Fortify::registerView(function () {
            return view('user.register');
        });

        // 登録後のリダイレクト先を変更
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/attendance'); // 登録後は勤怠画面へ
            }
        });

        // ログイン画面
        Fortify::loginView(function () {
            return view('user.login');
        });

        // ログイン後のリダイレクト先を変更
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return auth()->user()->admin_status
                        ? redirect('/admin/attendance/list')
                        : redirect('/attendance');
                }
            };
        });

        $this->app->instance(LogoutResponse::class, new class implements LogoutResponse {
        public function toResponse($request)
        {
            return $request->logout_type === 'admin'
            ? redirect('/admin/login')
            : redirect('/login');
        }
        });
    }
}
