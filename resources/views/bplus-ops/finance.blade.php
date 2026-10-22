@extends('layout')
@section('title', 'เงินสด/ธนาคาร - PopCentral')
@section('page-title', 'เงินสดและบัญชีธนาคาร')
@section('page-subtitle', 'สมุดเงินสดรายวัน, รายการปรับปรุง, ฝาก ถอน โอน และสถานะบัญชีธนาคาร')

@push('head')
<style>
    .fn-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* KPI Summary Stat Cards */
    .fn-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .fn-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .fn-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .fn-kpi-info {
        flex: 1;
        min-width: 0;
    }
    .fn-kpi-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
    }
    .fn-kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }

    /* Standard Cards */
    .fn-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .fn-card-header {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fn-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .fn-card-body {
        padding: 20px;
    }

    /* Form Controls */
    .fn-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .fn-input, .fn-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        padding: 8px 12px;
        transition: all 0.15s ease;
    }
    .fn-input:focus, .fn-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }

    /* Cash In / Out Amount Groups */
    .cash-amount-box {
        position: relative;
        display: flex;
        align-items: center;
    }
    .cash-amount-box input {
        padding-left: 36px;
        font-weight: 700;
        font-size: 15px;
    }
    .cash-symbol {
        position: absolute;
        left: 12px;
        font-weight: 800;
        font-size: 16px;
        pointer-events: none;
    }
    .cash-in input {
        color: #16a34a;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .cash-in .cash-symbol {
        color: #16a34a;
    }
    .cash-out input {
        color: #dc2626;
        background: #fef2f2;
        border-color: #fecaca;
    }
    .cash-out .cash-symbol {
        color: #dc2626;
    }

    /* Bank Account Card Grid */
    .bank-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }
    .bank-card-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .bank-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .bank-card-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #0284c7;
    }
    .bank-card-item.bank-kbank::before { background: #138f2d; }
    .bank-card-item.bank-scb::before   { background: #4e2a84; }
    .bank-card-item.bank-bbl::before   { background: #1e40af; }
    .bank-card-item.bank-ktb::before   { background: #00a5e5; }
    .bank-card-item.bank-ttb::before   { background: #002d62; }
    .bank-card-item.is-inactive::before { background: #94a3b8; }

    .bank-num-display {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: 0.04em;
    }

    /* Modern Table */
    .fn-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .fn-table th {
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
    }
    .fn-table td {
        padding: 12px 16px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .fn-table tr:hover td {
        background: #f8fafc;
    }

    /* Soft Badges */
    .badge-soft-success { background: #dcfce7; color: #15803d; font-weight: 600; padding: 4px 8px; border-radius: 6px; font-size: 11px; }
    .badge-soft-secondary { background: #f1f5f9; color: #64748b; font-weight: 600; padding: 4px 8px; border-radius: 6px; font-size: 11px; }

    @media (max-width: 1200px) {
        .fn-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .fn-kpi-grid { grid-template-columns: 1fr; }
        .bank-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="fn-container">
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

    @php
        $latestBalance = $cashBooks->first()?->running_balance ?? 0;
        $totalInToday = $cashBooks->where('entry_date', now()->toDateString())->sum('cash_in');
        $totalOutToday = $cashBooks->where('entry_date', now()->toDateString())->sum('cash_out');
        $activeBankCount = $bankAccounts->where('is_active', true)->count();
    @endphp

    {{-- Top KPI Summary Ribbon --}}
    <section class="fn-kpi-grid">
        <div class="fn-kpi-card">
            <div class="fn-kpi-icon bg-primary bg-opacity-10 text-primary">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="fn-kpi-info">
                <div class="fn-kpi-label">ยอดเงินสดคงเหลือล่าสุด</div>
                <div class="fn-kpi-value text-primary">฿{{ number_format((float)$latestBalance, 2) }}</div>
            </div>
        </div>
        <div class="fn-kpi-card">
            <div class="fn-kpi-icon bg-success bg-opacity-10 text-success">
                <i class="bi bi-arrow-down-left"></i>
            </div>
            <div class="fn-kpi-info">
                <div class="fn-kpi-label">เงินสดรับเข้า (วันนี้)</div>
                <div class="fn-kpi-value text-success">฿{{ number_format((float)$totalInToday, 2) }}</div>
            </div>
        </div>
        <div class="fn-kpi-card">
            <div class="fn-kpi-icon bg-danger bg-opacity-10 text-danger">
                <i class="bi bi-arrow-up-right"></i>
            </div>
            <div class="fn-kpi-info">
                <div class="fn-kpi-label">เงินสดจ่ายออก (วันนี้)</div>
                <div class="fn-kpi-value text-danger">฿{{ number_format((float)$totalOutToday, 2) }}</div>
            </div>
        </div>
        <div class="fn-kpi-card">
            <div class="fn-kpi-icon bg-info bg-opacity-10 text-info">
                <i class="bi bi-bank2"></i>
            </div>
            <div class="fn-kpi-info">
                <div class="fn-kpi-label">บัญชีธนาคารพร้อมใช้งาน</div>
                <div class="fn-kpi-value text-dark">{{ $activeBankCount }} / {{ count($bankAccounts) }} <small class="fs-6 text-muted fw-normal">บัญชี</small></div>
            </div>
        </div>
    </section>

    {{-- Bank Accounts Section --}}
    <div class="fn-card">
        <div class="fn-card-header">
            <div>
                <h2 class="fn-card-title">
                    <i class="bi bi-bank2 text-primary"></i> บัญชีธนาคารของกิจการ
                </h2>
                <div class="text-muted small mt-1">บัญชีธนาคารสำหรับรับชำระเงิน POS, โอนจ่ายซัพพลายเออร์ และกระทบยอด Statement</div>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                {{ count($bankAccounts) }} บัญชีทั้งหมด
            </span>
        </div>
        <div class="fn-card-body">
            <div class="bank-grid">
                @forelse($bankAccounts as $account)
                    @php
                        $bankClass = '';
                        $bankName = $account->bank_name;
                        if (str_contains($bankName, 'กสิกร')) $bankClass = 'bank-kbank';
                        elseif (str_contains($bankName, 'ไทยพาณิชย์')) $bankClass = 'bank-scb';
                        elseif (str_contains($bankName, 'กรุงเทพ')) $bankClass = 'bank-bbl';
                        elseif (str_contains($bankName, 'กรุงไทย')) $bankClass = 'bank-ktb';
                        elseif (str_contains($bankName, 'ทหารไทย') || str_contains($bankName, 'ttb')) $bankClass = 'bank-ttb';
                    @endphp
                    <div class="bank-card-item {{ $bankClass }} {{ $account->is_active ? '' : 'is-inactive' }}">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-light rounded-circle text-primary">
                                    <i class="bi bi-building fs-5"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block">{{ $account->bank_name }}</strong>
                                    <span class="text-muted small">{{ $account->branch?->name_th ?? 'ทุกสาขา / สำนักงานใหญ่' }}</span>
                                </div>
                            </div>
                            <span class="{{ $account->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                <i class="bi {{ $account->is_active ? 'bi-check-circle-fill' : 'bi-dash-circle' }} me-1"></i>
                                {{ $account->is_active ? 'เปิดใช้งาน' : 'ปิด' }}
                            </span>
                        </div>

                        <div>
                            <div class="text-muted small mb-1">เลขที่บัญชี</div>
                            <div class="bank-num-display text-primary">{{ $account->account_no }}</div>
                        </div>

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center text-muted small">
                            <span><i class="bi bi-person me-1"></i>ชื่อบัญชี:</span>
                            <strong class="text-dark">{{ $account->account_name }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted col-12">
                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>ยังไม่มีบัญชีธนาคารในระบบ
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Main Section: Form Adjust Cash (Collapsible or Clean Card) & Cash Book Table --}}
    <div class="row g-3">
        {{-- Card: Form Adjust Cash Book --}}
        <div class="col-xl-4">
            <div class="fn-card h-100">
                <div class="fn-card-header">
                    <div>
                        <h2 class="fn-card-title">
                            <i class="bi bi-pencil-square text-danger"></i> ปรับปรุงสมุดเงินสด
                        </h2>
                        <div class="text-muted small mt-1">บันทึกยอดเงินสดส่วนต่างพิเศษ</div>
                    </div>
                </div>
                <div class="fn-card-body">
                    <div class="p-3 bg-light rounded-3 border mb-3 small text-muted">
                        <i class="bi bi-info-circle-fill text-primary me-1"></i>
                        ยอดขาย POS, ปิดกะ และรับชำระ <strong>ลงบัญชีอัตโนมัติแล้ว</strong> ฟอร์มนี้ใช้เฉพาะปรับยอดพิเศษ
                    </div>

                    <form method="post" action="{{ route('bplus.cash-books.store') }}">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="fn-label">วันที่ <span class="text-danger">*</span></label>
                                <input type="date" name="entry_date" value="{{ now()->toDateString() }}" required class="fn-input">
                            </div>
                            <div class="col-6">
                                <label class="fn-label">สาขา</label>
                                <select name="branch_id" class="fn-select">
                                    <option value="">-- ส่วนกลาง --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->code }} · {{ $branch->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="fn-label text-success fw-bold">รับเข้า (Cash In)</label>
                                <div class="cash-amount-box cash-in">
                                    <span class="cash-symbol">+</span>
                                    <input type="number" step="0.0001" name="cash_in" value="0" class="fn-input">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="fn-label text-danger fw-bold">จ่ายออก (Cash Out)</label>
                                <div class="cash-amount-box cash-out">
                                    <span class="cash-symbol">-</span>
                                    <input type="number" step="0.0001" name="cash_out" value="0" class="fn-input">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="fn-label">ชื่อรายการ <span class="text-danger">*</span></label>
                            <input name="description" class="fn-input" placeholder="เช่น เงินทอนเกิน/ขาด, ยืมเงินสดย่อย" required>
                        </div>

                        <div class="mb-2">
                            <label class="fn-label">เหตุผลที่ต้องปรับ <span class="text-danger">*</span></label>
                            <input name="reason" class="fn-input" placeholder="ระบุเหตุผลและหลักฐานอ้างอิง..." required>
                        </div>

                        <div class="mb-3">
                            <label class="fn-label">ผู้อนุมัติ <span class="text-danger">*</span></label>
                            <select name="approved_by" class="fn-select" required>
                                <option value="">-- เลือกผู้อนุมัติ --</option>
                                @foreach($approvers as $approver)
                                    <option value="{{ $approver->id }}">{{ $approver->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="text-end">
                            <button class="btn btn-danger w-100 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center">
                                <i class="bi bi-check2-circle me-2"></i> บันทึกรายการปรับปรุง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table: Cash Book Ledger --}}
        <div class="col-xl-8">
            <div class="fn-card h-100">
                <div class="fn-card-header">
                    <div>
                        <h2 class="fn-card-title">
                            <i class="bi bi-journal-bookmark-fill text-danger"></i> สมุดเงินสดรายวัน (Cash Book)
                        </h2>
                        <div class="text-muted small mt-1">ประวัติความเคลื่อนไหวเงินสด เข้า-ออก และยอดคงเหลือยกไป</div>
                    </div>
                </div>
                <div class="fn-card-body p-0">
                    <div class="table-responsive">
                        <table class="fn-table">
                            <thead>
                                <tr>
                                    <th>วันที่</th>
                                    <th>สาขา</th>
                                    <th>ที่มา</th>
                                    <th>รายการ</th>
                                    <th class="text-end">รับ</th>
                                    <th class="text-end">จ่าย</th>
                                    <th class="text-end">คงเหลือ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cashBooks as $entry)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $entry->entry_date?->thaiDate() }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $entry->branch?->name_th ?? 'ส่วนกลาง' }}</span>
                                        </td>
                                        <td>
                                            <code class="small text-muted">{{ $entry->source_type }}</code>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $entry->description }}</span>
                                        </td>
                                        <td class="text-end fw-bold {{ (float)$entry->cash_in > 0 ? 'text-success' : 'text-muted' }}">
                                            {{ (float)$entry->cash_in > 0 ? '+'.number_format((float) $entry->cash_in, 2) : '-' }}
                                        </td>
                                        <td class="text-end fw-bold {{ (float)$entry->cash_out > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ (float)$entry->cash_out > 0 ? '-'.number_format((float) $entry->cash_out, 2) : '-' }}
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-6 bg-light bg-opacity-50">
                                            ฿{{ number_format((float) $entry->running_balance, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>ยังไม่มีรายการเคลื่อนไหวในสมุดเงินสด
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($cashBooks->hasPages())
                        <div class="p-3 border-top">
                            {{ $cashBooks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
