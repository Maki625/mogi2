@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/user/request.css') }}" rel="stylesheet">

<h2>申請一覧</h2>
<table border="1" cellspacing="0" cellpadding="8">

    <div class="section-header">
        <a href="" class="section-link {{ request('tab') === 'sold' || !request()->has('tab') ? 'active' : '' }}">承認待ち</a>
        <a href="" class="section-link {{ request('tab') === 'bought' ? 'active' : '' }}">承認済み</a>
    </div>
    <hr class="divider">

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
        <td>{{ $request->attendance->work_date }}</td>
        <td>{{ $request->reason }}</td>
        <td>{{ $request->created_at }}</td>
        <td>
            <a href="">詳細</a>
        </td>
    </tr>
    @endforeach

@endsection