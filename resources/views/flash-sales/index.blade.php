@extends('layout')
@section('title', 'ราคานาทีทอง (Flash Sale) - PopCentral')

@section('content')
<style>
    .flash-sale-wrapper {
        font-family: "Sarabun", "Segoe UI", Tahoma, sans-serif;
    }
    .hero-banner {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 50%, #ea580c 100%);
        border-radius: 20px;
        color: white;
        padding: 24px 28px;
        box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.3);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .stat-badge-card {
        background: #ffffff;
        border: 1px solid #fee2e2;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .stat-badge-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.08);
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .form-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .form-card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .form-card-body {
        padding: 24px;
    }
    .section-label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .day-selector-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s;
    }
    .day-selector-pill:hover {
        background: #e2e8f0;
    }
    .day-selector-pill input:checked + span {
        color: #b91c1c;
        font-weight: 700;
    }
    .day-selector-pill:has(input:checked) {
        background: #fee2e2;
        border-color: #f87171;
        color: #b91c1c;
    }
    .modern-table {
        margin-bottom: 0;
    }
    .modern-table th {
        background: #f8fafc;
        font-size: 12.5px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
    }
    .modern-table td {
        padding: 16px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    .modern-table tbody tr:hover {
        background-color: #fef2f2;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-active {
        background: #dcfce7;
        color: #15803d;
    }
    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }
    .btn-create-flash {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        border: none;
        color: #ffffff;
        font-weight: 700;
        padding: 12px 24px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        transition: all 0.2s;
    }
    .btn-create-flash:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.35);
        color: #ffffff;
    }
</style>

