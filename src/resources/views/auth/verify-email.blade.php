@extends('layouts.app')

@section('content')
    <link href="{{ asset('css/auth/mail-a.css') }}" rel="stylesheet">

<main class="mail-page">
<div class="container">

    <span class="text1">登録していただいたメールアドレスに認証メールを送信しました。</span>
    <p class="text2">メール認証を完了してください。</p>

    <a href="/email/verify-confirm">
        <button type="button" class="link-btn">
            認証はこちらから
        </button>
    </a>

    <form method="POST" action="/email/verification-notification">
        @csrf

        <button type="submit" class="resend">
            認証メールを再送する
        </button>
    </form>

</div>
</main>

@endsection