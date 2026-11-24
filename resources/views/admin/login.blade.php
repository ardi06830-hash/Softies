@extends('admin.layout')
@section('title', 'Login')
@section('content')
<form method="POST" action="{{ route('admin.login.post') }}" class="box">
    @csrf
    <h1 style="margin-bottom:16px">Login Admin</h1>
    <label>Nama <input type="text" name="name" value="{{ old('name') }}" required autofocus></label>
    <label>Password <input type="password" name="password" required></label>
    <button class="btn" type="submit">Masuk</button>
</form>
@endsection
