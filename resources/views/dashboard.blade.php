@extends('layout')

@section('title', 'ภาพรวมกิจการ')
@section('page-title', 'ภาพรวมกิจการ')

@push('styles')
<style>
    /* Theme Variables for Dual Mode */
    :root, html[data-color-mode="light"], [data-bs-theme="light"] {
        --dash-bg: #f8fafc;
        --dash-surface: #ffffff;
        --dash-text: #0f172a;
        --dash-muted: #64748b;
        --dash-border: #e2e8f0;
        
        --dash-red: #be123c;
        --dash-red-soft: #ffe4e6;
        --dash-red-text: #e11d48;
        
        --dash-green: #059669;
        --dash-green-soft: #d1fae5;
        --dash-green-text: #059669;
        
        --dash-yellow: #ca8a04;
        --dash-yellow-soft: #fef08a;
        --dash-yellow-text: #ca8a04;
        
        --dash-blue: #0284c7;
        --dash-blue-soft: #e0f2fe;

        --dash-alert-bg: #fff1f2;
        --dash-alert-border: #fecdd3;
        --dash-alert-text: #9f1239;

        --dash-input-bg: #ffffff;
    }

    /* Dark Mode Overrides */
    body.dark-mode, [data-bs-theme="dark"], [data-theme="dark"], html[data-color-mode="night"] {
        --dash-bg: #0f172a;
        --dash-surface: #1e293b;
        --dash-text: #f1f5f9;
        --dash-muted: #94a3b8;
        --dash-border: #334155;
        
        --dash-red: #f43f5e;
        --dash-red-soft: rgba(244, 63, 94, 0.15);
        --dash-red-text: #fda4af;
        
        --dash-green: #10b981;
        --dash-green-soft: rgba(16, 185, 129, 0.15);
        --dash-green-text: #6ee7b7;
        
        --dash-yellow: #f59e0b;
        --dash-yellow-soft: rgba(245, 158, 11, 0.15);
        --dash-yellow-text: #fde68a;
        
        --dash-blue: #38bdf8;
        --dash-blue-soft: rgba(56, 189, 248, 0.15);

        --dash-alert-bg: rgba(225, 29, 72, 0.15);
        --dash-alert-border: rgba(225, 29, 72, 0.3);
        --dash-alert-text: #fda4af;

        --dash-input-bg: #0f172a;
    }

    /* Utility Classes mapped to Theme Variables */
    .dash-card {
        background-color: var(--dash-surface) !important;
        color: var(--dash-text) !important;
        border: 1px solid var(--dash-border) !important;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .dash-header { padding: 16px 24px; }
    .dash-kpi { padding: 20px; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }
    .dash-kpi-link { display: block; height: 100%; color: inherit; text-decoration: none; }
    .dash-kpi-link:hover { color: inherit; text-decoration: none; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(15, 23, 42, .12); }
    .dash-kpi-link:focus-visible { outline: 3px solid var(--dash-blue); outline-offset: 3px; border-radius: 12px; }
    .dash-chart { padding: 20px; }
    .dash-chart-drilldown { cursor: pointer; }
    
    .dash-text { color: var(--dash-text) !important; }
    .dash-muted { color: var(--dash-muted) !important; }
    .dash-border { border-color: var(--dash-border) !important; }
    
    .dash-text-red { color: var(--dash-red-text) !important; }
    .dash-bg-red-soft { background-color: var(--dash-red-soft) !important; color: var(--dash-red-text) !important; }
    
    .dash-text-green { color: var(--dash-green-text) !important; }
    .dash-bg-green-soft { background-color: var(--dash-green-soft) !important; color: var(--dash-green-text) !important; }
    
    .dash-text-yellow { color: var(--dash-yellow-text) !important; }
    .dash-bg-yellow-soft { background-color: var(--dash-yellow-soft) !important; color: var(--dash-yellow-text) !important; }

    .dash-bg-blue-soft { background-color: var(--dash-blue-soft) !important; color: var(--dash-blue) !important; }

    .dash-alert {
        background-color: var(--dash-alert-bg) !important;
        border: 1px solid var(--dash-alert-border) !important;
        color: var(--dash-alert-text) !important;
        border-radius: 12px;
        padding: 12px 20px;
    }
    
    .dash-filter-bar {
        background-color: var(--dash-surface) !important;
        border: 1px solid var(--dash-border) !important;
        border-radius: 12px;
        padding: 12px;
    }

    .dash-input {
        background-color: var(--dash-input-bg) !important;
        color: var(--dash-text) !important;
        border-color: var(--dash-border) !important;
    }

    .dash-logo-box {
        width: 32px; height: 32px;
        background-color: var(--dash-red);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #ffffff !important;
        font-size: 14px;
    }

    .dash-kpi-icon {
        width: 40px; height: 40px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }

    .dash-table { color: var(--dash-text) !important; width: 100%; margin: 0; }
    .dash-table th {
        background-color: var(--dash-red) !important;
        color: #ffffff !important;
        font-weight: 600; padding: 12px 16px; border: none;
    }
    .dash-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--dash-border) !important;
        vertical-align: middle;
        background-color: transparent !important;
        color: var(--dash-text) !important;
    }
    .dash-table tr:last-child td { border-bottom: none !important; }
    .dash-alert-table { border: 2px solid var(--dash-red) !important; }

    .dash-bg-red { background-color: var(--dash-red) !important; color: #ffffff !important; }
    .dash-bg-surface { background-color: var(--dash-surface) !important; }
    .dash-bg { background-color: var(--dash-bg) !important; }
    .dash-text-blue { color: var(--dash-blue) !important; }

    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 767.98px) {
        /* Header: stack vertically */
        .dash-header { padding: 12px 16px; }
        .dash-header .d-flex.align-items-center.gap-4 {
            gap: 16px !important;
            justify-content: space-between;
            width: 100%;
        }
        /* Branch selector full width */
        .dash-header select.form-select { min-width: unset !important; width: 100% !important; }
        .dash-header .ms-3 { margin-left: 0 !important; width: 100%; margin-top: 8px; }

        /* KPI cards: 2 per row on phone */
        .row.g-3 > .col-lg-3,
        .row.g-3 > .col-md-3 { flex: 0 0 50%; max-width: 50%; }

        /* KPI text size */
        .dash-kpi .fs-3 { font-size: 1.3rem !important; }
        .dash-kpi .fs-7 { font-size: 0.7rem !important; }
        .dash-kpi { padding: 14px; }
        .dash-kpi-icon { width: 32px; height: 32px; font-size: 14px; }

        /* Chart cards */
        .dash-chart { padding: 12px; }

        /* Tables: horizontal scroll */
        .dash-alert-table .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .dash-table th, .dash-table td { padding: 8px 10px; font-size: 0.78rem; }

        /* Filter bar: stack */

        /* Alert banner */
        .dash-alert { flex-direction: column !important; gap: 8px; align-items: flex-start !important; }
        .dash-alert a.btn { width: 100%; text-align: center; }

        /* Summary numbers in header */
        .text-end.mt-3 { text-align: left !important; }
    }

    @media (max-width: 575.98px) {
        /* On very small phones: 1 chart per row */
        .row.g-3 > .col-lg-6,
        .row.g-3 > .col-md-6 { flex: 0 0 100%; max-width: 100%; }

        /* KPI still 2 per row */
        .row.g-3 > .col-lg-3,
        .row.g-3 > .col-md-3 { flex: 0 0 50%; max-width: 50%; }
    }
