@extends('layout')
@section('title', 'บัญชีครบวงจร - PopCentral')
@section('page-title', 'ศูนย์บัญชีครบวงจร (Accounting Automation)')
@section('page-subtitle', 'ศูนย์รวมควบคุมงานบัญชี: กระทบยอดธนาคาร, ภาษี/E-Tax, Audit Trail, เอกสารประจำ และคิวนำเข้า OCR/AI')

@push('head')
<style>
    [x-cloak] { display: none !important; }
    
    .acc-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Top Filter Bar */
    .acc-filter-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* Stats Grid */
    .acc-stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 14px;
    }
    .acc-stat-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .acc-stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .acc-stat-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--stat-accent, #3b82f6);
    }
    .acc-stat-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .acc-stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    /* Control Flow Cards */
    .control-flow-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .control-flow-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        margin-top: 14px;
    }
    .flow-action-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none;
        color: #1e293b;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.15s ease;
    }
    .flow-action-card:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(37,99,235,0.08);
    }
    .flow-action-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
        color: var(--erp-primary, #dc2626);
    }

    /* 2 Column Main Section */
    .acc-main-grid {
        display: grid;
        grid-template-columns: 1.1fr 0.9fr;
        gap: 20px;
    }
    .acc-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .acc-card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fafbfc;
    }
    .acc-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .acc-card-body {
        padding: 20px;
    }

    /* Modern Form Controls */
    .modern-form-group {
        margin-bottom: 14px;
    }
    .modern-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 5px;
        display: block;
    }
    .modern-input, .modern-select, .modern-textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        padding: 8px 12px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .modern-input:focus, .modern-select:focus, .modern-textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }
    .modern-input-file {
        display: block;
        width: 100%;
        font-size: 12px;
        color: #64748b;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 16px;
        background: #f8fafc;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .modern-input-file:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    /* Modern Table */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table th {
        background: #f8fafc;
        padding: 10px 14px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
    }
    .modern-table td {
        padding: 12px 14px;
        font-size: 12px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .modern-table tr:hover td {
        background: #f8fafc;
    }

    /* Status Badges */
    .badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-warning-soft { background: #fef3c7; color: #92400e; }
    .badge-success-soft { background: #dcfce7; color: #166534; }
    .badge-danger-soft  { background: #fee2e2; color: #991b1b; }
    .badge-info-soft    { background: #e0f2fe; color: #0369a1; }

    /* Note Box */
    .info-callout {
        background: #f0fdf4;
        border-left: 4px solid #22c55e;
        border-radius: 6px;
        padding: 12px 14px;
        font-size: 12px;
        color: #166534;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    @media (max-width: 1200px) {
        .acc-stats-grid { grid-template-columns: repeat(3, 1fr); }
        .control-flow-grid { grid-template-columns: repeat(3, 1fr); }
        .acc-main-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .acc-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .control-flow-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="acc-container" x-data="{ tab: 'recurring' }" x-cloak>
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

    {{-- Filter Toolbar --}}
    <form class="acc-filter-card d-flex align-items-center justify-content-between flex-wrap gap-3" method="get">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <label class="modern-label mb-0 text-nowrap"><i class="bi bi-calendar3 me-1 text-primary"></i> เดือนควบคุม</label>
                <input type="month" name="period" value="{{ $period }}" class="modern-input" style="width: 170px;">
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="modern-label mb-0 text-nowrap"><i class="bi bi-shop me-1 text-primary"></i> สาขา</label>
                <select name="branch_id" class="modern-select" style="min-width: 200px;">
                    <option value="">ทั้งบริษัท (ทุกสาขา)</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected($branchId == $branch->id)>{{ $branch->code }} · {{ $branch->name_th }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <button class="btn btn-primary px-4 py-2 rounded-3 fw-bold d-inline-flex align-items-center shadow-sm">
                <i class="bi bi-funnel-fill me-2"></i> แสดงข้อมูล
            </button>
        </div>
    </form>

    {{-- Stats Indicator Grid --}}
    <section class="acc-stats-grid">
        <div class="acc-stat-box" style="--stat-accent: #f59e0b;">
            <div class="acc-stat-label">
                <i class="bi bi-bank2 text-warning"></i> Bank ยังไม่กระทบยอด
            </div>
            <div class="acc-stat-value text-warning">{{ number_format($stats['unreconciled_bank']) }}</div>
        </div>
        <div class="acc-stat-box" style="--stat-accent: #3b82f6;">
            <div class="acc-stat-label">
                <i class="bi bi-receipt text-primary"></i> ชุดภาษีรอดำเนินการ
            </div>
            <div class="acc-stat-value text-primary">{{ number_format($stats['pending_tax']) }}</div>
        </div>
        <div class="acc-stat-box" style="--stat-accent: #06b6d4;">
            <div class="acc-stat-label">
                <i class="bi bi-file-earmark-check text-info"></i> E-Tax รอติดตาม
            </div>
            <div class="acc-stat-value text-info">{{ number_format($stats['pending_etax']) }}</div>
        </div>
        <div class="acc-stat-box" style="--stat-accent: #10b981;">
            <div class="acc-stat-label">
                <i class="bi bi-journal-text text-success"></i> GL lines เดือนนี้
            </div>
            <div class="acc-stat-value text-dark">{{ number_format($stats['gl_lines']) }}</div>
        </div>
        <div class="acc-stat-box" style="--stat-accent: #ef4444;">
            <div class="acc-stat-label">
                <i class="bi bi-clock-history text-danger"></i> Recurring ถึงกำหนด
            </div>
            <div class="acc-stat-value {{ $stats['recurring_due'] > 0 ? 'text-danger' : 'text-muted' }}">
                {{ number_format($stats['recurring_due']) }}
            </div>
        </div>
        <div class="acc-stat-box" style="--stat-accent: #8b5cf6;">
            <div class="acc-stat-label">
                <i class="bi bi-robot text-purple"></i> OCR/AI queue
            </div>
            <div class="acc-stat-value text-dark">{{ number_format($stats['import_queue']) }}</div>
        </div>
    </section>

    {{-- Accounting Control Flow --}}
    <section class="control-flow-panel">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h2 class="h6 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-diagram-3-fill text-primary"></i> Accounting Control Flow
                </h2>
                <div class="text-muted small">เปิดจุดควบคุมหลัก รวดเร็ว สะดวก โดยไม่ต้องไล่หาในเมนูย่อย</div>
            </div>
        </div>
        <div class="control-flow-grid">
            <a class="flow-action-card" href="{{ route('monthly-accounting.index', ['period' => $period, 'branch_id' => $branchId]) }}">
                <div class="flow-action-icon text-warning"><i class="bi bi-bank2"></i></div>
                <div>
                    <div>Bank reconcile</div>
                    <small class="text-muted fw-normal">กระทบยอดธนาคาร</small>
                </div>
            </a>
            <a class="flow-action-card" href="{{ route('tax-compliance.index', ['period' => $period, 'branch_id' => $branchId]) }}">
                <div class="flow-action-icon text-primary"><i class="bi bi-receipt"></i></div>
                <div>
                    <div>ภาษี / E-Tax</div>
                    <small class="text-muted fw-normal">ภ.ง.ด. / ภ.พ.30</small>
                </div>
            </a>
            <a class="flow-action-card" href="{{ route('gl-journals.index') }}">
                <div class="flow-action-icon text-success"><i class="bi bi-journal-text"></i></div>
                <div>
                    <div>GL audit trail</div>
                    <small class="text-muted fw-normal">สมุดรายวันทั่วไป</small>
                </div>
            </a>
            <a class="flow-action-card" href="{{ route('financial-statements.index') }}">
                <div class="flow-action-icon text-info"><i class="bi bi-graph-up"></i></div>
                <div>
                    <div>งบการเงิน</div>
                    <small class="text-muted fw-normal">กำไรขาดทุน / ดุล</small>
                </div>
            </a>
            <a class="flow-action-card" href="{{ route('accounting-periods.index') }}">
                <div class="flow-action-icon text-danger"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div>ปิดงวดบัญชี</div>
                    <small class="text-muted fw-normal">ล็อกและปิดงวด</small>
                </div>
            </a>
        </div>
    </section>

    {{-- 2-Column Main Section: Recurring Rules & OCR/AI Queue --}}
    <div class="acc-main-grid">
        {{-- Section 1: Recurring Documents --}}
        <section class="acc-card">
            <div class="acc-card-header">
                <div>
                    <h3 class="acc-card-title">
                        <i class="bi bi-arrow-repeat text-danger"></i> รายการเอกสารประจำ (Recurring)
                    </h3>
                    <div class="text-muted small mt-1">ตั้ง Schedule ล่วงหน้า ผูกสร้างเอกสารจริงอัตโนมัติเฉพาะในรอบถัดไป</div>
                </div>
            </div>
            <div class="acc-card-body">
                <form method="post" action="{{ route('accounting-automation.recurring.store') }}" class="mb-4">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="modern-label">ชนิดเอกสาร <span class="text-danger">*</span></label>
                            <select name="rule_type" required class="modern-select">
                                <option value="expense">ค่าใช้จ่ายประจำ</option>
                                <option value="sales_invoice">ใบแจ้งหนี้รายเดือน</option>
                                <option value="purchase">ซื้อ/บริการประจำ</option>
                                <option value="billing_note">วางบิลประจำ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="modern-label">สาขา</label>
                            <select name="branch_id" class="modern-select">
                                <option value="">ทั้งบริษัท (ทุกสาขา)</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->code }} · {{ $branch->name_th }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-7">
                            <label class="modern-label">ชื่อรายการ <span class="text-danger">*</span></label>
                            <input name="name" required placeholder="เช่น ค่าเช่าสาขารายเดือน, ค่าอินเทอร์เน็ต" class="modern-input">
                        </div>
                        <div class="col-md-5">
                            <label class="modern-label">คู่ค้า / ผู้รับเงิน</label>
                            <input name="party_name" placeholder="ชื่อคู่ค้าหรือบริษัท" class="modern-input">
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-3">
                            <label class="modern-label">ยอดก่อน VAT</label>
                            <input type="number" step="0.01" min="0" name="base_amount" value="0" class="modern-input text-end">
                        </div>
                        <div class="col-md-3">
                            <label class="modern-label">VAT</label>
                            <input type="number" step="0.01" min="0" name="vat_amount" value="0" class="modern-input text-end">
                        </div>
                        <div class="col-md-3">
                            <label class="modern-label">ความถี่ <span class="text-danger">*</span></label>
                            <select name="frequency" required class="modern-select">
                                <option value="monthly">รายเดือน</option>
                                <option value="weekly">รายสัปดาห์</option>
                                <option value="quarterly">รายไตรมาส</option>
                                <option value="yearly">รายปี</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="modern-label">รอบถัดไป <span class="text-danger">*</span></label>
                            <input type="date" name="next_run_date" value="{{ now()->toDateString() }}" required class="modern-input">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="modern-label">หมายเหตุ / เงื่อนไข</label>
                        <textarea name="note" placeholder="เลขที่สัญญา, เงื่อนไข VAT/WHT หรือวิธีตรวจเอกสาร" class="modern-textarea" rows="2"></textarea>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-danger px-4 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-calendar-plus me-2"></i> เพิ่มรายการประจำ
                        </button>
                    </div>
                </form>

                <div class="table-responsive border rounded-3">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>รายการ</th>
                                <th>สาขา</th>
                                <th>รอบถัดไป</th>
                                <th class="text-end">ยอดรวม</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-end">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recurringRules as $rule)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $rule->name }}</strong>
                                        <div class="text-muted small">{{ $rule->party_name ?: '-' }} · <span class="badge bg-light text-dark border">{{ $rule->rule_type }}</span></div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $rule->branch?->code ?: 'ALL' }}</span></td>
                                    <td><i class="bi bi-calendar-event me-1 text-muted"></i>{{ $rule->next_run_date->thaiDate() }}</td>
                                    <td class="text-end fw-bold text-dark">
                                        ฿{{ number_format((float)$rule->base_amount + (float)$rule->vat_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-modern {{ $rule->is_active && $rule->next_run_date->lte(now()) ? 'badge-warning-soft' : 'badge-success-soft' }}">
                                            <i class="bi {{ $rule->is_active && $rule->next_run_date->lte(now()) ? 'bi-exclamation-circle' : 'bi-clock' }}"></i>
                                            {{ $rule->is_active && $rule->next_run_date->lte(now()) ? 'ถึงกำหนด' : 'รอรอบ' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="{{ route('accounting-automation.recurring.run', $rule) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold">
                                                <i class="bi bi-check-lg me-1"></i> ทำแล้ว
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>ยังไม่มีรายการเอกสารประจำ
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Section 2: OCR/AI Import Queue --}}
        <section class="acc-card">
            <div class="acc-card-header">
                <div>
                    <h3 class="acc-card-title">
                        <i class="bi bi-cpu text-primary"></i> OCR / AI Import Queue
                    </h3>
                    <div class="text-muted small mt-1">คิวนำเข้าไฟล์เอกสารและผลอ่านอัตโนมัติ ตรวจสอบก่อนลงบัญชีจริง</div>
                </div>
            </div>
            <div class="acc-card-body">
                <div class="info-callout">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    <strong>คำแนะนำ:</strong> ตอนนี้ระบบใช้ heuristic จากชื่อไฟล์เพื่อเติมวันที่/ยอด/คู่ค้าเบื้องต้น และตั้งสถานะ needs AI review ไว้ใน extracted JSON เมื่อต่อเชื่อม provider OCR/AI จะเสียบเข้ากับระบบตรงจุดนี้ได้ทันที
                </div>

                <form method="post" action="{{ route('accounting-automation.imports.upload') }}" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="modern-label">ประเภทไฟล์ <span class="text-danger">*</span></label>
                            <select name="source_type" required class="modern-select">
                                <option value="purchase_tax_invoice">ใบกำกับภาษีซื้อ</option>
                                <option value="expense_receipt">ใบเสร็จค่าใช้จ่าย</option>
                                <option value="bank_statement">Bank Statement</option>
                                <option value="sales_document">เอกสารขาย</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="modern-label">สาขา</label>
                            <select name="branch_id" class="modern-select">
                                <option value="">ทั้งบริษัท (ทุกสาขา)</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->code }} · {{ $branch->name_th }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="modern-label">แนบไฟล์ PDF / JPG / PNG / CSV <span class="text-danger">*</span></label>
                        <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.csv,.txt" required class="modern-input">
                    </div>

                    <div class="mb-3">
                        <label class="modern-label">หมายเหตุผู้ส่ง / บันทึกการตรวจ</label>
                        <textarea name="review_note" placeholder="รายละเอียดเพิ่มเติมหรือข้อความถึงผู้ตรวจ..." class="modern-textarea" rows="2"></textarea>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary px-4 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center">
                            <i class="bi bi-cloud-arrow-up me-2"></i> นำเข้า Queue
                        </button>
                    </div>
                </form>

                <div class="table-responsive border rounded-3">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ไฟล์เอกสาร</th>
                                <th>ผลอ่านเบื้องต้น</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-end">ตรวจทาน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($imports as $batch)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf fs-4 text-danger"></i>
                                            <div>
                                                <strong class="text-dark text-break">{{ $batch->original_name }}</strong>
                                                <div class="text-muted small">
                                                    <span class="badge bg-light text-dark border">{{ $batch->source_type }}</span> · {{ $batch->branch?->code ?: 'ALL' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">{{ $batch->suggested_date?->format('d/m/Y') ?: '-' }}</div>
                                        <div class="fw-semibold text-dark">{{ $batch->suggested_party ?: '-' }}</div>
                                        @if($batch->suggested_amount)
                                            <div class="fw-bold text-primary">฿{{ number_format((float)$batch->suggested_amount, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-modern {{ $batch->status === 'approved' ? 'badge-success-soft' : ($batch->status === 'rejected' ? 'badge-danger-soft' : 'badge-warning-soft') }}">
                                            {{ $batch->status === 'approved' ? 'อนุมัติ' : ($batch->status === 'rejected' ? 'ตีกลับ' : 'รอตรวจ') }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="{{ route('accounting-automation.imports.review', $batch) }}" class="d-inline-flex align-items-center gap-1">
                                            @csrf
                                            <select name="status" class="modern-select py-1 px-2 text-xs" style="width: auto; height: 32px;">
                                                <option value="approved">ผ่าน</option>
                                                <option value="rejected">ตีกลับ</option>
                                            </select>
                                            <button class="btn btn-sm btn-light border px-2 py-1 rounded" title="บันทึกผล">
                                                <i class="bi bi-check2"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-4 d-block mb-1"></i>ยังไม่มีไฟล์ในคิวนำเข้า
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