<div class="container-fluid px-4 py-4 flash-sale-wrapper">

    {{-- ── Banner หัวหน้าจอ ── --}}
    <div class="hero-banner d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white text-danger px-3 py-1 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-lightning-charge-fill me-1"></i> Flash Sale Engine
                </span>
                <span class="badge bg-black bg-opacity-25 px-2 py-1 rounded-pill small">POS Auto-Sync</span>
            </div>
            <h1 class="h3 fw-bold mb-1 text-white">แคมเปญราคานาทีทอง (Flash Sale)</h1>
            <p class="text-white text-opacity-90 small mb-0">
                ตั้งราคานาทีทองล่วงหน้า กำหนดช่วงเวลาเป็นรายวัน/สัปดาห์ เมื่อถึงเวลาระบบ POS จะเปลี่ยนราคาอัตโนมัติทันที
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('promotions.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-percent me-1"></i>โปรโมชั่นทั่วไป
            </a>
            <a href="{{ route('price-tables.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-table me-1"></i>ตารางราคาปกติ
            </a>
        </div>
    </div>

    {{-- ── บัตรสรุปสถานะ (KPI Summary Cards) ── --}}
    @php
        $totalCampaigns = $flashSales->total();
        $activeCampaigns = \App\Models\FlashSale::where('is_active', true)->count();
        $totalItems = \App\Models\FlashSaleItem::count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-badge-card">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">แคมเปญทั้งหมด</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ number_format($totalCampaigns) }} <span class="fs-6 text-muted fw-normal">แคมเปญ</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-badge-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">เปิดใช้งานขณะนี้</div>
                    <div class="h4 fw-bold mb-0 text-success">{{ number_format($activeCampaigns) }} <span class="fs-6 text-muted fw-normal">แคมเปญ</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-badge-card">
                <div class="stat-icon bg-warning bg-opacity-15 text-warning">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">สินค้าในนาทีทองทั้งหมด</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ number_format($totalItems) }} <span class="fs-6 text-muted fw-normal">รายการ</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── การ์ดแบบฟอร์มสร้างแคมเปญ (New Form Layout) ── --}}
    <div class="form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-danger text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-plus-lg fs-6"></i>
                </div>
                <div>
                    <h2 class="h6 fw-bold mb-0 text-dark">สร้างแคมเปญนาทีทองใหม่</h2>
                    <span class="text-muted small">กำหนดช่วงเวลา สาขา และเงื่อนไขวันในสัปดาห์</span>
                </div>
            </div>
            <div class="form-check form-switch mb-0">
                <input type="checkbox" name="is_active" value="1" checked form="createFlashSaleForm" class="form-check-input" id="flashActiveSwitch" style="cursor: pointer; width: 44px; height: 22px;">
                <label class="form-check-label fw-bold ms-2 text-dark small" for="flashActiveSwitch" style="cursor: pointer;">เปิดใช้งานทันที</label>
            </div>
        </div>

        <div class="form-card-body">
            <form id="createFlashSaleForm" method="post" action="{{ route('flash-sales.store') }}">
                @csrf

                {{-- ส่วนที่ 1: ข้อมูลพื้นฐาน --}}
                <div class="section-label">
                    <i class="bi bi-info-circle-fill text-danger"></i> 1. ข้อมูลแคมเปญและสาขา
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700">รหัสแคมเปญ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-upc"></i></span>
                            <input name="code" required class="form-control border-start-0 ps-0" placeholder="เช่น FS-001" value="{{ old('code', 'FS-' . date('ymd') . '-' . rand(10, 99)) }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-slate-700">ชื่อกลุ่มนาทีทอง <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-megaphone-fill"></i></span>
                            <input name="name" required class="form-control border-start-0 ps-0" placeholder="เช่น นาทีทองหมูสามชั้นรอบเย็น / นาทีทองปลาดุก" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-slate-700">สาขาที่เข้าร่วม</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-shop"></i></span>
                            <select name="branch_id" class="form-select border-start-0 ps-0">
                                <option value="">-- ทุกสาขา (แคมเปญกลาง) --</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" @selected(old('branch_id') == $b->id)>{{ $b->code }} - {{ $b->name_th }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 2: ช่วงวันที่และเวลา --}}
                <div class="section-label">
                    <i class="bi bi-clock-fill text-danger"></i> 2. กำหนดช่วงวันที่และเวลาที่มีผล (Date & Time Window)
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                        <input type="date" name="starts_date" required class="form-control" value="{{ old('starts_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700">วันที่สิ้นสุด <span class="text-muted fw-normal">(เว้นว่างถ้าไม่มีกำหนด)</span></label>
                        <input type="date" name="ends_date" class="form-control" value="{{ old('ends_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700">เวลาเริ่มนาทีทอง</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-clock"></i></span>
                            <input type="time" name="start_time" class="form-control border-start-0 ps-0" placeholder="เช่น 17:00" value="{{ old('start_time', '17:00') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-slate-700">เวลาสิ้นสุดนาทีทอง</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-clock-history"></i></span>
                            <input type="time" name="end_time" class="form-control border-start-0 ps-0" placeholder="เช่น 19:30" value="{{ old('end_time', '19:00') }}">
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 3: เงื่อนไขวันในสัปดาห์ & หมายเหตุ --}}
                <div class="section-label">
                    <i class="bi bi-calendar-week-fill text-danger"></i> 3. วันที่มีผลในสัปดาห์ & เงื่อนไขเพิ่มเติม
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-slate-700 d-block mb-2">
                            เลือกวันในสัปดาห์ที่มีผล <span class="text-muted fw-normal">(หากไม่เลือกวันใดเลย = มีผลทุกวัน)</span>
                        </label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach([
                                '0' => ['อาทิตย์', 'text-danger'],
                                '1' => ['จันทร์', 'text-warning'],
                                '2' => ['อังคาร', 'text-pink'],
                                '3' => ['พุธ', 'text-success'],
                                '4' => ['พฤหัสบดี', 'text-orange'],
                                '5' => ['ศุกร์', 'text-primary'],
                                '6' => ['เสาร์', 'text-purple']
                            ] as $val => [$label, $colorClass])
                                <label class="day-selector-pill">
                                    <input type="checkbox" name="days_of_week[]" value="{{ $val }}" class="form-check-input mt-0" @checked(in_array((string)$val, old('days_of_week', []), true))>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-slate-700">หมายเหตุ / เงื่อนไขบันทึกความจำ</label>
                        <input name="note" class="form-control" placeholder="เช่น จำกัดเฉพาะหน้าร้านสาขาวารินเท่านั้น หรือลดราคาช่วงค่ำระบายสต็อก" value="{{ old('note') }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                    <button type="reset" class="btn btn-light px-4 py-2 border rounded-pill">ล้างข้อมูล</button>
                    <button type="submit" class="btn-create-flash rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> สร้างแคมเปญนาทีทอง → กำหนดสินค้า
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── การ์ดรายการแคมเปญนาทีทอง (Campaign Listing Table) ── --}}
    <div class="form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-list-stars text-danger fs-5"></i>
                <h2 class="h6 fw-bold mb-0 text-dark">รายการแคมเปญนาทีทองทั้งหมด</h2>
            </div>
            <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">
                ทั้งหมด {{ number_format($flashSales->total()) }} รายการ
            </span>
        </div>

        <div class="table-responsive">
            <table class="table modern-table align-middle">
                <thead>
                    <tr>
                        <th style="width: 120px;">รหัส</th>
                        <th>กลุ่มนาทีทอง</th>
                        <th>สาขา</th>
                        <th>ช่วงวันที่</th>
                        <th>ช่วงเวลา</th>
                        <th>วันที่มีผล</th>
                        <th class="text-center">จำนวนสินค้า</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-end" style="width: 140px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($flashSales as $flashSale)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border fw-bold px-2 py-1">
                                {{ $flashSale->code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $flashSale->name }}</div>
                            @if($flashSale->note)
                                <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $flashSale->note }}</div>
                            @endif
                        </td>
                        <td>
                            @if($flashSale->branch)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded">
                                    <i class="bi bi-shop me-1"></i>{{ $flashSale->branch->name_th }}
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded">
                                    <i class="bi bi-globe me-1"></i>ทุกสาขา
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-slate-700">
                                {{ $flashSale->starts_date?->thaiDate() ?? '-' }}
                            </div>
                            <div class="text-muted small">
                                ถึง {{ $flashSale->ends_date?->thaiDate() ?? 'ไม่มีกำหนด' }}
                            </div>
                        </td>
                        <td>
                            <div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                                <i class="bi bi-clock me-1"></i>
                                {{ $flashSale->start_time ? $flashSale->start_time->format('H:i') : '00:00' }} - {{ $flashSale->end_time ? $flashSale->end_time->format('H:i') : '23:59' }}
                            </div>
                        </td>
                        <td>
                            @if(empty($flashSale->days_of_week))
                                <span class="badge bg-light text-muted border">ทุกวัน</span>
                            @else
                                @php
                                    $dowMap = ['0'=>'อา','1'=>'จ','2'=>'อ','3'=>'พ','4'=>'พฤ','5'=>'ศ','6'=>'ส'];
                                    $dows = explode(',', $flashSale->days_of_week);
                                @endphp
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($dows as $d)
                                        @if(isset($dowMap[$d]))
                                            <span class="badge bg-danger text-white rounded-pill px-2 py-0.5" style="font-size: 11px;">
                                                {{ $dowMap[$d] }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $flashSale->items_count > 0 ? 'bg-primary' : 'bg-light text-muted border' }} rounded-pill px-2.5 py-1">
                                {{ number_format($flashSale->items_count) }} รายการ
                            </span>
                        </td>
                        <td class="text-center">
                            @if($flashSale->is_active)
                                <span class="status-badge status-active">
                                    <i class="bi bi-check-circle-fill"></i> เปิดใช้งาน
                                </span>
                            @else
                                <span class="status-badge status-inactive">
                                    <i class="bi bi-dash-circle"></i> ปิดใช้งาน
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('flash-sales.show', $flashSale) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                <i class="bi bi-gear-fill me-1"></i> จัดการสินค้า
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 mb-3 d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="bi bi-lightning-charge fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">ยังไม่มีแคมเปญนาทีทองในระบบ</h6>
                                <p class="text-muted small mb-3">กรอกแบบฟอร์มด้านบนเพื่อเริ่มสร้างแคมเปญแรกของคุณ</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($flashSales->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $flashSales->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
