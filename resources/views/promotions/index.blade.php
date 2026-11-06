@extends('layout')
@section('title', 'จัดการโปรโมชั่นและส่วนลด - PopCentral')
@section('page-title', 'จัดการโปรโมชั่นและส่วนลด')
@section('page-subtitle', 'ตั้งแคมเปญ ส่วนลด คูปอง และเงื่อนไขขายหน้าร้าน POS ได้ง่ายและแม่นยำ')

@push('styles')
<style>
    .promo-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .promo-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .promo-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .promo-section-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 18px;
    }
    .promo-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .product-selected-card {
        background: #ffffff;
        border: 2px solid #3b82f6;
        border-radius: 10px;
        padding: 14px 18px;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.08);
    }
    .product-empty-card {
        background: #ffffff;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 16px 20px;
        transition: border-color 0.2s ease, background 0.2s ease;
    }
    .product-empty-card:hover {
        border-color: #3b82f6;
        background: #f0f7ff;
    }
    .table thead th {
        background: #0f172a;
        color: #f8fafc;
        font-weight: 600;
        font-size: 13px;
        letter-spacing: 0.3px;
        padding: 12px 14px;
        border: none;
    }
    .table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div x-data="promotionManager()">

    {{-- แถบสถิติสรุปโปรโมชั่น --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="promo-stat-card">
                <div class="promo-stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">โปรโมชั่นทั้งหมด</div>
                    <div class="h4 fw-bold mb-0 text-dark">{{ number_format($stats['total'] ?? 0) }} <small class="fs-6 fw-normal text-muted">รายการ</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="promo-stat-card">
                <div class="promo-stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">กำลังใช้งาน</div>
                    <div class="h4 fw-bold mb-0 text-success">{{ number_format($stats['active'] ?? 0) }} <small class="fs-6 fw-normal text-muted">รายการ</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="promo-stat-card">
                <div class="promo-stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">รอเริ่มตามกำหนด</div>
                    <div class="h4 fw-bold mb-0 text-warning">{{ number_format($stats['upcoming'] ?? 0) }} <small class="fs-6 fw-normal text-muted">รายการ</small></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="promo-stat-card">
                <div class="promo-stat-icon bg-secondary-subtle text-secondary">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">หมดอายุแล้ว</div>
                    <div class="h4 fw-bold mb-0 text-muted">{{ number_format($stats['expired'] ?? 0) }} <small class="fs-6 fw-normal text-muted">รายการ</small></div>
                </div>
            </div>
        </div>
    </div>

    {{-- การ์ดฟอร์ม: สร้าง / แก้ไขโปรโมชั่น --}}
    <div class="content-card p-4 mb-4 shadow-sm" style="border-radius: 14px;">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge" :class="isEditing ? 'bg-warning text-dark' : 'bg-primary'" style="font-size: 14px; padding: 7px 14px; border-radius: 8px;">
                    <i class="bi" :class="isEditing ? 'bi-pencil-square me-1' : 'bi-plus-circle me-1'"></i>
                    <span x-text="isEditing ? 'กำลังแก้ไขโปรโมชั่น' : 'สร้างโปรโมชั่นใหม่'"></span>
                </span>
                <span class="text-muted small d-none d-md-inline" x-text="isEditing ? 'แก้ไขเงื่อนไขโปรโมชั่น รหัส: ' + form.code : 'กำหนดข้อมูล ส่วนลด และสินค้าที่ร่วมรายการเพื่อส่งไปใช้งานที่ POS หน้าร้าน'"></span>
            </div>
            <template x-if="isEditing">
                <button type="button" @click="resetForm()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> ยกเลิกการแก้ไข
                </button>
            </template>
        </div>

        <form :action="isEditing ? ('/promotions/' + editingId) : '{{ route('promotions.store') }}'" method="post">
            @csrf
            <template x-if="isEditing">
                <input type="hidden" name="_method" value="PUT">
            </template>

            {{-- 1. ส่วนข้อมูลโปรโมชั่นทั่วไป --}}
            <div class="promo-section-box">
                <div class="promo-section-title">
                    <i class="bi bi-info-circle-fill text-primary"></i>
                    <span>1. ข้อมูลทั่วไปและช่วงเวลาโปรโมชั่น</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">รหัสโปรโมชั่น</label>
                        <input type="text" name="code" x-model="form.code" class="form-control font-monospace" placeholder="เว้นว่างได้ ระบบรันให้ (PM...)">
                        <div class="form-text small text-muted">เว้นว่างเพื่อให้ระบบตั้งรหัสให้อัตโนมัติ</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark mb-1">ชื่อโปรโมชั่น <span class="text-danger">*</span></label>
                        <input type="text" name="name" x-model="form.name" required class="form-control" placeholder="เช่น เห็ดหิมะ ซื้อ 3 แพ็ค ลด 10 บาท">
                        <div class="form-text small text-muted">ระบุชื่อโปรโมชั่นให้ชัดเจนสำหรับแสดงที่ POS</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">ประเภทโปรโมชั่น</label>
                        <select name="promotion_type" x-model="form.promotion_type" class="form-select">
                            <option value="discount">ส่วนลดทั่วไป (Discount)</option>
                            <option value="coupon">คูปองส่วนลด (Coupon)</option>
                            <option value="free_item">ซื้อครบได้ของแถม (Free Item)</option>
                            <option value="member_points">แต้มสะสมพิเศษ (Points)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">วันที่เริ่มต้น</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar-check text-primary"></i></span>
                            <input type="date" name="starts_at" x-model="form.starts_at" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">วันที่สิ้นสุด</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar-x text-danger"></i></span>
                            <input type="date" name="ends_at" x-model="form.ends_at" class="form-control">
                        </div>
                        <div class="form-text small text-muted">เว้นว่างไว้หากไม่มีกำหนดวันหมดอายุ</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center pt-2">
                        <div class="form-check form-switch p-0 mt-3 d-flex align-items-center gap-2">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" value="1" id="promoActiveSwitch" x-model="form.is_active" style="width: 2.8em; height: 1.4em; cursor: pointer;">
                            <label class="form-check-label fw-bold small text-dark cursor-pointer" for="promoActiveSwitch">
                                <span :class="form.is_active ? 'text-success' : 'text-muted'" x-text="form.is_active ? 'เปิดใช้งานโปรโมชั่นนี้ (Active)' : 'ปิดใช้งานชั่วคราว (Disabled)'"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. ส่วนเลือกสินค้าที่ร่วมรายการ --}}
            <div class="promo-section-box">
                <div class="promo-section-title">
                    <i class="bi bi-box-seam-fill text-success"></i>
                    <span>2. สินค้าที่ร่วมรายการ</span>
                </div>
                <input type="hidden" name="product_id" x-model="items[0].product_id">

                {{-- กรณีเลือกสินค้าแล้ว --}}
                <div x-show="items[0].product_id" x-cloak class="product-selected-card">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-3 px-3 py-2 text-center" style="min-width: 130px;">
                                <div style="font-size: 10px; opacity: 0.85;">รหัสสินค้า / บาร์โค้ด</div>
                                <div class="fw-bold font-monospace fs-6" x-text="items[0].sku_code"></div>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark" x-text="items[0].name_th"></h5>
                                <div class="text-muted small">
                                    <span class="badge bg-light text-dark border me-2">หน่วย: <span x-text="items[0].unit_name || 'หน่วยมาตรฐาน'"></span></span>
                                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>กำหนดส่วนลดเฉพาะสินค้านี้</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-self-end align-self-md-center">
                            <button type="button" class="btn btn-outline-primary btn-sm px-3" @click="openProductPicker(0)">
                                <i class="bi bi-arrow-repeat me-1"></i> เปลี่ยนสินค้า
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3" @click="items[0] = emptyProduct()">
                                <i class="bi bi-x-circle me-1"></i> ใช้กับทุกสินค้า (ล้าง)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- กรณีไม่ได้ระบุสินค้า (ใช้กับทุกสินค้า) --}}
                <div x-show="!items[0].product_id" class="product-empty-card">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-light border text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 22px;">
                                <i class="bi bi-shop"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6">ใช้กับทุกสินค้าในร้าน (ลดทั้งบิล)</div>
                                <div class="text-muted small">โปรโมชั่นนี้จะมีผลกับสินค้าทุกรายการ หรือใช้เป็นส่วนลดท้ายบิล</div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm px-3 py-2 shadow-sm" @click="openProductPicker(0)">
                                <i class="bi bi-search me-1"></i> ค้นหาและระบุสินค้าเฉพาะ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. ส่วนกำหนดส่วนลดและเงื่อนไขขั้นต่ำ --}}
            <div class="promo-section-box">
                <div class="promo-section-title">
                    <i class="bi bi-percent text-danger"></i>
                    <span>3. กำหนดมูลค่าส่วนลดและเงื่อนไขการซื้อ</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">ส่วนลด (บาท)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold text-success">฿</span>
                            <input type="number" step="0.01" min="0" name="discount_amount" x-model="form.discount_amount" class="form-control" placeholder="เช่น 10.00">
                        </div>
                        <div class="form-text small text-muted">หักลดเป็นจำนวนเงินบาท</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">ส่วนลด (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" name="discount_percent" x-model="form.discount_percent" class="form-control" placeholder="เช่น 15">
                            <span class="input-group-text bg-white fw-bold text-danger">%</span>
                        </div>
                        <div class="form-text small text-muted">หักลดเป็นเปอร์เซ็นต์</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">จำนวนซื้อขั้นต่ำ</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" name="min_qty" x-model="form.min_qty" class="form-control" placeholder="เช่น 3">
                            <span class="input-group-text bg-white text-muted">ชิ้น/แพ็ค</span>
                        </div>
                        <div class="form-text small text-muted">ต้องซื้อครบจำนวนนี้ถึงได้ลด</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark mb-1">ยอดซื้อขั้นต่ำ</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted">฿</span>
                            <input type="number" step="0.01" min="0" name="min_amount" x-model="form.min_amount" class="form-control" placeholder="เช่น 300.00">
                        </div>
                        <div class="form-text small text-muted">ยอดซื้อรวมในบิลขั้นต่ำ</div>
                    </div>
                </div>
            </div>

            {{-- 4. หมายเหตุและปุ่มบันทึก --}}
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <label class="form-label small fw-bold text-dark mb-1">หมายเหตุ / เงื่อนไขเพิ่มเติม</label>
                    <input type="text" name="note" x-model="form.note" class="form-control" placeholder="ระบุเงื่อนไขเพิ่มเติม (ถ้ามี) เช่น เฉพาะสมาชิก, ห้ามใช้ร่วมกับโปรโมชั่นอื่น">
                </div>
                <div class="col-md-4 d-flex justify-content-end gap-2 pt-md-4">
                    <button type="button" class="btn btn-light border px-3" @click="resetForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> ล้างฟอร์ม
                    </button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="bi" :class="isEditing ? 'bi-check-lg me-1' : 'bi-plus-lg me-1'"></i>
                        <span x-text="isEditing ? 'บันทึกการแก้ไข' : 'บันทึกโปรโมชั่น'"></span>
                    </button>
                </div>
            </div>
        </form>

        @include('partials.product-picker')
    </div>

    {{-- ตารางรายการโปรโมชั่นทั้งหมด --}}
    <div class="content-card p-4 shadow-sm" style="border-radius: 14px;">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-3 pb-2 border-bottom">
            <div>
                <h3 class="h5 fw-bold text-dark mb-1">
                    <i class="bi bi-list-stars text-primary me-2"></i>รายการโปรโมชั่นทั้งหมด
                </h3>
                <p class="text-muted small mb-0">โปรโมชั่นที่เปิดใช้งานอยู่จะถูกส่งต่อไปคำนวณอัตโนมัติที่หน้าจอขายหน้าร้าน POS</p>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto" style="max-width: 320px;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" x-model="searchQuery" class="form-control" placeholder="ค้นหารหัส, ชื่อ, หรือสินค้า...">
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="btn btn-outline-secondary">ล้าง</button>
                </div>
            </div>
        </div>

        <div class="table-responsive rounded-3 border">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">รหัสโปรโมชั่น</th>
                        <th>ชื่อโปรโมชั่น</th>
                        <th style="width: 120px;">ประเภท</th>
                        <th>สินค้าที่ร่วมรายการ</th>
                        <th>ส่วนลด / สิทธิพิเศษ</th>
                        <th>เงื่อนไขขั้นต่ำ</th>
                        <th style="width: 180px;">ช่วงเวลาใช้งาน</th>
                        <th style="width: 100px;">สถานะ</th>
                        <th style="width: 110px;" class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($promotions as $promotion)
                    @php
                        $nowStr = now()->toDateString();
                        $isExpired = $promotion->ends_at && $promotion->ends_at->toDateString() < $nowStr;
                        $isUpcoming = $promotion->starts_at && $promotion->starts_at->toDateString() > $nowStr;
                    @endphp
                    <tr x-show="!searchQuery || '{{ strtolower($promotion->code.' '.$promotion->name.' '.($promotion->product?->name_th ?? '').' '.($promotion->product?->sku_code ?? '')) }}'.includes(searchQuery.toLowerCase())">
                        <td>
                            <span class="badge bg-light text-dark font-monospace border px-2 py-1 fs-7">
                                {{ $promotion->code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $promotion->name }}</div>
                            @if($promotion->note)
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    <i class="bi bi-chat-left-text me-1"></i>{{ $promotion->note }}
                                </small>
                            @endif
                        </td>
                        <td>
                            @if($promotion->promotion_type === 'discount')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">ส่วนลด</span>
                            @elseif($promotion->promotion_type === 'coupon')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">คูปอง</span>
                            @elseif($promotion->promotion_type === 'free_item')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">ของแถม</span>
                            @elseif($promotion->promotion_type === 'member_points')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">แต้มสะสม</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">{{ $promotion->promotion_type }}</span>
                            @endif
                        </td>
                        <td>
                            @if($promotion->product)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-light text-dark border font-monospace">{{ $promotion->product->sku_code }}</span>
                                    <span class="fw-medium text-dark">{{ $promotion->product->name_th }}</span>
                                </div>
                            @else
                                <span class="badge bg-light text-secondary border">
                                    <i class="bi bi-shop me-1"></i>ทุกสินค้าในร้าน
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($promotion->discount_percent > 0)
                                <span class="fw-bold text-danger fs-6">ลด {{ number_format($promotion->discount_percent, 2) }}%</span>
                            @elseif($promotion->discount_amount > 0)
                                <span class="fw-bold text-success fs-6">ลด ฿{{ number_format((float) $promotion->discount_amount, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($promotion->min_qty > 0)
                                <span class="badge bg-light text-dark border me-1">ซื้อครบ {{ (float)$promotion->min_qty }} ชิ้น</span>
                            @endif
                            @if($promotion->min_amount > 0)
                                <span class="badge bg-light text-dark border">ยอดครบ ฿{{ number_format((float)$promotion->min_amount, 2) }}</span>
                            @endif
                            @if(!$promotion->min_qty && !$promotion->min_amount)
                                <span class="text-muted small">ไม่มีขั้นต่ำ</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                @if($promotion->starts_at || $promotion->ends_at)
                                    <div>{{ $promotion->starts_at?->format('d/m/Y') ?? 'เริ่มทันที' }}</div>
                                    <div class="text-muted">ถึง {{ $promotion->ends_at?->format('d/m/Y') ?? 'ไม่มีกำหนด' }}</div>
                                @else
                                    <span class="text-muted">ตลอดเวลา</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if(!$promotion->is_active)
                                <span class="badge bg-secondary-subtle text-secondary border">ปิดใช้งาน</span>
                            @elseif($isExpired)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">หมดอายุ</span>
                            @elseif($isUpcoming)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">รอเริ่ม</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle">กำลังใช้งาน</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        title="แก้ไขโปรโมชั่น"
                                        @click="editPromotion(@js($promotion))">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="post" action="{{ route('promotions.destroy', $promotion) }}" class="d-inline" onsubmit="return confirm('ยืนยันลบโปรโมชั่น {{ $promotion->code }} หรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="ลบโปรโมชั่น">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted mb-3" style="font-size: 42px;">
                                <i class="bi bi-tags"></i>
                            </div>
                            <h5 class="fw-bold text-dark">ยังไม่มีโปรโมชั่น</h5>
                            <p class="text-muted small mb-0">กรอกข้อมูลที่ฟอร์มด้านบนเพื่อเริ่มสร้างโปรโมชั่นหรือส่วนลดรายการแรก</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($promotions->hasPages())
            <div class="mt-4">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function promotionManager() {
    return {
        ...erpProductPicker(),
        items: [{ product_id: '', sku_code: '', name_th: '', unit_name: '' }],
        emptyProduct() { 
            return { product_id: '', sku_code: '', name_th: '', unit_name: '' }; 
        },
        isEditing: false,
        editingId: null,
        searchQuery: '',
        form: {
            code: '',
            name: '',
            promotion_type: 'discount',
            starts_at: '{{ now()->toDateString() }}',
            ends_at: '',
            is_active: true,
            min_qty: '',
            min_amount: '',
            discount_amount: '',
            discount_percent: '',
            note: ''
        },
        editPromotion(p) {
            this.isEditing = true;
            this.editingId = p.id;
            this.form.code = p.code || '';
            this.form.name = p.name || '';
            this.form.promotion_type = p.promotion_type || 'discount';
            this.form.starts_at = p.starts_at ? p.starts_at.substring(0, 10) : '';
            this.form.ends_at = p.ends_at ? p.ends_at.substring(0, 10) : '';
            this.form.is_active = !!p.is_active;
            this.form.min_qty = (p.min_qty && parseFloat(p.min_qty) > 0) ? parseFloat(p.min_qty) : '';
            this.form.min_amount = (p.min_amount && parseFloat(p.min_amount) > 0) ? parseFloat(p.min_amount) : '';
            this.form.discount_amount = (p.discount_amount && parseFloat(p.discount_amount) > 0) ? parseFloat(p.discount_amount) : '';
            this.form.discount_percent = (p.discount_percent && parseFloat(p.discount_percent) > 0) ? parseFloat(p.discount_percent) : '';
            this.form.note = p.note || '';

            if (p.product) {
                this.items[0] = {
                    product_id: p.product_id,
                    sku_code: p.product.sku_code || '',
                    name_th: p.product.name_th || '',
                    unit_name: p.product.unit_name || ''
                };
            } else {
                this.items[0] = this.emptyProduct();
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        resetForm() {
            this.isEditing = false;
            this.editingId = null;
            this.form = {
                code: '',
                name: '',
                promotion_type: 'discount',
                starts_at: '{{ now()->toDateString() }}',
                ends_at: '',
                is_active: true,
                min_qty: '',
                min_amount: '',
                discount_amount: '',
                discount_percent: '',
                note: ''
            };
            this.items[0] = this.emptyProduct();
        }
    };
}
</script>
@endpush
