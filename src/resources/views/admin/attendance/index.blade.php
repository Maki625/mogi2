@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/admin/index.css') }}" rel="stylesheet">

<h3>{{ $date->format('Y年m月d日の勤怠') }}</h3>

<a href="/admin/attendance/list?date={{ $date->copy()->subDay()->format('Y-m-d') }}">
    前日
</a>

<h2>{{ $date->format('Y/m/d') }}</h2>

<a href="/admin/attendance/list?date={{ $date->copy()->addDay()->format('Y-m-d') }}">
    翌日
</a>

<table border="1" cellspacing="0" cellpadding="8">
    <tr>
        <th class="username">名前</th>
        <th class="clock_in">出勤</th>
        <th class="clock_out">退勤</th>
        <th class="break_time">休憩</th>
        <th class="break_total">合計</th>
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
            <a href="/admin/attendance/{{ $attendance->user_id }}/{{ $date->format('Y-m-d') }}" class="content">詳細</a>
            </td>
        </tr>
    @endforeach
</table>
</main>

@endsection