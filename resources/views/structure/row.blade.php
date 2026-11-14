@extends('layouts.app')
@section('content')
<div class="panel qr-print-area">
    <div class="panel-head no-print">
        <h2>جزئیات موقعیت {{ $row->code }}</h2>
        <button class="btn primary" type="button" onclick="window.print()">چاپ برچسب QR</button>
    </div>
    <div class="print-heading"><strong>برچسب موقعیت انبار</strong></div>
    <div class="row-detail qr-detail">
        <div class="qr-card">
            <img src="{{ $qrDataUri }}" width="300" height="300" alt="QR Code ردیف {{ $row->code }}" onerror="this.closest('.qr-card').classList.add('qr-error')">
            <h3>{{ $row->code }}</h3>
            <small>{{ $row->public_id }}</small>
        </div>
        <div class="qr-location">
            <p><b>انبار:</b> {{ $row->rack->aisle->warehouse->name }}</p>
            <p><b>راهرو:</b> {{ $row->rack->aisle->name }}</p>
            <p><b>قفسه:</b> {{ $row->rack->name }}</p>
            <p><b>ردیف:</b> {{ $row->name }}</p>
        </div>
    </div>
    <h3 class="no-print">کالاهای موجود</h3>
    <div class="table-wrap no-print"><table>
        <thead><tr><th>کالا</th><th>SKU</th><th>موجودی</th></tr></thead>
        <tbody>
        @forelse($row->inventories as $i)
            <tr><td>{{ $i->product->name }}</td><td>{{ $i->product->sku }}</td><td>{{ $i->quantity }} {{ $i->product->unit }}</td></tr>
        @empty<tr><td colspan="3">این ردیف خالی است.</td></tr>@endforelse
        </tbody>
    </table></div>
</div>
@endsection
@push('scripts')
<style>
.print-heading{display:none}
@media print {
    @page { size: 100mm 100mm; margin: 5mm; }
    body{background:#fff!important;color:#000!important}
    .sidebar,.topbar,footer,.no-print{display:none!important}
    .shell,.main,.content{display:block!important;width:100%!important;margin:0!important;padding:0!important}
    .qr-print-area{border:0!important;box-shadow:none!important;margin:0!important;padding:0!important}
    .print-heading{display:block!important;text-align:center;font-size:14pt;margin-bottom:4mm}
    .qr-detail{display:flex!important;align-items:center!important;justify-content:center!important;gap:8mm!important;margin:0!important}
    .qr-card{border:0!important;padding:0!important;width:55mm!important;text-align:center!important}
    .qr-card img{display:block!important;width:48mm!important;height:48mm!important;object-fit:contain!important;margin:0 auto!important}
    .qr-card h3{font-size:13pt!important;margin:2mm 0!important}
    .qr-card small{font-size:6pt!important;overflow-wrap:anywhere}
    .qr-location{font-size:8pt!important}
}
</style>
@endpush
