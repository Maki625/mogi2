@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/user/request.css') }}" rel="stylesheet">

<div class="container">
<h2 class="title">申請一覧</h2>
    <div class="section-header">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}"
        class="section-link {{ $tab === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'approved']) }}"
        class="section-link {{ $tab === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>
    <hr class="divider">

    <table border="1" cellspacing="0" cellpadding="8">

    <tr>
        <th class="status">状態</th>
        <th class="username">名前</th>
        <th class="date">対象日時</th>
        <th class="reason">申請理由</th>
        <th class="request_date">申請日時</th>
        <th class="content">詳細</th>
    </tr>

    @foreach($requests as $request)
    <tr>
        <td>@if($request->status === 'pending')
        承認待ち
        @elseif($request->status === 'approved')
        承認済み
        @endif</td>
        <td>{{ $request->user->name }}</td>
        <td>{{ $request->attendance->work_date->format('Y/m/d') }}</td>
        <td>{{ $request->reason }}</td>
        <td>{{ $request->created_at->format('Y/m/d') }}</td>
        <td>
        <a href="/attendance/detail/{{ $request->attendance->work_date->format('Y-m-d') }}">詳細</a>
        </td>
    </tr>
    @endforeach
</table>
</div>
</main>

@endsection