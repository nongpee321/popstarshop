@extends('layout')
@section('title', 'ราคานาทีทอง (Flash Sale) - PopCentral')

@section('content')
<style>
    .flash-sale-wrapper {
        font-family: "Sarabun", "Segoe UI", Tahoma, sans-serif;
    }
    .hero-banner {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 50%, #f97316 100%);
        border-radius: 20px;
        color: white;
        padding: 24px 28px;
        box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.35);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .hero-banner::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
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
        box-shadow: 0 6px 20px rgba(0,0,0,0.04);
        overflow: hidden;
        margin-bottom: 28px;
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
        padding: 26px 28px;
    }
    .section-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px 20px;
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: 0.3px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .quick-preset-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #334155;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .quick-preset-btn:hover {
        background: #fee2e2;
        border-color: #f87171;
        color: #b91c1c;
        transform: translateY(-1px);
    }
    .day-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        transition: all 0.15s ease;
        user-select: none;
    }
    .day-pill:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .day-pill input {
        display: none;
    }
    .day-pill.active {
        border-color: #dc2626 !important;
        background: #fef2f2 !important;
        color: #b91c1c !important;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.15);
    }
    .product-search-dropdown {
        position: absolute;
        z-index: 1050;
        left: 0;
        right: 0;
        top: 100%;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        max-height: 280px;
        overflow-y: auto;
        padding: 6px;
        margin-top: 4px;
    }
    .product-search-item {
        padding: 10px 14px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.12s;
    }
    .product-search-item:hover {
        background: #fee2e2;
    }
    .product-quick-box {
        background: #ffffff;
        border: 1.5px solid #fca5a5;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.08);
        margin-bottom: 16px;
    }
    .item-table {
        background: #ffffff;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .btn-create-flash {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        border: none;
        color: #ffffff;
        font-weight: 700;
        padding: 12px 28px;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3);
        transition: all 0.2s;
        font-size: 14.5px;
    }
    .btn-create-flash:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(220, 38, 38, 0.4);
        color: #ffffff;
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
        padding: 15px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
    }
    .modern-table tbody tr:hover {
        background-color: #fef2f2;
    }
    .status-badge {
        padding: 5px 12px;
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
</style>

<div class="container-fluid px-4 py-4 flash-sale-wrapper" x-data="flashSaleApp()">

    {{-- Banner หัวหน้าจอ --}}
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
                ตั้งราคานาทีทองล่วงหน้า เลือกสินค้าและใส่ราคาพิเศษได้ทันที เมื่อถึงเวลาระบบ POS จะเปลี่ยนราคาอัตโนมัติ
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

    {{-- บัตรสรุปสถานะ (KPI Summary Cards) --}}
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

    {{-- การ์ดแบบฟอร์มสร้างแคมเปญใหม่ --}}
    <div class="form-card">
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-danger text-white rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-plus-lg fs-6"></i>
                </div>
                <div>
                    <h2 class="h6 fw-bold mb-0 text-dark">สร้างแคมเปญนาทีทองใหม่</h2>
                    <span class="text-muted small">กำหนดช่วงเวลา สาขา และเลือกสินค้าที่เข้าร่วมนาทีทองได้ทันทีในหน้านี้</span>
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

                {{-- ส่วนที่ 1: ข้อมูลแคมเปญและสาขา --}}
                <div class="section-box">
                    <div class="section-title">
                        <i class="bi bi-info-circle-fill text-danger fs-5"></i>
                        <span>1. ข้อมูลแคมเปญและสาขา</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-slate-700">
                                ชื่อกลุ่มนาทีทอง <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-megaphone-fill text-danger"></i></span>
                                <input name="name" required class="form-control border-start-0 ps-0" placeholder="เช่น นาทีทองหมูสามชั้นรอบเย็น / นาทีทองปลาดุก" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-slate-700">สาขาที่เข้าร่วม</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-shop text-primary"></i></span>
                                <select name="branch_id" class="form-select border-start-0 ps-0">
                                    <option value="">-- ทุกสาขา (แคมเปญกลาง) --</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" @selected(old('branch_id') == $b->id)>{{ $b->code }} - {{ $b->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label small fw-bold text-slate-700 mb-1">รหัสแคมเปญ</label>
                                <span class="text-muted" style="font-size: 11px;">(เว้นว่างได้ ระบบรันให้)</span>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-upc"></i></span>
                                <input name="code" x-model="code" class="form-control border-start-0 ps-0" placeholder="เว้นว่างได้ ระบบรันให้อัตโนมัติ" value="{{ old('code') }}">
                                <button type="button" @click="generateCode()" class="btn btn-outline-secondary btn-sm" title="สุ่มรหัสใหม่">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 2: ช่วงวันที่และเวลา พร้อม Quick Presets --}}
                <div class="section-box">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <div class="section-title mb-0">
                            <i class="bi bi-clock-fill text-danger fs-5"></i>
                            <span>2. กำหนดช่วงวันที่และเวลาที่มีผล (Date & Time Window)</span>
                        </div>
                        {{-- ปุ่มลัดเลือกเวลายอดนิยม --}}
                        <div class="d-flex flex-wrap align-items-center gap-1 mt-1 mt-md-0">
                            <span class="text-muted small me-1">ปุ่มลัดเวลา:</span>
                            <button type="button" class="quick-preset-btn" @click="setTimePreset('17:00', '19:30')">
                                <i class="bi bi-sunset-fill text-danger"></i> รอบเย็น (17:00-19:30)
                            </button>
                            <button type="button" class="quick-preset-btn" @click="setTimePreset('14:00', '16:00')">
                                <i class="bi bi-sun-fill text-warning"></i> รอบบ่าย (14:00-16:00)
                            </button>
                            <button type="button" class="quick-preset-btn" @click="setTimePreset('06:00', '09:00')">
                                <i class="bi bi-brightness-alt-high text-primary"></i> รอบเช้า (06:00-09:00)
                            </button>
                            <button type="button" class="quick-preset-btn" @click="setTimePreset('00:00', '23:59')">
                                <i class="bi bi-clock"></i> ทั้งวัน
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 pt-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-slate-700">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                            <input type="date" name="starts_date" required class="form-control" value="{{ old('starts_date', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label small fw-bold text-slate-700 mb-1">วันที่สิ้นสุด</label>
                                <label class="small text-muted mb-1" style="font-size: 11px; cursor: pointer;">
                                    <input type="checkbox" x-model="noEndDate" @change="toggleNoEndDate()" class="form-check-input me-1">ตลอดไป
                                </label>
                            </div>
                            <input type="date" name="ends_date" x-ref="endsDateInput" :disabled="noEndDate" class="form-control" value="{{ old('ends_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-slate-700">เวลาเริ่มนาทีทอง</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-clock"></i></span>
                                <input type="time" name="start_time" x-model="startTime" class="form-control border-start-0 ps-0" placeholder="เช่น 17:00">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-slate-700">เวลาสิ้นสุดนาทีทอง</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-clock-history"></i></span>
                                <input type="time" name="end_time" x-model="endTime" class="form-control border-start-0 ps-0" placeholder="เช่น 19:30">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 3: วันที่มีผลในสัปดาห์ & หมายเหตุ --}}
                <div class="section-box">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                        <div class="section-title mb-0">
                            <i class="bi bi-calendar-week-fill text-danger fs-5"></i>
                            <span>3. วันที่มีผลในสัปดาห์ & หมายเหตุ</span>
                        </div>
                        {{-- ปุ่มลัดเลือกวัน --}}
                        <div class="d-flex flex-wrap align-items-center gap-1 mt-1 mt-md-0">
                            <span class="text-muted small me-1">ปุ่มลัดวัน:</span>
                            <button type="button" class="quick-preset-btn" @click="setDayPreset('all')">
                                <i class="bi bi-calendar-check text-success"></i> ทุกวัน
                            </button>
                            <button type="button" class="quick-preset-btn" @click="setDayPreset('weekdays')">
                                <i class="bi bi-briefcase text-primary"></i> จันทร์-ศุกร์
                            </button>
                            <button type="button" class="quick-preset-btn" @click="setDayPreset('weekends')">
                                <i class="bi bi-cup-hot text-danger"></i> เสาร์-อาทิตย์
                            </button>
                            <button type="button" class="quick-preset-btn" @click="setDayPreset('none')">
                                ล้าง
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 pt-2">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-slate-700 d-block mb-2">
                                เลือกวันในสัปดาห์ที่มีผล <span class="text-muted fw-normal">(หากไม่เลือกวันใดเลย = มีผลทุกวันอัตโนมัติ)</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $dayConfig = [
                                        '0' => ['อาทิตย์', '#dc2626'],
                                        '1' => ['จันทร์', '#d97706'],
                                        '2' => ['อังคาร', '#db2777'],
                                        '3' => ['พุธ', '#16a34a'],
                                        '4' => ['พฤหัสบดี', '#ea580c'],
                                        '5' => ['ศุกร์', '#2563eb'],
                                        '6' => ['เสาร์', '#7c3aed']
                                    ];
                                @endphp
                                @foreach($dayConfig as $val => [$label, $color])
                                    <label class="day-pill" :class="{ 'active': selectedDays.includes('{{ $val }}') }">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $val }}" x-model="selectedDays">
                                        <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $color }}; display: inline-block;"></span>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 pt-2">
                            <label class="form-label small fw-bold text-slate-700">หมายเหตุ / เงื่อนไขบันทึกความจำ</label>
                            <input name="note" class="form-control" placeholder="เช่น จำกัดเฉพาะหน้าร้านสาขาวารินเท่านั้น หรือลดราคาช่วงค่ำระบายสต็อก" value="{{ old('note') }}">
                        </div>
                    </div>
                </div>

                {{-- ส่วนที่ 4: เลือกสินค้าและกำหนดราคานาทีทอง --}}
                <div class="section-box" style="background: #ffffff; border: 1.5px solid #fecaca;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mb-0 text-danger">
                            <i class="bi bi-box-seam-fill fs-5"></i>
                            <span>4. เลือกสินค้าที่เข้าร่วมนาทีทอง (กำหนดราคานาทีทองที่นี่ได้ทันที)</span>
                        </div>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-pill" x-text="'เลือกแล้ว ' + items.length + ' รายการ'"></span>
                    </div>

                    {{-- ช่องค้นหาสินค้า --}}
                    <div class="position-relative mb-3">
                        <div class="input-group">
                            <span class="input-group-text bg-white text-danger border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" x-model="searchQuery" @input.debounce.250ms="searchProducts()"
                                placeholder="พิมพ์ชื่อสินค้า, รหัส SKU, หรือยิงสแกนบาร์โค้ดเพื่อเลือกสินค้า..." class="form-control border-start-0 ps-0" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" x-show="searchQuery" @click="searchQuery = ''; searchResults = []">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        {{-- Dropdown ผลการค้นหา --}}
                        <div class="product-search-dropdown" x-show="searchResults.length > 0" x-cloak>
                            <template x-for="p in searchResults" :key="p.id">
                                <div class="product-search-item" @click="selectProduct(p)">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-light text-dark border me-1" x-text="p.sku_code"></span>
                                            <strong class="text-dark" x-text="p.name_th"></strong>
                                        </div>
                                        <div class="text-end small">
                                            <span class="text-muted me-2" x-text="'ราคาปกติ ฿' + Number(p.default_price).toFixed(2)"></span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary" x-text="'ทุน ฿' + Number(p.average_cost || 0).toFixed(2)"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- กล่องกำหนดราคาของสินค้าที่เลือก --}}
                    <div class="product-quick-box" x-show="selectedProduct" x-cloak x-transition>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger text-white px-2 py-1" x-text="selectedProduct?.sku_code"></span>
                                <strong class="fs-6 text-dark" x-text="selectedProduct?.name_th"></strong>
                            </div>
                            <button type="button" @click="cancelSelectProduct()" class="btn-close btn-sm" title="ปิด"></button>
                        </div>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">ราคาปกติปัจจุบัน</label>
                                <input type="text" readonly class="form-control form-control-sm bg-light text-end fw-semibold" :value="'฿ ' + Number(selectedProduct?.default_price || 0).toFixed(2)">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-danger mb-1">ราคานาทีทอง (฿) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" x-model="newItemPrice" x-ref="flashPriceInput" class="form-control form-control-sm text-end fw-bold text-danger" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted mb-1">จำกัดต่อบิล (เว้นว่าง = ไม่จำกัด)</label>
                                <input type="number" step="0.0001" min="0" x-model="newItemMaxQty" class="form-control form-control-sm text-end" placeholder="ไม่จำกัด">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="button" @click="addItem()" class="btn btn-success btn-sm w-100 fw-bold">
                                    <i class="bi bi-plus-lg me-1"></i> เพิ่มลงรายการ
                                </button>
                                <button type="button" @click="cancelSelectProduct()" class="btn btn-light btn-sm border">
                                    ยกเลิก
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ตารางรายการสินค้าที่เลือกแล้ว --}}
                    <div class="item-table table-responsive">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>สินค้า</th>
                                    <th class="text-end" style="width: 130px;">ราคาปกติ</th>
                                    <th class="text-end" style="width: 160px;">ราคานาทีทอง</th>
                                    <th class="text-center" style="width: 110px;">ส่วนลด</th>
                                    <th class="text-center" style="width: 130px;">จำกัด/บิล</th>
                                    <th class="text-end" style="width: 80px;">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="item.product_id">
                                    <tr>
                                        <td class="text-muted small" x-text="index + 1"></td>
                                        <td>
                                            <span class="badge bg-light text-dark border me-1" x-text="item.sku_code"></span>
                                            <span class="fw-semibold text-dark" x-text="item.name_th"></span>
                                            {{-- Hidden form inputs --}}
                                            <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                                            <input type="hidden" :name="'items[' + index + '][flash_price]'" :value="item.flash_price">
                                            <input type="hidden" :name="'items[' + index + '][max_qty_per_bill]'" :value="item.max_qty_per_bill">
                                        </td>
                                        <td class="text-end text-muted">
                                            <span x-text="'฿ ' + Number(item.default_price).toFixed(2)"></span>
                                        </td>
                                        <td class="text-end">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white text-danger border-end-0">฿</span>
                                                <input type="number" step="0.01" min="0" x-model.number="item.flash_price" class="form-control form-control-sm text-end fw-bold text-danger border-start-0 ps-0">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger bg-opacity-10 text-danger"
                                                x-text="item.default_price > 0 ? ('-' + Math.round((1 - (item.flash_price / item.default_price)) * 100) + '%') : '-'">
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border" x-text="item.max_qty_per_bill ? item.max_qty_per_bill : 'ไม่จำกัด'"></span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" @click="removeItem(index)" class="btn btn-outline-danger btn-sm p-1 px-2 rounded-circle" title="ลบรายการนี้">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-box-arrow-in-down fs-4 d-block mb-1 text-secondary"></i>
                                        ยังไม่ได้เลือกสินค้า — ค้นหาและกดเพิ่มสินค้าด้านบนได้ทันที (หรือสร้างแคมเปญก่อนแล้วไปเลือกสินค้าทีหลังก็ได้)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2">
                    <span class="text-muted small">
                        <i class="bi bi-shield-check text-success me-1"></i> สินค้าและราคานาทีทองที่เลือกจะถูกบันทึกและซิงค์ไปยังเครื่อง POS อัตโนมัติทันที
                    </span>
                    <div class="d-flex gap-2">
                        <button type="reset" @click="resetForm()" class="btn btn-light px-4 py-2 border rounded-pill text-muted">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> ล้างข้อมูล
                        </button>
                        <button type="submit" class="btn-create-flash rounded-pill">
                            <i class="bi bi-plus-circle me-1"></i> บันทึกและสร้างแคมเปญนาทีทอง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- การ์ดรายการแคมเปญนาทีทองทั้งหมด (Campaign Listing Table) --}}
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
                        <th style="width: 130px;">รหัส</th>
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

