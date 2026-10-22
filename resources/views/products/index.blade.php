@extends('layout')

@section('title', 'สินค้า - PopCentral')
@section('page-title', 'สินค้า / บริการ')
@section('page-subtitle', 'ระบบจัดการสินค้าและสต็อก')

@section('content')
    <div x-data="productPage()" x-cloak class="product-page">
        <div class="product-toolbar mb-3">
            <form method="get" class="product-filter-bar" x-ref="filterForm">
                <div class="product-filter-field search-field">
                    <label>ค้นหาสินค้า</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="รหัสหรือชื่อสินค้า" autocomplete="off" @input.debounce.450ms="$refs.filterForm.requestSubmit()">
                        <input type="hidden" name="status" value="{{ $status }}">
                    </div>
                </div>
                <div class="product-filter-field">
                    <label>ประเภทสินค้า</label>
                    <select name="category_id" class="form-select" @change="$refs.filterForm.requestSubmit()">
                        <option value="">ทุกหมวดสินค้า</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected($categoryId === $category->id)>{{ $category->name_th }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="product-filter-field">
                    <label>คลังสินค้า</label>
                    <select name="warehouse_id" class="form-select" @change="$refs.filterForm.requestSubmit()">
                        <option value="">ทุกคลังสินค้ารวมกัน</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected($warehouseId === $warehouse->id)>{{ $warehouse->name ?: $warehouse->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="product-filter-field">
                    <label>รูปแบบการขาย</label>
                    <select name="product_type" class="form-select" @change="$refs.filterForm.requestSubmit()">
                        <option value="">สินค้าทั้งหมด</option>
                        <option value="scale" @selected($productType === 'scale')>สินค้าชั่งน้ำหนัก</option>
                    </select>
                </div>
                @if($q !== '' || $categoryId || $warehouseId || $productType)
                <div class="product-filter-field" style="min-width:auto; flex: 0 0 auto;">
                    <label>&nbsp;</label>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center p-0" style="width: 32px; height: 32px; border-radius: 7px;" title="ล้างตัวกรอง">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
                @endif
            </form>
            <div class="product-actions">
                <button type="button" class="btn btn-outline-secondary px-3" @click="helpModalOpen = true" title="วิธีใช้งานหน้านี้">
                    <i class="bi bi-question-circle me-1"></i> วิธีใช้งาน
                </button>
                <a href="{{ route('product-units.index') }}" class="btn btn-light border px-3">
                    <i class="bi bi-rulers me-1"></i> จัดการหน่วยนับ
                </a>
                <button type="button" class="btn btn-danger px-3" @click="modalOpen = true">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้า
                </button>
            </div>
        </div>

        <div class="row g-3 mb-3">
            @php
                $badges = [
                    'all' => '<span class="badge bg-primary-subtle text-primary rounded-pill px-3">รายการ</span>',
                    'active' => '<span class="badge bg-success-subtle text-success rounded-pill px-3">ปกติ 100%</span>',
                    'inactive' => '<span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">ไม่มี</span>'
                ];
            @endphp
            @foreach([['key' => 'all', 'label' => 'สินค้าทั้งหมด', 'tone' => 'primary', 'icon' => 'bi-box-seam'], ['key' => 'active', 'label' => 'กำลังใช้งาน', 'tone' => 'success', 'icon' => 'bi-box'], ['key' => 'inactive', 'label' => 'ไม่ใช้งาน / พักไว้', 'tone' => 'secondary', 'icon' => 'bi-box-seam']] as $summary)
                <div class="col-md-4">
                    <a href="{{ route('products.index', array_filter(['q' => $q, 'category_id' => $categoryId, 'product_type' => $productType, 'status' => $summary['key'] === 'all' ? null : $summary['key']])) }}" class="status-summary {{ $status === $summary['key'] ? 'is-selected' : '' }}">
                        <span class="status-summary-icon text-{{ $summary['tone'] }}"><i class="bi {{ $summary['icon'] }}"></i></span>
                        <span><small>{{ $summary['label'] }}</small><strong class="{{ $summary['tone'] === 'success' ? 'text-success' : '' }}">{{ number_format($counts[$summary['key']]) }}</strong></span>
                        <div class="ms-auto">{!! $badges[$summary['key']] !!}</div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="scale-plu-summary mb-3">
            <div class="scale-plu-icon"><i class="bi bi-upc-scan"></i></div>
            <div><span>PLU สินค้าชั่งล่าสุด</span><strong>{{ $maxScalePlu }}</strong></div>
            <i class="bi bi-arrow-right text-muted"></i>
            <div><span>เลขถัดไปที่ระบบจะใช้</span><strong class="text-success">{{ $nextScalePlu }}</strong></div>
            <a href="{{ route('scale-prices.index') }}" class="btn btn-outline-primary ms-auto"><i class="bi bi-speedometer2 me-1"></i>ทะเบียนสินค้าเครื่องชั่ง</a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="inventory-summary">
                    <span class="inventory-summary-icon text-primary"><i class="bi bi-funnel"></i></span>
                    <span><small>สินค้าตามตัวกรอง</small><strong>{{ number_format($stockSummary['products']) }}</strong></span>
                    <div class="ms-auto"><span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">ตรงตามเกณฑ์</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="inventory-summary">
                    <span class="inventory-summary-icon text-success"><i class="bi bi-stack"></i></span>
                    <span><small>คงเหลือรวมทุกคลัง</small><strong>{{ rtrim(rtrim(number_format($stockSummary['on_hand'], 4), '0'), '.') }}</strong></span>
                    <div class="ms-auto"><span class="badge bg-success-subtle text-success rounded-pill px-3">ชิ้น / หน่วย</span></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="inventory-summary">
                    <span class="inventory-summary-icon text-warning"><i class="bi bi-exclamation-triangle"></i></span>
                    <span><small>ถึงจุดสั่งซื้อ (ต่ำกว่าเกณฑ์)</small><strong>{{ number_format($stockSummary['low_stock']) }}</strong></span>
                    <div class="ms-auto">
                        @if($stockSummary['low_stock'] > 0)
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">ต้องสั่งซื้อ</span>
                        @else
                            <span class="badge bg-success-subtle text-success rounded-pill px-3">สต็อกเพียงพอ</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-2 px-1">
            <div>
                <i class="bi bi-list-ul text-danger me-1"></i>
                <span class="fw-bold" style="font-size:13px; color:#374151;">ตารางแสดงรายการสินค้า</span>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-2">{{ number_format($stockSummary['products']) }} รายการ</span>
            </div>
            <div class="text-muted" style="font-size:11px;">เรียงลำดับ: รหัสสินค้าล่าสุด</div>
        </div>

        <div class="content-card product-table-card">
            <div class="table-responsive">
                <table class="table align-middle product-table mb-0">
                    <thead>
                        <tr style="background:#b91c1c; color:#fff;">
                            <th style="background:inherit; color:inherit;">รหัส (SKU)</th>
                            <th style="background:inherit; color:inherit;">ชื่อสินค้า</th>
                            <th style="background:inherit; color:inherit;">หมวดหมู่</th>
                            <th style="background:inherit; color:inherit;">ยี่ห้อ</th>
                            <th style="background:inherit; color:inherit;">หน่วยหลัก</th>
                            <th style="background:inherit; color:inherit;" class="text-end">คงเหลือ</th>
                            <th style="background:inherit; color:inherit;" class="text-end">ราคาเริ่มต้น</th>
                            <th style="background:inherit; color:inherit;">สถานะ</th>
                            <th style="background:inherit; color:inherit;" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="fw-semibold">{{ $product->sku_code }}</td>
                                <td>{{ $product->name_th }}</td>
                                <td>{{ $product->category?->name_th ?? '-' }}</td>
                                <td>{{ $product->brand?->name_th ?? '-' }}</td>
                                <td>{{ $product->baseUnit?->displayLabel() ?? '-' }}</td>
                                <td class="text-end fw-semibold">{{ rtrim(rtrim(number_format((float) $product->stockBalances->sum('on_hand_qty'), 4), '0'), '.') }}</td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-sm btn-link p-0 text-decoration-none fw-bold text-dark font-monospace" 
                                            @click="openPriceModal({{ $product->id }}, '{{ addslashes($product->name_th) }}', '{{ $product->sku_code }}', {{ (float)($product->default_price ?? 0) }})"
                                            title="คลิกเพื่อเปลี่ยนราคา (ทุกสาขา / เฉพาะสาขา)">
                                        <span id="price-val-{{ $product->id }}">฿{{ number_format($product->default_price ?? 0, 2) }}</span>
                                        <i class="bi bi-pencil-square text-danger ms-1" style="font-size: 11px;"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $product->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-3">
                                        {{ $product->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark bg-warning-subtle border-warning-subtle px-2 py-1 rounded fw-bold" style="font-size:10px;"
                                            @click="openPriceModal({{ $product->id }}, '{{ addslashes($product->name_th) }}', '{{ $product->sku_code }}', {{ (float)($product->default_price ?? 0) }})"
                                            title="เปลี่ยนราคาขาย (ทุกสาขา / เฉพาะสาขา)">
                                            <i class="bi bi-tag-fill text-danger me-1"></i> ปรับราคา
                                        </button>
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-danger px-2 py-1 rounded border-0" style="font-size:10px;" @click.prevent="openProduct('{{ route('products.show', $product) }}?popup=1')">
                                            <i class="bi bi-pencil-square me-1"></i> แก้ไข
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success bg-success-subtle border-success-subtle px-2 py-1 rounded" style="font-size:10px;"
                                            @click="openStockIn({{ $product->id }}, '{{ addslashes($product->name_th) }}', '{{ $product->sku_code }}')">
                                            <i class="bi bi-box-arrow-in-down"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger bg-danger-subtle border-danger-subtle px-2 py-1 rounded" style="font-size:10px;"
                                            @click="openDelete({{ $product->id }}, '{{ addslashes($product->name_th) }}', '{{ $product->sku_code }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-5 text-center text-muted">ไม่พบสินค้า</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="product-pagination">{{ $products->links() }}</div>
        </div>

        {{-- Modal ปรับราคาขาย (เปลี่ยนทุกสาขา / เฉพาะสาขา) --}}
        <div class="booking-modal-backdrop" x-show="priceModalOpen" x-cloak x-transition.opacity @keydown.escape.window="priceModalOpen = false" style="z-index: 1080;">
            <div class="booking-modal" style="width: min(560px, 95vw);" @click.outside="priceModalOpen = false" x-transition>
                <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center bg-danger text-white rounded-top-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-tags-fill fs-5"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-white" style="font-size: 16px;">ปรับราคาขายสินค้า</h5>
                            <span class="small opacity-75" x-text="targetProductSku + ' · ' + targetProductName"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" @click="priceModalOpen = false" aria-label="Close"></button>
                </div>

                <form @submit.prevent="savePrice()">
                    <div class="modal-body p-4">
                        {{-- ข้อความแจ้งเตือนผลลัพธ์ --}}
                        <div x-show="priceModalNotice" x-transition class="alert py-2 px-3 small mb-3 rounded-3" :class="priceModalSuccess ? 'alert-success' : 'alert-danger'" x-text="priceModalNotice"></div>

                        {{-- เลือกขอบเขตสาขา --}}
                        <label class="form-label small fw-bold text-dark mb-2">
                            <i class="bi bi-shop me-1 text-danger"></i>ต้องการเปลี่ยนราคาที่สาขาใด:
                        </label>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="border p-2 rounded-3 w-100 d-flex align-items-center gap-2 cursor-pointer"
                                       :class="priceScope === 'all' ? 'bg-primary-subtle border-primary text-primary fw-bold' : 'bg-light text-muted'"
                                       style="cursor: pointer;">
                                    <input type="radio" name="scope" value="all" x-model="priceScope" class="form-check-input mt-0" @change="onScopeChange()">
                                    <span>ทุกสาขา (ราคามาตรฐาน)</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="border p-2 rounded-3 w-100 d-flex align-items-center gap-2 cursor-pointer"
                                       :class="priceScope === 'branch' ? 'bg-danger-subtle border-danger text-danger fw-bold' : 'bg-light text-muted'"
                                       style="cursor: pointer;">
                                    <input type="radio" name="scope" value="branch" x-model="priceScope" class="form-check-input mt-0" @change="onScopeChange()">
                                    <span>เฉพาะสาขานี้</span>
                                </label>
                            </div>
                        </div>

                        {{-- กรณีเลือกเฉพาะสาขา --}}
                        <div class="mb-3 p-3 bg-light rounded-3 border" x-show="priceScope === 'branch'" x-transition>
                            <label class="form-label small fw-bold text-dark mb-1">เลือกสาขาที่ต้องการปรับราคา:</label>
                            <select x-model="selectedBranchId" @change="onBranchChange()" class="form-select form-select-sm mb-2">
                                <template x-for="b in branchList" :key="b.id">
                                    <option :value="b.id" x-text="'[' + b.code + '] ' + b.name_th + ' (ปัจจุบัน ฿' + Number(b.price).toFixed(2) + ')'"></option>
                                </template>
                            </select>
                            <div class="small text-muted d-flex align-items-center justify-content-between">
                                <span>ราคาขายปัจจุบันของสาขานี้:</span>
                                <strong class="text-dark" x-text="'฿' + Number(currentBranchPrice).toFixed(2)"></strong>
                            </div>
                        </div>

                        {{-- ช่องกรอกราคาขายใหม่ --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">
                                ราคาขายใหม่ (บาท) <span class="text-danger">*</span>:
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">฿</span>
                                <input type="number" step="0.01" min="0" x-model="newPriceInput" required class="form-control form-control-lg fw-bold text-danger text-end" placeholder="0.00" autofocus>
                                <span class="input-group-text bg-light small text-muted">บาท / หน่วย</span>
                            </div>
                        </div>

                        {{-- สรุปเปรียบเทียบ --}}
                        <div class="p-3 rounded-3 border bg-light small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">ขอบเขตที่จะมีผล:</span>
                                <strong :class="priceScope === 'all' ? 'text-primary' : 'text-danger'"
                                        x-text="priceScope === 'all' ? 'มีผลกับทุกสาขาในระบบ' : ('เฉพาะสาขา ' + getSelectedBranchName())"></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">ราคาขายเดิม:</span>
                                <span class="text-muted text-decoration-line-through" x-text="'฿' + Number(priceScope === 'all' ? targetProductPrice : currentBranchPrice).toFixed(2)"></span>
                            </div>
                            <div class="d-flex justify-content-between pt-1 border-top mt-1">
                                <span class="fw-bold text-dark">ราคาขายใหม่:</span>
                                <strong class="fs-6 text-danger" x-text="newPriceInput ? ('฿' + Number(newPriceInput).toFixed(2)) : '฿0.00'"></strong>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" @click="priceModalOpen = false">ยกเลิก</button>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm" :disabled="priceSaving">
                            <i class="bi bi-check2-circle me-1"></i>
                            <span x-text="priceSaving ? 'กำลังบันทึก...' : 'บันทึกราคาขาย'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="product-popup-backdrop" x-show="productPopupOpen" x-transition.opacity @keydown.escape.window="closeProduct()">
            <div class="product-popup-window" x-transition @click.outside="closeProduct()">
                <div class="product-popup-titlebar">
                    <div><i class="bi bi-box-seam me-2"></i>แฟ้มสินค้า</div>
                    <button type="button" @click="closeProduct()" aria-label="ปิด"><i class="bi bi-x-lg"></i></button>
                </div>
                <iframe x-show="productUrl" :src="productUrl" title="รายละเอียดสินค้า"></iframe>
            </div>
        </div>

        {{-- Modal ช่วยเหลือ: วิธีใช้งานหน้ารายการสินค้า --}}
        <div class="booking-modal-backdrop" x-show="helpModalOpen" x-cloak x-transition.opacity @keydown.escape.window="helpModalOpen = false">
            <div class="booking-modal" style="width: min(840px, 100%);" @click.outside="helpModalOpen = false" x-transition>
                <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-camera me-1"></i>คู่มือการใช้งาน</span>
                        <h4 class="h5 fw-bold mb-0">วิธีใช้งาน: ทะเบียนสินค้าและจัดการสต็อก</h4>
                    </div>
                    <button type="button" class="btn btn-light btn-sm rounded-circle" @click="helpModalOpen = false" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body p-4" style="max-height: calc(85vh - 120px); overflow-y: auto;">
                    <div class="mb-3 rounded-3 overflow-hidden border shadow-sm">
                        <img src="{{ asset('images/manual/products.png') }}" alt="ตัวอย่างหน้าจอสินค้า" class="w-100 d-block" style="object-fit:cover">
                    </div>
                    <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-list-check me-2"></i>ขั้นตอนการทำงานสำคัญ</h5>
                    <div class="d-grid gap-2 mb-3">
                        <div class="p-3 rounded-2 border bg-light d-flex gap-3 align-items-start">
                            <span class="badge bg-primary fs-6 rounded-pill px-3 py-2">1</span>
                            <div>
                                <strong class="d-block text-dark">ตรวจสอบยอดสต็อก & เลือกดูรายสาขา</strong>
                                <span class="small text-muted">ดูยอดคงเหลือรวมจากการ์ดสรุป หรือใช้กล่องเลือก "คลังสินค้า" ด้านบนเพื่อดูสต็อกแยกเฉพาะสาขาที่ต้องการ</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-2 border bg-light d-flex gap-3 align-items-start">
                            <span class="badge bg-primary fs-6 rounded-pill px-3 py-2">2</span>
                            <div>
                                <strong class="d-block text-dark">ค้นหาสินค้า & กรองตามประเภท</strong>
                                <span class="small text-muted">พิมพ์รหัส SKU หรือชื่อสินค้าในช่องค้นหา ระบบจะคัดกรองรายการในตารางให้ทันทีแบบเรียลไทม์</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-2 border bg-light d-flex gap-3 align-items-start">
                            <span class="badge bg-primary fs-6 rounded-pill px-3 py-2">3</span>
                            <div>
                                <strong class="d-block text-dark">เพิ่มสินค้าใหม่ (+ เพิ่มสินค้า)</strong>
                                <span class="small text-muted">กดปุ่มสีแดงขวาบน กรอกชื่อสินค้า รหัส SKU หมวดหมู่ ราคา และกำหนดเงื่อนไขชั่งน้ำหนัก</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-2 border bg-light d-flex gap-3 align-items-start">
                            <span class="badge bg-primary fs-6 rounded-pill px-3 py-2">4</span>
                            <div>
                                <strong class="d-block text-dark">แก้ไขข้อมูล / รับเข้าสต็อกด่วน</strong>
                                <span class="small text-muted">กดปุ่มสีแดง "แก้ไข" เพื่อเปลี่ยนราคา/บาร์โค้ด หรือกดไอคอนสีเขียวท้ายรายการเพื่อรับของเข้าคลัง</span>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 px-3 small mb-0 d-flex gap-2 align-items-center">
                        <i class="bi bi-info-circle-fill fs-5 text-warning"></i>
                        <span>ต้องการดูคู่มือฉบับเต็มของระบบทั้งหมด สามารถคลิก <a href="{{ route('core-modules.index') }}" target="_blank" class="fw-bold alert-link">เปิดหน้าคู่มือระบบรวม (/core-modules)</a></span>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3 d-flex justify-content-between">
                    <a href="{{ route('core-modules.index') }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-book me-1"></i> ดูคู่มือฉบับเต็มทั้งหมด
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary px-4" @click="helpModalOpen = false">ปิดหน้าต่าง</button>
                </div>
            </div>
        </div>

        <div class="booking-modal-backdrop" x-show="modalOpen" x-transition.opacity @keydown.escape.window="modalOpen = false">
            <div class="booking-modal" style="width: min(720px, 100%);" @click.outside="modalOpen = false" x-transition>
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h3 class="h4 fw-bold mb-0">เพิ่มสินค้าใหม่</h3>
                    <button type="button" class="btn btn-light rounded-circle" @click="modalOpen = false" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('products.store') }}">
                    @csrf
                    <div class="modal-body px-4 pb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">ประเภทสินค้า <span class="text-danger">*</span></label>
                                <select name="product_category_id" required class="form-select">
                                    <option value="">-- เลือกประเภทเพื่อรันรหัสอัตโนมัติ --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->code }} - {{ $category->name_th }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">ระบบจะรันรหัสตามประเภท เช่น 101001, 101002 โดยไม่ต้องกรอกเอง</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">หน่วยหลัก</label>
                                <select name="base_unit_id" required class="form-select">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->displayLabel() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">ชื่อสินค้า (ไทย)</label>
                                <input type="text" name="name_th" required class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">ชื่อสินค้า (อังกฤษ)</label>
                                <input type="text" name="name_en" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">หมายเหตุสินค้า / ข้อมูลช่วยจำ</label>
                                <textarea name="note" rows="2" maxlength="2000" class="form-control" placeholder="เช่น วิธีเก็บรักษา รุ่น สี หรือเงื่อนไขที่พนักงานควรทราบ"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">ขายเมื่อสต๊อกไม่พอ</label>
                                <select name="negative_stock_policy" class="form-select" required>
                                    <option value="allow">เตือนแล้วอนุญาต</option>
                                    <option value="block">ห้ามขายเกินสต๊อก</option>
                                </select>
                            </div>
                            <div class="col-md-3"><label class="form-label text-muted small">จุดสั่งซื้อ</label><input type="number" step="0.0001" min="0" name="reorder_point" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label text-muted small">สต๊อกขั้นต่ำ</label><input type="number" step="0.0001" min="0" name="minimum_stock" class="form-control"></div>
                            <div class="col-md-3"><label class="form-label text-muted small">สต๊อกสูงสุด</label><input type="number" step="0.0001" min="0" name="maximum_stock" class="form-control"></div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">แผนก</label>
                                <select name="product_department_id" class="form-select">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">ยี่ห้อ</label>
                                <select name="product_brand_id" class="form-select">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name_th }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">ราคาเริ่มต้น</label>
                                <input type="number" step="0.01" min="0" name="default_price" class="form-control">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="is_vat" value="1" checked class="form-check-input" id="newProductVat">
                                    <label class="form-check-label" for="newProductVat">คิด VAT</label>
                                </div>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="tracks_expiry" value="1" class="form-check-input" id="newProductExpiry">
                                    <label class="form-check-label" for="newProductExpiry">ควบคุม Lot และวันหมดอายุ</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small">ราคาขายสูงสุด/หน่วยฐาน</label>
                                <input type="number" step="0.00000001" min="0" name="maximum_sale_price" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small">กำไรขั้นต่ำ (%)</label>
                                <input type="number" step="0.00000001" max="100" name="minimum_margin_percent" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-muted small">ควบคุมกำไร</label>
                                <select name="margin_control_policy" class="form-select"><option value="warn">แจ้งเตือน</option><option value="block">บังคับขออนุมัติ</option></select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">เตือนก่อนหมดอายุ (วัน)</label>
                                <input type="number" min="0" max="3650" name="expiry_warning_days" value="30" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">อายุสินค้านับจากวันผลิต (วัน)</label>
                                <input type="number" min="1" max="36500" name="shelf_life_days" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">เริ่มระบายก่อนหมดอายุ (วัน)</label>
                                <input type="number" min="0" max="3650" name="clearance_warning_days" value="7" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">ส่วนลดระบายแนะนำ (%)</label>
                                <input type="number" step="0.01" min="0" max="100" name="clearance_discount_percent" value="0" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small">เมื่อ Lot หมดอายุ</label>
                                <select name="expiry_sale_policy" class="form-select" required>
                                    <option value="block">ห้ามขาย/ห้ามใช้</option>
                                    <option value="allow">เตือนแต่อนุญาต</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" value="1" checked class="form-check-input" id="newProductActive">
                                    <label class="form-check-label" for="newProductActive">ใช้งาน</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light border px-4" @click="modalOpen = false">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> บันทึกสินค้า
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- Quick Stock In modal --}}
        <div class="booking-modal-backdrop" x-show="stockInOpen" x-transition.opacity @keydown.escape.window="stockInOpen = false" style="display:none">
            <div class="booking-modal" style="width:min(480px,100%)" @click.outside="stockInOpen = false" x-transition>
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-box-arrow-in-down me-2 text-success"></i>เพิ่มสต็อกสินค้า</h5>
                    <button type="button" class="btn btn-light rounded-circle" @click="stockInOpen = false"><i class="bi bi-x-lg"></i></button>
                </div>
                <form method="POST" :action="stockInUrl" class="p-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">สินค้า</label>
                        <div class="form-control bg-light" x-text="stockInName"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">คลังสินค้า / ตำแหน่งเก็บ <span class="text-danger">*</span></label>
                        <select name="warehouse_location_id" class="form-select" required>
                            <option value="">-- เลือกตำแหน่งเก็บ --</option>
                            @foreach($branches as $branch)
                                @foreach($branch->warehouses as $wh)
                                    @foreach($wh->locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $branch->name_th }} › {{ $wh->name }} › {{ $loc->name }}</option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">จำนวนที่เพิ่ม <span class="text-danger">*</span></label>
                            <input type="number" name="qty" step="0.0001" min="0.0001" class="form-control" placeholder="เช่น 10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">ราคาทุน/หน่วย</label>
                            <input type="number" name="unit_cost" step="0.01" min="0" class="form-control" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">หมายเหตุ</label>
                        <input type="text" name="remark" class="form-control" placeholder="เช่น รับสินค้าจากซัพพลายเออร์">
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-light border px-4" @click="stockInOpen = false">ยกเลิก</button>
                        <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-circle me-1"></i> บันทึกสต็อก</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete product modal --}}
        <div class="booking-modal-backdrop" x-show="deleteOpen" x-transition.opacity @keydown.escape.window="deleteOpen = false" style="display:none">
            <div class="booking-modal" style="width:min(420px,100%)" @click.outside="deleteOpen = false" x-transition>
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-trash me-2"></i>ลบสินค้า</h5>
                    <button type="button" class="btn btn-light rounded-circle" @click="deleteOpen = false"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="p-4">
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>คำเตือน:</strong> ถ้ายังมีสต็อกในคลัง จะไม่สามารถลบได้
                    </div>
                    <p class="mb-4">คุณต้องการลบ <strong x-text="deleteName"></strong> ใช่หรือไม่?</p>
                    <form method="POST" :action="deleteUrl">
                        @csrf
                        @method('DELETE')
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-light border px-4" @click="deleteOpen = false">ยกเลิก</button>
                            <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i> ยืนยันลบ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('head')
