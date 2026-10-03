<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-color: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #10b981;
            --accent-dark: #059669;
            --danger: #ef4444;
            --border: rgba(255, 255, 255, 0.1);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Prompt', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cfd-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 40px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid var(--border);
        }
        .cfd-header img {
            max-height: 50px;
            object-fit: contain;
        }
        .cfd-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: var(--text-color);
        }
        .cfd-layout {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .cfd-cart {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            background: var(--card-bg);
        }
        .cfd-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px 40px;
        }
        .cart-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 100px 120px;
            gap: 15px;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
            font-size: 20px;
        }
        .cart-item.gift { color: var(--accent); }
        .cart-item-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .cart-item-qty { text-align: right; color: var(--text-muted); font-weight: 500; }
        .cart-item-total { text-align: right; font-weight: 700; color: #38bdf8; }
        
        .cfd-summary {
            flex: 0.8;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: linear-gradient(135deg, #0f172a, #020617);
        }
        .total-box {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 2px solid var(--accent-dark);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            margin-bottom: 30px;
        }
        .total-label {
            font-size: 24px;
            color: var(--text-muted);
            margin-bottom: 10px;
            font-weight: 600;
        }
        .total-amount {
            font-size: 84px;
            font-weight: 900;
            color: var(--accent);
            line-height: 1;
            text-shadow: 0 4px 10px rgba(16, 185, 129, 0.4);
        }
        .qr-box {
            background: white;
            padding: 20px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .qr-box img {
            width: 260px;
            height: 260px;
            display: block;
        }
        .payment-label {
            margin-top: 20px;
            font-size: 28px;
            font-weight: 800;
            color: #fcd34d;
        }
        .idle-screen {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0.4;
        }
        .idle-screen i {
            font-size: 100px;
            margin-bottom: 20px;
        }
        .thanks-screen {
            position: absolute;
            inset: 0;
            background: var(--bg-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
        .thanks-screen i {
            font-size: 120px;
            color: var(--accent);
            margin-bottom: 20px;
        }
        .thanks-screen h2 {
            font-size: 48px;
            font-weight: 900;
            color: #f8fafc;
            margin: 0;
        }
    </style>
</head>
<body x-data="cfdApp()">
    
    <div class="cfd-header">
        @if(isset($logo) && $logo)
            <img src="{{ $logo }}" alt="Logo">
        @else
            <h1>{{ $company['name'] ?? 'PopCentral POS' }}</h1>
        @endif
        <h1 style="color:var(--text-muted)">ยินดีต้อนรับ (Welcome)</h1>
    </div>

    <!-- MAIN CFD LAYOUT -->
    <div class="cfd-layout" x-show="!receiptOpen">
        <!-- CART SIDE -->
        <div class="cfd-cart">
            <template x-if="cart.length === 0">
                <div class="idle-screen">
                    <i class="bi bi-cart"></i>
                    <h2>รอทำรายการ...</h2>
                </div>
            </template>
            <div class="cfd-cart-items" x-show="cart.length > 0">
                <template x-for="item in cart" :key="item.name">
                    <div class="cart-item" :class="{'gift': item.is_free_gift}">
                        <div class="cart-item-name" x-text="item.name"></div>
                        <div class="cart-item-qty" x-text="item.qty + ' ' + (item.unit_name || '')"></div>
                        <div class="cart-item-total" x-text="item.is_free_gift ? 'FREE' : money(item.lineNet)"></div>
                    </div>
                </template>
            </div>
        </div>

        <!-- SUMMARY SIDE -->
        <div class="cfd-summary">
            <div class="total-box">
                <div class="total-label">ยอดรวมสุทธิ (Total)</div>
                <div class="total-amount" x-text="money(totalAmount)"></div>
            </div>

            <!-- QR PAYMENT -->
            <template x-if="payModalOpen && method === 'transfer'">
                <div style="animation: fade-in 0.3s ease-out">
                    <!-- Since QR generation requires backend PromptPay library, we rely on the cashier scanner or a static image if possible. 
                         For CFD, we'll display a generic QR icon to prompt the customer to scan the cashier's QR display. -->
                    <div class="qr-box" id="cfd-qr-box" x-effect="renderQR(qrPayload)">
                        <div x-show="!qrPayload" style="width:260px;height:260px;display:flex;align-items:center;justify-content:center;color:#64748b;font-weight:700">กำลังสร้าง QR...</div>
                    </div>
                    <div class="payment-label">สแกน QR พร้อมเพย์<br>เพื่อชำระเงิน</div>
                </div>
            </template>
        </div>
    </div>

    <!-- THANKS SCREEN -->
    <div class="thanks-screen" x-show="receiptOpen" style="display:none" x-transition.opacity.duration.500ms>
        <i class="bi bi-check-circle-fill"></i>
        <h2>ขอบคุณที่ใช้บริการครับ!</h2>
        <div class="total-amount" style="margin-top:20px; font-size:64px" x-text="'ยอดชำระ: ' + money(lastTotal)"></div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cfdApp', () => ({
                cart: [],
                totalAmount: 0,
                totalQty: 0,
                method: 'cash',
                payModalOpen: false,
                receiptOpen: false,
                lastTotal: 0,
                lastMethod: 'cash',
                qrPayload: null,

                init() {
                    const channel = new BroadcastChannel('pos_cfd');
                    channel.onmessage = (e) => {
                        const data = e.data;
                        this.cart = data.cart || [];
                        this.totalAmount = data.totalAmount || 0;
                        this.totalQty = data.totalQty || 0;
                        this.method = data.method || 'cash';
                        this.payModalOpen = data.payModalOpen || false;
                        this.receiptOpen = data.receiptOpen || false;
                        this.lastTotal = data.lastTotal || 0;
                        this.lastMethod = data.lastMethod || 'cash';
                        this.qrPayload = data.qrPayload || null;
                    };
                },
                money(val) {
                    return Number(val || 0).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }));
        });
    </script>
</body>
</html>
