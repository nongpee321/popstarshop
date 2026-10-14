@extends('layout')

@section('title', 'ระบบวิเคราะห์ & แชท AI อัจฉริยะ')
@section('page-title', 'ศูนย์วิเคราะห์ & แชท AI อัจฉริยะ (POP STAR AI)')
@section('page-subtitle', 'ศูนย์กลางสั่งงาน AI อัจฉริยะ ตรวจสอบสต๊อก วิเคราะห์ยอดขาย ตรวจคำถามย้อนหลัง และตรวจสอบโควตา Token')

@section('content')
<div x-data="aiFullPage()" class="ai-fullpage-container">
    <style>
        .ai-fullpage-container {
            font-family: 'IBM Plex Sans Thai', sans-serif;
            color: var(--erp-ink, #0f172a);
        }
        .ai-shell {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 20px;
            height: calc(100vh - 175px);
            min-height: 600px;
        }
        @media (max-width: 991px) {
            .ai-shell {
                grid-template-columns: 1fr;
                height: auto;
            }
        }
        .ai-panel {
            background: var(--erp-surface, #ffffff);
            border: 1px solid var(--erp-border, #e2e8f0);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .ai-sidebar-head {
            padding: 16px;
            background: linear-gradient(135deg, #e11d48, #9f1239);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ai-history-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ai-history-item {
            padding: 11px 14px;
            border-radius: 12px;
            background: var(--erp-bg, #f8fafc);
            border: 1px solid var(--erp-border, #e2e8f0);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }
        .ai-history-item:hover {
            border-color: #f43f5e;
            background: rgba(244, 63, 94, 0.06);
            transform: translateY(-1px);
        }
        .ai-token-card {
            padding: 14px;
            background: var(--erp-bg, #f8fafc);
            border-top: 1px solid var(--erp-border, #e2e8f0);
        }
        .ai-chat-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--erp-border, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--erp-surface, #ffffff);
        }
        .ai-messages-canvas {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: var(--erp-bg, #f8fafc);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .msg-bubble-user {
            align-self: flex-end;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 16px 16px 3px 16px;
            padding: 12px 18px;
            max-width: 80%;
            font-size: 14px;
            line-height: 1.6;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        .msg-bubble-ai {
            align-self: flex-start;
            background: var(--erp-surface, #ffffff);
            border: 1px solid var(--erp-border, #e2e8f0);
            color: var(--erp-ink, #0f172a);
            border-radius: 16px 16px 16px 3px;
            padding: 14px 20px;
            max-width: 85%;
            font-size: 14.5px;
            line-height: 1.65;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .msg-export-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed var(--erp-border, #e2e8f0);
            flex-wrap: wrap;
        }
        .msg-export-btn {
            font-size: 11.5px;
            padding: 3px 10px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }
        .msg-export-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .msg-export-btn.btn-pdf:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fca5a5;
        }
        .msg-export-btn.btn-excel:hover {
            background: #f0fdf4;
            color: #16a34a;
            border-color: #86efac;
        }
        .ai-quick-bar {
            padding: 10px 18px;
            background: var(--erp-surface, #ffffff);
            border-top: 1px solid var(--erp-border, #e2e8f0);
            display: flex;
            gap: 8px;
            overflow-x: auto;
            white-space: nowrap;
        }
        .ai-input-wrap {
            padding: 14px 18px;
            background: var(--erp-surface, #ffffff);
            border-top: 1px solid var(--erp-border, #e2e8f0);
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .spin-slow {
            animation: spin 3s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <div class="ai-shell">
        <!-- ฝั่งซ้าย: ประวัติคำถามย้อนหลัง & แผงสถานะ Token -->
        <aside class="ai-panel">
            <div class="ai-sidebar-head">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-5"></i>
                    <div>
                        <strong class="d-block" style="font-size:14px;">ประวัติคำถามที่ผ่านมา</strong>
                        <span class="small" style="opacity:0.85;">คลิกเพื่อถามซ้ำได้ทันที</span>
                    </div>
                </div>
                <button type="button" @click="clearPromptHistory()" class="btn btn-sm btn-link text-white text-decoration-none p-0" title="ล้างประวัติคำถาม">
                    <i class="bi bi-trash"></i>
                </button>
            </div>

            <!-- List of Old Questions -->
            <div class="ai-history-list">
                <template x-for="(item, idx) in questionHistory" :key="idx">
                    <div class="ai-history-item" @click="askAgain(item.text)">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-chat-left-text text-danger mt-1" style="font-size:12px;"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-truncate" style="max-width:240px; font-size:13px;" x-text="item.text"></div>
                                <div class="small text-muted" style="font-size:11px;" x-text="item.time"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="questionHistory.length === 0" class="text-center text-muted py-5">
                    <i class="bi bi-chat-square-dots fs-3 d-block mb-2 text-secondary"></i>
                    <span style="font-size:13px;">ยังไม่มีคำถามที่เคยถามไว้</span>
                </div>
            </div>

            <!-- Token Counter & Quota Monitor พร้อมตัวนับเวลาถอยหลังรีเซ็ตโควตา -->
            <div class="ai-token-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold" style="font-size:12.5px;">
                        <i class="bi bi-cpu-fill text-primary me-1"></i> โควตา Token (Gemini 3.5)
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size:11px;" 
                          x-text="((tokenInfo.total / 1000000) * 100).toFixed(2) + '% (' + tokenInfo.total.toLocaleString() + ')'"></span>
                </div>

                <!-- Progress Bar -->
                <div class="progress mb-2" style="height: 8px; background: #e2e8f0; border-radius: 999px;">
                    <div class="progress-bar bg-danger" role="progressbar" 
                         :style="'width: ' + Math.min(100, Math.max(0.5, (tokenInfo.total / 1000000) * 100)) + '%;'"></div>
                </div>

                <div class="d-flex justify-content-between text-muted" style="font-size:11.5px;">
                    <span>ใช้ไป: <strong class="text-danger" x-text="((tokenInfo.total / 1000000) * 100).toFixed(2) + '%'"></strong></span>
                    <span>คงเหลือ: <strong class="text-success" x-text="((tokenInfo.remaining / 1000000) * 100).toFixed(2) + '%'"></strong></span>
                </div>

                <div class="d-flex justify-content-between text-muted mt-1" style="font-size:10.5px; opacity: 0.85;">
                    <span x-text="tokenInfo.total.toLocaleString() + ' Tokens'"></span>
                    <span x-text="tokenInfo.remaining.toLocaleString() + ' Tokens'"></span>
                </div>

                <!-- นับถอยหลังรีเซ็ต Token (RPM / RPD) -->
                <div class="mt-2 pt-2 border-top d-flex align-items-center justify-content-between" style="font-size:11px;">
                    <span class="text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-arrow-repeat text-info spin-slow"></i> รีเซ็ตโควตารอบถัดไป:
                    </span>
                    <span class="fw-bold text-primary font-monospace" x-text="resetCountdownText"></span>
                </div>

                <div class="mt-2 text-muted" style="font-size:10.5px; line-height:1.4;">
                    * โมเดล Gemini 3.5 Flash รองรับสูงสุด 1,000,000 Tokens ต่อรอบ (รีเซ็ตรอบใหม่ทุกวันเวลาเที่ยงคืน)
                </div>
            </div>
        </aside>

        <!-- ฝั่งขวา: แคนวาสแชทหลัก พร้อมปุ่มด่วนและเครื่องมือวิเคราะห์ -->
        <main class="ai-panel">
            <!-- Header Bar -->
            <div class="ai-chat-header">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px; height:40px; border-radius:12px; background:linear-gradient(135deg, #e11d48, #be123c); color:white; display:grid; place-items:center; font-size:22px;">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h2 class="h5 fw-bold mb-0">POP STAR AI Assistant</h2>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Online</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Gemini 3.5 Flash</span>
                        </div>
                        <div class="text-muted small">เชื่อมต่อฐานข้อมูลระบบ ERP แบบเรียลไทม์</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" @click="resetConversation()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> เริ่มบทสนทนาใหม่
                    </button>
                </div>
            </div>

            <!-- Messages Canvas -->
            <div id="fullpage-chat-box" class="ai-messages-canvas">
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'msg-bubble-user' : 'msg-bubble-ai'">
                        <div :id="'msg-content-' + index" x-html="formatMessage(msg.content)"></div>
                        
                        <!-- แถบเครื่องมือส่งออกไฟล์สำหรับข้อความตอบกลับของ AI -->
                        <template x-if="msg.role === 'ai'">
                            <div class="msg-export-toolbar">
                                <span class="text-muted" style="font-size:11px;"><i class="bi bi-file-earmark-arrow-down"></i> ส่งออก:</span>
                                <button type="button" @click="exportToPdf(index)" class="msg-export-btn btn-pdf" title="บันทึกรายงานนี้เป็นไฟล์ PDF">
                                    <i class="bi bi-filetype-pdf text-danger"></i> ดาวน์โหลด PDF
                                </button>
                                <button type="button" @click="exportToExcel(index)" class="msg-export-btn btn-excel" title="ส่งออกข้อมูลเป็นไฟล์ Excel (.xlsx)">
                                    <i class="bi bi-filetype-xlsx text-success"></i> ส่งออก Excel
                                </button>
                                <button type="button" @click="copyMessage(msg.content)" class="msg-export-btn" title="คัดลอกข้อความ">
                                    <i class="bi bi-clipboard"></i> คัดลอก
                                </button>
                                <button type="button" @click="printReport(index)" class="msg-export-btn" title="พิมพ์รายงาน">
                                    <i class="bi bi-printer"></i> พิมพ์
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Quick Action Pills -->
            <div class="ai-quick-bar">
                <span class="small text-muted align-self-center me-1"><i class="bi bi-lightning-charge-fill text-warning"></i> คำสั่งด่วน:</span>
                <button type="button" @click="askQuick('ขอรายงานสรุปยอดขาย 20 วันย้อนหลัง พร้อมตารางตัวเลขและบทวิเคราะห์สำหรับจัดทำรายงานทางการ')" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold">
                    📄 สรุปยอดขาย & กำไร 20 วัน
                </button>
                <button type="button" @click="askQuick('ขอรายงานตรวจสอบสินค้าสต๊อกติดลบและสต๊อกวิกฤต พร้อมข้อเสนอแนะ')" class="btn btn-sm btn-outline-secondary rounded-pill">
                    🔥 เช็กสต๊อกติดลบ
                </button>
                <button type="button" @click="askQuick('ขอรายงานสรุปยอดขายหน้าร้านวันนี้ ประจำวัน')" class="btn btn-sm btn-outline-primary rounded-pill">
                    📊 ยอดขายวันนี้
                </button>
                <button type="button" @click="askQuick('ขอรายงานสถานะใบสั่งซื้อ PO ค้างส่งและกำหนดการรับสินค้า')" class="btn btn-sm btn-outline-success rounded-pill">
                    🚚 PO รอรับของ
                </button>
                <button type="button" @click="askQuick('ขอรายงานสินค้าขายดี 5 อันดับแรก พร้อมตารางสัดส่วนยอดขาย')" class="btn btn-sm btn-outline-warning rounded-pill">
                    ⭐ 5 สินค้าขายดี
                </button>
            </div>

            <!-- Input Area -->
            <div class="ai-input-wrap">
                <input type="text" x-model="inputText" @keydown.enter="sendMessage()" placeholder="พิมพ์คำถามของคุณ หรือเลือกจากคำถามย้อนหลังทางซ้าย... (กด Enter เพื่อส่ง)" 
                       class="form-control rounded-pill px-4 py-2" style="font-size:14px; border-color: #cbd5e1;">
                <button type="button" @click="sendMessage()" :disabled="loading || inputText.trim() === ''"
                        class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 44px; height: 44px; flex-shrink: 0;" :style="(loading || inputText.trim() === '') ? 'opacity: 0.6;' : 'opacity: 1;'">
                    <template x-if="!loading">
                        <i class="bi bi-send-fill"></i>
                    </template>
                    <template x-if="loading">
                        <div class="spinner-border spinner-border-sm text-white" role="status" style="width: 16px; height: 16px; border-width: 2px;"></div>
                    </template>
                </button>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('aiFullPage', () => ({
        inputText: '',
        loading: false,
        messages: [],
        questionHistory: [],
        currentUser: @json(auth()->user()?->name ?? 'ผู้ดูแลระบบ'),
        company: {
            nameTh: @json(\App\Models\AppSetting::company('name_th') ?: 'บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด'),
            nameEn: @json(\App\Models\AppSetting::company('name_en') ?: 'POP STAR FOOD TRADING CO., LTD.'),
            taxId: @json(\App\Models\AppSetting::company('tax_id') ?: '0345566003246'),
            address: @json(\App\Models\AppSetting::company('address') ?: '480 หมู่ที่ 1 ตำบลแสนสุข อำเภอวารินชำราบ จังหวัดอุบลราชธานี 34190'),
            logo: @json(asset('images/popstar-brand-logo.png')),
        },
        tokenInfo: {
            total: 0,
            remaining: 1000000
        },
        resetCountdownText: '-- ชม. -- นาที',
        timerInterval: null,
        init() {
            this.loading = false;
            // โหลดแชทเดิม
            const saved = localStorage.getItem('ai_chat_history');
            if (saved) {
                try { 
                    this.messages = JSON.parse(saved); 
                    // ถ้าข้อความสุดท้ายมีคำว่ากำลังประมวลผลให้เอาออก
                    this.messages = this.messages.filter(m => m && m.content && !m.content.includes('กำลังประมวลผล'));
                } catch(e) {}
            }
            if (!this.messages.length) {
                this.messages = [{ role: 'ai', content: 'สวัสดีครับ ยินดีต้อนรับสู่ **ศูนย์กลางแชท AI อัจฉริยะ (POP STAR AI)** ท่านสามารถตรวจสอบสต๊อกเรียลไทม์, ยอดขายหน้าร้าน, เอกสารค้างส่ง หรือเลือกดูคำถามเก่าในแถบประวัติด้านซ้ายมือได้ทันทีครับ!' }];
            }

            // โหลดประวัติคำถาม
            const savedHistory = localStorage.getItem('ai_prompt_history');
            if (savedHistory) {
                try { this.questionHistory = JSON.parse(savedHistory); } catch(e) {}
            }

            // โหลด Token ล่าสุด
            const savedTokens = localStorage.getItem('ai_token_info');
            if (savedTokens) {
                try { this.tokenInfo = JSON.parse(savedTokens); } catch(e) {}
            }

            this.$watch('messages', val => localStorage.setItem('ai_chat_history', JSON.stringify(val)));
            this.$watch('questionHistory', val => localStorage.setItem('ai_prompt_history', JSON.stringify(val)));
            this.$watch('tokenInfo', val => localStorage.setItem('ai_token_info', JSON.stringify(val)));

            // เริ่มนับถอยหลังรีเซ็ต Token ทุกวินาที
            this.updateCountdown();
            this.timerInterval = setInterval(() => {
                this.updateCountdown();
            }, 1000);

            this.scrollToBottom();
        },
        updateCountdown() {
            const now = new Date();
            // รีเซ็ตโควตารอบวันถัดไปเวลา 00:00 น.
            const midnight = new Date();
            midnight.setHours(24, 0, 0, 0);

            const diffMs = midnight - now;
            if (diffMs <= 0) {
                this.resetCountdownText = 'กำลังรีเซ็ต...';
                return;
            }

            const diffSec = Math.floor(diffMs / 1000);
            const hours = Math.floor(diffSec / 3600);
            const minutes = Math.floor((diffSec % 3600) / 60);
            const seconds = diffSec % 60;

            const pad = (n) => String(n).padStart(2, '0');
            this.resetCountdownText = `${hours} ชม. ${pad(minutes)} นาที ${pad(seconds)} วินาที`;
        },
        askAgain(text) {
            this.inputText = text;
            this.sendMessage();
        },
        askQuick(text) {
            this.inputText = text;
            this.sendMessage();
        },
        resetConversation() {
            if (confirm('ต้องการเริ่มบทสนทนาใหม่ใช่หรือไม่? (ประวัติคำถามยังคงอยู่ในแถบซ้ายมือ)')) {
                this.loading = false;
                this.messages = [{ role: 'ai', content: 'เริ่มต้นการสนทนาใหม่เรียบร้อยครับ มีข้อมูลส่วนใดให้ POP STAR AI ช่วยเหลือไหมครับ?' }];
                localStorage.removeItem('ai_chat_history');
            }
        },
        clearPromptHistory() {
            if (confirm('คุณต้องการล้างประวัติคำถามเก่าทั้งหมดใช่หรือไม่?')) {
                this.questionHistory = [];
            }
        },
        formatMessage(text) {
            if (!text) return '';

            // 1. เก็บ Code block ไว้ก่อนเพื่อความปลอดภัย
            const codeBlocks = [];
            let processed = text.replace(/```([\s\S]*?)```/g, (match, code) => {
                const id = `__CODE_BLOCK_${codeBlocks.length}__`;
                codeBlocks.push(`<pre style="background:#1e293b; color:#f8fafc; padding:10px 14px; border-radius:8px; font-size:12.5px; overflow-x:auto; margin:8px 0; font-family:monospace;"><code>${this.escapeHtml(code.trim())}</code></pre>`);
                return id;
            });

            // 2. Parse Markdown Table เป็นตารางทางการ
            const lines = processed.split('\n');
            const outputLines = [];
            let tableLines = [];
            let inTable = false;
            const tableBlocks = [];

            const isTableLine = (l) => {
                const t = l.trim();
                return t.startsWith('|') && t.endsWith('|') && t.length > 2;
            };

            const isDivider = (l) => {
                const t = l.trim();
                return t.startsWith('|') && t.endsWith('|') && /^[\s|:-]+$/.test(t);
            };

            const convertTableToHtml = (tLines) => {
                if (tLines.length < 2) return tLines.join('<br>');
                const headerLine = tLines[0];
                let startIndex = 1;
                let aligns = [];

                if (tLines.length > 1 && isDivider(tLines[1])) {
                    const divCells = tLines[1].split('|').slice(1, -1);
                    aligns = divCells.map(c => {
                        const s = c.trim();
                        if (s.startsWith(':') && s.endsWith(':')) return 'center';
                        if (s.endsWith(':')) return 'right';
                        return 'left';
                    });
                    startIndex = 2;
                }

                const headers = headerLine.split('|').slice(1, -1).map(c => c.trim());
                let html = '<div style="overflow-x:auto; margin:12px 0 16px; border:1px solid #cbd5e1; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.04);">';
                html += '<table class="report-table" style="width:100%; border-collapse:collapse; font-size:12.5px; background:#fff; text-align:left;">';
                
                // Thead
                html += '<thead style="background:#0f172a; color:#ffffff;"><tr>';
                headers.forEach((h, i) => {
                    const align = aligns[i] || 'left';
                    html += `<th style="padding:10px 12px; border:1px solid #334155; font-weight:700; text-align:${align}; white-space:nowrap;">${this.parseInline(h)}</th>`;
                });
                html += '</tr></thead><tbody>';

                // Tbody
                let rowCount = 0;
                for (let r = startIndex; r < tLines.length; r++) {
                    if (isDivider(tLines[r])) continue;
                    const cells = tLines[r].split('|').slice(1, -1).map(c => c.trim());
                    const bg = (rowCount % 2 === 0) ? '#ffffff' : '#f8fafc';
                    rowCount++;
                    html += `<tr style="background:${bg};">`;
                    cells.forEach((cell, i) => {
                        const cleanNum = cell.replace(/[,%฿\-]/g, '').trim();
                        const isNum = cleanNum !== '' && !isNaN(cleanNum);
                        const align = aligns[i] || (isNum ? 'right' : 'left');
                        html += `<td style="padding:8px 12px; border:1px solid #e2e8f0; text-align:${align}; color:#1e293b; ${isNum ? 'font-variant-numeric: tabular-nums; font-weight:500;' : ''}">${this.parseInline(cell)}</td>`;
                    });
                    html += '</tr>';
                }

                html += '</tbody></table></div>';
                return html;
            };

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                if (isTableLine(line)) {
                    tableLines.push(line);
                    inTable = true;
                } else {
                    if (inTable) {
                        const tblId = `__TABLE_BLOCK_${tableBlocks.length}__`;
                        tableBlocks.push(convertTableToHtml(tableLines));
                        outputLines.push(tblId);
                        tableLines = [];
                        inTable = false;
                    }
                    outputLines.push(line);
                }
            }
            if (inTable && tableLines.length > 0) {
                const tblId = `__TABLE_BLOCK_${tableBlocks.length}__`;
                tableBlocks.push(convertTableToHtml(tableLines));
                outputLines.push(tblId);
            }

            processed = outputLines.join('\n');

            // 3. หัวข้อระดับต่างๆ แบบทางการ
            processed = processed
                .replace(/^### (.*$)/gim, '<div style="font-size:14px; font-weight:700; color:#0f172a; margin-top:14px; margin-bottom:5px; padding-left:10px; border-left:3.5px solid #be123c;">$1</div>')
                .replace(/^## (.*$)/gim, '<div style="font-size:15px; font-weight:700; color:#0f172a; margin-top:16px; margin-bottom:6px; padding:6px 12px; background:#fff1f2; border-left:4px solid #be123c; border-radius:0 4px 4px 0;">$1</div>')
                .replace(/^# (.*$)/gim, '<div style="font-size:16.5px; font-weight:800; color:#9f1239; margin-top:18px; margin-bottom:8px; border-bottom:2px solid #fecdd3; padding-bottom:5px;">$1</div>');

            // 4. บูลเล็ต
            processed = processed
                .replace(/^\s*[\-\*]\s+(.*$)/gim, '<div style="display:flex; gap:8px; margin:3px 0;"><span style="color:#be123c; font-weight:bold;">•</span><span>$1</span></div>');

            // 5. ตัวหนา ตัวเอียง โค้ดอินไลน์
            processed = this.parseInline(processed);

            // 6. ขึ้นบรรทัดใหม่
            processed = processed.replace(/\n/g, '<br>');

            // 7. คืนค่า Table Blocks
            tableBlocks.forEach((tbl, idx) => {
                processed = processed.replace(`__TABLE_BLOCK_${idx}__`, tbl);
            });

            // 8. คืนค่า Code Blocks
            codeBlocks.forEach((code, idx) => {
                processed = processed.replace(`__CODE_BLOCK_${idx}__`, code);
            });

            return processed;
        },
        parseInline(str) {
            if (!str) return '';
            return str
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code style="background:rgba(0,0,0,0.06); padding:2px 6px; border-radius:4px; font-size:12px; font-family:monospace;">$1</code>');
        },
        escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        },
        scrollToBottom() {
            setTimeout(() => {
                const box = document.getElementById('fullpage-chat-box');
                if (box) box.scrollTop = box.scrollHeight;
            }, 60);
        },
        async sendMessage() {
            if (this.inputText.trim() === '') return;
            const userMsg = this.inputText.trim();
            this.messages.push({ role: 'user', content: userMsg });

            // บันทึกลง Prompt History
            const now = new Date();
            const timeStr = now.toLocaleDateString('th-TH', { day:'numeric', month:'short' }) + ' ' + now.toLocaleTimeString('th-TH', { hour:'2-digit', minute:'2-digit' });
            this.questionHistory.unshift({ text: userMsg, time: timeStr });
            if (this.questionHistory.length > 50) this.questionHistory.pop();

            this.inputText = '';
            this.loading = true;
            this.scrollToBottom();

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 12000);

            try {
                const response = await fetch('/api/ai-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ messages: this.messages }),
                    signal: controller.signal
                });
                clearTimeout(timeoutId);

                const data = await response.json();
                this.messages.push({ role: 'ai', content: data.reply || 'ขออภัยครับ ไม่ได้รับคำตอบจากระบบ' });

                if (data.tokens) {
                    this.tokenInfo.total = data.tokens.total_tokens || 0;
                    this.tokenInfo.remaining = data.tokens.remaining_tokens || 1000000;
                }
            } catch (e) {
                clearTimeout(timeoutId);
                console.error('AI Chat Error:', e);
                if (e.name === 'AbortError') {
                    this.messages.push({ role: 'ai', content: 'ขออภัยครับ ระบบ AI ตอบกลับช้ากว่าปกติ กรุณาลองกดถามใหม่อีกครั้งครับ' });
                } else {
                    this.messages.push({ role: 'ai', content: 'ขออภัยครับ ไม่สามารถเชื่อมต่อระบบเครือข่ายได้ กรุณาลองใหม่อีกครั้ง (' + (e.message || 'error') + ')' });
                }
            } finally {
                clearTimeout(timeoutId);
                this.loading = false;
                this.scrollToBottom();
            }
        },
        copyMessage(content) {
            if (!content) return;
            navigator.clipboard.writeText(content).then(() => {
                alert('คัดลอกข้อความรายงานเรียบร้อยแล้วครับ');
            }).catch(() => {
                const ta = document.createElement('textarea');
                ta.value = content;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                alert('คัดลอกข้อความรายงานเรียบร้อยแล้วครับ');
            });
        },
        buildOfficialReportHtml(contentHtml, index) {
            const now = new Date();
            const dateStr = now.toLocaleDateString('th-TH', { year:'numeric', month:'long', day:'numeric' });
            const timeStr = now.toLocaleTimeString('th-TH', { hour:'2-digit', minute:'2-digit' });
            const docId = now.getFullYear().toString() + 
                          String(now.getMonth() + 1).padStart(2, '0') + 
                          String(now.getDate()).padStart(2, '0') + '-' + 
                          String(index + 1).padStart(3, '0');
            const authHash = 'ERP-' + Math.random().toString(36).substring(2, 8).toUpperCase();

            return `
                <div style="font-family: 'IBM Plex Sans Thai', 'Noto Sans Thai', sans-serif; color: #0f172a; line-height: 1.6; font-size: 13px;">
                    <!-- Official Header -->
                    <div style="border-bottom: 2.5px solid #0f172a; padding-bottom: 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="display: flex; gap: 14px; align-items: center;">
                            <div style="width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, #b71c1c, #d32f2f); display: flex; align-items: center; justify-content: center; color: white; font-size: 26px; font-weight: bold; flex-shrink: 0; box-shadow: 0 2px 8px rgba(183, 28, 28, 0.3);">
                                ★
                            </div>
                            <div>
                                <div style="font-size: 18px; font-weight: 800; color: #0f172a; line-height: 1.2;">
                                    ${this.company.nameTh}
                                </div>
                                <div style="font-size: 11px; font-weight: 700; color: #475569; letter-spacing: 0.5px; margin-top: 1px;">
                                    ${this.company.nameEn}
                                </div>
                                <div style="font-size: 10.5px; color: #64748b; margin-top: 3px; line-height: 1.4;">
                                    สำนักงานใหญ่: ${this.company.address}<br>
                                    เลขประจำตัวผู้เสียภาษีอากร: <strong style="color: #0f172a;">${this.company.taxId}</strong>
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="display: inline-block; padding: 4px 10px; background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 10px; font-weight: 800; border-radius: 4px; margin-bottom: 6px; letter-spacing: 0.3px;">
                                เอกสารภายในองค์กร (STRICTLY CONFIDENTIAL)
                            </div>
                            <div style="font-size: 11px; color: #334155; line-height: 1.45;">
                                <div>เลขที่เอกสาร: <strong style="font-family: monospace; font-size: 11.5px; color: #0f172a;">AI-RPT-${docId}</strong></div>
                                <div>วันที่ออกเอกสาร: <strong>${dateStr}</strong></div>
                                <div>เวลาประมวลผล: <strong>${timeStr} น.</strong></div>
                            </div>
                        </div>
                    </div>

                    <!-- Title Banner -->
                    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 10px 16px; border-radius: 6px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-size: 15px; font-weight: 700; letter-spacing: 0.3px;">
                                รายงานสรุปผลการดำเนินงานและการวิเคราะห์ข้อมูลเชิงบริหาร
                            </div>
                            <div style="font-size: 10.5px; color: #94a3b8; margin-top: 2px;">
                                EXECUTIVE MANAGEMENT & BUSINESS INTELLIGENCE REPORT
                            </div>
                        </div>
                        <div style="text-align: right; font-size: 11px; color: #cbd5e1;">
                            ผู้ขอรายงาน: <strong style="color: #ffffff;">${this.currentUser}</strong><br>
                            <span style="font-size: 10px; color: #94a3b8;">ระบบสารสนเทศ PopCentral ERP</span>
                        </div>
                    </div>

                    <!-- Content Body -->
                    <div class="official-content" style="font-size: 13px; line-height: 1.75; color: #1e293b; min-height: 280px;">
                        ${contentHtml}
                    </div>

                    <!-- 3-Party Corporate Approval Signatures (ช่องลงนามอนุมัติ 3 ฝ่าย) -->
                    <div style="margin-top: 40px; page-break-inside: avoid; border-top: 1.5px solid #cbd5e1; padding-top: 18px;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center;">
                            <!-- 1. ผู้จัดทำรายงาน -->
                            <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 8px; background: #f8fafc;">
                                <div style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 35px;">ผู้จัดทำรายงาน (Prepared By)</div>
                                <div style="border-bottom: 1px dotted #94a3b8; width: 85%; margin: 0 auto 6px;"></div>
                                <div style="font-size: 11.5px; font-weight: 700; color: #0f172a;">( ${this.currentUser} )</div>
                                <div style="font-size: 10px; color: #64748b;">เจ้าหน้าที่ฝ่ายปฏิบัติการ / ประจำระบบ</div>
                                <div style="font-size: 9.5px; color: #94a3b8; margin-top: 3px;">วันที่: ...... / ...... / ..........</div>
                            </div>

                            <!-- 2. ผู้ตรวจสอบข้อมูล -->
                            <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 8px; background: #f8fafc;">
                                <div style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 35px;">ผู้ตรวจสอบข้อมูล (Verified By)</div>
                                <div style="border-bottom: 1px dotted #94a3b8; width: 85%; margin: 0 auto 6px;"></div>
                                <div style="font-size: 11.5px; font-weight: 700; color: #0f172a;">( .................................................... )</div>
                                <div style="font-size: 10px; color: #64748b;">หัวหน้าฝ่ายบัญชีและการเงิน / คลังสินค้า</div>
                                <div style="font-size: 9.5px; color: #94a3b8; margin-top: 3px;">วันที่: ...... / ...... / ..........</div>
                            </div>

                            <!-- 3. ผู้อนุมัติ / ฝ่ายบริหาร -->
                            <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px 8px; background: #f8fafc;">
                                <div style="font-size: 11px; font-weight: 700; color: #334155; margin-bottom: 35px;">ผู้อนุมัติ (Approved By)</div>
                                <div style="border-bottom: 1px dotted #94a3b8; width: 85%; margin: 0 auto 6px;"></div>
                                <div style="font-size: 11.5px; font-weight: 700; color: #0f172a;">( .................................................... )</div>
                                <div style="font-size: 10px; color: #64748b;">กรรมการผู้จัดการ / ผู้มีอำนาจลงนาม</div>
                                <div style="font-size: 9.5px; color: #94a3b8; margin-top: 3px;">วันที่: ...... / ...... / ..........</div>
                            </div>
                        </div>
                    </div>

                    <!-- Formal Footer -->
                    <div style="margin-top: 25px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span>เอกสารทางการออกโดยระบบสารสนเทศ PopCentral ERP &middot; ${this.company.nameTh}</span><br>
                            <span style="color: #94a3b8;">รหัสตรวจสอบความถูกต้องดิจิทัล: <strong style="font-family: monospace;">${authHash}</strong></span>
                        </div>
                        <div style="text-align: right; color: #64748b; font-weight: 600;">
                            หน้า 1 / 1
                        </div>
                    </div>
                </div>
            `;
        },
        async exportToPdf(index) {
            const el = document.getElementById('msg-content-' + index);
            if (!el) return;

            // 1. สร้าง Overlay Loading & Preview สวยงามระดับมืออาชีพ
            const overlay = document.createElement('div');
            overlay.id = 'ai-pdf-export-overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100vw';
            overlay.style.height = '100vh';
            overlay.style.background = 'rgba(15, 23, 42, 0.75)';
            overlay.style.backdropFilter = 'blur(4px)';
            overlay.style.zIndex = '999999';
            overlay.style.display = 'flex';
            overlay.style.flexDirection = 'column';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'flex-start';
            overlay.style.padding = '30px 16px';
            overlay.style.overflow = 'auto';

            // แถบสถานะด้านบน
            const statusBanner = document.createElement('div');
            statusBanner.style.background = '#0f172a';
            statusBanner.style.color = '#ffffff';
            statusBanner.style.padding = '10px 22px';
            statusBanner.style.borderRadius = '30px';
            statusBanner.style.fontSize = '13.5px';
            statusBanner.style.fontWeight = '600';
            statusBanner.style.marginBottom = '20px';
            statusBanner.style.boxShadow = '0 4px 15px rgba(0,0,0,0.3)';
            statusBanner.style.display = 'flex';
            statusBanner.style.alignItems = 'center';
            statusBanner.style.gap = '10px';
            statusBanner.innerHTML = `
                <div class="spinner-border spinner-border-sm text-danger" style="width:16px; height:16px; border-width:2.5px;" role="status"></div>
                <span>กำลังประมวลผลและสร้างไฟล์ PDF ทางการ... กรุณารอสักครู่</span>
            `;
            overlay.appendChild(statusBanner);

            // เอกสาร Card A4 วางใน normal block flow เพื่อให้ html2canvas เรนเดอร์ได้ 100% ครบถ้วน ไม่ว่างเปล่า
            const card = document.createElement('div');
            card.style.width = '780px';
            card.style.maxWidth = '100%';
            card.style.background = '#ffffff';
            card.style.padding = '35px 40px';
            card.style.boxSizing = 'border-box';
            card.style.boxShadow = '0 10px 40px rgba(0,0,0,0.4)';
            card.style.borderRadius = '6px';
            card.style.fontFamily = "'IBM Plex Sans Thai', sans-serif";
            card.style.color = '#0f172a';
            card.innerHTML = this.buildOfficialReportHtml(el.innerHTML, index);

            overlay.appendChild(card);
            document.body.appendChild(overlay);

            // บังคับ Reflow และหน่วงเวลาสั้นๆ ให้เบราว์เซอร์ Render ครบถ้วน
            void card.offsetHeight;
            await new Promise(resolve => setTimeout(resolve, 250));

            const now = new Date();
            const fileDateStr = now.toISOString().slice(0,10);
            const opt = {
                margin:       [8, 10, 8, 10],
                filename:     `POPSTAR_AI_Executive_Report_${fileDateStr}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { 
                    scale: 2, 
                    useCORS: true, 
                    scrollY: 0, 
                    scrollX: 0, 
                    logging: false 
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            try {
                if (typeof html2pdf !== 'undefined') {
                    await html2pdf().set(opt).from(card).save();
                } else {
                    this.printReport(index);
                }
            } catch (err) {
                console.error('html2pdf failed:', err);
                this.printReport(index);
            } finally {
                // ลบ overlay ออกหลังเสร็จสิ้น
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
            }
        },
        exportToExcel(index) {
            const msg = this.messages[index];
            if (!msg || !msg.content) return;

            const lines = msg.content.split('\n');
            const dataRows = [];
            dataRows.push(['รายงานทางการ (Official Executive Report)', this.company.nameTh]);
            dataRows.push(['เลขประจำตัวผู้เสียภาษี', this.company.taxId]);
            dataRows.push(['วันที่ออกเอกสาร', new Date().toLocaleString('th-TH')]);
            dataRows.push(['ผู้จัดทำ', this.currentUser]);
            dataRows.push([]); // blank line

            lines.forEach(line => {
                const trimmed = line.trim();
                if (trimmed === '') return;

                // ตรวจสอบว่าเป็น Table Row หรือไม่
                if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
                    if (/^[\s|:-]+$/.test(trimmed)) return; // ข้าม Divider line
                    const cells = trimmed.split('|').slice(1, -1).map(c => c.trim().replace(/[\*`]/g, ''));
                    dataRows.push(cells);
                    return;
                }

                if (trimmed.includes(':')) {
                    const parts = trimmed.split(/:(.+)/);
                    const cleanKey = parts[0].replace(/^[\*\-\#\s]+/, '').replace(/[\*]+/g, '').trim();
                    const cleanVal = (parts[1] || '').replace(/[\*]+/g, '').trim();
                    dataRows.push([cleanKey, cleanVal]);
                } else {
                    const cleanLine = trimmed.replace(/^[\*\-\#\s]+/, '').replace(/[\*]+/g, '').trim();
                    dataRows.push([cleanLine]);
                }
            });

            if (typeof XLSX !== 'undefined') {
                const ws = XLSX.utils.aoa_to_sheet(dataRows);
                ws['!cols'] = [{ wch: 35 }, { wch: 45 }, { wch: 25 }, { wch: 25 }];
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "AI Report");
                const dateStr = new Date().toISOString().slice(0,10);
                XLSX.writeFile(wb, `รายงาน_POPSTAR_AI_${dateStr}.xlsx`);
            } else {
                alert('ไม่พบไลบรารี Excel กำลังส่งออกแบบ CSV แทน...');
                let csvContent = "\uFEFF" + dataRows.map(e => e.map(cell => `"${(cell||'').replace(/"/g, '""')}"`).join(",")).join("\n");
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.setAttribute("download", `รายงาน_POPSTAR_AI_${new Date().toISOString().slice(0,10)}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        },
        printReport(index) {
            const el = document.getElementById('msg-content-' + index);
            if (!el) return;

            const printWin = window.open('', '_blank');
            if (!printWin) {
                alert('กรุณาอนุญาตให้เปิดหน้าต่างป๊อปอัปเพื่อพิมพ์รายงานหรือบันทึกเป็น PDF');
                return;
            }

            const reportHtml = this.buildOfficialReportHtml(el.innerHTML, index);

            printWin.document.write(`
                <!DOCTYPE html>
                <html lang="th">
                <head>
                    <title>รายงานทางการ - ${this.company.nameTh}</title>
                    <meta charset="utf-8">
                    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        @page { size: A4 portrait; margin: 12mm 15mm; }
                        * { box-sizing: border-box; }
                        body { 
                            font-family: 'IBM Plex Sans Thai', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
                            padding: 20px 25px; 
                            color: #0f172a; 
                            background: #ffffff;
                            margin: 0;
                        }
                        @media print { 
                            body { padding: 0; }
                            button { display: none; } 
                        }
                    </style>
                </head>
                <body>
                    ${reportHtml}
                    <script>
                        window.onload = function() { 
                            setTimeout(function() { window.print(); }, 300); 
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWin.document.close();
        }
    }));
});
</script>
@endsection
