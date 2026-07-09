@extends('layouts.app')

@section('content')

<link href="{{ asset('css/user/show.css') }}" rel="stylesheet">

<h1>スタッフ一覧</h1>

<table border="1" cellspacing="0" cellpadding="8">
    <tr>
        <th class="username">名前</th>
        <th class="email">メールアドレス</th>
        <th class="content">月次勤怠</th>
    </tr>

    @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>
                <a href="/admin/attendance/list/{{ $user->id }}">詳細</a>
            </td>
        </tr>
    @endforeach

    @endsection