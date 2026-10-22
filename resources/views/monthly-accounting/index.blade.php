@extends('layout')
@section('title', 'ปิดบัญชีรายเดือน - PopCentral')
@section('page-title', 'ศูนย์ปิดบัญชีรายเดือน')
@section('page-subtitle', 'ตรวจ Statement สลิป ค่าใช้จ่าย VAT/WHT และส่งข้อมูลให้สำนักงานบัญชี')

@push('head')
<style>
    [x-cloak]{display:none!important}
    
    .ma-shell {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* Cards */
    .ma-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    /* Filter Toolbar */
    .ma-filter-bar {
        padding: 16px 20px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* Stats Grid */
    .ma-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .ma-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .ma-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .ma-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--stat-bar-color, #3b82f6);
    }
    .ma-stat-title {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ma-stat-number {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    /* POS Control Box */
    .pos-reconcile-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        padding: 20px;
    }
    .pos-summary-badge-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }
    .pos-sub-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        transition: all 0.15s;
    }
    .pos-sub-card.urgent {
        background: #fef2f2;
        border-color: #fecaca;
    }

    /* Tabs Bar */
    .ma-nav-tabs {
        display: flex;
        gap: 8px;
        padding: 8px 16px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        overflow-x: auto;
    }
    .ma-tab-btn {
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1px solid transparent;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .ma-tab-btn:hover {
        color: #1e293b;
        background: #e2e8f0;
    }
    .ma-tab-btn.active {
        color: #ffffff;
        background: #dc2626; /* Pop Star primary */
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    /* Section Content */
    .ma-section-body {
        padding: 22px;
    }

    /* Modern Form Controls */
    .control-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
    }
    .control-input, .control-select, .control-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        padding: 8px 12px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .control-input:focus, .control-select:focus, .control-textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }

    /* Form Grid */
    .form-grid-4 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .span-2 { grid-column: span 2; }
    .span-4 { grid-column: span 4; }

    /* Modern Table */
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-modern th {
        background: #f8fafc;
        padding: 11px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
    }
    .table-modern td {
        padding: 12px 14px;
        font-size: 12px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .table-modern tr:hover td {
        background: #f8fafc;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-matched  { background: #dcfce7; color: #15803d; }
    .status-mismatch { background: #fee2e2; color: #b91c1c; }
    .status-pending  { background: #fef3c7; color: #b45309; }

    /* Alert / Notes */
    .note-info {
        background: #f0fdf4;
        border-left: 4px solid #22c55e;
        border-radius: 6px;
        padding: 12px 16px;
        font-size: 12px;
        color: #166534;
        line-height: 1.5;
    }

    .export-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    @media (max-width: 1100px) {
        .ma-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .pos-summary-badge-grid { grid-template-columns: repeat(2, 1fr); }
        .form-grid-4 { grid-template-columns: repeat(2, 1fr); }
        .span-4 { grid-column: span 2; }
    }
    @media (max-width: 640px) {
        .ma-stats-grid { grid-template-columns: 1fr; }
        .pos-summary-badge-grid { grid-template-columns: 1fr; }
        .form-grid-4 { grid-template-columns: 1fr; }
        .span-2, .span-4 { grid-column: span 1; }
        .export-card { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
<div class="ma-shell" x-data="{ tab: 'expenses', reconcileId: null, accountModalOpen: false, accountEditingId: null, accountCode: '', accountNameTh: '', accountNameEn: '', accountFormAction: '{{ route('chart-of-accounts.store') }}', openAccountCreate() { this.accountEditingId = null; this.accountCode = ''; this.accountNameTh = ''; this.accountNameEn = ''; this.accountFormAction = '{{ route('chart-of-accounts.store') }}'; this.accountModalOpen = true; }, openAccountEdit(id, code, nameTh, nameEn) { this.accountEditingId = id; this.accountCode = code; this.accountNameTh = nameTh; this.accountNameEn = nameEn || ''; this.accountFormAction = '{{ url('/chart-of-accounts') }}/' + id; this.accountModalOpen = true; } }" x-cloak>
    
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Toolbar Filter --}}
    <form class="ma-filter-bar" method="get">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <label class="control-label mb-0 text-nowrap"><i class="bi bi-calendar3 me-1 text-primary"></i> เดือนบัญชี</label>
                <input type="month" name="period" value="{{ $period }}" class="control-input" style="width: 170px;">
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="control-label mb-0 text-nowrap"><i class="bi bi-shop me-1 text-primary"></i> สาขา</label>
                <select name="branch_id" class="control-select" style="min-width: 200px;">
                    <option value="">ทุกสาขา (ทั้งบริษัท)</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branchId==$branch->id)>{{ $branch->code }} · {{ $branch->name_th }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <button class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i> แสดงข้อมูล
            </button>
        </div>
    </form>

    {{-- Stats Cards (4 Boxes) --}}
    <section class="ma-stats-grid">
        <div class="ma-stat-card" style="--stat-bar-color: #3b82f6;">
            <div class="ma-stat-title"><i class="bi bi-wallet2 text-primary"></i> ค่าใช้จ่ายเดือนนี้</div>
            <div class="ma-stat-number text-primary">฿{{ number_format($stats['expense_total'], 2) }}</div>
        </div>
        <div class="ma-stat-card" style="--stat-bar-color: #10b981;">
            <div class="ma-stat-title"><i class="bi bi-file-earmark-text text-success"></i> ภาษีหัก ณ ที่จ่าย (WHT)</div>
            <div class="ma-stat-number text-success">฿{{ number_format($stats['withholding_total'], 2) }}</div>
        </div>
        <div class="ma-stat-card" style="--stat-bar-color: #6366f1;">
            <div class="ma-stat-title"><i class="bi bi-bank text-indigo"></i> รายการ Statement</div>
            <div class="ma-stat-number text-dark">{{ number_format($stats['statement_count']) }} <small class="fs-6 text-muted fw-normal">รายการ</small></div>
        </div>
        <div class="ma-stat-card" style="--stat-bar-color: {{ $stats['unreconciled_count'] > 0 ? '#ef4444' : '#10b981' }};">
            <div class="ma-stat-title">
                <i class="bi {{ $stats['unreconciled_count'] > 0 ? 'bi-exclamation-octagon-fill text-danger' : 'bi-check-circle-fill text-success' }}"></i> 
                ยังไม่ตรง / รอตรวจ
            </div>
            <div class="ma-stat-number {{ $stats['unreconciled_count'] > 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($stats['unreconciled_count']) }} <small class="fs-6 text-muted fw-normal">รายการ</small>
            </div>
        </div>
    </section>

    {{-- POS Reconcile Section --}}
    <section class="pos-reconcile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h6 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-primary fs-5"></i> กระทบยอดขาย POS กับ Statement ธนาคาร
                </h2>
                <div class="text-muted small">เฉพาะใบเสร็จ POS ที่ชำระเงินสำเร็จและยังไม่ถูกยกเลิก</div>
            </div>
        </div>

        <div class="note-info mb-3">
            <i class="bi bi-info-circle-fill me-1"></i>
            <strong>ลำดับการตรวจกระทบยอด:</strong> ใบเสร็จ POS → ช่องทางชำระเงิน → เลขอ้างอิงโอน/QR → ตรวจ Statement → แนบสลิปจริง หากยอดไม่ตรง ระบบจะไม่นับว่าผ่านและจะไม่อนุญาตให้สร้างชุดส่งออกให้สำนักงานบัญชี
        </div>

        <div class="pos-summary-badge-grid">
            <div class="pos-sub-card">
                <div class="control-label text-muted">ใบเสร็จ POS รวม</div>
                <div class="fs-5 fw-bold text-dark">{{ number_format($posControl['receipt_count']) }} <small class="fs-6 fw-normal text-muted">ใบ</small></div>
                <div class="text-muted small mt-1">ยอดขายสุทธิ ฿{{ number_format($posControl['net_sales'], 2) }}</div>
                <div class="text-muted small">VAT ขาย ฿{{ number_format($posControl['vat_sales'], 2) }}</div>
            </div>
            <div class="pos-sub-card">
                <div class="control-label text-muted">โอน / QR Code รวม</div>
                <div class="fs-5 fw-bold text-dark">฿{{ number_format((float)($posControl['methods']['transfer']['amount'] ?? 0) + (float)($posControl['methods']['qr']['amount'] ?? 0) + (float)($posControl['methods']['bank']['amount'] ?? 0), 2) }}</div>
                <div class="text-danger small mt-1 fw-semibold"><i class="bi bi-hourglass-split"></i> ยังไม่จับคู่ {{ number_format($posControl['transfer_unmatched_count']) }} รายการ</div>
            </div>
            <div class="pos-sub-card">
                <div class="control-label text-muted">เงินเข้าตาม Statement</div>
                <div class="fs-5 fw-bold text-dark">{{ number_format($posControl['statement_income_count']) }} <small class="fs-6 fw-normal text-muted">รายการ</small></div>
                <div class="text-muted small mt-1">ยอดรวม ฿{{ number_format($posControl['statement_income_amount'], 2) }}</div>
                <div class="text-success small fw-semibold">จับคู่แล้ว ฿{{ number_format($posControl['matched_statement_income_amount'], 2) }}</div>
            </div>
            <div class="pos-sub-card {{ $posControl['transfer_unmatched_count'] > 0 ? 'urgent' : '' }}">
                <div class="control-label {{ $posControl['transfer_unmatched_count'] > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                    <i class="bi bi-bell-fill me-1"></i> รายการที่ต้องตรวจ
                </div>
                <div class="fs-5 fw-bold {{ $posControl['transfer_unmatched_count'] > 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($posControl['transfer_unmatched_count']) }} ใบ
                </div>
                <div class="{{ $posControl['transfer_unmatched_count'] > 0 ? 'text-danger' : 'text-success' }} small mt-1 fw-bold">
                    ฿{{ number_format($posControl['transfer_unmatched_amount'], 2) }}
                </div>
                <div class="text-muted small">เปิดแท็บ Statement ด้านล่างเพื่อตรวจ</div>
            </div>
        </div>

        <div class="table-responsive border rounded-3">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>วันที่ / เวลา</th>
                        <th>เลขที่ใบเสร็จ POS</th>
                        <th>ช่องทาง</th>
                        <th>เลขอ้างอิงจาก POS</th>
                        <th class="text-end">ยอดโอน (บาท)</th>
                        <th class="text-center">สถานะการตรวจ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posControl['unmatched_transfers'] as $row)
                        <tr>
                            <td><i class="bi bi-clock me-1 text-muted"></i>{{ \Carbon\Carbon::parse($row->receipt_date)->format('d/m/Y H:i') }}</td>
                            <td><strong class="text-dark">{{ $row->receipt_no }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ ['transfer'=>'โอน','qr'=>'QR Code','bank'=>'ธนาคาร'][$row->method] ?? $row->method }}</span></td>
                            <td><code class="text-muted">{{ $row->payment_reference ?: '-' }}</code></td>
                            <td class="text-end fw-bold text-dark fs-6">{{ number_format((float)$row->amount, 2) }}</td>
                            <td class="text-center">
                                <span class="status-badge status-pending">
                                    <i class="bi bi-hourglass-split"></i> รอจับคู่ Statement/สลิป
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-success py-4">
                                <i class="bi bi-check-circle-fill fs-4 d-block mb-1"></i> ยอดเยี่ยม! ไม่พบรายการโอน POS ที่ค้างตรวจกระทบยอด
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Tabs Section: Expenses, Bank Statement, Export --}}
    <section class="ma-card">
        <div class="ma-nav-tabs">
            <button class="ma-tab-btn" :class="tab==='expenses'&&'active'" @click="tab='expenses'" type="button">
                <i class="bi bi-receipt"></i> บันทึกค่าใช้จ่ายสาขา
            </button>
            <button class="ma-tab-btn" :class="tab==='bank'&&'active'" @click="tab='bank'" type="button">
                <i class="bi bi-bank"></i> Statement ธนาคาร / ตรวจสลิป
            </button>
            <button class="ma-tab-btn" :class="tab==='export'&&'active'" @click="tab='export'" type="button">
                <i class="bi bi-file-earmark-zip"></i> ส่งข้อมูลให้สำนักงานบัญชี
            </button>
        </div>

        {{-- TAB 1: Expenses --}}
        <div class="ma-section-body" x-show="tab==='expenses'">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 fw-bold mb-0 text-dark">
                    <i class="bi bi-plus-circle me-1 text-danger"></i> บันทึกค่าใช้จ่ายและภาษี
                </h3>
                <span class="badge bg-light text-muted border">บันทึกแล้วบันทึกลง GL ให้อัตโนมัติ</span>
            </div>

            <form method="post" action="{{ route('monthly-accounting.expenses.store') }}" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="form-grid-4 mb-3">
                    <div>
                        <label class="control-label">วันที่ค่าใช้จ่าย <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required class="control-input">
                    </div>
                    <div>
                        <label class="control-label">สาขา <span class="text-danger">*</span></label>
                        <select name="branch_id" required class="control-select">
                            <option value="">เลือกสาขา</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('branch_id', $branchId)==$branch->id)>{{ $branch->code }} · {{ $branch->name_th }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="span-2">
                        <label class="control-label">บัญชีค่าใช้จ่าย <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <select name="expense_account_id" required class="control-select" style="flex:1">
                                <option value="">เลือกบัญชีค่าใช้จ่าย</option>
                                @foreach($expenseAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name_th }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-primary btn-sm text-nowrap rounded-3" @click="openAccountCreate()" title="เพิ่ม/แก้ไขบัญชีค่าใช้จ่าย">
                                <i class="bi bi-plus-lg me-1"></i>เพิ่ม/แก้ไข
                            </button>
                        </div>
                    </div>

                    <div class="span-2">
                        <label class="control-label">Cost Center</label>
                        <select name="cost_center_id" class="control-select">
                            <option value="">ไม่ระบุ</option>
                            @foreach($costCenters as $center)
                                <option value="{{ $center->id }}">{{ $center->code }} · {{ $center->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="span-2">
                        <label class="control-label">ผู้ขาย / ผู้รับเงิน <span class="text-danger">*</span></label>
                        <input name="supplier_name" list="supplier-list" value="{{ old('supplier_name') }}" required placeholder="พิมพ์ชื่อผู้ขายหรือคู่ค้า" class="control-input">
                        <datalist id="supplier-list">
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->name_th }}">{{ $supplier->tax_id }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="control-label">เลขผู้เสียภาษี 13 หลัก</label>
                        <input name="supplier_tax_id" value="{{ old('supplier_tax_id') }}" placeholder="เลขประจำตัวผู้เสียภาษี" class="control-input">
                    </div>
                    <div>
                        <label class="control-label">สาขาภาษี</label>
                        <input name="tax_branch" value="{{ old('tax_branch','00000') }}" class="control-input">
                    </div>
                    <div>
                        <label class="control-label">เลขใบกำกับ / ใบเสร็จ</label>
                        <input name="tax_invoice_no" value="{{ old('tax_invoice_no') }}" placeholder="เช่น INV-001" class="control-input">
                    </div>
                    <div>
                        <label class="control-label">วันที่ใบกำกับ</label>
                        <input type="date" name="tax_invoice_date" value="{{ old('tax_invoice_date') }}" class="control-input">
                    </div>

                    <div>
                        <label class="control-label">มูลค่าก่อน VAT <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="base_amount" value="{{ old('base_amount') }}" required placeholder="0.00" class="control-input text-end">
                    </div>
                    <div>
                        <label class="control-label">ภาษีมูลค่าเพิ่ม (VAT)</label>
                        <input type="number" step="0.01" min="0" name="vat_amount" value="{{ old('vat_amount',0) }}" class="control-input text-end">
                    </div>
                    <div>
                        <label class="control-label">อัตราหัก ณ ที่จ่าย %</label>
                        <input type="number" step="0.01" min="0" max="100" name="withholding_rate" value="{{ old('withholding_rate',0) }}" class="control-input text-end">
                    </div>
                    <div>
                        <label class="control-label">แบบ WHT</label>
                        <select name="withholding_form" class="control-select">
                            <option value="">ไม่มี</option>
                            <option value="PND3">ภ.ง.ด.3 (บุคคลธรรมดา)</option>
                            <option value="PND53">ภ.ง.ด.53 (นิติบุคคล)</option>
                        </select>
                    </div>

                    <div>
                        <label class="control-label">วิธีชำระเงิน <span class="text-danger">*</span></label>
                        <select name="payment_method" required class="control-select">
                            <option value="cash">เงินสด (Cash)</option>
                            <option value="transfer">โอนเงินผ่านธนาคาร (Bank Transfer)</option>
                        </select>
                    </div>
                    <div>
                        <label class="control-label">บัญชีธนาคาร</label>
                        <select name="bank_account_id" class="control-select">
                            <option value="">- เลือกบัญชีธนาคาร -</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->bank_name }} · {{ $account->account_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="control-label">เลขอ้างอิงการจ่าย</label>
                        <input name="payment_reference" value="{{ old('payment_reference') }}" placeholder="เลขที่อ้างอิงโอน" class="control-input">
                    </div>
                    <div>
                        <label class="control-label">หลักฐาน (PDF/JPG/PNG)</label>
                        <input type="file" name="evidence" accept=".pdf,.jpg,.jpeg,.png" class="control-input">
                    </div>

                    <div class="span-4">
                        <label class="control-label">รายละเอียดค่าใช้จ่าย <span class="text-danger">*</span></label>
                        <textarea name="description" required placeholder="ระบุเหตุผลและรายละเอียดของค่าใช้จ่าย..." class="control-textarea" rows="2">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="text-end">
                    <button class="btn btn-danger px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center">
                        <i class="bi bi-check2-circle me-2"></i> บันทึกและลงบัญชี
                    </button>
                </div>
            </form>

            <div class="table-responsive border rounded-3 mt-4">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>วันที่ / เลขที่เอกสาร</th>
                            <th>สาขา</th>
                            <th>ผู้ขาย</th>
                            <th>รายละเอียดรายการ</th>
                            <th>เลขใบกำกับ</th>
                            <th class="text-end">ก่อน VAT</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">WHT</th>
                            <th class="text-end">ยอดรวมสุทธิ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $e->expense_date->thaiDate() }}</div>
                                    <div class="text-muted small">{{ $e->document->doc_number }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $e->branch->code }}</span></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $e->supplier_name }}</div>
                                    <div class="text-muted small">{{ $e->supplier_tax_id }}</div>
                                </td>
                                <td>{{ $e->description }}</td>
                                <td><code class="text-muted">{{ $e->tax_invoice_no ?: '-' }}</code></td>
                                <td class="text-end">{{ number_format((float)$e->base_amount, 2) }}</td>
                                <td class="text-end text-muted">{{ number_format((float)$e->vat_amount, 2) }}</td>
                                <td class="text-end text-muted">{{ number_format((float)$e->withholding_amount, 2) }}</td>
                                <td class="text-end fw-bold text-dark fs-6">{{ number_format((float)$e->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>ยังไม่มีรายการค่าใช้จ่ายในเดือนนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        </div>

        {{-- TAB 2: Statement --}}
        <div class="ma-section-body" x-show="tab==='bank'">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 fw-bold mb-0 text-dark">
                    <i class="bi bi-bank me-1 text-primary"></i> นำเข้าและตรวจกระทบยอด Statement
                </h3>
            </div>

            <div class="note-info mb-3">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                <strong>รูปแบบไฟล์ CSV:</strong> ต้องมีหัวคอลัมน์ <code>date, description, amount, balance</code> หรือภาษาไทย <code>วันที่, รายละเอียด, จำนวนเงิน, คงเหลือ</code> โดยยอดเงินเข้าเป็นบวก (+) และเงินออกเป็นลบ (-)
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form method="post" action="{{ route('monthly-accounting.statements.import') }}" enctype="multipart/form-data" class="d-flex gap-2 flex-wrap align-items-end">
                        @csrf
                        <div style="flex: 1; min-width: 220px;">
                            <label class="control-label">บัญชีธนาคาร <span class="text-danger">*</span></label>
                            <select name="bank_account_id" required class="control-select">
                                <option value="">เลือกบัญชีธนาคาร</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->bank_name }} · {{ $account->account_no }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex: 1; min-width: 220px;">
                            <label class="control-label">ไฟล์ Statement CSV <span class="text-danger">*</span></label>
                            <input type="file" name="statement_file" accept=".csv,.txt" required class="control-input">
                        </div>
                        <button class="btn btn-primary px-4 py-2 rounded-3 fw-bold d-inline-flex align-items-center shadow-sm">
                            <i class="bi bi-upload me-2"></i> นำเข้า
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-md-end d-flex align-items-end justify-content-md-end">
                    <form method="post" action="{{ route('monthly-accounting.statements.auto-reconcile') }}">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        @if($branchId)<input type="hidden" name="branch_id" value="{{ $branchId }}">@endif
                        <button class="btn btn-outline-success px-4 py-2 rounded-3 fw-bold d-inline-flex align-items-center">
                            <i class="bi bi-magic me-2"></i> จับคู่อัตโนมัติ (Auto-Reconcile)
                        </button>
                    </form>
                </div>
            </div>

            <div class="table-responsive border rounded-3">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>วันที่ทำรายการ</th>
                            <th>บัญชีธนาคาร</th>
                            <th>รายละเอียดรายการ</th>
                            <th class="text-end">ยอดเงิน Statement</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-end">ตรวจเทียบหลักฐาน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statements as $s)
                            @php($status = $s->reconciliation?->status ?? 'pending')
                            <tr>
                                <td><i class="bi bi-calendar3 me-1 text-muted"></i>{{ $s->statement_date->thaiDate() }}</td>
                                <td>
                                    <strong>{{ $s->bankAccount->bank_name }}</strong>
                                    <div class="text-muted small">{{ $s->bankAccount->account_no }}</div>
                                </td>
                                <td>{{ $s->description }}</td>
                                <td class="text-end fw-bold fs-6 {{ (float)$s->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format((float)$s->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="status-badge status-{{ $status }}">
                                        {{ ['matched'=>'ตรงแล้ว','mismatch'=>'ยอดต่าง','pending'=>'รอตรวจ'][$status] ?? $status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light border px-3 rounded-pill" @click="reconcileId = reconcileId === {{ $s->id }} ? null : {{ $s->id }}">
                                        <i class="bi bi-search me-1"></i> ตรวจ
                                    </button>
                                </td>
                            </tr>
                            <tr x-show="reconcileId === {{ $s->id }}" class="bg-light">
                                <td colspan="6" class="p-3">
                                    <form method="post" action="{{ route('monthly-accounting.statements.reconcile', $s) }}" enctype="multipart/form-data" class="p-3 bg-white border rounded-3">
                                        @csrf
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-3">
                                                <label class="control-label">ประเภทรายการ</label>
                                                <select name="match_type" class="control-select">
                                                    <option value="pos_transfer">ยอดโอนจาก POS</option>
                                                    <option value="expense">ค่าใช้จ่าย</option>
                                                    <option value="payment">รับ/จ่ายชำระ</option>
                                                    <option value="other">อื่น ๆ</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">ยอดตามหลักฐาน</label>
                                                <input type="number" step="0.01" min="0" name="expected_amount" value="{{ abs((float)$s->reconciliation?->expected_amount ?: (float)$s->amount) }}" required class="control-input text-end">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">เลขอ้างอิง</label>
                                                <input name="reference" value="{{ $s->reconciliation?->reference }}" placeholder="เลขอ้างอิงจากสลิป" class="control-input">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">แนบสลิป / หลักฐาน</label>
                                                <input type="file" name="slip" accept=".pdf,.jpg,.jpeg,.png" class="control-input">
                                            </div>
                                            <div class="col-md-1">
                                                <input type="hidden" name="branch_id" value="{{ $s->bankAccount->branch_id }}">
                                                <button class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                                                    บันทึก
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>ยังไม่มี Statement ในเดือนนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 3: Export --}}
        <div class="ma-section-body" x-show="tab==='export'">
            <div class="export-card mb-4">
                <div>
                    <h3 class="h6 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-zip-fill text-danger fs-5"></i> สร้างชุดส่งสำนักงานบัญชี
                    </h3>
                    <p class="text-muted small mb-0">
                        ไฟล์ ZIP ครบวงจรประกอบด้วย Summary, Bank Reconciliation, รายงานภาษีขาย, รายงานภาษีซื้อ, ค่าใช้จ่าย, ภ.ง.ด., สมุดรายวัน GL, Manifest และไฟล์หลักฐานทั้งหมด
                    </p>
                </div>
                <div>
                    <form method="post" action="{{ route('monthly-accounting.export') }}">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        @if($branchId)<input type="hidden" name="branch_id" value="{{ $branchId }}">@endif
                        <button class="btn btn-danger px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center text-nowrap" @disabled($stats['unreconciled_count'] > 0)>
                            <i class="bi bi-download me-2"></i> สร้างและดาวน์โหลด ZIP
                        </button>
                    </form>
                </div>
            </div>

            @if($stats['unreconciled_count'] > 0)
                <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-3">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div><strong>แจ้งเตือน:</strong> ต้องตรวจ Statement และกระทบยอดให้ตรงครบทุกรายการก่อนสร้างชุดส่งออกให้สำนักงานบัญชี</div>
                </div>
            @endif

            <div class="note-info mb-4">
                <i class="bi bi-info-circle-fill me-1"></i>
                ชุด ZIP นี้เป็นข้อมูลส่งมอบสำหรับสำนักงานบัญชีนำไปตรวจสอบและจัดทำ ภ.พ.30, ภ.ง.ด.3/53 หรือยื่นผ่านระบบ e-Tax ต่อกรมสรรพากรตามรอบภาษีของบริษัท
            </div>

            <div class="table-responsive border rounded-3">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>วันเวลาที่ส่งออก</th>
                            <th>เดือนภาษี</th>
                            <th>ชื่อไฟล์ ZIP</th>
                            <th>SHA-256 Hash</th>
                            <th>ขนาดไฟล์</th>
                            <th class="text-end">ดาวน์โหลด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exportRuns as $run)
                            <tr>
                                <td><i class="bi bi-clock me-1 text-muted"></i>{{ $run->exported_at->thaiDate(true) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $run->period }}</span></td>
                                <td><strong class="text-dark">{{ basename($run->file_name) }}</strong></td>
                                <td><code class="text-muted">{{ substr($run->file_hash, 0, 16) }}…</code></td>
                                <td>{{ number_format($run->file_size / 1024, 1) }} KB</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary px-3 rounded-pill" href="{{ route('monthly-accounting.exports.download', $run) }}">
                                        <i class="bi bi-download me-1"></i> ดาวน์โหลด
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-4 d-block mb-1"></i>ยังไม่เคยส่งออกข้อมูลในเดือนนี้
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Account Modal --}}
    <div class="account-modal-backdrop" x-show="accountModalOpen" x-transition.opacity @keydown.escape.window="accountModalOpen = false">
        <div class="account-modal" @click.outside="accountModalOpen = false" x-transition>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1 text-dark" x-text="accountEditingId ? 'แก้ไขบัญชีค่าใช้จ่าย' : 'เพิ่มบัญชีค่าใช้จ่าย'"></h2>
                    <p class="text-muted small mb-0">เพิ่มหรือแก้ไขแล้วกลับมาบันทึกค่าใช้จ่ายต่อได้ทันที</p>
                </div>
                <button type="button" class="btn btn-light rounded-circle" @click="accountModalOpen = false"><i class="bi bi-x-lg"></i></button>
            </div>
            <form method="post" :action="accountFormAction">
                @csrf
                <template x-if="accountEditingId"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="account_type" value="expense">
                <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}">
                <div class="row g-3">
                    <div class="col-5">
                        <label class="control-label">รหัสบัญชี <span class="text-danger">*</span></label>
                        <input name="code" x-model="accountCode" class="control-input" required placeholder="เช่น 510101">
                    </div>
                    <div class="col-7">
                        <label class="control-label">ชื่อบัญชีภาษาไทย <span class="text-danger">*</span></label>
                        <input name="name_th" x-model="accountNameTh" class="control-input" required placeholder="เช่น ค่าไฟฟ้า">
                    </div>
                    <div class="col-12">
                        <label class="control-label">ชื่อบัญชีภาษาอังกฤษ (ถ้ามี)</label>
                        <input name="name_en" x-model="accountNameEn" class="control-input" placeholder="Electricity expense">
                    </div>
                </div>
                <div class="account-list mt-4">
                    <div class="small fw-bold text-muted mb-2">บัญชีค่าใช้จ่ายที่มีอยู่แล้ว</div>
                    @forelse($expenseAccounts as $account)
                        <div class="account-list-row">
                            <span><strong>{{ $account->code }}</strong> · {{ $account->name_th }}</span>
                            <button type="button" class="btn btn-sm btn-light border" @click="openAccountEdit(@js($account->id), @js($account->code), @js($account->name_th), @js($account->name_en ?? ''))">แก้ไข</button>
                        </div>
                    @empty
                        <div class="small text-muted py-2">ยังไม่มีบัญชีค่าใช้จ่าย</div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light border px-4 rounded-3" @click="accountModalOpen = false">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger px-4 rounded-3 fw-bold shadow-sm">
                        <i class="bi bi-check2 me-1"></i>บันทึกบัญชี
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('head')
<style>
    .account-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 2050;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }
    .account-modal {
        width: min(650px, 100%);
        max-height: calc(100vh - 40px);
        overflow: auto;
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .account-list {
        border-top: 1px solid #e2e8f0;
        padding-top: 14px;
    }
    .account-list-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 6px;
        margin-bottom: 2px;
        font-size: 13px;
        color: #334155;
    }
    .account-list-row:hover {
        background: #f8fafc;
    }
</style>
@endpush
