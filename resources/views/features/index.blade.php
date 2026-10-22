@extends('layout')

@section('title', 'Workflow ผังงานระบบ - PopCentral Flow')
@section('page-title', 'ผังการทำงานระบบ (Workflow & Modules)')
@section('page-subtitle', 'ศูนย์รวมผังการทำงานและจุดเชื่อมต่อโมดูล ERP / POS อิงลำดับงานมาตรฐาน')

@push('head')
<style>
    .flow-wrapper {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .flow-shell {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 20px;
        align-items: start;
    }

    /* Left Sidebar: Workflow Navigation */
    .flow-nav-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .flow-nav-header {
        padding: 18px 20px 14px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .flow-nav-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .flow-search-box {
        position: relative;
        margin-top: 10px;
    }
    .flow-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
        pointer-events: none;
    }
    .flow-search-input {
        width: 100%;
        padding: 9px 12px 9px 36px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        background: #ffffff;
        transition: all 0.15s ease;
    }
    .flow-search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        outline: none;
    }

    /* Module Tabs Scroller */
    .flow-tabs-scroll {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .flow-tab-chip {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 20px;
        color: #475569;
        font-weight: 600;
        font-size: 12px;
        padding: 6px 14px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .flow-tab-chip:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .flow-tab-chip.active {
        background: #dc2626; /* Pop Star primary */
        color: #ffffff;
        border-color: #dc2626;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    /* Items List */
    .flow-items-list {
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: calc(100vh - 280px);
        overflow-y: auto;
    }
    .flow-item-card {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 10px;
        padding: 12px 14px;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        transition: all 0.15s ease;
        position: relative;
    }
    .flow-item-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.03);
    }
    .flow-item-card.active {
        border-color: #fecaca;
        background: #fef2f2;
    }
    .flow-item-card.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 10px 0 0 10px;
        background: #dc2626;
    }
    .flow-item-num {
        width: 28px;
        height: 28px;
        border-radius: 7px;
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .flow-item-card.active .flow-item-num {
        background: #dc2626;
        color: #ffffff;
    }
    .flow-item-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 2px;
    }
    .flow-item-card.active .flow-item-name {
        color: #991b1b;
    }
    .flow-item-note {
        font-size: 12px;
        color: #64748b;
        line-height: 1.35;
    }

    /* Right Main Panel: Detail & Actions */
    .flow-detail-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .flow-hero-header {
        padding: 24px 28px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }
    .flow-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #fef2f2;
        color: #dc2626;
        margin-bottom: 8px;
    }
    .flow-hero-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
        line-height: 1.25;
    }
    .flow-hero-sub {
        font-size: 14px;
        color: #475569;
        line-height: 1.5;
        max-width: 800px;
        margin: 0;
    }

    /* Action Ribbon */
    .flow-action-strip {
        padding: 14px 28px;
        background: #fafbfc;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-flow-launch {
        background: #dc2626;
        color: #ffffff;
        font-weight: 600;
        font-size: 13px;
        border-radius: 8px;
        padding: 8px 18px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.2);
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .btn-flow-launch:hover {
        background: #b91c1c;
        color: #ffffff;
        transform: translateY(-1px);
    }
    .btn-flow-sec {
        background: #ffffff;
        color: #334155;
        font-weight: 600;
        font-size: 13px;
        border-radius: 8px;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #cbd5e1;
        transition: all 0.15s ease;
        text-decoration: none;
    }
    .btn-flow-sec:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    /* Content Area */
    .flow-detail-content {
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Section Boxes */
    .content-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 18px 20px;
    }
    .content-box-title {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Document Code Chips */
    .code-chips-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .doc-code-pill {
        display: inline-flex;
        align-items: center;
        padding: 5px 12px;
        border-radius: 6px;
        font-family: ui-monospace, SFMono-Regular, monospace;
        font-size: 12px;
        font-weight: 700;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .doc-code-pill:hover {
        border-color: #3b82f6;
        color: #1d4ed8;
        background: #eff6ff;
        transform: translateY(-1px);
    }

    /* Workflow Stepper */
    .bplus-stepper {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .bplus-step-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .bplus-step-num {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Quick Grid Cards */
    .quick-tiles-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .quick-tile-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .quick-tile-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .quick-tile-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .quick-tile-desc {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
    }

    /* Footer */
    .flow-footer-bar {
        padding: 12px 28px;
        background: #fafbfc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #64748b;
    }

    @media (max-width: 1100px) {
        .flow-shell {
            grid-template-columns: 1fr;
        }
        .flow-items-list {
            max-height: 320px;
        }
    }
    @media (max-width: 640px) {
        .quick-tiles-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $flowModules = collect($modules)->values()->map(function ($module, $index) {
        return [
            'index' => $index,
            'key' => $module['key'],
            'title' => $module['title'],
            'icon' => $module['icon'],
            'accent' => $module['accent'],
            'summary' => $module['summary'],
            'legacy_title' => $module['legacy_title'] ?? null,
            'legacy_codes' => collect($module['legacy_codes'] ?? [])->map(fn ($code) => [
                'code' => $code,
                'url' => route('documents.browser', ['legacy_type' => $code, 'year' => now()->subMonth()->year, 'month' => now()->subMonth()->month]),
            ])->values()->all(),
            'bplus_flow' => collect($module['bplus_flow'] ?? [])->values()->map(function ($step, $stepIndex) {
                return [
                    'no' => sprintf('%02d', $stepIndex + 1),
                    'name' => $step['name'],
                    'codes' => collect($step['codes'] ?? [])->map(fn ($code) => [
                        'code' => $code,
                        'url' => route('documents.browser', ['legacy_type' => $code, 'year' => now()->subMonth()->year, 'month' => now()->subMonth()->month]),
                    ])->values()->all(),
                ];
            })->all(),
            'items' => collect($module['items'])->values()->map(function ($item, $itemIndex) {
                return [
                    'code' => sprintf('%02d', $itemIndex + 1),
                    'name' => $item['name'],
                    'note' => $item['note'] ?? 'เปิดหน้าจอทำงานตาม flow เดิม',
                    'url' => ! empty($item['route']) ? route($item['route'][0], $item['route'][1]) : '#',
                    'status' => $item['status'],
                ];
            })->all(),
        ];
    })->all();
    $totalItems = collect($flowModules)->sum(fn ($module) => count($module['items']));
@endphp

<div class="flow-wrapper" x-data="popstarFlow(@js($flowModules))" x-cloak>
    <div class="flow-shell">
        {{-- Left: Workflow Navigation Sidebar --}}
        <section class="flow-nav-panel">
            <div class="flow-nav-header">
                <h2 class="flow-nav-title">
                    <i class="bi bi-diagram-3-fill text-danger"></i> ผังงาน Workflow
                </h2>
                <div class="text-muted small">ค้นหาและเลือกเปิดงานตามขั้นตอน</div>
                <div class="flow-search-box">
                    <i class="bi bi-search flow-search-icon"></i>
                    <input class="flow-search-input" x-model="query" placeholder="ค้นหา เช่น ใบจอง, ภาษี, QR, สต็อก...">
                </div>
            </div>

            {{-- Module Category Pills --}}
            <div class="flow-tabs-scroll">
                <template x-for="module in modules" :key="module.key">
                    <button type="button" class="flow-tab-chip" :class="{ active: activeKey === module.key }" @click="selectModule(module.key)" x-text="module.title"></button>
                </template>
            </div>

            {{-- Step Items List --}}
            <div class="flow-items-list">
                <template x-for="item in filteredItems" :key="item.code + item.name">
                    <div class="flow-item-card" :class="{ active: activeItem?.name === item.name }" @click="selectItem(item)">
                        <div class="flow-item-num" x-text="item.code"></div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="flow-item-name" x-text="item.name"></div>
                            <div class="flow-item-note" x-text="item.note"></div>
                        </div>
                    </div>
                </template>
                <div class="text-center text-muted py-5" x-show="filteredItems.length === 0">
                    <i class="bi bi-search fs-3 text-secondary opacity-50 d-block mb-1"></i>
                    ไม่พบรายการที่ตรงกับคำค้นหา
                </div>
            </div>
        </section>

        {{-- Right: Detail & Actions Panel --}}
        <section class="flow-detail-panel">
            {{-- Hero Header --}}
            <div class="flow-hero-header">
                <div class="flow-hero-badge">
                    <i class="bi bi-folder2-open"></i>
                    <span x-text="activeModule.title"></span>
                </div>
                <h2 class="flow-hero-title" x-text="activeItem?.name || activeModule.title"></h2>
                <p class="flow-hero-sub" x-text="activeItem?.note || activeModule.summary"></p>
            </div>

            {{-- Action Buttons Ribbon --}}
            <div class="flow-action-strip">
                <a class="btn-flow-launch" :href="activeItem?.url || '#'">
                    <i class="bi bi-box-arrow-up-right"></i>
                    เปิดหน้าจอทำงาน
                </a>
                <a class="btn-flow-sec" href="{{ route('reports.index') }}">
                    <i class="bi bi-clipboard-data text-primary"></i>
                    ศูนย์รายงาน
                </a>
                <a class="btn-flow-sec" href="{{ route('dashboard') }}">
                    <i class="bi bi-bar-chart-line text-success"></i>
                    แดชบอร์ดภาพรวม
                </a>
            </div>

            {{-- Body Content --}}
            <div class="flow-detail-content">
                {{-- Document Codes Box --}}
                <div class="content-box" x-show="activeModule.legacy_codes?.length">
                    <div class="content-box-title">
                        <i class="bi bi-file-earmark-code text-primary"></i>
                        <span>รหัสเอกสาร BPlus ในหมวดนี้</span>
                        <span class="text-muted fw-normal" x-text="activeModule.legacy_title ? '· ' + activeModule.legacy_title : ''"></span>
                    </div>
                    <div class="code-chips-wrap">
                        <template x-for="docCode in activeModule.legacy_codes" :key="docCode.code">
                            <a class="doc-code-pill" :href="docCode.url" x-text="docCode.code" title="คลิกเพื่อเปิดดูเอกสาร"></a>
                        </template>
                    </div>
                </div>

                {{-- BPlus Stepper List --}}
                <div class="content-box" x-show="activeModule.bplus_flow?.length">
                    <div class="content-box-title">
                        <i class="bi bi-diagram-2 text-danger"></i>
                        <span>ลำดับงานอิงมาตรฐาน BPlus</span>
                    </div>
                    <div class="bplus-stepper">
                        <template x-for="step in activeModule.bplus_flow" :key="step.no + step.name">
                            <div class="bplus-step-row">
                                <div class="bplus-step-num" x-text="step.no"></div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark fs-6" x-text="step.name"></div>
                                    <div class="code-chips-wrap mt-1">
                                        <template x-for="docCode in step.codes" :key="docCode.code">
                                            <a class="doc-code-pill" :href="docCode.url" x-text="docCode.code"></a>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Quick Access Grid --}}
                <div>
                    <h3 class="h6 fw-bold text-dark mb-3">
                        <i class="bi bi-grid-fill text-secondary me-1"></i> งานทั้งหมดในหมวดนี้
                    </h3>
                    <div class="quick-tiles-grid">
                        <template x-for="item in activeModule.items" :key="item.code + item.name">
                            <a class="quick-tile-card" :href="item.url" @mouseenter="activeItem = item">
                                <div class="quick-tile-title">
                                    <span class="badge bg-light text-dark border" x-text="item.code"></span>
                                    <span x-text="item.name"></span>
                                </div>
                                <div class="quick-tile-desc" x-text="item.note"></div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="flow-footer-bar">
                <span><i class="bi bi-info-circle me-1"></i>PopCentral Flow · อิงลำดับงานมาตรฐาน BPlus</span>
                <span>{{ count($flowModules) }} หมวดหมู่โมดูล · {{ $totalItems }} รายการงาน</span>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
function popstarFlow(modules) {
    return {
        modules,
        query: '',
        activeKey: modules[0]?.key || '',
        activeItem: modules[0]?.items?.[0] || null,
        get activeModule() {
            return this.modules.find(module => module.key === this.activeKey) || this.modules[0] || { title: '', items: [] };
        },
        get filteredItems() {
            const needle = this.query.trim().toLowerCase();
            if (!needle) return this.activeModule.items;
            return this.activeModule.items.filter(item =>
                `${item.name} ${item.note}`.toLowerCase().includes(needle)
            );
        },
        selectModule(key) {
            this.activeKey = key;
            this.query = '';
            this.activeItem = this.activeModule.items[0] || null;
        },
        selectItem(item) {
            this.activeItem = item;
        },
    };
}
</script>
@endpush
