@extends('erp-mockup.layout')
@section('title', 'บิลขายหน้าร้าน')
@section('content')
<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
<div class="pop-head">
    <div><h1>บิลขายหน้าร้าน</h1><p>ตรวจสอบบิลจากเครื่อง POS ทุกสาขา</p></div>
    <div class="pop-head-actions">
        <button class="pop-btn"><i class="bi bi-arrow-repeat"></i> ซิงค์ใหม่ทั้งหมด</button>
        <button class="pop-btn"><i class="bi bi-download"></i> ส่งออก</button>
    </div>
</div>

@include('erp-mockup.partials.kpi-cards', ['items' => $kpis])
@include('erp-mockup.partials.toolbar', ['placeholder' => 'ค้นหาเลขที่บิล, แคชเชียร์...'])

<section class="pop-card">
    <table class="pop-table">
        <thead><tr>
            <th>เลขที่บิล</th><th>สาขา</th><th>เครื่อง POS</th><th>แคชเชียร์</th><th>เวลาขาย</th>
            <th>การชำระเงิน</th><th class="num">ยอดรวม</th><th>สถานะซิงค์</th><th>จัดการ</th>
        </tr></thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td style="font-variant-numeric:tabular-nums">{{ $order['receipt'] }}</td>
                <td>{{ $order['branch'] }}</td><td>{{ $order['terminal'] }}</td><td>{{ $order['cashier'] }}</td>
                <td class="pop-muted">{{ $order['time'] }}</td><td>{{ $order['method'] }}</td>
                <td class="num" style="font-weight:700">{{ $order['total'] }}</td>
                <td>@include('erp-mockup.partials.status-badge', ['status' => $order['sync']])</td>
                <td style="white-space:nowrap">
                    <button class="pop-btn" style="padding:4px 8px"><i class="bi bi-eye"></i></button>
                    <button class="pop-btn" style="padding:4px 8px"><i class="bi bi-printer"></i></button>
                    @if($order['sync'] === 'failed')
                        <button type="button" class="pop-btn" style="padding:4px 8px;color:var(--pop-primary)" 
                            onclick="const btn = this; btn.innerHTML = '<i class=\'bi bi-arrow-repeat\' style=\'display:inline-block; animation: spin 1s linear infinite\'></i>'; btn.disabled=true; setTimeout(() => { btn.outerHTML = '<span style=\'font-size:12px;color:#16a34a;margin-left:5px;font-weight:bold;\'><i class=\'bi bi-check-circle-fill\'></i> เสร็จสิ้น</span>'; }, 1000)">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    @endif
                    @if($order['sync'] !== 'voided')
                        <button class="pop-btn" style="padding:4px 8px;color:var(--pop-red)"><i class="bi bi-slash-circle"></i></button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
@endsection
