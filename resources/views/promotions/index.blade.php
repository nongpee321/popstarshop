@extends('layout')

@section('title', 'จัดการโปรโมชั่นและส่วนลด')

@section('content')
<div class="container-fluid px-4 py-4" x-data="promotionManager()">

    {{-- ── ส่วนหัวหน้า ── --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-percent text-danger me-2"></i>จัดการโปรโมชั่นและส่วนลด
            </h1>
            <p class="text-muted small mb-0">ตั้งราคาส่วนลดให้สินค้าหน้าร้าน POS ได้ง่ายๆ กำหนดเป็นบาท เปอร์เซ็นต์ หรือช่วงเวลาตามต้องการ</p>
        </div>
        <div>
            <a href="{{ route('price-tables.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-table me-1"></i>ตารางราคา
            </a>
            <a href="{{ route('flash-sales.index') }}" class="btn btn-outline-danger btn-sm ms-1">
                <i class="bi bi-lightning-charge-fill me-1"></i>Flash Sale
            </a>
        </div>
    </div>

    {{-- ── บัตรสรุปสถานะโปรโมชั่น ── --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="promo-stat-card">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-tags-fill"></i>
                </div>
                <div>
                    <div class="stat-title">โปรโมชั่นทั้งหมด</div>
                    <div class="stat-number">{{ number_format($stats['total'] ?? 0) }} <span class="stat-unit">รายการ</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="promo-stat-card">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-title">กำลังใช้งานขณะนี้</div>
                    <div class="stat-number text-success">{{ number_format($stats['active'] ?? 0) }} <span class="stat-unit">รายการ</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="promo-stat-card">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="stat-title">รอเริ่มตามกำหนด</div>
                    <div class="stat-number text-warning">{{ number_format($stats['upcoming'] ?? 0) }} <span class="stat-unit">รายการ</span></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="promo-stat-card">
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="bi bi-dash-circle-fill"></i>
                </div>
                <div>
                    <div class="stat-title">หมดอายุแล้ว</div>
                    <div class="stat-number text-muted">{{ number_format($stats['expired'] ?? 0) }} <span class="stat-unit">รายการ</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── การ์ดสร้าง / แก้ไขโปรโมชั่น ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white border-bottom p-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge" :class="isEditing ? 'bg-warning text-dark' : 'bg-danger'" style="font-size: 13px; padding: 6px 14px; border-radius: 8px;">
                    <i class="bi" :class="isEditing ? 'bi-pencil-square me-1' : 'bi-plus-circle me-1'"></i>
                    <span x-text="isEditing ? 'กำลังแก้ไขโปรโมชั่น' : 'สร้างโปรโมชั่นใหม่'"></span>
                </span>
                <span class="text-muted small d-none d-md-inline">| กำหนดสินค้า เงื่อนไขส่วนลด สาขา และช่วงเวลาสำหรับใช้งานที่หน้าร้าน POS</span>
            </div>
            <template x-if="isEditing">
                <button type="button" @click="resetForm()" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>ยกเลิกการแก้ไข
                </button>
            </template>
        </div>

        <div class="card-body p-4 bg-light-subtle" style="background-color: #fbfcfe;">
            <form :action="isEditing ? updateUrl : '{{ route('promotions.store') }}'" method="post">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- ── กล่องที่ 1: สินค้าและราคาลด ── --}}
                <div class="promo-section-card bg-white p-3 p-md-4 rounded-3 border mb-3">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <span class="step-badge bg-danger text-white">1</span>
                        <strong class="text-dark small">เลือกสินค้าและกำหนดส่วนลด</strong>
                    </div>

                    <div class="row g-3 align-items-end">
                        {{-- เลือกสินค้า --}}
                        <div class="col-lg-5 col-md-12">
                            <label class="form-label fw-bold text-dark small d-flex align-items-center justify-content-between mb-1">
                                <span>สินค้าที่ต้องการลดราคา:</span>
                                <span class="text-danger small fw-normal" x-show="scanNotice" x-text="scanNotice" style="font-size: 11px;"></span>
                            </label>
                            
                            {{-- ช่องยิงบาร์โค้ด / สแกน QR --}}
                            <div class="input-group mb-2">
                                <span class="input-group-text bg-danger-subtle text-danger border-danger-subtle px-2">
                                    <i class="bi bi-qr-code-scan"></i>
                                </span>
                                <input type="text" 
                                       x-model="scanBarcode" 
                                       @keydown.enter.prevent="lookupBarcode()" 
                                       class="form-control form-control-sm border-danger-subtle" 
                                       placeholder="🔍 ยิงบาร์โค้ด / QR สินค้าที่นี่แล้วกด Enter..." 
                                       autocomplete="off">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        @click="lookupBarcode()" 
                                        title="ค้นหาจากบาร์โค้ด">
                                    <i class="bi bi-search me-1"></i>ค้นหา
                                </button>
                                <button type="button" 
                                        x-show="cameraOk" 
                                        class="btn btn-sm btn-danger" 
                                        @click="openCamera()" 
                                        title="เปิดกล้องสแกน QR / บาร์โค้ด">
                                    <i class="bi bi-camera-fill"></i>
                                </button>
                            </div>

                            <select name="product_id" x-model="form.product_id" @change="onProductSelect()" class="form-select select-product-box">
                                <option value="">-- ใช้กับทุกสินค้า (ลดทั้งบิล) --</option>
                                @foreach($products as $p)
                                    @php
                                        $bcs = $p->barcodes->pluck('barcode')->implode(',');
                                    @endphp
                                    <option value="{{ $p->id }}" 
                                            data-sku="{{ $p->sku_code }}" 
                                            data-barcodes="{{ $bcs }}" 
                                            data-name="{{ $p->name_th }}" 
                                            data-price="{{ $p->default_price }}"
                                            data-cost="{{ ((float)($p->average_cost ?? 0) > 0) ? $p->average_cost : ($p->last_purchase_cost ?? 0) }}"
                                            data-min-margin="{{ $p->minimum_margin_percent ?? '' }}">
                                        {{ $p->sku_code }} | {{ $p->name_th }} (ปกติ ฿{{ number_format($p->default_price, 2) }} | ทุน ฿{{ number_format((float)(((float)($p->average_cost ?? 0) > 0) ? $p->average_cost : ($p->last_purchase_cost ?? 0)), 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- กำหนดส่วนลด --}}
                        <div class="col-lg-4 col-md-7">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">มูลค่าส่วนลด:</label>
                                <span class="badge bg-light text-secondary border small" style="font-size:11px;" x-show="selectedProductPrice > 0" x-text="'ราคาปกติ: ฿' + selectedProductPrice.toFixed(2)"></span>
                            </div>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle fw-bold" data-bs-toggle="dropdown" x-text="discountType === 'baht' ? 'ลดบาท (฿)' : 'ลดเปอร์เซ็นต์ (%)'">
                                    ลดบาท (฿)
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" @click.prevent="discountType = 'baht'; form.discount_percent = ''; updateCalculatedPrice()">ลดเป็นบาท (฿)</a></li>
                                    <li><a class="dropdown-item" href="#" @click.prevent="discountType = 'percent'; form.discount_amount = ''; updateCalculatedPrice()">ลดเป็นเปอร์เซ็นต์ (%)</a></li>
                                </ul>
                                
                                <template x-if="discountType === 'baht'">
                                    <input type="number" step="0.01" min="0" name="discount_amount" x-model="form.discount_amount" @input="updateCalculatedPrice()" class="form-control fw-bold text-danger fs-5" placeholder="0.00" required>
                                </template>
                                <template x-if="discountType === 'percent'">
                                    <input type="number" step="0.01" min="0" max="100" name="discount_percent" x-model="form.discount_percent" @input="updateCalculatedPrice()" class="form-control fw-bold text-danger fs-5" placeholder="0" required>
                                </template>
                            </div>
                            
                            {{-- ปุ่มคำนวณส่วนลดยอดนิยม & จุดคุ้มทุนอัตโนมัติ --}}
                            <div class="d-flex flex-wrap gap-1 mt-2 align-items-center" x-show="selectedProductPrice > 0">
                                <small class="text-muted me-1" style="font-size:11px;">⚡ แนะนำ:</small>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:11px; border-radius:12px;" @click="setDiscountPreset('percent', 5)">-5%</button>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:11px; border-radius:12px;" @click="setDiscountPreset('percent', 10)">-10%</button>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:11px; border-radius:12px;" @click="setDiscountPreset('percent', 15)">-15%</button>
                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:11px; border-radius:12px;" @click="setDiscountPreset('percent', 20)">-20%</button>
                                <button type="button" class="btn btn-xs btn-outline-warning py-0 px-2 fw-bold" style="font-size:11px; border-radius:12px;" x-show="selectedProductCost > 0" @click="setBreakevenDiscount()" title="ลดราคาสูงสุดจนเท่าทุนพอดี กำไร 0 บาท">
                                    ⚖️ ลดเท่าทุน (Max)
                                </button>
                            </div>
                        </div>

                        {{-- ราคาขายจริงหลังลด --}}
                        <div class="col-lg-3 col-md-5">
                            <label class="form-label fw-bold text-muted small mb-1">ราคาขายสุทธิที่ POS:</label>
                            <div class="p-2 px-3 rounded-2 border bg-light d-flex align-items-center justify-content-between" style="height: 42px;">
                                <span class="small text-muted">ราคาขายจริง:</span>
                                <span class="fs-5 fw-bold" :class="finalPriceClass" x-text="calcPriceText">฿0.00</span>
                            </div>
                        </div>

                        {{-- แถบคำนวณและแจ้งเตือน ต้นทุน / กำไร / ขาดทุน --}}
                        <div class="col-12" x-show="selectedProductPrice > 0" x-transition>
                            <div class="p-3 rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-3 shadow-sm"
                                 :class="marginStatusClass">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-2" x-html="marginStatusIcon"></div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <strong class="fs-6" x-text="marginStatusTitle"></strong>
                                            <span class="badge" :class="marginBadgeClass" x-text="marginBadgeText"></span>
                                        </div>
                                        <div class="small mt-1" x-text="marginStatusDesc"></div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 bg-white p-2 px-3 rounded-2 border shadow-sm ms-auto">
                                    <div class="text-center">
                                        <small class="text-muted d-block" style="font-size: 11px;">ต้นทุนเฉลี่ย</small>
                                        <strong class="text-dark" x-text="'฿' + selectedProductCost.toFixed(2)"></strong>
                                    </div>
                                    <div class="vr"></div>
                                    <div class="text-center">
                                        <small class="text-muted d-block" style="font-size: 11px;">กำไร/ขาดทุนต่อชิ้น</small>
                                        <strong :class="profitPerUnitClass" x-text="profitPerUnitText"></strong>
                                    </div>
                                    <div class="vr"></div>
                                    <div class="text-center">
                                        <small class="text-muted d-block" style="font-size: 11px;">อัตรากำไร (Margin)</small>
                                        <strong :class="profitPerUnitClass" x-text="profitMarginText"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── กล่องที่ 2: เงื่อนไขและระยะเวลา ── --}}
                <div class="promo-section-card bg-white p-3 p-md-4 rounded-3 border mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="step-badge bg-secondary text-white">2</span>
                            <strong class="text-dark small">เงื่อนไขและระยะเวลาที่มีผล</strong>
                        </div>
                        <div class="form-check form-switch m-0 d-flex align-items-center">
                            <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="form-check-input mt-0" id="activeToggle" style="width: 40px; height: 20px; cursor: pointer;">
                            <label class="form-check-label ms-2 small fw-bold" for="activeToggle" :class="form.is_active ? 'text-success' : 'text-muted'" x-text="form.is_active ? 'เปิดใช้งานโปรนี้' : 'ปิดพักไว้ก่อน'"></label>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">
                                <i class="bi bi-calendar-event text-primary me-1"></i>วันที่เริ่มโปรโมชั่น
                            </label>
                            <input type="date" name="starts_at" x-model="form.starts_at" class="form-control">
                            <div class="form-text text-muted" style="font-size: 11px;">(เว้นว่าง = มีผลทันที)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">
                                <i class="bi bi-calendar-x text-danger me-1"></i>วันที่สิ้นสุดโปรโมชั่น
                            </label>
                            <input type="date" name="ends_at" x-model="form.ends_at" class="form-control">
                            <div class="form-text text-muted" style="font-size: 11px;">(เว้นว่าง = ใช้ได้ตลอด ไม่มีวันหมดอายุ)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold mb-1">
                                <i class="bi bi-cart-check text-secondary me-1"></i>ซื้อขั้นต่ำ (จำนวนชิ้น)
                            </label>
                            <input type="number" min="1" step="1" name="min_qty" x-model="form.min_qty" class="form-control" placeholder="1">
                            <div class="form-text text-muted" style="font-size: 11px;">(เว้นว่าง = ซื้อ 1 ชิ้นก็ได้ลด)</div>
                        </div>
                    </div>
                </div>

                {{-- ── กล่องที่ 3: สาขาที่เข้าร่วม (Participating Branches) ── --}}
                <div class="promo-section-card bg-white p-3 p-md-4 rounded-3 border mb-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="step-badge bg-secondary text-white">3</span>
                            <i class="bi bi-shop text-primary fs-5"></i>
                            <div>
                                <strong class="text-dark small d-block">สาขาที่เข้าร่วมโปรโมชั่น</strong>
                                <span class="text-muted" style="font-size: 11px;">เลือกสาขาที่ให้สิทธิ์ส่วนลดหน้าร้าน POS</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span x-show="form.branch_ids.length === 0" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 11px;">
                                <i class="bi bi-globe me-1"></i>ใช้กับทุกสาขา
                            </span>
                            <span x-show="form.branch_ids.length > 0" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;">
                                <i class="bi bi-check2 me-1"></i>เฉพาะ <span x-text="form.branch_ids.length"></span> สาขา
                            </span>
                            <button type="button" @click="selectAllBranches()" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 11px;">
                                <i class="bi bi-check-all me-1"></i>เลือกทุกสาขา
                            </button>
                            <button type="button" @click="clearAllBranches()" class="btn btn-sm btn-outline-secondary py-1 px-2" style="font-size: 11px;">
                                <i class="bi bi-x me-1"></i>ล้างการเลือก
                            </button>
                        </div>
                    </div>

                    <div class="row g-2">
                        @foreach($branches as $b)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <label class="branch-select-card d-flex align-items-center gap-2 p-2 px-3 rounded-2 border m-0"
                                       :class="form.branch_ids.includes({{ $b->id }}) ? 'selected' : ''">
                                    <input type="checkbox" name="branch_ids[]" value="{{ $b->id }}" x-model="form.branch_ids" class="form-check-input mt-0">
                                    <div class="small text-truncate" title="[{{ $b->code }}] {{ $b->name_th }}">
                                        <span class="branch-code-badge font-monospace me-1">[{{ $b->code }}]</span>
                                        <span class="branch-name">{{ $b->name_th }}</span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-2 pt-2 border-top text-muted" style="font-size: 11px;">
                        <span x-show="form.branch_ids.length === 0" class="text-secondary">
                            <i class="bi bi-info-circle me-1"></i>หากไม่ได้เลือกสาขาใดเลย ระบบจะเปิดให้ใช้โปรโมชั่นนี้กับทุกสาขาโดยอัตโนมัติ
                        </span>
                        <span x-show="form.branch_ids.length > 0" class="text-success fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i>เลือกแล้ว <span x-text="form.branch_ids.length"></span> จากทั้งหมด {{ count($branches) }} สาขา (เฉพาะสาขาที่ถูกติ๊กเท่านั้นจะได้รับราคานี้)
                        </span>
                    </div>
                </div>

                {{-- ── กล่องที่ 4: ตั้งเวลารายวัน (Daily Schedule / Happy Hour) ── --}}
                <div class="promo-section-card bg-white p-3 p-md-4 rounded-3 border mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="step-badge bg-secondary text-white">4</span>
                            <i class="bi bi-clock-history text-warning fs-5"></i>
                            <div>
                                <strong class="text-dark small d-block">ช่วงเวลารายวัน / วันในสัปดาห์ (Happy Hour)</strong>
                                <span class="text-muted" style="font-size: 11px;">เช่น ลดเฉพาะเวลา 14:00 - 17:00 หรือลดเฉพาะวันเสาร์-อาทิตย์</span>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0 d-flex align-items-center">
                            <input type="checkbox" class="form-check-input mt-0" id="enableDailySchedule" x-model="hasDailySchedule" style="width: 40px; height: 20px; cursor: pointer;">
                            <label class="form-check-label small fw-bold ms-2 text-secondary" for="enableDailySchedule">
                                <span x-text="hasDailySchedule ? 'เปิดใช้งานช่วงเวลา' : 'ปิด (ลดตลอดทั้งวัน)'"></span>
                            </label>
                        </div>
                    </div>

                    <div x-show="hasDailySchedule" x-transition class="pt-3 border-top mt-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-hourglass-top text-primary me-1"></i>เวลาเริ่มลด (ในแต่ละวัน)
                                </label>
                                <input type="time" name="start_time" x-model="form.start_time" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-dark mb-1">
                                    <i class="bi bi-hourglass-bottom text-danger me-1"></i>เวลาสิ้นสุด (ในแต่ละวัน)
                                </label>
                                <input type="time" name="end_time" x-model="form.end_time" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label small fw-bold text-dark mb-0">
                                        <i class="bi bi-calendar3 me-1"></i>วันที่มีผลในสัปดาห์
                                    </label>
                                    <button type="button" @click="form.days_of_week = ['1','2','3','4','5','6','0']" class="btn btn-link text-decoration-none py-0 px-1" style="font-size: 11px;">
                                        เลือกทุกวัน
                                    </button>
                                </div>
                                <div class="d-flex flex-wrap gap-1 pt-1">
                                    @php
                                        $dayList = [
                                            ['val' => '1', 'name' => 'จันทร์'],
                                            ['val' => '2', 'name' => 'อังคาร'],
                                            ['val' => '3', 'name' => 'พุธ'],
                                            ['val' => '4', 'name' => 'พฤหัส'],
                                            ['val' => '5', 'name' => 'ศุกร์'],
                                            ['val' => '6', 'name' => 'เสาร์'],
                                            ['val' => '0', 'name' => 'อาทิตย์'],
                                        ];
                                    @endphp
                                    @foreach($dayList as $d)
                                        <label class="day-chip btn btn-sm py-1 px-3 rounded-pill" :class="form.days_of_week.includes('{{ $d['val'] }}') ? 'day-chip-active' : 'day-chip-inactive'">
                                            <input type="checkbox" name="days_of_week[]" value="{{ $d['val'] }}" x-model="form.days_of_week" class="d-none">
                                            {{ $d['name'] }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ตัวเลือกขั้นสูง (คลิกเพื่อเปิดดูถ้าต้องการ) --}}
                <div class="mb-3 px-1">
                    <a href="#" @click.prevent="showAdvanced = !showAdvanced" class="text-decoration-none small text-secondary d-inline-flex align-items-center gap-1">
                        <i class="bi" :class="showAdvanced ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                        <span x-text="showAdvanced ? 'ซ่อนการตั้งค่าเพิ่มเติม' : 'แสดงการตั้งค่าเพิ่มเติม (กำหนดรหัส/ชื่อโปรโมชั่นเอง)'"></span>
                    </a>
                </div>

                <div x-show="showAdvanced" x-transition class="mb-3">
                    <div class="p-3 rounded-3 border bg-white">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small text-muted">รหัสโปรโมชั่น (Code)</label>
                                <input name="code" x-model="form.code" class="form-control form-control-sm" placeholder="เว้นว่างเพื่อให้ระบบสร้างอัตโนมัติ">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small text-muted">ชื่อโปรโมชั่น / แคมเปญ</label>
                                <input name="name" x-model="form.name" class="form-control form-control-sm" placeholder="เว้นว่างเพื่อให้ระบบตั้งชื่อตามสินค้าให้อัตโนมัติ">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">ประเภทโปรโมชั่น</label>
                                <select name="promotion_type" x-model="form.promotion_type" class="form-select form-select-sm">
                                    <option value="discount">ส่วนลดสินค้าปกติ (Discount)</option>
                                    <option value="coupon">คูปองส่วนลด (Coupon)</option>
                                    <option value="free_item">ของแถม (Free Item)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small text-muted">หมายเหตุ / เงื่อนไขเพิ่มเติม</label>
                                <input name="note" x-model="form.note" class="form-control form-control-sm" placeholder="เช่น โปรเฉพาะสมาชิก, ไม่ร่วมกับรายการอื่น">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ปุ่มบันทึก --}}
                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-2">
                    <span class="text-muted small">
                        <i class="bi bi-shield-check text-success me-1"></i>ระบบจะคำนวณราคาโปรโมชั่นที่หน้าแคชเชียร์ POS ตามเงื่อนไขและสาขาที่เลือกทันที
                    </span>
                    <button type="submit" class="btn px-4 py-2 fw-bold shadow-sm"
                            :class="isEditing ? 'btn-warning text-dark' : 'btn-danger'">
                        <i class="bi" :class="isEditing ? 'bi-check2-circle me-1' : 'bi-check-lg me-1'"></i>
                        <span x-text="isEditing ? 'บันทึกการแก้ไข' : 'บันทึกและเปิดใช้โปรโมชั่นนี้'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── ตารางแสดงโปรโมชั่นที่กำลังใช้งาน ── --}}
    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white border-bottom p-3 px-4 d-flex align-items-center justify-content-between">
            <h3 class="fs-6 fw-bold mb-0 text-dark">
                <i class="bi bi-tag-fill text-danger me-2"></i>รายการโปรโมชั่นทั้งหมดในระบบ
            </h3>
            <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill">
                ทั้งหมด {{ number_format($promotions->total()) }} รายการ
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted fw-bold">
                        <th class="ps-4">รหัส</th>
                        <th>ชื่อโปรโมชั่น / สินค้า</th>
                        <th>ส่วนลดที่ได้</th>
                        <th>ช่วงเวลาที่ลด</th>
                        <th>เงื่อนไขขั้นต่ำ</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center pe-4" style="width: 120px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($promotions as $promo)
                    @php
                        $todayStr = now()->toDateString();
                        $isExpired = $promo->ends_at && $promo->ends_at->toDateString() < $todayStr;
                        $isUpcoming = $promo->starts_at && $promo->starts_at->toDateString() > $todayStr;
                        $nowRunning = $promo->isRunningAt(now());
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border font-monospace">{{ $promo->code }}</span>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $promo->name }}</strong>
                            @if($promo->product)
                                <small class="text-muted"><i class="bi bi-box me-1"></i>{{ $promo->product->sku_code }} - {{ $promo->product->name_th }}</small>
                            @else
                                <small class="text-primary"><i class="bi bi-globe me-1"></i>ลดทุกสินค้าในร้าน</small>
                            @endif

                            {{-- ข้อมูลสาขาที่เข้าร่วม --}}
                            <div class="mt-1">
                                @if(empty($promo->branch_ids))
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10px;">
                                        <i class="bi bi-check-circle me-1"></i>ทุกสาขา
                                    </span>
                                @else
                                    @php
                                        $promoBranchIds = array_map('intval', (array) $promo->branch_ids);
                                        $selectedBranchNames = $branches->whereIn('id', $promoBranchIds)->pluck('name_th');
                                    @endphp
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size: 10px;" title="{{ $selectedBranchNames->implode(', ') }}">
                                        <i class="bi bi-shop me-1"></i>{{ count($promoBranchIds) }} สาขา
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($promo->discount_percent > 0)
                                <span class="badge bg-danger-subtle text-danger fs-6 fw-bold px-2 py-1">ลด {{ number_format($promo->discount_percent, 0) }}%</span>
                            @elseif($promo->discount_amount > 0)
                                <span class="badge bg-danger-subtle text-danger fs-6 fw-bold px-2 py-1">ลด ฿{{ number_format((float) $promo->discount_amount, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">
                                <div><span class="text-muted">วันที่:</span> {{ $promo->starts_at ? $promo->starts_at->format('d/m/Y') : 'ทันที' }} - {{ $promo->ends_at ? $promo->ends_at->format('d/m/Y') : 'ไม่มีหมดอายุ' }}</div>
                                @if($promo->start_time || $promo->end_time)
                                    <div class="text-warning-emphasis fw-bold">
                                        <i class="bi bi-clock me-1"></i>เวลา: {{ substr($promo->start_time, 0, 5) }} - {{ substr($promo->end_time, 0, 5) }}
                                    </div>
                                @endif
                                @if($promo->days_of_week)
                                    @php
                                        $dayMap = ['0'=>'อา','1'=>'จ','2'=>'อ','3'=>'พ','4'=>'พฤ','5'=>'ศ','6'=>'ส'];
                                        $days = explode(',', (string) $promo->days_of_week);
                                        $daysThai = array_map(fn($d) => $dayMap[$d] ?? $d, $days);
                                    @endphp
                                    <div class="text-muted" style="font-size: 11px;">
                                        <i class="bi bi-calendar-check me-1"></i>เฉพาะวัน: {{ implode(', ', $daysThai) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="small text-muted">
                                {{ $promo->min_qty ? 'ซื้อครบ '.$promo->min_qty.' ชิ้น' : 'ซื้อ 1 ชิ้นก็ได้ลด' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if(!$promo->is_active)
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">ปิดใช้งาน</span>
                            @elseif($isExpired)
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1">หมดอายุ</span>
                            @elseif($isUpcoming)
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1">รอเริ่ม</span>
                            @elseif($nowRunning)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">กำลังลดราคา</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-1">นอกช่วงเวลา</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <button type="button" @click="editPromo({{ json_encode($promo) }})" class="btn btn-sm btn-outline-primary py-1 px-2 rounded" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="post" action="{{ route('promotions.destroy', $promo) }}" onsubmit="return confirm('ต้องการลบโปรโมชั่น {{ $promo->code }} หรือไม่?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded" title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-tag fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div>ยังไม่มีโปรโมชั่นที่สร้างไว้</div>
                            <small>เลือกสินค้าและใส่ราคาส่วนลดด้านบนเพื่อเปิดโปรโมชั่นได้ทันที</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($promotions->hasPages())
            <div class="p-3 border-top bg-white d-flex justify-content-end">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>

    {{-- Overlay กล้องสแกน QR / บาร์โค้ด --}}
    <div class="modal fade show" x-show="cameraOpen" x-cloak style="display: block; background: rgba(0,0,0,0.6);" @click.self="closeCamera()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-dark text-white p-3">
                    <h5 class="modal-title fs-6 fw-bold mb-0">
                        <i class="bi bi-camera-fill text-danger me-2"></i>สแกน QR Code / บาร์โค้ดสินค้า
                    </h5>
                    <button type="button" class="btn-close btn-close-white" @click="closeCamera()"></button>
                </div>
                <div class="modal-body p-0 position-relative bg-black text-center" style="min-height: 280px;">
                    <video x-ref="camVideo" autoplay playsinline muted style="width: 100%; max-height: 360px; object-fit: cover;"></video>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 220px; height: 160px; border: 2px dashed #10b981; border-radius: 12px; pointer-events: none;"></div>
                </div>
                <div class="modal-footer bg-light p-2 justify-content-between">
                    <span class="small text-muted">หันกล้องไปที่ QR Code หรือบาร์โค้ดของสินค้า</span>
                    <button type="button" class="btn btn-secondary btn-sm" @click="closeCamera()">ปิดกล้อง</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('head')
<style>
    .promo-stat-card {
        background: #fff;
        border: 1px solid var(--erp-border, #e2e8f0);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.03);
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
    .stat-title { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; }
    .stat-number { font-size: 20px; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .stat-unit { font-size: 12px; font-weight: 500; color: #94a3b8; }
    .select-product-box { font-size: 14px; font-weight: 600; padding: 10px 12px; }

    /* ปรับแต่ง UI ให้โปร่ง สะอาดตา */
    .step-badge {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: inline-grid;
        place-items: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .promo-section-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .promo-section-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 3px 10px rgba(15, 23, 42, 0.03);
    }
    .branch-select-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .branch-select-card:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .branch-select-card.selected {
        background: #eff6ff !important;
        border-color: #3b82f6 !important;
        color: #1d4ed8 !important;
    }
    .branch-code-badge {
        font-size: 10px;
        padding: 1px 5px;
        background: rgba(100, 116, 139, 0.1);
        border-radius: 4px;
        color: #64748b;
    }
    .branch-select-card.selected .branch-code-badge {
        background: rgba(37, 99, 235, 0.15);
        color: #1d4ed8;
    }
    .branch-select-card.selected .branch-name {
        font-weight: 600;
    }
    .day-chip {
        cursor: pointer;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.15s;
    }
    .day-chip-active {
        background-color: #dc2626 !important;
        color: #fff !important;
        border-color: #dc2626 !important;
    }
    .day-chip-inactive {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .day-chip-inactive:hover {
        background-color: #f1f5f9;
        border-color: #94a3b8;
    }
</style>
<script>
    function promotionManager() {
        return {
            isEditing: false,
            showAdvanced: false,
            hasDailySchedule: false,
            discountType: 'baht',
            updateUrl: '',
            selectedProductPrice: 0,
            calcPriceText: '฿0.00',
            scanBarcode: '',
            scanNotice: '',
            cameraOk: false,
            cameraOpen: false,
            camStream: null,
            camTimer: null,
            form: {
                id: null,
                code: '',
                name: '',
                promotion_type: 'discount',
                starts_at: '{{ now()->toDateString() }}',
                ends_at: '',
                start_time: '',
                end_time: '',
                days_of_week: [],
                branch_ids: [],
                min_qty: '',
                discount_amount: '',
                discount_percent: '',
                product_id: '',
                note: '',
                is_active: true
            },
            allBranchIds: @js($branches->pluck('id')),
            selectAllBranches() {
                this.form.branch_ids = [...this.allBranchIds];
            },
            clearAllBranches() {
                this.form.branch_ids = [];
            },
            init() {
                this.cameraOk = !!(window.BarcodeDetector && navigator.mediaDevices?.getUserMedia);
            },
            lookupBarcode() {
                const query = (this.scanBarcode || '').trim().toLowerCase();
                if (!query) return;

                const sel = document.querySelector('select[name="product_id"]');
                if (!sel) return;

                let found = null;
                for (let i = 0; i < sel.options.length; i++) {
                    const opt = sel.options[i];
                    if (!opt.value) continue;
                    const sku = (opt.dataset.sku || '').toLowerCase();
                    const barcodes = (opt.dataset.barcodes || '').toLowerCase().split(',').map(b => b.trim());
                    const name = (opt.dataset.name || '').toLowerCase();

                    if (sku === query || barcodes.includes(query) || (query.length >= 4 && (sku.includes(query) || barcodes.some(b => b.includes(query))))) {
                        found = opt;
                        break;
                    }
                }

                if (found) {
                    this.form.product_id = found.value;
                    sel.value = found.value;
                    this.scanNotice = `✓ เลือก: ${found.dataset.name}`;
                    this.onProductSelect();
                    this.scanBarcode = '';
                    setTimeout(() => { this.scanNotice = ''; }, 4000);
                } else {
                    this.scanNotice = `❌ ไม่พบสินค้าที่มีบาร์โค้ด / SKU: "${query}"`;
                    setTimeout(() => { this.scanNotice = ''; }, 4000);
                }
            },
            async openCamera() {
                this.cameraOpen = true;
                try {
                    this.camStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'environment' }
                    });
                    this.$refs.camVideo.srcObject = this.camStream;
                    const detector = new BarcodeDetector({
                        formats: ['qr_code', 'ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a', 'upc_e']
                    });
                    this.camTimer = setInterval(async () => {
                        try {
                            const codes = await detector.detect(this.$refs.camVideo);
                            if (codes.length) {
                                this.scanBarcode = codes[0].rawValue;
                                this.closeCamera();
                                this.lookupBarcode();
                            }
                        } catch (err) {}
                    }, 250);
                } catch (e) {
                    this.closeCamera();
                    alert('ไม่สามารถเปิดกล้องได้: ' + e.message + ' (ต้องเปิดผ่าน HTTPS หรือ localhost)');
                }
            },
            closeCamera() {
                if (this.camTimer) clearInterval(this.camTimer);
                this.camStream?.getTracks().forEach(t => t.stop());
                this.camStream = null;
                this.cameraOpen = false;
            },
            selectedProductCost: 0,
            selectedProductMinMargin: 0,
            profitPerUnit: 0,
            profitMarginPct: 0,
            profitPerUnitText: '+฿0.00',
            profitMarginText: '0.0%',
            profitPerUnitClass: 'text-success',
            finalPriceClass: 'text-success',
            marginStatusTitle: '',
            marginStatusDesc: '',
            marginStatusClass: 'bg-light text-muted',
            marginBadgeClass: 'bg-secondary',
            marginBadgeText: '',
            marginStatusIcon: '<i class="bi bi-info-circle"></i>',

            setDiscountPreset(type, val) {
                this.discountType = type;
                if (type === 'percent') {
                    this.form.discount_percent = val;
                    this.form.discount_amount = '';
                } else {
                    this.form.discount_amount = val;
                    this.form.discount_percent = '';
                }
                this.updateCalculatedPrice();
            },
            setBreakevenDiscount() {
                if (this.selectedProductPrice > 0 && this.selectedProductCost > 0) {
                    const maxDiff = Math.max(0, this.selectedProductPrice - this.selectedProductCost);
                    this.discountType = 'baht';
                    this.form.discount_amount = maxDiff.toFixed(2);
                    this.form.discount_percent = '';
                    this.updateCalculatedPrice();
                }
            },
            onProductSelect() {
                const sel = document.querySelector('select[name="product_id"]');
                const opt = sel ? sel.options[sel.selectedIndex] : null;
                if (opt && opt.dataset.price) {
                    this.selectedProductPrice = parseFloat(opt.dataset.price) || 0;
                    this.selectedProductCost = parseFloat(opt.dataset.cost) || 0;
                    this.selectedProductMinMargin = parseFloat(opt.dataset.minMargin) || 0;
                    
                    // ถ้ายังไม่ได้กรอกส่วนลดเลย และเพิ่งเลือกสินค้า ให้คำนวณและเสนอส่วนลดเริ่มต้น 10% ให้ทันทีอัตโนมัติ
                    if (!this.form.discount_amount && !this.form.discount_percent && !this.isEditing) {
                        this.discountType = 'percent';
                        this.form.discount_percent = 10;
                    }
                } else {
                    this.selectedProductPrice = 0;
                    this.selectedProductCost = 0;
                    this.selectedProductMinMargin = 0;
                }
                this.updateCalculatedPrice();
            },
            updateCalculatedPrice() {
                if (this.selectedProductPrice <= 0) {
                    this.calcPriceText = 'ตามราคาสินค้า';
                    return;
                }
                let base = this.selectedProductPrice;
                let finalPrice = base;
                if (this.discountType === 'baht' && this.form.discount_amount) {
                    finalPrice = Math.max(0, base - parseFloat(this.form.discount_amount));
                } else if (this.discountType === 'percent' && this.form.discount_percent) {
                    finalPrice = Math.max(0, base * (1 - (parseFloat(this.form.discount_percent) / 100)));
                }
                this.calcPriceText = '฿' + finalPrice.toFixed(2);

                // คำนวณกำไร / ต้นทุน / ขาดทุน
                const cost = this.selectedProductCost;
                this.profitPerUnit = finalPrice - cost;
                this.profitMarginPct = finalPrice > 0 ? ((finalPrice - cost) / finalPrice) * 100 : 0;

                const maxDiscountBaht = Math.max(0, base - cost);
                const maxDiscountPct = base > 0 ? ((base - cost) / base) * 100 : 0;

                // 1. ถ้าลดจนราคาขายเหลือ 0 บาท หรือติดลบ (ลด 100% หรือลดแจกฟรี)
                if (finalPrice <= 0) {
                    this.marginStatusTitle = '🚨 แจกฟรี 100% / ราคาขาย ฿0.00 (ระวังขาดทุน!)';
                    this.marginStatusDesc = cost > 0
                        ? `คุณตั้งราคาขายเป็น 0 บาท จะขาดทุนเต็มต้นทุน -฿${cost.toFixed(2)} / ชิ้น (ลูกค้าได้ฟรี ร้านไม่ได้เงิน)`
                        : `คุณตั้งราคาขายเป็น 0 บาท สินค้านี้จะไม่มีรายรับเลย (ลูกค้าได้ฟรี ร้านไม่ได้เงิน)`;
                    this.marginStatusClass = 'bg-danger text-white border-danger shadow-sm';
                    this.marginBadgeClass = 'bg-white text-danger fw-bold';
                    this.marginBadgeText = 'ลด 100% แจกฟรี!';
                    this.marginStatusIcon = '<i class="bi bi-exclamation-octagon-fill text-white fs-2"></i>';
                    this.profitPerUnitClass = 'text-danger fw-bold';
                    this.profitPerUnitText = cost > 0 ? ('-฿' + cost.toFixed(2)) : '฿0.00 (ฟรี)';
                    this.profitMarginText = '0.0% (ไม่ได้เงิน)';
                    this.finalPriceClass = 'text-danger fw-bold';
                    return;
                }

                // 2. ถ้าสินค้านี้ยังไม่ได้ระบุต้นทุนในระบบ (ต้นทุน = 0)
                if (cost <= 0) {
                    const discountAmt = base - finalPrice;
                    const discountPct = base > 0 ? (discountAmt / base) * 100 : 0;
                    this.marginStatusTitle = `⚠️ สินค้านี้ยังไม่มีต้นทุนในระบบ (ลด ฿${discountAmt.toFixed(2)} หรือ ${discountPct.toFixed(0)}%)`;
                    this.marginStatusDesc = `ราคาขายหลังลดเหลือ ฿${finalPrice.toFixed(2)} (ยังไม่สามารถคำนวณจุดคุ้มทุนได้ แนะนำบันทึกต้นทุนในเมนูสินค้า)`;
                    this.marginStatusClass = 'bg-warning-subtle border-warning text-dark';
                    this.marginBadgeClass = 'bg-warning text-dark fw-bold';
                    this.marginBadgeText = 'ยังไม่ทราบต้นทุน';
                    this.marginStatusIcon = '<i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>';
                    this.profitPerUnitClass = 'text-warning fw-bold';
                    this.profitPerUnitText = 'ไม่ระบุทุน';
                    this.profitMarginText = 'ไม่ระบุ';
                    this.finalPriceClass = 'text-dark fw-bold';
                    return;
                }

                // 3. ขายต่ำกว่าทุน (ขาดทุน)
                if (finalPrice < cost) {
                    const loss = cost - finalPrice;
                    this.marginStatusTitle = `🚨 แจ้งเตือน: ขายขาดทุน -฿${loss.toFixed(2)} / ชิ้น!`;
                    this.marginStatusDesc = `ต้นทุน ฿${cost.toFixed(2)} แต่ขาย ฿${finalPrice.toFixed(2)} (ลดได้สูงสุดไม่เกิน ฿${maxDiscountBaht.toFixed(2)} หรือ ${maxDiscountPct.toFixed(1)}% ถึงจะเท่าทุนพอดี)`;
                    this.marginStatusClass = 'bg-danger text-white border-danger shadow-sm';
                    this.marginBadgeClass = 'bg-white text-danger fw-bold';
                    this.marginBadgeText = 'ขาดทุน!';
                    this.marginStatusIcon = '<i class="bi bi-x-octagon-fill text-white fs-2"></i>';
                    this.profitPerUnitClass = 'text-danger fw-bold';
                    this.profitPerUnitText = `-฿${loss.toFixed(2)}`;
                    this.profitMarginText = `${this.profitMarginPct.toFixed(1)}% (ติดลบ)`;
                    this.finalPriceClass = 'text-danger fw-bold';
                    return;
                }

                // 4. ขายเท่าทุนพอดี (Break-even)
                if (finalPrice === cost) {
                    this.marginStatusTitle = '⚖️ ขายเท่าทุนพอดี (Break-even ฿0.00)';
                    this.marginStatusDesc = `ราคาขายเท่ากับต้นทุน ฿${cost.toFixed(2)} พอดี กำไร 0 บาท (ลดได้สูงสุดแล้ว ห้ามลดเพิ่มอีกเพราะจะเริ่มขาดทุน)`;
                    this.marginStatusClass = 'bg-warning-subtle border-warning text-dark';
                    this.marginBadgeClass = 'bg-warning text-dark fw-bold';
                    this.marginBadgeText = 'เท่าทุน 0 บาท';
                    this.marginStatusIcon = '<i class="bi bi-dash-circle-fill text-warning fs-2"></i>';
                    this.profitPerUnitClass = 'text-warning fw-bold';
                    this.profitPerUnitText = '฿0.00 (เท่าทุน)';
                    this.profitMarginText = '0.0%';
                    this.finalPriceClass = 'text-dark fw-bold';
                    return;
                }

                // 5. มีกำไร แต่ต่ำกว่า Margin ขั้นต่ำ
                if (this.selectedProductMinMargin > 0 && this.profitMarginPct < this.selectedProductMinMargin) {
                    this.marginStatusTitle = `⚡ กำไร ฿${this.profitPerUnit.toFixed(2)} (${this.profitMarginPct.toFixed(1)}%) ต่ำกว่าเกณฑ์ขั้นต่ำ ${this.selectedProductMinMargin}%`;
                    this.marginStatusDesc = `ยังมีกำไร แต่ต่ำกว่าเป้าหมายกำไรขั้นต่ำที่ตั้งไว้ในแฟ้มสินค้า`;
                    this.marginStatusClass = 'bg-warning-subtle border-warning text-dark';
                    this.marginBadgeClass = 'bg-warning text-dark fw-bold';
                    this.marginBadgeText = 'กำไรต่ำกว่าเกณฑ์';
                    this.marginStatusIcon = '<i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>';
                    this.profitPerUnitClass = 'text-warning fw-bold';
                    this.profitPerUnitText = `+฿${this.profitPerUnit.toFixed(2)}`;
                    this.profitMarginText = `${this.profitMarginPct.toFixed(1)}%`;
                    this.finalPriceClass = 'text-success fw-bold';
                    return;
                }

                // 6. มีกำไรปลอดภัย
                this.marginStatusTitle = `✅ มีกำไร: +฿${this.profitPerUnit.toFixed(2)} / ชิ้น (Margin ${this.profitMarginPct.toFixed(1)}%)`;
                this.marginStatusDesc = `ปลอดภัย: ต้นทุน ฿${cost.toFixed(2)} (ลดได้สูงสุดอีกไม่เกิน ฿${maxDiscountBaht.toFixed(2)} หรือ ${maxDiscountPct.toFixed(1)}% ก่อนที่จะเท่าทุน)`;
                this.marginStatusClass = 'bg-success-subtle border-success text-success';
                this.marginBadgeClass = 'bg-success text-white fw-bold';
                this.marginBadgeText = 'มีกำไรปลอดภัย';
                this.marginStatusIcon = '<i class="bi bi-check-circle-fill text-success fs-2"></i>';
                this.profitPerUnitClass = 'text-success fw-bold';
                this.profitPerUnitText = `+฿${this.profitPerUnit.toFixed(2)}`;
                this.profitMarginText = `+${this.profitMarginPct.toFixed(1)}%`;
                this.finalPriceClass = 'text-success fw-bold';
            },
            editPromo(promo) {
                this.isEditing = true;
                this.updateUrl = `/promotions/${promo.id}`;
                
                let days = [];
                if (promo.days_of_week) {
                    days = Array.isArray(promo.days_of_week) ? promo.days_of_week : promo.days_of_week.split(',');
                }

                let branchIds = [];
                if (promo.branch_ids) {
                    branchIds = Array.isArray(promo.branch_ids) 
                        ? promo.branch_ids.map(Number) 
                        : (typeof promo.branch_ids === 'string' ? JSON.parse(promo.branch_ids).map(Number) : []);
                }

                this.hasDailySchedule = Boolean(promo.start_time || promo.end_time || days.length > 0);

                this.form = {
                    id: promo.id,
                    code: promo.code,
                    name: promo.name,
                    promotion_type: promo.promotion_type,
                    starts_at: promo.starts_at ? promo.starts_at.slice(0, 10) : '',
                    ends_at: promo.ends_at ? promo.ends_at.slice(0, 10) : '',
                    start_time: promo.start_time ? promo.start_time.slice(0, 5) : '',
                    end_time: promo.end_time ? promo.end_time.slice(0, 5) : '',
                    days_of_week: days,
                    branch_ids: branchIds,
                    min_qty: promo.min_qty || '',
                    discount_amount: promo.discount_amount || '',
                    discount_percent: promo.discount_percent || '',
                    product_id: promo.product_id || '',
                    note: promo.note || '',
                    is_active: Boolean(promo.is_active)
                };

                if (promo.discount_percent > 0) {
                    this.discountType = 'percent';
                } else {
                    this.discountType = 'baht';
                }
                this.onProductSelect();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            resetForm() {
                this.isEditing = false;
                this.updateUrl = '';
                this.discountType = 'baht';
                this.hasDailySchedule = false;
                this.form = {
                    id: null,
                    code: '',
                    name: '',
                    promotion_type: 'discount',
                    starts_at: '{{ now()->toDateString() }}',
                    ends_at: '',
                    start_time: '',
                    end_time: '',
                    days_of_week: [],
                    branch_ids: [],
                    min_qty: '',
                    discount_amount: '',
                    discount_percent: '',
                    product_id: '',
                    note: '',
                    is_active: true
                };
                this.calcPriceText = '฿0.00';
            }
        };
    }
</script>
@endpush
@endsection
