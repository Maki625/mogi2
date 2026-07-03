@extends('layouts.app')

@section('content')

<link href="{{ asset('css/user/show.css') }}" rel="stylesheet">

<h1>勤怠詳細</h1>

@foreach (collect($errors->all())->unique() as $error)
    <li>{{ $error }}</li>
@endforeach

<table class="detail-table">

    <tr>
        <th class="label">名前</th>
        <td class="value">{{ $user->name }}</td>
    </tr>

    <tr>
        <th class="label">日付</th>
        <td class="value">{{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}</td>
    </tr>

    <form class="form" method="POST" action="/attendance/fix/{{ $attendance->id }}">
    @csrf

    <tr>
        <th class="label">出勤・退勤</th>
        <td class="value">
            <input
            type="time"
            name="clock_in"
            value="{{ old('clock_in', $attendance?->clock_in?->format('H:i')) }}">

            <span>～</span>

            <input
            type="time"
            name="clock_out"
            value="{{ old('clock_out', $attendance?->clock_out?->format('H:i')) }}">
        </td>
    </tr>

    <tr>
        <th class="label">休憩</th>
        @php
        $break1 = $attendance?->workbreaks[0] ?? null;
        $break2 = $attendance?->workbreaks[1] ?? null;
        @endphp
        <td class="value">
            <input
            type="time"
            name="break1_start"
            value="{{ old('break1_start', $break1?->break_start?->format('H:i')) }}">

            <span>〜</span>

            <input
            type="time"
            name="break1_end"
            value="{{ old('break1_end', $break1?->break_end?->format('H:i')) }}">

        </td>
    </tr>

    <tr>
    <th class="label">休憩2</th>
    <td class="value">
        <input
            type="time"
            name="break2_start"
            value="{{ old('break2_start', $break2?->break_start?->format('H:i')) }}">

        <span>〜</span>

        <input
            type="time"
            name="break2_end"
            value="{{ old('break2_end', $break2?->break_end?->format('H:i')) }}">
    </td>
    </tr>

    <tr>
        <th class="label">備考</th>
        <td class="value">
            <textarea class="text-area" name="reason">{{ old('reason') }}</textarea>
        </td>
    </tr>

</table>

<button type="submit" name="send" class="send-btn" value="fix">修正する</button>
</form>

@endsection