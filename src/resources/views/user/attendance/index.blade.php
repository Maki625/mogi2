@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/user/index.css') }}" rel="stylesheet">

<div class="container">
<h2 class="title">勤怠一覧</h2>

    <div class="month-navigation">
        <a href="{{ url()->current() }}?month={{ $month->copy()->subMonth()->format('Y-m') }}" class="month-nav">
            ←前月
        </a>

        <h2 class="month">
            <img src="/images/calendar.jpeg" alt="" class="calendar-icon">{{ $month->format('Y/m') }}</h2>

        <a href="{{ url()->current() }}?month={{ $month->copy()->addMonth()->format('Y-m') }}" class="month-nav">
            翌月→
        </a>
    </div>

    <table>
        <tr>
            <th class="date">日付</th>
            <th class="clock_in">出勤</th>
            <th class="clock_out">退勤</th>
            <th class="break_time">休憩</th>
            <th class="work_total">合計</th>
            <th class="content">詳細</th>
        </tr>
        @foreach ($dates as $date)

        @php
            $attendance = $attendanceMap[$date->format('Y-m-d')] ?? null;
        @endphp

            <tr>
                <td>{{ $date->isoformat('MM/DD(ddd)') }}</td>
                <td>{{ $attendance?->clock_in?->format('H:i') }}</td>
                <td>{{ $attendance?->clock_out?->format('H:i') }}</td>
                <td>{{ $attendance?->show_break_time }}</td>
                <td>{{ $attendance?->show_work_time }}</td>
            <td>
                <a href="/attendance/detail/{{ $date->format('Y-m-d') }}" class="content-nav">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
</main>

@endsection