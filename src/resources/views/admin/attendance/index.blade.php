<link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')

<header class="header">
    <div class="header__inner">
        <a class="header__logo" href="/">
        <img src="/images/logo.svg" alt="logo">
        </a>
</header>

@section('content')

<main>
<link href="{{ asset('css/admin/index.css') }}" rel="stylesheet">

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
        <th class="username">名前</th>
        <th class="clock_in">出勤</th>
        <th class="clock_out">退勤</th>
        <th class="break_time">休憩</th>
        <th class="break_total">合計</th>
        <th class="content">詳細</th>
    </tr>
    @foreach ($dates as $date)

    @php
        $attendance = $attendances->firstWhere(
            'work_date',
            $date->format('Y-m-d')
        );
    @endphp

        <tr>
            <td>{{ $date->format('m/d(D)') }}</td>
            <td>{{ $attendance?->clock_in?->format('H:i') }}</td>
            <td>{{ $attendance?->clock_out?->format('H:i') }}</td>
            <td>{{ $attendance?->show_break_time }}</td>
        <td></td>
        <td>
            <a href="/attendance/detail/{{ $date->format('Y-m-d') }}" class="content">詳細</a>
        </td>
    </tr>
    @endforeach
</table>
</main>

@endsection