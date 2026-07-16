@extends('layouts.app')

@section('head')
    <meta http-equiv="refresh" content="60">
@endsection

@section('content')

<main>

<link href="{{ asset('css/user/create.css') }}" rel="stylesheet">

    <!-- 勤務状態 -->
    <h2 class="status">
        {{ $status }}
    </h2>

    <!-- 日付 -->
    <h3>{{ now()->isoformat('Y年M月D日 (ddd)') }}</h3>

    <!-- 時間 -->
    <h1>{{ now()->format('H:i') }}</h1>

    <!-- ボタン -->
        @if(!$todayAttendance || $status === '勤務外')
            <form method="POST" action="/attendance/start">
                @csrf
                <button type="submit" class="btn">出勤</button>
            </form>
        @elseif($status === '出勤中')
            <form method="POST" action="/attendance/break/start">
                @csrf
                <button type="submit" class="btn">休憩入</button>
            </form>
            <form method="POST" action="/attendance/end">
                @csrf
                <button type="submit" class="btn">退勤</button>
            </form>

        @elseif($status === '休憩中')
            <form method="POST" action="/attendance/break/end">
                @csrf
                <button type="submit" class="btn">休憩戻</button>
            </form>
        @elseif($status === '退勤済')
            <h3 class="finished">お疲れ様でした。</h3>
        @endif
    </div>
</div>
@endsection

</main>
