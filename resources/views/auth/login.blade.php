@extends('layout')

@section('content')
    <h1>登入</h1>
    <form method="POST" action="{{ route('login.attempt') }}" class="card">
        @csrf
        <div><input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required></div>
        <div><input type="password" name="password" placeholder="密碼" required></div>
        <button type="submit">登入</button>
    </form>

    @if($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif

    <a href="{{ route('register') }}">還沒有帳號？註冊</a>
@endsection
