@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/user/list.css') }}" rel="stylesheet">

<h2>勤怠一覧</h2>

<a href="{{ url()->current() }}?month={{ $month->copy()->subMonth()->format('Y-m') }}">
    前月
</a>

<h2>{{ $month->format('Y/m') }}</h2>

<a href="{{ url()->current() }}?month={{ $month->copy()->addMonth()->format('Y-m') }}">
    次月
</a>

<table border="1" cellspacing="0" cellpadding="8">
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
            <a href="/attendance/detail/{{ $date->format('Y-m-d') }}" class="content">詳細</a>
        </td>
    </tr>
    @endforeach
</table>
</main>

@endsection