@extends('layout')

@section('content')
    <h1>註冊</h1>
    <form method="POST" action="{{ route('register.store') }}" class="card">
        @csrf
        <div><input type="text" name="name" placeholder="姓名" value="{{ old('name') }}" required></div>
        <div><input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required></div>
        <div><input type="password" name="password" placeholder="密碼" required></div>
        <div><input type="password" name="password_confirmation" placeholder="確認密碼" required></div>
        <button type="submit">建立帳號</button>
    </form>

    @if($errors->any())
        <p style="color: red">{{ $errors->first() }}</p>
    @endif
@endsection
