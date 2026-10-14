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
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:11px;" x-text="tokenInfo.total.toLocaleString() + ' Tokens'"></span>
                </div>

                <!-- Progress Bar -->
                <div class="progress mb-2" style="height: 7px; background: #e2e8f0; border-radius: 999px;">
                    <div class="progress-bar bg-danger" role="progressbar" 
                         :style="'width: ' + Math.min(100, Math.max(1, (tokenInfo.total / 1000000) * 100)) + '%;'"></div>
                </div>

                <div class="d-flex justify-content-between text-muted" style="font-size:11.5px;">
                    <span>ใช้ไป: <strong class="text-dark" x-text="tokenInfo.total.toLocaleString()"></strong></span>
                    <span>คงเหลือ: <strong class="text-success" x-text="tokenInfo.remaining.toLocaleString()"></strong></span>
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
                        <div x-html="formatMessage(msg.content)"></div>
                    </div>
                </template>

                <div x-show="loading" style="display: none;" class="msg-bubble-ai d-flex align-items-center gap-2 text-muted">
                    <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
                    <span>AI กำลังประมวลผลข้อมูลและคำนวณ Token...</span>
                </div>
            </div>

            <!-- Quick Action Pills -->
            <div class="ai-quick-bar">
                <span class="small text-muted align-self-center me-1"><i class="bi bi-lightning-charge-fill text-warning"></i> คำสั่งด่วน:</span>
                <button type="button" @click="askQuick('ตอนนี้มีสินค้าสต๊อกติดลบไหม?')" class="btn btn-sm btn-outline-danger rounded-pill">
                    🔥 เช็กสต๊อกติดลบทั้งหมด
                </button>
                <button type="button" @click="askQuick('สรุปยอดขายหน้าร้านวันนี้')" class="btn btn-sm btn-outline-primary rounded-pill">
                    📊 สรุปยอดขายวันนี้
                </button>
                <button type="button" @click="askQuick('มีใบสั่งซื้อ PO อะไรค้างส่งบ้าง?')" class="btn btn-sm btn-outline-success rounded-pill">
                    🚚 เช็ก PO รอรับของ
                </button>
                <button type="button" @click="askQuick('สูตรคำนวณต้นทุน Moving Average')" class="btn btn-sm btn-outline-warning rounded-pill">
                    🧮 สูตรคำนวณต้นทุน
                </button>
                <button type="button" @click="askQuick('อธิบายขั้นตอนการโอนย้ายสต็อกข้ามสาขา')" class="btn btn-sm btn-outline-secondary rounded-pill">
                    📦 ขั้นตอนโอนสต็อก
                </button>
            </div>

            <!-- Input Area -->
            <div class="ai-input-wrap">
                <input type="text" x-model="inputText" @keydown.enter="sendMessage()" placeholder="พิมพ์คำถามของคุณ หรือเลือกจากคำถามย้อนหลังทางซ้าย... (กด Enter เพื่อส่ง)" 
                       class="form-control rounded-pill px-4 py-2" style="font-size:14px; border-color: #cbd5e1;">
                <button type="button" @click="sendMessage()" :disabled="loading || inputText.trim() === ''"
                        class="btn btn-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 44px; height: 44px; flex-shrink: 0;" :style="(loading || inputText.trim() === '') ? 'opacity: 0.5;' : 'opacity: 1;'">
                    <i class="bi bi-send-fill"></i>
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
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`(.*?)`/g, '<code style="background:rgba(0,0,0,0.06); padding:2px 6px; border-radius:4px; font-size:12px;">$1</code>')
                .replace(/\n/g, '<br>');
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
        }
    }));
});
</script>
@endsection
