@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/admin/attendance/index.css') }}" rel="stylesheet">

<div class="container">
    <h2 class="title">{{ $date->format('Y年m月d日の勤怠') }}</h2>

    <div class="month-navigation">

        <a href="/admin/attendance/list?date={{ $date->copy()->subDay()->format('Y-m-d') }}" class="month-nav">
            ←前日
        </a>

        <h2 class="month"><img src="/images/calendar.jpeg" alt="" class="calendar-icon">{{ $date->format('Y/m') }}</h2>

        <a href="/admin/attendance/list?date={{ $date->copy()->addDay()->format('Y-m-d') }}" class="month-nav">
            翌日→
        </a>
    </div>

    <table>
        <tr>
            <th class="username">名前</th>
            <th class="clock_in">出勤</th>
            <th class="clock_out">退勤</th>
            <th class="break_time">休憩</th>
            <th class="work_total">合計</th>
            <th class="content">詳細</th>
        </tr>

        @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ optional($attendance->clock_in)->format('H:i') }}</td>
                <td>{{ optional($attendance->clock_out)->format('H:i') }}</td>
                <td>{{ $attendance->show_break_time }}</td>
                <td>{{ $attendance->show_work_time }}</td>
                <td>
                <a href="/admin/attendance/{{ $attendance->user_id }}/{{ $date->format('Y-m-d') }}" class="content-nav">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
</div>
</main>

@endsection