<script>
function flashSaleApp() {
    return {
        code: '{{ old("code", "FS-" . date("ymd") . "-" . rand(10, 99)) }}',
        startTime: '{{ old("start_time", "17:00") }}',
        endTime: '{{ old("end_time", "19:30") }}',
        noEndDate: false,
        selectedDays: @json(old('days_of_week', [])),

        // ค้นหาและจัดการสินค้า
        searchQuery: '',
        searchResults: [],
        selectedProduct: null,
        newItemPrice: '',
        newItemMaxQty: '',
        items: [],

        generateCode() {
            const dateStr = new Date().toISOString().slice(2, 10).replace(/-/g, '');
            const rand = Math.floor(1000 + Math.random() * 9000);
            this.code = 'FS-' + dateStr + '-' + rand;
        },

        setTimePreset(start, end) {
            this.startTime = start;
            this.endTime = end;
        },

        toggleNoEndDate() {
            if (this.noEndDate && this.$refs.endsDateInput) {
                this.$refs.endsDateInput.value = '';
            }
        },

        setDayPreset(type) {
            if (type === 'all') {
                this.selectedDays = ['0', '1', '2', '3', '4', '5', '6'];
            } else if (type === 'weekdays') {
                this.selectedDays = ['1', '2', '3', '4', '5'];
            } else if (type === 'weekends') {
                this.selectedDays = ['0', '6'];
            } else if (type === 'none') {
                this.selectedDays = [];
            }
        },

        searchProducts() {
            const q = this.searchQuery.trim();
            if (q.length < 1) {
                this.searchResults = [];
                return;
            }
            fetch('{{ route("flash-sales.search-products-global") }}?q=' + encodeURIComponent(q))
                .then(res => res.json())
                .then(data => {
                    this.searchResults = data;
                })
                .catch(() => {
                    this.searchResults = [];
                });
        },

        selectProduct(p) {
            this.selectedProduct = p;
            this.newItemPrice = Number(p.default_price || 0).toFixed(2);
            this.newItemMaxQty = '';
            this.searchResults = [];
            this.searchQuery = '';
            this.$nextTick(() => {
                if (this.$refs.flashPriceInput) {
                    this.$refs.flashPriceInput.focus();
                    this.$refs.flashPriceInput.select();
                }
            });
        },

        cancelSelectProduct() {
            this.selectedProduct = null;
            this.newItemPrice = '';
            this.newItemMaxQty = '';
        },

        addItem() {
            if (!this.selectedProduct) return;
            const price = parseFloat(this.newItemPrice);
            if (isNaN(price) || price < 0) {
                alert('กรุณาระบุราคานาทีทองที่ถูกต้อง');
                return;
            }

            const existing = this.items.find(i => i.product_id === this.selectedProduct.id);
            if (existing) {
                existing.flash_price = price;
                existing.max_qty_per_bill = this.newItemMaxQty ? parseFloat(this.newItemMaxQty) : null;
            } else {
                this.items.push({
                    product_id: this.selectedProduct.id,
                    sku_code: this.selectedProduct.sku_code,
                    name_th: this.selectedProduct.name_th,
                    default_price: parseFloat(this.selectedProduct.default_price || 0),
                    average_cost: parseFloat(this.selectedProduct.average_cost || 0),
                    flash_price: price,
                    max_qty_per_bill: this.newItemMaxQty ? parseFloat(this.newItemMaxQty) : null
                });
            }

            this.cancelSelectProduct();
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        resetForm() {
            this.generateCode();
            this.startTime = '17:00';
            this.endTime = '19:30';
            this.noEndDate = false;
            this.selectedDays = [];
            this.items = [];
            this.cancelSelectProduct();
        }
    };
}
</script>
@endsection
