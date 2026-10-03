<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display</title>
    <script src="https://unpkg.com/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-color: #f1f5f9;
            --text-color: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --card-bg: #ffffff;
            --primary: #059669;
            --danger: #dc2626;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Sarabun', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .cfd-header {
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--bg-color);
        }
        .cfd-header img {
            max-height: 50px;
            object-fit: contain;
        }
        .cfd-header h1 {
            margin: 0;
            font-size: 30px;
            font-weight: 900;
            color: var(--primary);
        }
        .clock-box {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 5px 20px;
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cfd-layout {
            display: flex;
            flex: 1;
            padding: 0 15px 15px 15px;
            gap: 15px;
            overflow: hidden;
        }
        .cfd-cart {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
        }
        .tbl-header {
            background-color: var(--primary);
            display: grid;
            grid-template-columns: minmax(0, 1fr) 80px 100px 120px;
            padding: 15px 20px;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }
        .cfd-cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }
        .cart-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 80px 100px 120px;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
            font-size: 18px;
        }
        .cart-item.gift { color: var(--primary); }
        .cart-item-name { font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #1e293b; }
        .cart-item-qty { text-align: center; font-weight: 700; color: #1e293b; }
        .cart-item-price { text-align: right; color: var(--text-muted); font-size: 16px; }
        .cart-item-total { text-align: right; font-weight: 700; color: #1e293b; }
        
        .idle-screen {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: var(--card-bg);
        }
        .idle-screen i {
            font-size: 80px;
            color: #94a3b8;
            margin-bottom: 10px;
        }
        .idle-screen h2 {
            color: #64748b;
            font-weight: 700;
            margin: 0;
        }
        
        .cfd-summary {
            flex: 0.8;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .subtotal-box {
            background: var(--card-bg);
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .sub-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sub-label { color: #475569; font-weight: 700; font-size: 14px; }
        .sub-val { color: #1e293b; font-weight: 700; font-size: 16px; }
        .sub-val.disc { color: var(--primary); }
        .sub-label.disc { color: var(--primary); }

        .total-box {
            background: var(--card-bg);
            border: 2px solid var(--primary);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
        }
        .total-label {
            font-size: 20px;
            color: #475569;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .total-amount {
            font-size: 70px;
            font-weight: 900;
            color: var(--danger);
            line-height: 1;
            font-family: Arial, sans-serif;
        }
        
        .qr-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }
        .qr-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .qr-badge {
            background: #1e3a8a;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .qr-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 16px;
        }
        .qr-status {
            background: #d1fae5;
            color: var(--primary);
            padding: 4px 10px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 12px;
        }
        .qr-img-wrapper {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 10px;
            background: white;
            margin-bottom: 15px;
        }
        .qr-img-wrapper img {
            width: 250px;
            height: 250px;
            display: block;
        }
        .qr-timer {
            color: var(--danger);
            font-size: 22px;
            font-weight: 900;
        }
        
        .thanks-screen {
            position: absolute;
            inset: 0;
            background: var(--card-bg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
        .thanks-screen i {
            font-size: 100px;
            color: var(--primary);
            margin-bottom: 20px;
        }
        .thanks-screen h2 {
            font-size: 40px;
            font-weight: 900;
            color: var(--text-color);
            margin: 0;
        }
    </style>
</head>
<body x-data="cfdApp()">
    
    <div class="cfd-header">
        <h1 style="color:var(--primary)">ยินดีต้อนรับ (Welcome)</h1>
        <div class="clock-box">
            <i class="bi bi-clock"></i> <span x-text="timeStr"></span>
        </div>
    </div>

    <!-- MAIN CFD LAYOUT -->
    <div class="cfd-layout" x-show="!receiptOpen">
        <!-- CART SIDE -->
        <div class="cfd-cart">
            <template x-if="cart.length === 0">
                <div class="idle-screen">
                    <i class="bi bi-cart3"></i>
                    <h2>รอทำรายการ...</h2>
                </div>
            </template>
            <div style="display:flex; flex-direction:column; flex:1;" x-show="cart.length > 0">
                <div class="tbl-header">
                    <div>รายการสินค้า</div>
                    <div style="text-align:center">จำนวน</div>
                    <div style="text-align:right">ราคา</div>
                    <div style="text-align:right">รวม</div>
                </div>
                <div class="cfd-cart-items">
                    <template x-for="item in cart" :key="item.name">
                        <div class="cart-item" :class="{'gift': item.is_free_gift}">
                            <div class="cart-item-name" x-text="item.name"></div>
                            <div class="cart-item-qty" x-text="item.qty"></div>
                            <div class="cart-item-price" x-text="item.is_free_gift ? '' : money(item.price)"></div>
                            <div class="cart-item-total" x-text="item.is_free_gift ? 'FREE' : money(item.lineNet)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- SUMMARY SIDE -->
        <div class="cfd-summary">
            <div class="subtotal-box">
                <div class="sub-row">
                    <span class="sub-label">ยอดรวมก่อนหักส่วนลด</span>
                    <span class="sub-val" x-text="money(totalAmount) + ' ฿'"></span>
                </div>
                <div class="sub-row">
                    <span class="sub-label">VAT 7% (คำนวณรวมในราคา)</span>
                    <span class="sub-val" x-text="money(totalAmount - (totalAmount/1.07)) + ' ฿'"></span>
                </div>
                <div class="sub-row">
                    <span class="sub-label disc">ส่วนลด</span>
                    <span class="sub-val disc" x-text="'0.00 ฿'"></span>
                </div>
            </div>
            
            <div class="total-box">
                <div class="total-label">ยอดรวมสุทธิ (Total)</div>
                <div class="total-amount" x-text="money(totalAmount)"></div>
            </div>

            <!-- QR PAYMENT -->
            <template x-if="payModalOpen && method === 'transfer'">
                <div class="qr-box">
                    <div class="qr-header">
                        <span class="qr-badge">THAI QR</span>
                        <span class="qr-title">พร้อมเพย์ (PromptPay)</span>
                        <span class="qr-status">สแกนเลย</span>
                    </div>
                    <div class="qr-img-wrapper" id="cfd-qr-box" x-effect="renderQR(qrPayload)">
                        <div x-show="!qrPayload" style="width:250px;height:250px;display:flex;align-items:center;justify-content:center;color:#64748b;font-weight:700">กำลังสร้าง QR...</div>
                    </div>
                    <div class="qr-timer">สแกนชำระเงินที่นี่</div>
                </div>
            </template>
        </div>
    </div>

    <!-- THANKS SCREEN -->
    <div class="thanks-screen" x-show="receiptOpen" style="display:none" x-transition.opacity.duration.500ms>
        <i class="bi bi-check-circle-fill"></i>
        <h2>ทำรายการสำเร็จ!</h2>
        <div class="total-amount" style="margin-top:20px; font-size:64px; color: var(--primary);" x-text="money(lastTotal)"></div>
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
                timeStr: '',

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
                    
                    setInterval(() => {
                        const now = new Date();
                        this.timeStr = now.toLocaleDateString('th-TH') + ' ' + now.toLocaleTimeString('th-TH');
                    }, 1000);
                },
                money(val) {
                    return Number(val || 0).toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                renderQR(payload) {
                    if (!payload) return;
                    setTimeout(() => {
                        const box = document.getElementById('cfd-qr-box');
                        if (box) {
                            box.innerHTML = '';
                            const qr = qrcode(0, 'M');
                            qr.addData(payload);
                            qr.make();
                            box.innerHTML = qr.createImgTag(5, 0);
                        }
                    }, 50);
                }
            }));
        });
    </script>
</body>
</html>
