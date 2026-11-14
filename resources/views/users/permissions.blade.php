@extends('layouts.app')
@section('content')
<div class="panel">
    <div class="panel-head">
        <div><h2>نقش و مجوزهای کاربر: {{ $user->name }}</h2><small class="muted">{{ $user->username }}</small></div>
        <a href="{{ route('users.index') }}" class="btn secondary">بازگشت به کاربران</a>
    </div>
    <form method="post" action="{{ route('users.permissions.save',$user) }}" class="stack">
        @csrf @method('PUT')
        <label>نقش کاربر</label>
        <select name="role" required>@foreach($roles as $role)<option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $role->name }}</option>@endforeach</select>
        <h3>مجوزهای مستقیم این کاربر</h3>
        <p class="muted">این مجوزها علاوه بر مجوزهای نقش کاربر اعمال می‌شوند. برای حذف مجوز مستقیم، تیک آن را بردارید.</p>
        <div class="permission-grid">
            @foreach($permissions as $permission)
                <label class="check"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked($user->hasDirectPermission($permission->name))> {{ $permission->name }}</label>
            @endforeach
        </div>
        <button class="btn primary">ذخیره نقش و مجوزها</button>
    </form>
</div>
@endsection
