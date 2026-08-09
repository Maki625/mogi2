@extends('layouts.app')

@section('content')

<link href="{{ asset('css/admin/request/show.css') }}" rel="stylesheet">

<div class="container">
    <h1 class="title">修正申請詳細</h1>

    @php
        $break1 = $correctionBreaks[0] ?? null;
        $break2 = $correctionBreaks[1] ?? null;
    @endphp

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
                <input type="time"
                    value="{{ $correction?->clock_in?->format('H:i') }}"
                    disabled>

                <input type="time"
                    value="{{ $correction?->clock_out?->format('H:i') }}"
                    disabled>
            </td>
        </tr>

        <tr>
            <th class="label">休憩</th>
            <td class="value">
                <input type="time"
                    value="{{ $break1?->break_start?->format('H:i') }}"
                    disabled>

                <span>〜</span>

                <input type="time"
                    value="{{ $break1?->break_end?->format('H:i') }}"
                    disabled>
            </td>
        </tr>

        <tr>
            <th class="label">休憩2</th>
            <td class="value">
                <input type="time"
                    value="{{ $break2?->break_start?->format('H:i') }}"
                    disabled>

                <span>〜</span>

                <input type="time"
                    value="{{ $break2?->break_end?->format('H:i') }}"
                    disabled>
            </td>
        </tr>

        <tr>
            <th class="label">備考</th>
            <td class="value">
                <textarea
                    class="text-area"
                    disabled>{{ $correction?->reason }}</textarea>
            </td>
        </tr>

    </table>

    @if($pending)
    <form method="POST" action="/admin/correction/{{ $correction->id }}/approve">
        @csrf

        <button type="submit" class="send-btn">
            承認する
        </button>
    </form>
    @endif

    @if($approved)
        <p class="approved-btn">
            承認済み
        </p>
    @endif
</div>

@endsection