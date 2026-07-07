@extends('layouts.app')

@section('content')

<link href="{{ asset('css/user/show.css') }}" rel="stylesheet">

<h1>スタッフ一覧</h1>

    <tr>
        <th class="username">名前</th>
        <th class="email">メールアドレス</th>
        <th class="content">月次勤怠</th>
    </tr>
