@extends('layouts.app')

@section('content')
<link href="{{ asset('css/user/report.css') }}" rel="stylesheet">

<main class="report-page">

<div class="container">
    <h3 class="title">マイ勤怠レポート</h3>
    <h4 class="text-a">過去6ヶ月の勤怠データから集計しています。</h4>
    <a class="text-a">基本サマリー</a>

    <div class="summary">
        <div class="summary-box">
            <p class="box-title">総労働時間</p>
            <span class="content">
                {{ intdiv($totalWorkMinutes, 60) }}h
                {{ $totalWorkMinutes % 60 }}m
            </span>
        </div>

        <div class="summary-box">
            <p class="box-title">総残業時間</p>
            <span class="content">
                {{ intdiv($totalOvertimeMinutes, 60) }}h
                {{ $totalOvertimeMinutes % 60 }}m
            </span>
        </div>

        <div class="summary-box">
            <p class="box-title">平均労働時間/日</p>
            <span class="content">
                {{ intdiv($averageWorkMinutes, 60) }}h
                {{ $averageWorkMinutes % 60 }}m
            </span>
        </div>
    </div>

    <a class="text-a">月次推移 (過去6ヶ月)</a>

    <table class="m-table">
        <tr>
            <th class="month">月</th>
            <th class="work-time">労働時間</th>
            <th class="overtime">残業時間</th>
        </tr>

        @foreach($monthlyReports as $report)
            <tr>
                <td class="month">{{ $report['month'] }}</td>

                <td class="work-time">
                    {{ intdiv($report['work_minutes'], 60) }}h
                    {{ $report['work_minutes'] % 60 }}m
                </td>

                <td class="overtime">
                    {{ intdiv($report['overtime_minutes'], 60) }}h
                    {{ $report['overtime_minutes'] % 60 }}m
                </td>
            </tr>
        @endforeach
    </table>

    <h4 class="text-a">今月の異常検知</h4>
    <p class="box-title">基準：始業09:00/終業 18:00/長時間労働は一日10時間超</p>

    <div class="summary">
        <div class="summary-box">
            <p class="box-title">遅刻回数</p>
            <span class="content">{{ $lateCount }}回</span>
        </div>

        <div class="summary-box">
            <p class="box-title">早退回数</p>
            <span class="content">{{ $earlyLeaveCount }}回</span>
        </div>

        <div class="summary-box">
            <p class="box-title">長時間労働回数</p>
            <span class="content">{{ $longWorkCount }}回</span>
        </div>
    </div>
</div>

</main>

@endsection