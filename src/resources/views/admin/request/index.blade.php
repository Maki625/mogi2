@extends('layouts.app')

@section('content')

<main>
<link href="{{ asset('css/admin/request/index.css') }}" rel="stylesheet">

<div class="container">
    <h1 class="title">申請一覧</h1>
        <div class="section-header">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}"
        class="section-link {{ $tab === 'pending' ? 'active' : '' }}">承認待ち</a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'approved']) }}"
        class="section-link {{ $tab === 'approved' ? 'active' : '' }}">承認済み</a>
        </div>
        <hr class="divider">

        <table>
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
                @if($request->status === 'pending')
                <a href="/stamp_correction_request/approve/{{ $request->id }}" class="content-nav">詳細</a>
                @elseif($request->status === 'approved')
                <a href="/admin/attendance/{{ $request->user_id }}/{{ $request->attendance->work_date->format('Y-m-d') }}" class="content-nav">詳細</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
</main>

@endsection