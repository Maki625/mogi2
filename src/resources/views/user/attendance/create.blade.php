@extends('layouts.app')

@section('head')
    <meta http-equiv="refresh" content="60">
    <link href="{{ asset('css/user/create.css') }}" rel="stylesheet">

@endsection

@section('content')

<main>

<div class="container">
    <!-- 勤務状態 -->
    <h2 class="status">
        {{ $status }}
    </h2>

    <!-- 日付 -->
    <h3 class="date">{{ now()->isoformat('Y年M月D日 (ddd)') }}</h3>

    <!-- 時間 -->
    <h1 class="time">{{ now()->format('H:i') }}</h1>

    <!-- ボタン -->
        @if(!$todayAttendance || $status === '勤務外')
            <form method="POST" action="/attendance/start">
                @csrf
                <button type="submit" class="btn">出勤</button>
            </form>
        @elseif($status === '出勤中')
        <div class="button-group">
            <form method="POST" action="/attendance/end">
                @csrf
                <button type="submit" class="btn">退勤</button>
            </form>
            <form method="POST" action="/attendance/break/start">
                @csrf
            <button type="submit" class="btn break-btn">休憩入</button>
            </form>
        </div>

        @elseif($status === '休憩中')
            <form method="POST" action="/attendance/break/end">
                @csrf
                <button type="submit" class="btn break-btn">休憩戻</button>
            </form>
        @elseif($status === '退勤済')
            <h3 class="finished">お疲れ様でした。</h3>
        @endif
</div>
</main>
@endsection