</style>
@endpush

@section('content')

@php
    $alertCount = clone $expiryAlerts; $alertCount = $alertCount->count();
    $lowStockCount = clone $lowStock; $lowStockCount = $lowStockCount->count();
    $totalAlerts = $alertCount + $lowStockCount;
@endphp

<!-- Header -->
<div class="dash-card dash-header mb-3 d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center gap-3">
    <div class="d-flex flex-wrap align-items-center gap-3 w-100" style="flex: 0 1 auto;">
        <div class="d-flex align-items-center gap-3">
            <div class="dash-logo-box"><i class="bi bi-pie-chart-fill"></i></div>
            <div>
                <div class="dash-text-red fw-semibold fs-7 text-nowrap">POPSTAR EXECUTIVE BOARD</div>
                <h5 class="mb-0 fw-semibold dash-text text-nowrap">ภาพรวมเพื่อการตัดสินใจ</h5>
            </div>
        </div>
        <div class="dash-bg-green-soft px-3 py-1 rounded-pill fs-7 fw-semibold ms-auto ms-xl-3 d-flex align-items-center gap-2 text-nowrap">
            <i class="bi bi-circle-fill" style="font-size: 8px;"></i> ระบบออนไลน์
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4 text-start text-xl-end mt-2 mt-xl-0">
        <div class="text-nowrap">
            <div class="dash-muted fs-7">ยอดขายรวม</div>
            <div class="fw-bold fs-5 dash-text">฿{{ number_format($summary->total_sales, 2) }}</div>
        </div>
        <div class="text-nowrap">
            <div class="dash-muted fs-7">รายการขาย</div>
            <div class="fw-bold fs-5 dash-text">{{ number_format($summary->receipt_count) }} <span class="fs-7 dash-muted fw-normal">บิล</span></div>
        </div>
        <div class="text-nowrap">
            <div class="dash-muted fs-7">แจ้งเตือนคงค้าง</div>
            <div class="fw-bold fs-5 dash-text-red">{{ $totalAlerts }} <i class="bi bi-bell-fill fs-7"></i></div>
        </div>
        <div class="ms-0 ms-md-3 mt-2 mt-md-0 flex-grow-1 flex-md-grow-0">
            <form id="branchFilterForm" method="GET" action="{{ route('dashboard') }}" class="m-0">
                <input type="hidden" name="from" value="{{ $from }}">
                <input type="hidden" name="to" value="{{ $to }}">
                <select name="branch_id" class="form-select dash-input fw-semibold w-100" style="min-width: 180px;" onchange="document.getElementById('branchFilterForm').submit();">
                    <option value="all" {{ $currentBranchId === null ? 'selected' : '' }}>รวมยอดทุกสาขา</option>
                    @foreach($allBranches as $branch)
                        <option value="{{ $branch->id }}" {{ $currentBranchId == $branch->id ? 'selected' : '' }}>สาขา: {{ $branch->name_th }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>
</div>

<!-- Alert Banner -->
@if($totalAlerts > 0)
<div class="dash-alert mb-3 d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        @if($alertCount > 0)
            พบ Lot ใกล้หมดอายุหรือข้อมูลวันหมดอายุไม่ครบ <strong class="text-decoration-underline">{{ $alertCount }} รายการ</strong> ควรจัดการก่อนขาย
        @else
            พบสินค้าสต๊อกต่ำหรือติดลบ <strong class="text-decoration-underline">{{ $lowStockCount }} รายการ</strong> ควรตรวจสอบ
        @endif
    </div>
    <a href="{{ route('reports.index', ['category' => 'inventory', 'report' => $alertCount > 0 ? 'inventory.expiring_stock' : 'inventory.stock_alerts']) }}"
       class="btn btn-sm btn-danger fw-bold rounded-pill px-3">ตรวจสอบทันที ➔</a>
</div>
@endif

<!-- Filter Bar -->
<div class="dash-filter-bar mb-3 d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
    <form class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3 m-0 flex-grow-1 w-100">
        <input type="hidden" name="branch_id" value="{{ $currentBranchId ?? 'all' }}">
        <div class="d-flex align-items-center dash-muted fw-bold text-nowrap"><i class="bi bi-calendar3 me-2"></i> ช่วงวันที่:</div>
        
        <div class="d-flex flex-row gap-2 w-100 w-lg-auto">
            <div class="input-group input-group-sm flex-nowrap w-50 w-lg-auto">
                <span class="input-group-text dash-input border-end-0">จาก</span>
                <input type="date" name="from" value="{{ $from }}" class="form-control dash-input border-start-0 w-100" style="min-width: 110px;">
            </div>
            <div class="input-group input-group-sm flex-nowrap w-50 w-lg-auto">
                <span class="input-group-text dash-input border-end-0">ถึง</span>
                <input type="date" name="to" value="{{ $to }}" class="form-control dash-input border-start-0 w-100" style="min-width: 110px;">
            </div>
        </div>
        
        <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-lg-auto">
            <div class="btn-group w-100 w-sm-auto">
                <a href="{{ route('dashboard', ['from' => now()->toDateString(), 'to' => now()->toDateString(), 'branch_id' => $currentBranchId ?? 'all']) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold py-2 py-lg-1 text-nowrap">วันนี้</a>
                <a href="{{ route('dashboard', ['from' => now()->subDays(6)->toDateString(), 'to' => now()->toDateString(), 'branch_id' => $currentBranchId ?? 'all']) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold py-2 py-lg-1 text-nowrap">7 วัน</a>
                <a href="{{ route('dashboard', ['from' => now()->subDays(29)->toDateString(), 'to' => now()->toDateString(), 'branch_id' => $currentBranchId ?? 'all']) }}" class="btn btn-sm btn-outline-secondary dash-input dash-muted fw-semibold py-2 py-lg-1 text-nowrap">30 วัน</a>
            </div>
            <button type="submit" class="btn btn-sm btn-danger fw-bold rounded px-4 py-2 py-lg-1 w-100 w-sm-auto text-nowrap"><i class="bi bi-funnel-fill me-1"></i> แสดงผล</button>
        </div>
    </form>
    
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 fs-7 w-100 w-lg-auto mt-2 mt-lg-0 border-top border-lg-0 pt-3 pt-lg-0">
        <div class="dash-muted text-nowrap"><i class="bi bi-arrow-repeat me-1"></i> รีเฟรชใน <span id="dashboardRefreshCountdown" class="fw-bold">60</span> วิ</div>
        <div class="dash-bg-red-soft px-3 py-1 rounded fw-bold text-nowrap"><i class="bi bi-shop me-1"></i> {{ $scopeBranchName ?? 'รวมทุกสาขา' }}</div>
    </div>
</div>

<!-- Extra Banner -->
@if($alertCount > 0)
<div class="dash-card mb-4 p-3 d-flex justify-content-between align-items-center" style="border-color: var(--dash-red-soft) !important;">
    <div class="d-flex align-items-center gap-3">
        <div class="dash-bg-red text-white rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-box-seam fs-4"></i>
        </div>
        <div>
            <h6 class="mb-1 fw-bold dash-text">Lot ต้องจัดการ {{ $alertCount }} รายการ <span class="badge dash-bg-red-soft ms-2 rounded-pill">เร่งด่วน</span></h6>
            <div class="dash-muted fs-7">รวม Lot หมดอายุ, ใกล้ถึงวันที่เตือน และ Lot ที่ยังไม่ได้ระบุวันหมดอายุในคลังสินค้า</div>
        </div>
    </div>
    <form action="{{ route('reports.index') }}" method="GET" class="m-0">
        <input type="hidden" name="category" value="inventory">
        <input type="hidden" name="report" value="expiring_stock">
        <input type="hidden" name="branch_id" value="{{ $currentBranchId ?? 'all' }}">
        <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4"><i class="bi bi-journal-text me-1"></i> เปิดรายงาน Lot</button>
    </form>
</div>
@endif

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.index', ['category' => 'sales', 'report' => 'daily_by_channel', 'from' => $from, 'to' => $to, 'branch_id' => $currentBranchId ?? 'all']) }}"
           class="dash-card dash-kpi dash-kpi-link position-relative overflow-hidden"
           aria-label="ดูรายละเอียดรายได้ทั้งหมดหลังหักส่วนลด">
            <div class="d-flex justify-content-between mb-3">
                <div class="dash-muted fw-semibold">รายได้ทั้งหมด (สุทธิ)</div>
                <div class="dash-kpi-icon dash-bg-green-soft"><i class="bi bi-cash-stack"></i></div>
            </div>
            <div class="fs-3 fw-bold mb-3 dash-text">฿{{ number_format($summary->total_sales, 2) }}</div>
            <div class="d-flex justify-content-between dash-muted fs-7">
                <div class="{{ $summary->sales_change_percent !== null && $summary->sales_change_percent < 0 ? 'dash-text-red' : 'dash-text-green' }} fw-bold">
                    <i class="bi {{ $summary->sales_change_percent !== null && $summary->sales_change_percent < 0 ? 'bi-graph-down-arrow' : 'bi-graph-up-arrow' }}"></i>
                    {{ $summary->sales_change_percent === null ? '-' : $summary->sales_change_percent.'%' }} เทียบช่วงก่อน
                </div>
                <div>เฉลี่ยต่อบิล: <span class="dash-text fw-bold">฿{{ number_format($summary->average_bill, 2) }}</span></div>
            </div>
            <div class="dash-muted fs-8 mt-2">รวมทุกช่องทางหลังหักส่วนลด · คลิกเพื่อดูรายละเอียด</div>
        </a>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.index', ['category' => 'management', 'report' => 'gross_margin', 'from' => $from, 'to' => $to, 'branch_id' => $currentBranchId ?? 'all']) }}"
           class="dash-card dash-kpi dash-kpi-link"
           aria-label="ดูรายละเอียดรายงานกำไรขั้นต้น">
            <div class="d-flex justify-content-between mb-3">
                <div class="dash-muted fw-semibold">กำไรขั้นต้นที่คำนวณได้</div>
                <div class="dash-kpi-icon dash-bg-red-soft"><i class="bi bi-graph-up"></i></div>
            </div>
            <div class="fs-3 fw-bold mb-3 dash-text">฿{{ number_format($summary->gross_profit, 2) }}</div>
            <div class="d-flex justify-content-between align-items-center dash-muted fs-7">
                <div>ต้นทุนเอกสาร: <span class="dash-text fw-bold">฿{{ number_format($summary->total_cogs, 2) }}</span></div>
                <div class="dash-bg-green-soft px-2 py-1 rounded fw-bold">Gross margin {{ $summary->gross_margin_percent === null ? '-' : number_format($summary->gross_margin_percent, 1).'%' }}</div>
            </div>
            <div class="dash-muted fs-8 mt-2"><i class="bi bi-box-arrow-in-up-right me-1"></i>คลิกเพื่อดูรายละเอียด</div>
        </a>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.index', ['category' => 'ar', 'report' => 'open_items', 'branch_id' => $currentBranchId ?? 'all']) }}"
           class="dash-card dash-kpi dash-kpi-link"
           aria-label="ดูรายละเอียดรายการลูกหนี้คงค้าง">
            <div class="d-flex justify-content-between mb-3">
                <div class="dash-muted fw-semibold">ลูกหนี้คงค้าง</div>
                <div class="dash-kpi-icon dash-bg-yellow-soft"><i class="bi bi-person-fill-exclamation"></i></div>
            </div>
            <div class="fs-3 fw-bold mb-3 dash-text">฿{{ number_format($receivables->open_amount, 2) }}</div>
            <div class="d-flex justify-content-between dash-muted fs-7">
                <div>เอกสารค้าง: <span class="dash-text fw-bold">{{ number_format($receivables->open_count) }} รายการ</span></div>
                <div>เกินกำหนดชำระ: <span class="{{ $receivables->overdue_amount > 0 ? 'dash-text-red' : 'dash-text-green' }} fw-bold">฿{{ number_format($receivables->overdue_amount, 2) }}</span></div>
            </div>
            <div class="dash-muted fs-8 mt-2"><i class="bi bi-box-arrow-in-up-right me-1"></i>คลิกเพื่อดูรายละเอียด</div>
        </a>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('reports.index', ['category' => 'pos', 'report' => 'pos_receipts', 'from' => $from, 'to' => $to, 'branch_id' => $currentBranchId ?? 'all']) }}"
           class="dash-card dash-kpi dash-kpi-link"
           aria-label="ดูรายละเอียดใบเสร็จ POS">
            <div class="d-flex justify-content-between mb-3">
                <div class="dash-muted fw-semibold">ยอดขาย POS</div>
                <div class="dash-kpi-icon dash-bg-blue-soft"><i class="bi bi-printer-fill"></i></div>
            </div>
            <div class="fs-3 fw-bold mb-3 dash-text">{{ number_format($posTerminalSummary->sum('amount'), 2) }} ฿</div>
            <div class="d-flex justify-content-between align-items-center dash-muted fs-7">
                <div>ส่วนลด POS: <span class="dash-text fw-bold">฿{{ number_format($summary->total_discount, 2) }}</span></div>
                <div class="dash-bg-green-soft px-2 py-1 rounded-pill fw-bold"><i class="bi bi-circle-fill fs-8 me-1"></i> POS พร้อมใช้งาน</div>
            </div>
            <div class="dash-muted fs-8 mt-2"><i class="bi bi-box-arrow-in-up-right me-1"></i>คลิกเพื่อดูรายละเอียด</div>
        </a>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="dash-card dash-chart">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold dash-text"><i class="bi bi-circle-fill dash-text-red fs-8 me-2"></i> ยอดขายรายวัน</div>
                <div class="dash-muted fs-7">คลิกจุดเพื่อดูรายละเอียด · {{ $from }} - {{ $to }}</div>
            </div>
            <div class="dash-chart-drilldown" style="height: 250px;" title="คลิกจุดบนกราฟเพื่อดูรายงานยอดขายของวันนั้น">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dash-card dash-chart">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold dash-text"><i class="bi bi-circle-fill text-secondary fs-8 me-2"></i> ยอดขายแยกสาขา</div>
                <div class="dash-muted fs-7">คลิกแท่งเพื่อเจาะดูสาขา · ฿{{ number_format($summary->total_sales, 2) }}</div>
            </div>
            <div class="dash-chart-drilldown" style="height: 250px;" title="คลิกแท่งของสาขาเพื่อดูรายงานยอดขายแยกพนักงาน">
                <canvas id="branchChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="dash-card dash-chart">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="fw-bold dash-text"><i class="bi bi-circle-fill dash-text-green fs-8 me-2"></i> สัดส่วนเอกสารขาย</div>
                <div class="dash-input dash-muted px-2 py-1 rounded fs-7 fw-bold border">100% POS</div>
            </div>
            <div style="height: 250px; display: flex; align-items: center; justify-content: center; position: relative;">
                <canvas id="salesMixChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row g-3">
    <!-- Top Products -->
    <div class="col-lg-6">
        <div class="dash-card overflow-hidden">
            <div class="d-flex justify-content-between align-items-center p-3 dash-border border-bottom">
                <div class="fw-bold dash-text"><i class="bi bi-star-fill dash-text-yellow me-2"></i> สินค้าขายดี Top 10</div>
                <div class="dash-muted fs-7">อัปเดตตามรอบบิลขาย</div>
            </div>
            <div class="table-responsive">
                <table class="table dash-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>รหัส (SKU)</th>
                            <th class="text-end">จำนวน</th>
                            <th class="text-end">ยอดขาย (฿)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $item)
                        <tr>
                            <td>
                                <div class="fw-bold dash-text">{{ $item->name_th }}</div>
                                <div class="dash-muted" style="font-size: 0.7rem;"><i class="bi bi-shop"></i> {{ $item->branch_name }}</div>
                            </td>
                            <td class="dash-muted fs-7 align-middle">{{ $item->sku_code ?: '-' }}</td>
                            <td class="text-end fw-bold align-middle">{{ number_format($item->total_qty, 0) }}</td>
                            <td class="text-end fw-bold align-middle">฿{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center dash-muted py-4">ไม่พบข้อมูลสินค้าขายดี</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Low Stock -->
    <div class="col-lg-6">
        <div class="dash-card overflow-hidden dash-alert-table">
            <div class="d-flex justify-content-between align-items-center p-3 dash-border border-bottom dash-bg-red-soft">
                <div class="fw-bold dash-text-red"><i class="bi bi-exclamation-triangle-fill me-2"></i> สต๊อกต่ำสุด / ติดลบ</div>
                <div class="dash-bg-red text-white px-2 py-1 rounded fs-7 fw-bold">ตรวจนับด่วน</div>
            </div>
            <div class="table-responsive">
                <table class="table dash-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>รหัส</th>
                            <th>ที่เก็บ</th>
                            <th class="text-end">คงเหลือ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStock->take(10) as $item)
                        <tr>
                            <td>
                                <div class="fw-bold dash-text">{{ $item->name_th ?? '-' }}</div>
                                <div class="dash-muted" style="font-size: 0.7rem;"><i class="bi bi-shop"></i> สาขา: {{ $item->branch_name ?? '-' }}</div>
                            </td>
                            <td class="dash-muted fs-7 align-middle">{{ $item->sku_code ?? '-' }}</td>
                            <td class="dash-muted fs-7 align-middle">{{ $item->location_name ?? '-' }}</td>
                            <td class="text-end align-middle">
                                <span class="badge rounded-pill dash-bg-red-soft px-3 py-2 fs-6">{{ number_format($item->on_hand_qty, 0) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center dash-muted py-4">ไม่พบสินค้าสต๊อกต่ำ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
<script>
    let dashboardRefreshSeconds = 60;
    const dashboardRefreshCountdown = document.getElementById('dashboardRefreshCountdown');
    window.setInterval(() => {
        dashboardRefreshSeconds -= 1;
        if (dashboardRefreshCountdown) dashboardRefreshCountdown.textContent = dashboardRefreshSeconds;
        if (dashboardRefreshSeconds <= 0) window.location.reload();
    }, 1000);

    // Get theme colors from CSS variables
    const getThemeColor = (cssVar, fallback) => {
        const val = getComputedStyle(document.documentElement).getPropertyValue(cssVar).trim();
        return val || fallback;
    };

    // Listen to theme changes if the app supports it, or just read once
    const themeRed = getThemeColor('--dash-red', '#e11d48');
    const themeRedSoft = getThemeColor('--dash-red-soft', 'rgba(225, 29, 72, 0.15)');
    const themeGreen = getThemeColor('--dash-green', '#059669');
    const themeBorder = getThemeColor('--dash-border', '#e2e8f0');

    // Configure charts with global defaults to support dark mode fonts
    Chart.defaults.color = getThemeColor('--dash-muted', '#64748b');

    new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales->pluck('sale_date')) !!},
            datasets: [{
                label: 'ยอดขาย',
                data: {!! json_encode($dailySales->pluck('total_sales')) !!},
                borderColor: themeRed,
                backgroundColor: themeRedSoft,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: getThemeColor('--dash-surface', '#ffffff'),
                pointBorderColor: themeRed,
                pointBorderWidth: 2,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            onHover: (event, elements) => {
                event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
            },
            onClick: (event, elements, chart) => {
                if (!elements.length) return;

                const selectedDate = chart.data.labels[elements[0].index];
                const reportUrl = new URL(@json(route('reports.index')), window.location.origin);
                reportUrl.searchParams.set('category', 'sales');
                reportUrl.searchParams.set('report', 'daily_sales');
                reportUrl.searchParams.set('from', selectedDate);
                reportUrl.searchParams.set('to', selectedDate);
                reportUrl.searchParams.set('branch_id', @json($currentBranchId ?? 'all'));
                window.location.assign(reportUrl.toString());
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: themeBorder, borderDash: [5, 5] }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('branchChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($byBranch->pluck('name_th')) !!},
            datasets: [{
                label: 'ยอดขาย',
                data: {!! json_encode($byBranch->pluck('total_sales')) !!},
                branchIds: {!! json_encode($byBranch->pluck('id')) !!},
                backgroundColor: themeRed,
                borderRadius: 4,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            onHover: (event, elements) => {
                event.native.target.style.cursor = elements.length ? 'pointer' : 'default';
            },
            onClick: (event, elements, chart) => {
                if (!elements.length) return;

                const branchId = chart.data.datasets[0].branchIds[elements[0].index];
                const reportUrl = new URL(@json(route('reports.index')), window.location.origin);
                reportUrl.searchParams.set('category', 'sales');
                reportUrl.searchParams.set('report', 'sales_by_staff');
                reportUrl.searchParams.set('from', @json($from));
                reportUrl.searchParams.set('to', @json($to));
                reportUrl.searchParams.set('branch_id', branchId);
                window.location.assign(reportUrl.toString());
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: { color: themeBorder, borderDash: [5, 5] }, beginAtZero: true }
            }
        }
    });

    new Chart(document.getElementById('salesMixChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($salesDocumentSummary->pluck('doc_name')) !!},
            datasets: [{
                data: {!! json_encode($salesDocumentSummary->pluck('amount')) !!},
                backgroundColor: [themeGreen, themeRed, '#6366f1'],
                borderColor: getThemeColor('--dash-surface', '#ffffff'),
                borderWidth: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 20 } },
                tooltip: { callbacks: { label: ctx => `${ctx.label}: ฿${Number(ctx.raw || 0).toLocaleString('th-TH')}` } }
            }
        }
    });
</script>
@endpush
