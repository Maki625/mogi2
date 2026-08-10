@extends('layouts.app')

@section('content')
<link href="{{ asset('css/user/report.css') }}" rel="stylesheet">

<div class="container">
    <h3 class="title">マイ勤怠レポート</h3>
    <h4 class="text-a">過去6ヶ月の勤怠データから集計しています。</h4>
    <a class="text-a">基本サマリー</a>

    <div class="summery">
    <p>
        総労働時間
        {{ intdiv($totalWorkMinutes, 60) }}h
        {{ $totalWorkMinutes % 60 }}m
    </p>

    <p>
        総残業時間
        {{ intdiv($totalOvertimeMinutes, 60) }}h
        {{ $totalOvertimeMinutes % 60 }}m
    </p>

    <p>
        平均労働時間/日
        {{ intdiv($averageWorkMinutes, 60) }}h
        {{ $averageWorkMinutes % 60 }}m
    </p>
    </div>

    <a class="text-a">月次推移 (過去6ヶ月)</a>

    <table class="m-table">
        <tr>
            <th>月</th>
            <th>労働時間</th>
            <th>残業時間</th>
        </tr>

        @foreach($monthlyReports as $report)
            <tr>
                <td>{{ $report['month'] }}</td>

                <td>
                    {{ intdiv($report['work_minutes'], 60) }}h
                    {{ $report['work_minutes'] % 60 }}m
                </td>

                <td>
                    {{ intdiv($report['overtime_minutes'], 60) }}h
                    {{ $report['overtime_minutes'] % 60 }}m
                </td>
            </tr>
        @endforeach
    </table>

    <h4 class="text-a">異常検知</h4>
    <a class="text-a">基準：始業09:00/終業 18:00/長時間労働は一日10時間超

    <div class="summery">
            <p>遅刻回数
            {{ $lateCount }}回</p>

            <p>早退回数
            {{ $earlyLeaveCount }}回</p>

            <p>長時間労働回数
            {{ $longWorkCount }}回</p>
    </div>

</div>
@endsection
