@extends('layouts.app')

@section('content')
 <link href="{{ asset('css/auth/mail-b.css') }}" rel="stylesheet">

<h2>メール認証</h2>

<p>
    登録したメールアドレスに届いたメールを確認してください。
</p>

<p>
    メールに記載されている認証リンクをクリックすると、
    メール認証が完了します。
</p>

@endsection