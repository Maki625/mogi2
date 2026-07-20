@extends('layouts.app')

@section('content')

<link href="{{ asset('css/admin/attendance/show.css') }}" rel="stylesheet">

<h1>勤怠詳細</h1>

@if($errors->any())
<ul class="error-list">
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

@php
    $break1 = $correctionBreaks[0] ?? null;
    $break2 = $correctionBreaks[1] ?? null;
@endphp

@if(!$pending)
<form method="POST" action="/admin/attendance/fix/{{ $attendance->id }}">
    @csrf
    @method('PUT')
@endif

<table class="detail-table">

    <tr>
        <th class="label">名前</th>
        <td class="value">{{ $user->name }}</td>
    </tr>

    <tr>
        <th class="label">日付</th>
        <td class="value">{{ $date->format('Y年n月j日') }}</td>
    </tr>

    <tr>
        <th class="label">出勤・退勤</th>
        <td class="value">
            <input
                type="time"
                name="clock_in"
                value="{{ old('clock_in', $correction?->clock_in?->format('H:i') ?? optional($attendance->clock_in)->format('H:i')) }}"
                @if($pending) disabled @endif>

            <input
                type="time"
                name="clock_out"
                value="{{ old('clock_out', $correction?->clock_out?->format('H:i') ?? optional($attendance->clock_out)->format('H:i')) }}"
                @if($pending) disabled @endif>
        </td>
    </tr>

    <tr>
        <th class="label">休憩</th>
        <td class="value">
            <input
                type="time"
                name="break1_start"
                value="{{ old('break1_start', $break1?->break_start?->format('H:i')) }}"
                @if($pending) disabled @endif>

            <span>〜</span>

            <input
                type="time"
                name="break1_end"
                value="{{ old('break1_end', $break1?->break_end?->format('H:i')) }}"
                @if($pending) disabled @endif>
        </td>
    </tr>

    <tr>
        <th class="label">休憩2</th>
        <td class="value">
            <input
                type="time"
                name="break2_start"
                value="{{ old('break2_start', $break2?->break_start?->format('H:i')) }}"
                @if($pending) disabled @endif>

            <span>〜</span>

            <input
                type="time"
                name="break2_end"
                value="{{ old('break2_end', $break2?->break_end?->format('H:i')) }}"
                @if($pending) disabled @endif>
        </td>
    </tr>

    <tr>
        <th class="label">備考</th>
        <td class="value">
            <textarea
                class="text-area"
                name="reason"
                @if($pending) disabled @endif>{{ old(
                    'reason',
                    $pending
                        ? $correction?->reason
                        : $attendance?->reason
                ) }}</textarea>
        </td>
    </tr>

</table>

@if($pending)
    <p class="pending-message">
        修正申請が提出されています。<br>
    <a href="/admin/stamp_correction_request/list" class="nav-link1">申請一覧</a>から内容を確認してください。
    </p>
@endif

@if(!$pending)
    <button type="submit" name="send" class="send-btn" value="fix">
    修正する
    </button>
</form>
@endif

@if(session('success'))
    <p class="success-message">
        {{ session('success') }}
    </p>
@endif

@endsection