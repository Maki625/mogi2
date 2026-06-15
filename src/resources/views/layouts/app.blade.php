<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '勤怠管理アプリ')</title>

    <!-- 共通CSS -->
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">

    <!-- 個別ページで追加したい head -->
    @yield('head')
</head>
<body>
    <!-- ヘッダー -->
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="/images/logo.svg" alt="logo">
            </a>

        <nav class="header_nav">
            <a href="/attendance" class="nav-link">勤怠</a>

            <a href="/attendance/list" class="nav-link">勤怠一覧</a>

            <a href="/stamp_correction_request/list" class="nav-link">申請</a>

            @auth
            <form id="logout-form" action="/logout" method="POST">
                @csrf
                <button type="submit" class="nav-link">
                ログアウト
                </button>
            </form>
            @endauth





        </div>
    </header>

    <!-- ページコンテンツ -->
    @yield('content')
</body>
</html>