<style>
    .status-summary { display:flex; align-items:center; gap:12px; height:72px; padding:12px 16px; background:var(--erp-surface, #fff); border:1px solid var(--erp-border); border-radius:14px; color:inherit; text-decoration:none; transition:.15s ease; }
    .inventory-summary { display:flex; align-items:center; gap:12px; min-height:72px; padding:12px 16px; background:var(--erp-surface, #fff); border:1px solid var(--erp-border); border-radius:14px; box-shadow:0 3px 12px rgba(15,51,74,.04); }
    .inventory-summary-icon { width:38px; height:38px; display:grid; place-items:center; border-radius:11px; background:var(--erp-surface-2); font-size:19px; }
    .inventory-summary small { display:block; color:var(--erp-muted); font-size:12px; }
    .inventory-summary strong { display:block; color:var(--erp-text); font-size:21px; line-height:1.1; }
    .status-summary:hover, .status-summary.is-selected { border-color:#93c5fd; box-shadow:0 6px 18px rgba(30,64,175,.10); transform:translateY(-1px); }
    .status-summary-icon { width:38px; height:38px; display:grid; place-items:center; border-radius:11px; background:var(--erp-surface-2); font-size:19px; }
    .status-summary small { display:block; color:var(--erp-muted); font-size:12px; }
    .status-summary strong { display:block; color:var(--erp-text); font-size:21px; line-height:1.1; }
    [x-cloak] { display: none !important; }
    .booking-modal-backdrop {
        position: fixed; inset: 0; z-index: 2000;
        background: rgba(15, 23, 42, .42);
        display: flex; align-items: center; justify-content: center; padding: 24px;
    }
    .booking-modal {
        width: min(1120px, 100%); max-height: calc(100vh - 48px); overflow: auto;
        background: var(--erp-surface, #fff); border-radius: 18px; box-shadow: 0 24px 80px rgba(15, 23, 42, .24);
    }
    .product-popup-backdrop { position:fixed; inset:0; z-index:1950; display:flex; align-items:center; justify-content:center; padding:12px; background:rgba(15,23,42,.48); }
    .product-popup-window { width:min(760px,calc(100vw - 24px)); height:min(540px,calc(100vh - 24px)); display:flex; flex-direction:column; overflow:hidden; border:1px solid #777d83; border-radius:2px; background:#ececec; box-shadow:0 18px 52px rgba(15,23,42,.3); }
    .product-popup-titlebar { min-height:32px; display:flex; align-items:center; justify-content:space-between; padding:0 4px 0 9px; color:#111827; background:#f5f5f5; border-bottom:1px solid #9da2a7; font:700 12px Tahoma,"Noto Sans Thai",sans-serif; }
    .product-popup-titlebar button { width:28px; height:27px; display:grid; place-items:center; border:0; border-radius:0; color:#374151; background:transparent; }
    .product-popup-titlebar button:hover { color:#b91c1c; background:#fee2e2; }
    .product-popup-window iframe { width:100%; height:100%; flex:1 1 auto; border:0; background:#eef5f9; }
    .product-page { --product-border:var(--erp-border); --product-ink:var(--erp-text); }
    .product-toolbar { display:flex; align-items:flex-end; justify-content:space-between; gap:9px; padding:9px 10px; border:1px solid var(--product-border); border-radius:10px; background:var(--erp-surface, rgba(255,255,255,.88)); box-shadow:0 3px 10px rgba(15,51,74,.04); flex-wrap: nowrap; overflow-x: auto; }
    .product-filter-bar { display:flex; align-items:flex-end; gap:8px; flex-wrap:nowrap; flex:1 1 auto; }
    .product-filter-field { display:grid; gap:2px; min-width:110px; flex: 1 1 auto; max-width: max-content; }
    .product-filter-field.search-field { min-width:180px; flex: 2 1 auto; width: auto; }
    .product-filter-field label { margin:0; color:var(--erp-muted); font-size:9px; line-height:1.1; font-weight:700; }
    .product-filter-field .form-control,.product-filter-field .form-select,.product-filter-field .input-group-text { min-height:32px; height:32px; border-color:var(--erp-border); font-size:11px; padding-top:3px; padding-bottom:3px; }
    .product-filter-field .form-select { max-width:none!important; }
    .product-filter-bar .btn,.product-actions .btn { min-height:32px; height:32px; border-radius:7px; font-size:11px; padding:4px 10px; font-weight:700; white-space:nowrap; }
    .product-actions { display:flex; gap:8px; align-items:center; flex:0 0 auto; }
    .scale-plu-summary { display:flex; align-items:center; gap:10px; padding:7px 10px; border:1px solid var(--erp-border); border-radius:9px; background:var(--erp-surface, #fff); box-shadow:0 2px 8px rgba(30,64,175,.03); }
    .scale-plu-summary>div:not(.scale-plu-icon) { display:grid; gap:2px; }
    .scale-plu-summary span { color:var(--erp-muted); font-size:10px; font-weight:700; }
    .scale-plu-summary strong { color:var(--product-ink); font-size:15px; line-height:1.05; letter-spacing:.035em; }
    .scale-plu-icon { width:31px; height:31px; display:grid; place-items:center; border-radius:7px; color:#fff; background:linear-gradient(145deg,var(--erp-primary-ink),var(--erp-primary-ink)); font-size:14px; box-shadow:0 3px 8px rgba(22,143,202,.15); }
    .scale-plu-summary .btn { min-height:30px; border-radius:7px; font-size:10px; padding:4px 9px; font-weight:700; }
    .product-table-card { overflow:hidden; padding:0; border:1px solid var(--product-border); border-radius:14px; box-shadow:0 5px 18px rgba(15,51,74,.055); background:var(--erp-surface, #fff); }
    .product-table { font-size:11px; }
    .product-table thead th { padding:8px 10px; border:0; background:var(--erp-primary-soft); color:var(--erp-primary-dark); font-size:9px; font-weight:800; letter-spacing:.01em; white-space:nowrap; }
    .product-table tbody td { padding:7px 10px; border-color:var(--erp-success-soft); color:var(--erp-text); vertical-align:middle; line-height:1.2; }
    .product-table tbody tr { transition:background-color .15s ease; }
    .product-table tbody tr:hover { background:var(--erp-surface-2); }
    .product-table .badge { padding:5px 10px; font-size:11px; font-weight:700; }
    .product-table tbody .btn-primary { min-width:58px; min-height:27px; border-radius:6px; font-size:9px; font-weight:700; padding:3px 7px; box-shadow:none; }
    .product-pagination { padding:12px 14px 4px; border-top:1px solid var(--erp-surface-2); }
    @media(max-width:1200px){.product-toolbar{align-items:stretch;flex-direction:column}.product-actions{justify-content:flex-end}.product-filter-field.search-field{width:min(280px,100%)}}
    @media(max-width:720px){.product-toolbar{padding:10px}.product-filter-bar,.product-filter-field,.product-filter-field.search-field{width:100%}.product-filter-field{min-width:0}.product-filter-bar .btn{flex:1}.product-actions{display:grid;grid-template-columns:1fr 1fr}.product-actions .btn{display:flex;align-items:center;justify-content:center}.product-table thead th,.product-table tbody td{padding:9px 10px}.product-popup-backdrop{padding:0}.product-popup-window{width:100vw;height:100vh;border:0;border-radius:0}}
    @media(max-width:720px){.scale-plu-summary{align-items:flex-start;flex-wrap:wrap}.scale-plu-summary .btn{width:100%;margin-left:0!important}}
</style>
@endpush

@push('scripts')
<script>
    function productPage() {
        return {
            modalOpen: {{ request('open_add') ? 'true' : 'false' }},
            helpModalOpen: false,
            stockInOpen: false,
            stockInUrl: '',
            stockInName: '',
            deleteOpen: false,
            deleteUrl: '',
            deleteName: '',
            productPopupOpen: false,
            productUrl: '',
            priceModalOpen: false,
            targetProductId: null,
            targetProductName: '',
            targetProductSku: '',
            targetProductPrice: 0,
            priceScope: 'all',
            selectedBranchId: '',
            currentBranchPrice: 0,
            newPriceInput: '',
            branchList: [],
            priceSaving: false,
            priceModalNotice: '',
            priceModalSuccess: false,

            async openPriceModal(id, name, sku, price) {
                this.targetProductId = id;
                this.targetProductName = name;
                this.targetProductSku = sku;
                this.targetProductPrice = price;
                this.newPriceInput = Number(price).toFixed(2);
                this.priceScope = 'all';
                this.priceModalNotice = '';
                this.priceModalOpen = true;

                try {
                    const res = await fetch(`/products/${id}/branch-prices`);
                    const data = await res.json();
                    this.branchList = data.branches || [];
                    if (this.branchList.length > 0) {
                        this.selectedBranchId = this.branchList[0].id;
                        this.currentBranchPrice = this.branchList[0].price;
                    }
                } catch(e) {}
            },

            onScopeChange() {
                if (this.priceScope === 'all') {
                    this.newPriceInput = Number(this.targetProductPrice).toFixed(2);
                } else {
                    this.onBranchChange();
                }
            },

            onBranchChange() {
                const b = this.branchList.find(x => x.id == this.selectedBranchId);
                if (b) {
                    this.currentBranchPrice = b.price;
                    this.newPriceInput = Number(b.price).toFixed(2);
                }
            },

            getSelectedBranchName() {
                const b = this.branchList.find(x => x.id == this.selectedBranchId);
                return b ? b.name_th : '';
            },

            async savePrice() {
                if (!this.newPriceInput || isNaN(this.newPriceInput) || Number(this.newPriceInput) < 0) {
                    this.priceModalNotice = 'กรุณากรอกราคาให้ถูกต้อง';
                    this.priceModalSuccess = false;
                    return;
                }

                this.priceSaving = true;
                this.priceModalNotice = '';

                try {
                    const payload = {
                        branch_id: this.priceScope === 'all' ? 'all' : this.selectedBranchId,
                        price: parseFloat(this.newPriceInput)
                    };

                    const res = await fetch(`/products/${this.targetProductId}/prices`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.priceModalSuccess = true;
                        this.priceModalNotice = data.message || 'บันทึกราคาเรียบร้อยแล้ว ✓';
                        
                        if (this.priceScope === 'all') {
                            this.targetProductPrice = parseFloat(this.newPriceInput);
                        }
                        const cell = document.getElementById(`price-val-${this.targetProductId}`);
                        if (cell) cell.innerText = '฿' + parseFloat(this.newPriceInput).toFixed(2);

                        setTimeout(() => {
                            this.priceModalOpen = false;
                            this.priceSaving = false;
                            this.priceModalNotice = '';
                        }, 900);
                    } else {
                        this.priceModalSuccess = false;
                        this.priceModalNotice = data.message || 'เกิดข้อผิดพลาดในการบันทึก';
                        this.priceSaving = false;
                    }
                } catch(e) {
                    this.priceModalSuccess = false;
                    this.priceModalNotice = 'ข้อผิดพลาด: ' + e.message;
                    this.priceSaving = false;
                }
            },
            openStockIn(id, name, sku) {
                this.stockInUrl = `{{ url('/products') }}/${id}/quick-stock-in`;
                this.stockInName = `${name} (${sku})`;
                this.stockInOpen = true;
            },
            openDelete(id, name, sku) {
                this.deleteUrl = `{{ url('/products') }}/${id}`;
                this.deleteName = `${name} (${sku})`;
                this.deleteOpen = true;
            },
            openProduct(url) {
                this.productUrl = url;
                this.productPopupOpen = true;
                document.body.style.overflow = 'hidden';
            },
            closeProduct() {
                this.productPopupOpen = false;
                this.productUrl = '';
                document.body.style.overflow = '';
                window.location.reload();
            }
        };
    }
</script>
@endpush
