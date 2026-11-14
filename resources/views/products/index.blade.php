@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-head">
        <h2>تعریف کالای جدید</h2>
    </div>

    <form method="post" action="{{ route('products.store') }}" class="form-grid">
        @csrf
        <div>
            <label>کد کالا (SKU)</label>
            <input name="sku" required>
        </div>
        <div>
            <label>نام کالا</label>
            <input name="name" required>
        </div>
        <div>
            <label>بارکد</label>
            <input name="barcode">
        </div>
        <div>
            <label>واحد شمارش</label>
            <input name="unit" value="عدد" required>
        </div>
        <div class="wide">
            <label>توضیحات</label>
            <input name="description">
        </div>
        <div>
            <button class="btn primary">ثبت کالا</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>فهرست کالاها</h2>
        <span>{{ number_format($items->total()) }} کالا</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>نام کالا</th>
                    <th>بارکد</th>
                    <th>واحد</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $p)
                    <tr>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->barcode ?? '—' }}</td>
                        <td>{{ $p->unit }}</td>
                        <td>{{ $p->is_active ? 'فعال' : 'غیرفعال' }}</td>
                        <td>
                            <form method="post" action="{{ route('products.destroy', $p) }}" onsubmit="return confirm('کالا حذف شود؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger small">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">کالایی ثبت نشده است.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- صفحه‌بندی همیشه بیرون از جدول و در پایین آن قرار می‌گیرد. --}}
    <div class="products-pagination-wrap">
        {{ $items->onEachSide(1)->links('vendor.pagination.warehouse') }}
    </div>
</div>
@endsection
