@extends('layouts.app')

@section('content')

<link href="{{ asset('css/user/show.css') }}" rel="stylesheet">

<div class="container">
    <h1 class="title">勤怠詳細</h1>

        @if($errors->any())
        <ul class="error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif

        <form class="form" method="POST" action="/attendance/fix/{{ $date->format('Y-m-d') }}">
        @csrf

        <table class="detail-table">

        <tr>
            <th class="label">名前</th>
            <td class="value">{{ $user->name }}</td>
        </tr>

        <tr>
            <th class="label">日付</th>
            <td class="value">
                <span class="date-year">{{ $date->format('Y年') }}</span>
                <span class="date-month">{{ $date->format('n月j日') }}</span></td>
        </tr>

        <tr>
            <th class="label">出勤・退勤</th>
            <td class="value">
                <input type="time"
                name="clock_in"
                value="{{ old('clock_in', $correction?->clock_in?->format('H:i') ?? optional($attendance?->clock_in)->format('H:i')) }}"
                class="@if($pending) pending-input @endif"
                @if($pending) disabled @endif>

                <span class="time-line">〜</span>

                <input type="time"
                name="clock_out"
                value="{{ old('clock_out', $correction?->clock_out?->format('H:i') ?? optional($attendance?->clock_out)->format('H:i')) }}"
                class="@if($pending) pending-input-out @endif"
                @if($pending) disabled @endif>
            </td>
        </tr>

        <tr>
            <th class="label">休憩</th>
            @php
            $break1 = $correctionBreaks[0] ?? null;
            $break2 = $correctionBreaks[1] ?? null;
            @endphp
            <td class="value">
                <input type="time" name="break1_start"
                value="{{ old('break1_start', $break1?->break_start?->format('H:i')) }}"
                class="@if($pending) pending-input @endif"
                @if($pending) disabled @endif>

                <span class="time-line">〜</span>

                <input type="time" name="break1_end"
                value="{{ old('break1_end', $break1?->break_end?->format('H:i')) }}"
                class="@if($pending) pending-input-out @endif"
                @if($pending) disabled @endif>
            </td>
        </tr>

        <tr>
        <th class="label">休憩2</th>
        <td class="value">
            <input type="time" name="break2_start"
            value="{{ old('break2_start', $break2?->break_start?->format('H:i')) }}"
            class="@if($pending) pending-input @endif"
            @if($pending) disabled @endif>

            <span class="time-line">〜</span>

            <input type="time" name="break2_end"
            value="{{ old('break2_end', $break2?->break_end?->format('H:i')) }}"
            class="@if($pending) pending-input-out @endif"
            @if($pending) disabled @endif>
        </td>
        </tr>

        <tr>
            <th class="label">備考</th>
            <td class="value">
                <textarea name="reason" class="text-area @if($pending) pending-input @endif"
                @if($pending) disabled @endif
                >{{ old( 'reason',
                $pending
                ? $correction?->reason
                : $attendance?->reason) }}
                </textarea>
            </td>
        </tr>

        </table>
        @if ($pending)
            <p class="text-danger">
                ※承認待ちのため修正はできません。
            </p>
        @else
        <button type="submit" name="send" class="send-btn" value="fix">
            修正する</button>
        @endif

        </form>
</div>
@endsection