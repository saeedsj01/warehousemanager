@extends('layouts.app')
@section('content')
<div class="panel">
    <div class="panel-head"><h2>افزودن انبار جدید</h2></div>
    <form method="post" action="{{ route('warehouses.store') }}" class="form-grid">
        @csrf
        <div><label>نام انبار</label><input name="name" required></div>
        <div><label>کد انبار</label><input name="code" required></div>
        <div class="wide"><label>توضیحات</label><input name="description"></div>
        <div><button class="btn primary">ثبت انبار</button></div>
    </form>
</div>
<div class="panel">
    <div class="panel-head"><h2>فهرست انبارها</h2><span>{{ $items->total() }} انبار</span></div>
    <div class="table-wrap"><table>
        <thead><tr><th>نام</th><th>کد</th><th>تعداد راهرو</th><th>وضعیت</th><th>عملیات</th></tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->name }}</td><td>{{ $item->code }}</td><td>{{ $item->aisles_count }}</td>
                <td><span class="status {{ $item->is_active?'active':'inactive' }}">{{ $item->is_active?'فعال':'غیرفعال' }}</span></td>
                <td class="actions">
                    <details class="edit-details"><summary class="btn secondary small">ویرایش</summary>
                        <form method="post" action="{{ route('warehouses.update',$item) }}" class="edit-form">
                            @csrf @method('PUT')
                            <label>نام</label><input name="name" value="{{ $item->name }}" required>
                            <label>کد</label><input name="code" value="{{ $item->code }}" required>
                            <label>توضیحات</label><textarea name="description">{{ $item->description }}</textarea>
                            <button class="btn primary small">ذخیره تغییرات</button>
                        </form>
                    </details>
                    <form method="post" action="{{ route('warehouses.toggle',$item) }}">@csrf @method('PATCH')
                        <button class="btn {{ $item->is_active?'warning':'success-btn' }} small">{{ $item->is_active?'غیرفعال‌سازی':'فعال‌سازی' }}</button>
                    </form>
                    <form method="post" action="{{ route('warehouses.destroy',$item) }}" onsubmit="return confirm('در صورت داشتن راهرو حذف ممکن نیست. ادامه؟')">@csrf @method('DELETE')
                        <button class="btn danger small" {{ $item->aisles_count?'disabled title=دارای_زیرمجموعه':'' }}>حذف</button>
                    </form>
                </td>
            </tr>
        @empty<tr><td colspan="5" class="empty">انبار ثبت نشده است.</td></tr>@endforelse
        </tbody>
    </table></div>{{ $items->links() }}
</div>
@endsection
