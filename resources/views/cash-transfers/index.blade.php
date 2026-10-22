@extends('layout')

@section('title', 'ฝาก/ถอนเงินสด - PopCentral')
@section('page-title', 'ฝาก/ถอนเงินสดกับธนาคาร')
@section('page-subtitle', 'บันทึกรายการฝากเข้าธนาคาร หรือถอนเงินสดเข้าลิ้นชัก — บันทึกสมุดเงินสดและลงบัญชี GL ให้อัตโนมัติ')

@push('head')
<style>
    .ct-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Cards */
    .ct-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .ct-card-header {
        padding: 16px 22px;
        background: #ffffff;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ct-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ct-card-body {
        padding: 22px;
    }

    /* Form Controls */
    .ct-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .ct-input, .ct-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        padding: 9px 12px;
        transition: all 0.15s ease;
    }
    .ct-input:focus, .ct-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }
    .ct-input:disabled, .ct-select:disabled {
        background: #f1f5f9;
        color: #64748b;
        cursor: not-allowed;
    }

    /* Info Callout */
    .ct-callout {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-left: 4px solid #3b82f6;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 12px;
        color: #1e40af;
        line-height: 1.5;
        margin-bottom: 18px;
    }

    /* Type Switcher Pills */
    .type-switcher {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        margin-bottom: 16px;
    }
    .type-radio-label {
        margin: 0;
        cursor: pointer;
        text-align: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .type-radio-label.is-active-deposit {
        background: #ffffff;
        color: #dc2626;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .type-radio-label.is-active-withdraw {
        background: #ffffff;
        color: #16a34a;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }

    /* Amount Input */
    .amount-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .amount-input-wrap .currency-tag {
        position: absolute;
        left: 12px;
        font-weight: 700;
        color: #64748b;
        pointer-events: none;
    }
    .amount-input-wrap input {
        padding-left: 32px;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
    }

    /* Table Styles */
    .ct-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ct-table th {
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
    .ct-table td {
        padding: 13px 16px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .ct-table tr:hover td {
        background: #f8fafc;
    }

    /* Badges */
    .badge-deposit {
        background: #fef2f2;
        color: #dc2626;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-withdraw {
        background: #f0fdf4;
        color: #16a34a;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>
@endpush

@section('content')
<div class="ct-container" x-data="{ transferType: '{{ old('type', 'CASH_DEPOSIT') }}' }">
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="row g-3">
        {{-- Card 1: New Transfer Form --}}
        <div class="col-xl-5">
            <div class="ct-card h-100">
                <div class="ct-card-header">
                    <div>
                        <h2 class="ct-card-title">
                            <i class="bi bi-arrow-left-right text-danger"></i> บันทึกรายการใหม่
                        </h2>
                        <div class="text-muted small mt-1">ฝากเงินเข้าธนาคาร หรือถอนเงินสดเข้าลิ้นชัก</div>
                    </div>
                </div>
                <div class="ct-card-body">
                    <div class="ct-callout">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>คำอธิบาย:</strong><br>
                        • <strong>ฝากเงินสด:</strong> เงินสดออกจากลิ้นชักเข้าบัญชีธนาคาร<br>
                        • <strong>ถอนเงินสด:</strong> เงินออกจากธนาคารเข้าลิ้นชัก<br>
                        <em>ระบบบันทึกลงสมุดเงินสดและลงบัญชี GL ให้อัตโนมัติทันที</em>
                    </div>

                    <form method="post" action="{{ route('cash-transfers.store') }}">
                        @csrf
                        
                        {{-- Type Selector (Deposit vs Withdrawal) --}}
                        <div class="type-switcher">
                            <label class="type-radio-label" :class="transferType === 'CASH_DEPOSIT' ? 'is-active-deposit' : ''" @click="transferType = 'CASH_DEPOSIT'">
                                <input type="radio" name="type" value="CASH_DEPOSIT" class="d-none" x-model="transferType">
                                <i class="bi bi-box-arrow-up-right text-danger"></i> ฝากเงินสดเข้าธนาคาร
                            </label>
                            <label class="type-radio-label" :class="transferType === 'CASH_WITHDRAWAL' ? 'is-active-withdraw' : ''" @click="transferType = 'CASH_WITHDRAWAL'">
                                <input type="radio" name="type" value="CASH_WITHDRAWAL" class="d-none" x-model="transferType">
                                <i class="bi bi-box-arrow-in-down-left text-success"></i> ถอนเงินสดจากธนาคาร
                            </label>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="ct-label">วันที่ทำรายการ <span class="text-danger">*</span></label>
                                <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->toDateString()) }}" class="ct-input" required>
                            </div>
                            <div class="col-md-6">
                                <label class="ct-label">จำนวนเงิน (บาท) <span class="text-danger">*</span></label>
                                <div class="amount-input-wrap">
                                    <span class="currency-tag">฿</span>
                                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="ct-input text-end" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="ct-label">สาขา <span class="text-danger">*</span></label>
                            <select name="branch_id" class="ct-select" required @disabled($defaultBranchId)>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('branch_id', $defaultBranchId) == $branch->id)>
                                        {{ $branch->code }} · {{ $branch->name_th }}
                                    </option>
                                @endforeach
                            </select>
                            @if($defaultBranchId)
                                <input type="hidden" name="branch_id" value="{{ $defaultBranchId }}">
                                <div class="text-muted small mt-1"><i class="bi bi-lock-fill me-1"></i>บันทึกได้เฉพาะสาขาที่คุณสังกัด</div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="ct-label">บัญชีธนาคาร <span class="text-danger">*</span></label>
                            <select name="bank_account_id" class="ct-select" required>
                                @forelse($bankAccounts as $account)
                                    <option value="{{ $account->id }}" @selected(old('bank_account_id') == $account->id)>
                                        {{ $account->bank_name }} · {{ $account->account_no }} ({{ $account->account_name }})
                                    </option>
                                @empty
                                    <option value="">ยังไม่มีบัญชีธนาคารในระบบ</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="ct-label">เลขที่อ้างอิง / สลิปโอนเงิน</label>
                            <input type="text" name="reference" value="{{ old('reference') }}" maxlength="100" placeholder="เช่น เลขที่สลิป หรือเลขอ้างอิงธนาคาร" class="ct-input">
                        </div>

                        <div class="mb-4">
                            <label class="ct-label">หมายเหตุเพิ่มเติม</label>
                            <input type="text" name="remark" value="{{ old('remark') }}" maxlength="500" placeholder="ระบุเหตุผลหรือรายละเอียดเพิ่มเติม (ถ้ามี)" class="ct-input">
                        </div>

                        <div>
                            <button class="btn btn-danger w-100 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center" @disabled($bankAccounts->isEmpty())>
                                <i class="bi bi-check2-circle me-2 fs-5"></i> บันทึกรายการ
                            </button>
                            @if($bankAccounts->isEmpty())
                                <div class="text-danger small mt-2 text-center">ต้องเพิ่มบัญชีธนาคารก่อนจึงจะสามารถทำรายการได้</div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Card 2: Recent Transactions Table --}}
        <div class="col-xl-7">
            <div class="ct-card h-100">
                <div class="ct-card-header">
                    <div>
                        <h2 class="ct-card-title">
                            <i class="bi bi-clock-history text-primary"></i> ประวัติรายการฝาก / ถอน ล่าสุด
                        </h2>
                        <div class="text-muted small mt-1">ประวัติการเคลื่อนไหวเงินสดระหว่างลิ้นชักกับบัญชีธนาคาร</div>
                    </div>
                </div>
                <div class="ct-card-body p-0">
                    <div class="table-responsive">
                        <table class="ct-table">
                            <thead>
                                <tr>
                                    <th>วันที่</th>
                                    <th>เลขที่เอกสาร</th>
                                    <th>ประเภทรายการ</th>
                                    <th>สาขา</th>
                                    <th class="text-end">จำนวนเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $transfer)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                <i class="bi bi-calendar-event me-1 text-muted"></i>
                                                {{ $transfer->doc_date->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <code class="text-dark fw-bold">{{ $transfer->doc_number }}</code>
                                        </td>
                                        <td>
                                            @if($transfer->documentType->code === 'CASH_DEPOSIT')
                                                <span class="badge-deposit">
                                                    <i class="bi bi-box-arrow-up-right"></i> ฝากเข้าธนาคาร
                                                </span>
                                            @else
                                                <span class="badge-withdraw">
                                                    <i class="bi bi-box-arrow-in-down-left"></i> ถอนเข้าลิ้นชัก
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                {{ $transfer->branch?->name_th ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-6">
                                            ฿{{ number_format((float) $transfer->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <div class="py-4">
                                                <i class="bi bi-file-earmark-text fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                                <div class="fw-semibold">ยังไม่มีรายการฝาก/ถอนเงินสดในระบบ</div>
                                                <div class="text-muted small">เมื่อบันทึกรายการฝากหรือถอนเงินสด ประวัติจะแสดงที่นี่</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($transfers->hasPages())
                        <div class="p-3 border-top">
                            {{ $transfers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
