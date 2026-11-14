@extends('layouts.app')
@section('content')
<div class="panel">
    <div class="panel-head"><h2>افزودن کاربر</h2></div>
    <form method="post" action="{{ route('users.store') }}" class="form-grid">
        @csrf
        <div><label>نام نمایشی</label><input name="name" required></div>
        <div><label>نام کاربری</label><input name="username" required></div>
        <div><label>رمز عبور</label><input type="password" name="password" required></div>
        <div><label>تکرار رمز عبور</label><input type="password" name="password_confirmation" required></div>
        <div><label>نقش اولیه</label><select name="role" required>@foreach($roles as $role)<option value="{{ $role->name }}">{{ $role->name }}</option>@endforeach</select></div>
        <div><button class="btn primary">ایجاد کاربر</button></div>
    </form>
</div>
<div class="panel">
    <div class="panel-head"><h2>کاربران سامانه</h2><div class="actions"><span>{{ $items->total() }} کاربر</span>@can('roles.view')<a class="btn secondary small" href="{{ route('roles.index') }}">مدیریت نقش‌ها و مجوزها</a>@endcan</div></div>
    <div class="table-wrap"><table>
        <thead><tr><th>نام</th><th>نام کاربری</th><th>نقش</th><th>وضعیت</th><th>عملیات</th></tr></thead>
        <tbody>
        @forelse($items as $u)
            <tr>
                <td>{{ $u->name }}</td><td>{{ $u->username }}</td><td>{{ $u->getRoleNames()->join('، ') }}</td>
                <td><span class="status {{ $u->is_active?'active':'inactive' }}">{{ $u->is_active?'فعال':'غیرفعال' }}</span></td>
                <td class="actions">
                    <details class="edit-details"><summary class="btn secondary small">ویرایش کاربر</summary>
                        <form method="post" action="{{ route('users.update',$u) }}" class="edit-form">
                            @csrf @method('PUT')
                            <label>نام نمایشی</label><input name="name" value="{{ $u->name }}" required>
                            <label>نام کاربری</label><input name="username" value="{{ $u->username }}" required>
                            <label>رمز جدید (اختیاری)</label><input type="password" name="password">
                            <label>تکرار رمز جدید</label><input type="password" name="password_confirmation">
                            <label>نقش</label><select name="role" required>@foreach($roles as $role)<option value="{{ $role->name }}" @selected($u->hasRole($role->name))>{{ $role->name }}</option>@endforeach</select>
                            <input type="hidden" name="is_active" value="{{ $u->is_active?1:0 }}">
                            <button class="btn primary small">ذخیره</button>
                        </form>
                    </details>
                    <a class="btn secondary small" href="{{ route('users.permissions',$u) }}">نقش و مجوزها</a>
                    @if($u->id !== auth()->id())
                        <form method="post" action="{{ route('users.toggle',$u) }}">@csrf @method('PATCH')
                            <button class="btn {{ $u->is_active?'warning':'success-btn' }} small">{{ $u->is_active?'غیرفعال‌سازی':'فعال‌سازی' }}</button>
                        </form>
                        <form method="post" action="{{ route('users.destroy',$u) }}" onsubmit="return confirm('کاربر حذف شود؟')">@csrf @method('DELETE')<button class="btn danger small">حذف</button></form>
                    @endif
                </td>
            </tr>
        @empty<tr><td colspan="5" class="empty">کاربری ثبت نشده است.</td></tr>@endforelse
        </tbody>
    </table></div>{{ $items->links() }}
</div>
@endsection
