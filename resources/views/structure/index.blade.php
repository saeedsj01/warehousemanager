@extends('layouts.app')
@section('content')
@php($labels=['aisles'=>'راهرو','racks'=>'قفسه','rows'=>'ردیف'])
<div class="panel">
    <div class="panel-head"><h2>افزودن {{ $labels[$type] }} جدید</h2></div>
    <form method="post" action="{{ route($type.'.store') }}" class="form-grid">
        @csrf
        <div><label>{{ ['aisles'=>'انبار والد','racks'=>'راهرو والد','rows'=>'قفسه والد'][$type] }}</label>
            <select name="parent_id" required><option value="">انتخاب کنید</option>
                @foreach($parents as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->code??($p->warehouse->name??$p->aisle->warehouse->name??'') }}</option>
                @endforeach
            </select>
        </div>
        <div><label>نام</label><input name="name" required></div>
        <div><label>کد یکتا</label><input name="code" required></div>
        <div><label>توضیحات</label><input name="description"></div>
        <div><button class="btn primary">ثبت {{ $labels[$type] }}</button></div>
    </form>
</div>
<div class="panel">
    <div class="panel-head"><h2>فهرست {{ $labels[$type] }}‌ها</h2><span>{{ $items->total() }} مورد</span></div>
    <div class="table-wrap"><table>
        <thead><tr><th>نام</th><th>کد</th><th>موقعیت والد</th><th>وضعیت</th><th>عملیات</th></tr></thead>
        <tbody>
        @forelse($items as $item)
            @php($parentId = $type==='aisles' ? $item->warehouse_id : ($type==='racks' ? $item->aisle_id : $item->rack_id))
            <tr>
                <td>{{ $item->name }}</td><td>{{ $item->code }}</td>
                <td>@if($type==='aisles'){{ $item->warehouse->name }}@elseif($type==='racks'){{ $item->aisle->warehouse->name }} / {{ $item->aisle->name }}@else{{ $item->rack->aisle->warehouse->name }} / {{ $item->rack->aisle->name }} / {{ $item->rack->name }}@endif</td>
                <td><span class="status {{ $item->is_active?'active':'inactive' }}">{{ $item->is_active?'فعال':'غیرفعال' }}</span></td>
                <td class="actions">
                    @if($type==='rows')<a class="btn secondary small" href="{{ route('rows.show',$item) }}">جزئیات و QR</a>@endif
                    <details class="edit-details"><summary class="btn secondary small">ویرایش</summary>
                        <form method="post" action="{{ route($type.'.update',$item->id) }}" class="edit-form">
                            @csrf @method('PUT')
                            <label>{{ ['aisles'=>'انبار والد','racks'=>'راهرو والد','rows'=>'قفسه والد'][$type] }}</label>
                            <select name="parent_id" required>@foreach($parents as $p)<option value="{{ $p->id }}" @selected($parentId==$p->id)>{{ $p->name }} — {{ $p->code??'' }}</option>@endforeach</select>
                            <label>نام</label><input name="name" value="{{ $item->name }}" required>
                            <label>کد</label><input name="code" value="{{ $item->code }}" required>
                            <label>توضیحات</label><textarea name="description">{{ $item->description }}</textarea>
                            <button class="btn primary small">ذخیره تغییرات</button>
                        </form>
                    </details>
                    <form method="post" action="{{ route($type.'.toggle',$item->id) }}">@csrf @method('PATCH')
                        <button class="btn {{ $item->is_active?'warning':'success-btn' }} small">{{ $item->is_active?'غیرفعال‌سازی':'فعال‌سازی' }}</button>
                    </form>
                    <form method="post" action="{{ route($type.'.destroy',$item->id) }}" onsubmit="return confirm('در صورت داشتن زیرمجموعه یا موجودی حذف ممکن نیست. ادامه؟')">@csrf @method('DELETE')
                        <button class="btn danger small">حذف</button>
                    </form>
                </td>
            </tr>
        @empty<tr><td colspan="5" class="empty">موردی ثبت نشده است.</td></tr>@endforelse
        </tbody>
    </table></div>{{ $items->links() }}
</div>
@endsection
