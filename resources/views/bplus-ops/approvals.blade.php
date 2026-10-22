@extends('layout')
@section('title', 'อนุมัติเอกสาร - PopCentral')
@section('page-title', 'ศูนย์อนุมัติเอกสาร')
@section('page-subtitle', 'วงเงินเครดิตขาย/ซื้อ, อนุมัติการจ่ายเงิน, การปรับยอดสต็อก และงานอนุมัติทั่วไป')

@push('head')
<style>
    .ap-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Cards */
    .ap-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .ap-card-header {
        padding: 16px 22px;
        background: #fafbfc;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ap-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .ap-card-body {
        padding: 22px;
    }

    /* Labels & Controls */
    .ap-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .ap-input, .ap-select {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        padding: 9px 12px;
        transition: all 0.15s ease;
    }
    .ap-input:focus, .ap-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }

    /* Modern Table */
    .ap-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ap-table th {
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
    .ap-table td {
        padding: 13px 16px;
        font-size: 13px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .ap-table tr:hover td {
        background: #f8fafc;
    }

    /* Status Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-pending  { background: #fef3c7; color: #b45309; }
    .status-approved { background: #dcfce7; color: #15803d; }
    .status-rejected { background: #fee2e2; color: #b91c1c; }
</style>
@endpush

@section('content')
<div class="ap-container">
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center mb-0">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- Form Card: สร้างคำขออนุมัติใหม่ --}}
    <div class="ap-card">
        <div class="ap-card-header">
            <div>
                <h2 class="ap-card-title">
                    <i class="bi bi-file-earmark-check text-danger"></i> ยื่นคำขออนุมัติเอกสารใหม่
                </h2>
                <div class="text-muted small mt-1">สร้างรายการขออนุมัติวงเงินเครดิต, ค่าใช้จ่ายพิเศษ, หรือการปรับยอดสต็อก</div>
            </div>
            <span class="badge bg-light text-muted border">New Request</span>
        </div>
        <div class="ap-card-body">
            <form method="post" action="{{ route('bplus.approvals.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="ap-label">เลขที่คำขอ <span class="text-danger">*</span></label>
                        <input name="request_no" value="AP{{ now()->format('ymdHis') }}" required class="ap-input fw-semibold text-dark">
                    </div>
                    <div class="col-md-3">
                        <label class="ap-label">ประเภทคำขอ <span class="text-danger">*</span></label>
                        <select name="approval_type" class="ap-select" required>
                            <option value="sales_credit">เครดิตขาย (ลูกค้า)</option>
                            <option value="purchase_credit">เครดิตซื้อ (เจ้าหนี้)</option>
                            <option value="payment">การจ่ายเงิน / ค่าใช้จ่าย</option>
                            <option value="stock">การปรับยอดสต็อกสินค้า</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="ap-label">เรื่อง / วัตถุประสงค์ <span class="text-danger">*</span></label>
                        <input name="subject" required placeholder="ระบุหัวข้อ เช่น ขอเพิ่มวงเงินเครดิตลูกค้า บจก.เอ บี ซี" class="ap-input">
                    </div>
                    <div class="col-md-2">
                        <label class="ap-label">ยอดเงินที่ขอ (บาท)</label>
                        <div class="position-relative">
                            <input type="number" step="0.0001" min="0" name="amount" value="0" class="ap-input text-end fw-bold text-dark" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="ap-label">ผู้ยื่นคำขอ</label>
                        <input name="requested_by" class="ap-input" value="{{ auth()->user()?->name }}" placeholder="ชื่อผู้ยื่นคำขอ">
                    </div>
                    <div class="col-md-3">
                        <label class="ap-label">ผู้อนุมัติ</label>
                        <input name="approved_by" class="ap-input" placeholder="ระบุชื่อผู้อนุมัติ (ถ้ามี)">
                    </div>
                    <div class="col-md-3">
                        <label class="ap-label">สถานะเริ่มต้น</label>
                        <select name="status" class="ap-select">
                            <option value="pending">รอการอนุมัติ (Pending)</option>
                            <option value="approved">อนุมัติแล้ว (Approved)</option>
                            <option value="rejected">ไม่อนุมัติ (Rejected)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="ap-label">หมายเหตุเพิ่มเติม</label>
                        <input name="note" class="ap-input" placeholder="รายละเอียดหรือเงื่อนไข">
                    </div>
                </div>

                <div class="text-end pt-2 border-top">
                    <button class="btn btn-danger px-4 py-2 rounded-3 fw-bold shadow-sm d-inline-flex align-items-center">
                        <i class="bi bi-send-plus me-2"></i> ยื่นคำขออนุมัติ
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card: รายการคำขออนุมัติ --}}
    <div class="ap-card">
        <div class="ap-card-header">
            <div>
                <h2 class="ap-card-title">
                    <i class="bi bi-card-checklist text-primary"></i> รายการคำขออนุมัติในระบบ
                </h2>
                <div class="text-muted small mt-1">ประวัติคำขออนุมัติและสถานะการพิจารณา</div>
            </div>
        </div>
        <div class="ap-card-body p-0">
            <div class="table-responsive">
                <table class="ap-table">
                    <thead>
                        <tr>
                            <th>เลขที่คำขอ</th>
                            <th>ประเภทคำขอ</th>
                            <th>เรื่อง / รายละเอียด</th>
                            <th class="text-end">ยอดเงิน</th>
                            <th>ผู้ยื่นคำขอ</th>
                            <th>ผู้อนุมัติ</th>
                            <th class="text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            @php
                                $typeLabel = [
                                    'sales_credit' => 'เครดิตขาย',
                                    'purchase_credit' => 'เครดิตซื้อ',
                                    'payment' => 'การจ่ายเงิน',
                                    'stock' => 'ปรับสต็อก',
                                ][$request->approval_type] ?? $request->approval_type;
                                
                                $statusClass = match($request->status) {
                                    'approved' => 'status-approved',
                                    'rejected' => 'status-rejected',
                                    default => 'status-pending',
                                };
                                $statusText = match($request->status) {
                                    'approved' => 'อนุมัติแล้ว',
                                    'rejected' => 'ไม่อนุมัติ',
                                    default => 'รออนุมัติ',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <code class="text-dark fw-bold">{{ $request->request_no }}</code>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $typeLabel }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark">{{ $request->subject }}</strong>
                                    @if($request->note)
                                        <div class="text-muted small mt-1">{{ $request->note }}</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-dark fs-6">
                                    ฿{{ number_format((float) $request->amount, 2) }}
                                </td>
                                <td>
                                    <i class="bi bi-person me-1 text-muted"></i>{{ $request->requested_by ?? '-' }}
                                </td>
                                <td>
                                    <span class="text-muted">{{ $request->approved_by ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status {{ $statusClass }}">
                                        <i class="bi {{ $request->status === 'approved' ? 'bi-check-circle-fill' : ($request->status === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') }}"></i>
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="py-4">
                                        <i class="bi bi-inbox fs-1 text-secondary opacity-50 d-block mb-2"></i>
                                        <div class="fw-semibold">ยังไม่มีรายการคำขออนุมัติในระบบ</div>
                                        <div class="text-muted small">เมื่อสร้างคำขออนุมัติใหม่ รายการจะแสดงในตารางนี้</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="p-3 border-top">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
