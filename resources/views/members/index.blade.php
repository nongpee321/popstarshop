@extends('layout')
@section('title', 'สมาชิก - PopCentral')
@section('page-title', 'สมาชิก')
@section('page-subtitle', 'แฟ้มสมาชิก คะแนนสะสม ประเภทสมาชิก และสาขาที่ดูแล')

@section('content')
<div class="member-page-wrapper">
    {{-- สถิติภาพรวมสมาชิก (Stats Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label">สมาชิกทั้งหมด</div>
                    <div class="stat-value">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-label">สถานะใช้งาน</div>
                    <div class="stat-value text-success">{{ number_format($stats['active']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="bi bi-pause-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-label">ปิดใช้งาน / พัก</div>
                    <div class="stat-value text-muted">{{ number_format($stats['inactive']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div>
                    <div class="stat-label">แต้มสะสมรวม</div>
                    <div class="stat-value text-warning">{{ number_format($stats['total_points'], 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- แถบค้นหา & ตัวกรอง (Search & Filter Bar) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 filter-card">
        <div class="card-body p-3">
            <form method="get" action="{{ route('members.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="ค้นหารหัส, ชื่อสมาชิก, เบอร์โทร..." class="form-control border-start-0 ps-0">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="member_type_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- ทุกประเภทสมาชิก --</option>
                        @foreach($memberTypes as $type)
                            <option value="{{ $type->id }}" @selected(($typeId ?? '') == $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- ทุกสาขา --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(($branchId ?? '') == $branch->id)>{{ $branch->code }} - {{ $branch->name_th }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">-- ทุกสถานะ --</option>
                        <option value="active" @selected(($status ?? '') === 'active')>เปิดใช้งาน</option>
                        <option value="inactive" @selected(($status ?? '') === 'inactive')>ปิดใช้งาน</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-funnel-fill me-1"></i> กรอง</button>
                    @if(!empty($q) || !empty($typeId) || !empty($branchId) || !empty($status))
                        <a href="{{ route('members.index') }}" class="btn btn-outline-secondary" title="ล้างตัวกรอง"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Main Content: Form + Table --}}
    <div class="row g-4">
        {{-- การ์ดเพิ่มสมาชิก --}}
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 member-card-form h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
                    <div class="icon-circle bg-primary-subtle text-primary">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h2 class="h6 fw-bold mb-0 text-dark">เพิ่มสมาชิกใหม่</h2>
                        <small class="text-muted">บันทึกข้อมูลสมาชิกและแต้มสะสม</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('members.store') }}" class="row g-3">
                        @csrf
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-secondary">รหัสสมาชิก <span class="text-danger">*</span></label>
                            <input name="member_code" required class="form-control" placeholder="เช่น MEM001" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-secondary">เบอร์โทรศัพท์</label>
                            <input name="phone" class="form-control" placeholder="08xxxxxxxx" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary">ชื่อ - นามสกุลสมาชิก <span class="text-danger">*</span></label>
                            <input name="name" required class="form-control" placeholder="ระบุชื่อสมาชิก" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-secondary">ประเภทสมาชิก</label>
                            <select name="member_type_id" class="form-select">
                                <option value="">-- ไม่ระบุ --</option>
                                @foreach($memberTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->code }} - {{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-secondary">สาขาที่ดูแล</label>
                            <select name="branch_id" class="form-select">
                                <option value="">-- ทุกสาขา --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->code }} - {{ $branch->name_th }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-secondary">แต้มเริ่มต้น</label>
                            <div class="input-group">
                                <input type="number" step="0.0001" min="0" name="points" value="0" class="form-control text-end">
                                <span class="input-group-text bg-light text-muted small">แต้ม</span>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="memberActive">
                                <label class="form-check-label fw-semibold small" for="memberActive">เปิดใช้งานทันที</label>
                            </div>
                        </div>
                        <div class="col-12 pt-2">
                            <button class="btn btn-primary w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-check2-circle fs-5"></i> บันทึกเพิ่มสมาชิก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ตารางรายการสมาชิก --}}
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100 d-flex flex-column">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-circle bg-info-subtle text-info">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <div>
                            <h2 class="h6 fw-bold mb-0 text-dark">แฟ้มรายการสมาชิก</h2>
                            <small class="text-muted">พบทั้งหมด {{ number_format($members->total()) }} รายการ</small>
                        </div>
                    </div>
                </div>

                <div class="table-responsive flex-grow-1">
                    <table class="table member-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">รหัสสมาชิก</th>
                                <th>ชื่อ - เบอร์โทร</th>
                                <th style="width: 150px;">ประเภท</th>
                                <th style="width: 130px;" class="text-end">แต้มสะสม</th>
                                <th style="width: 90px;" class="text-center">สถานะ</th>
                                <th style="width: 80px;" class="text-end">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($members as $member)
                            <tr class="member-row">
                                <form method="post" action="{{ route('members.update', $member) }}" id="form-member-{{ $member->id }}">
                                    @csrf @method('PUT')
                                    <td>
                                        <input name="member_code" value="{{ $member->member_code }}" required class="form-control form-control-sm fw-bold text-dark font-monospace">
                                    </td>
                                    <td>
                                        <input name="name" value="{{ $member->name }}" required class="form-control form-control-sm mb-1 fw-semibold" placeholder="ชื่อสมาชิก">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0 py-0 text-muted" style="font-size:11px"><i class="bi bi-telephone"></i></span>
                                            <input name="phone" value="{{ $member->phone }}" class="form-control form-control-sm border-start-0 py-0" placeholder="เบอร์โทร" style="font-size:12px">
                                        </div>
                                    </td>
                                    <td>
                                        <select name="member_type_id" class="form-select form-select-sm">
                                            <option value="">-- ไม่ระบุ --</option>
                                            @foreach($memberTypes as $type)
                                                <option value="{{ $type->id }}" @selected($member->member_type_id === $type->id)>{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" name="points" value="{{ $member->points }}" class="form-control form-control-sm text-end fw-bold text-primary font-monospace">
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" name="branch_id" value="{{ $member->branch_id }}">
                                        <div class="form-check form-switch d-inline-block">
                                            <input type="checkbox" name="is_active" value="1" @checked($member->is_active) class="form-check-input" title="เปิด/ปิดใช้งาน">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <button type="submit" class="btn btn-sm btn-outline-primary px-3 shadow-none fw-semibold">
                                            <i class="bi bi-save me-1"></i>บันทึก
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state-box">
                                        <div class="empty-icon"><i class="bi bi-person-x"></i></div>
                                        <h6 class="fw-bold text-dark mb-1">ยังไม่มีข้อมูลสมาชิก</h6>
                                        <p class="text-muted small mb-0">กรอกข้อมูลในแบบฟอร์มด้านซ้ายเพื่อเพิ่มสมาชิกใหม่ หรือเปลี่ยนเงื่อนไขการค้นหา</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($members->hasPages())
                    <div class="card-footer bg-white border-top py-3 px-4 d-flex align-items-center justify-content-between">
                        <small class="text-muted">แสดง {{ $members->firstItem() }} - {{ $members->lastItem() }} จากทั้งหมด {{ $members->total() }} รายการ</small>
                        <div>{{ $members->links() }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.member-page-wrapper {
    font-family: inherit;
}

/* Stat Cards */
.stat-card {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
}
.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 20px;
    flex-shrink: 0;
}
.stat-label {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
}
.stat-value {
    font-size: 20px;
    font-weight: 800;
    line-height: 1.2;
    margin-top: 2px;
    font-family: system-ui, -apple-system, sans-serif;
}

/* Icon circle in card header */
.icon-circle {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    font-size: 15px;
}

/* Filter Card */
.filter-card {
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
}

/* Table Design */
.member-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: none;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 14px;
    letter-spacing: 0.02em;
}
.member-table tbody td {
    padding: 12px 14px;
    font-size: 13.5px;
    border-bottom: 1px solid #f1f5f9;
}
.member-table tbody tr.member-row:hover {
    background-color: #f8fafc;
}

/* Empty State */
.empty-state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px 15px;
}
.empty-icon {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #94a3b8;
    display: grid;
    place-items: center;
    font-size: 32px;
    margin-bottom: 14px;
}

/* Inputs and Forms */
.form-control, .form-select {
    border-color: #cbd5e1;
    font-size: 13.5px;
    border-radius: 6px;
}
.form-control:focus, .form-select:focus {
    border-color: var(--erp-primary, #0284c7);
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
}
</style>
@endsection
