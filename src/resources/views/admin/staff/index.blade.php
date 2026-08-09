@extends('layouts.app')

@section('content')

<link href="{{ asset('css/admin/staff/index.css') }}" rel="stylesheet">

<div class="container">
<h1 class="title">スタッフ一覧</h1>

    <table>
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
                    <a href="/admin/attendance/list/{{ $user->id }}" class="content-nav">詳細</a>
                </td>
            </tr>
        @endforeach
    </table>
</div>

    @endsection