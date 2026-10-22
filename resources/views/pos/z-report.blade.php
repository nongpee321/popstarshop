<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ใบปิดกะขาย - {{ $shift->shift_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #cbd5e1;
            color: #0f172a;
            font-family: 'Courier New', Courier, 'Sarabun', 'Tahoma', monospace, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: #1e293b;
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
        }
        .toolbar button {
            border: 0;
            border-radius: 8px;
            padding: 9px 20px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .15s;
        }
        .btn-print { background: #0284c7; color: #fff; }
        .btn-print:hover { background: #0369a1; }
        .btn-close { background: #475569; color: #f1f5f9; }
        .btn-close:hover { background: #334155; }

        /* Thermal receipt paper style */
        .paper-container {
            padding: 24px 12px 60px;
            display: flex;
            justify-content: center;
        }
        .thermal-slip {
            width: 80mm;
            min-height: 180mm;
            background: #fff;
            padding: 10mm 6mm 14mm;
            box-shadow: 0 10px 30px rgba(15,23,42,.22), 0 1px 4px rgba(15,23,42,.1);
            font-size: 12.5px;
            line-height: 1.35;
            color: #000;
            position: relative;
        }
        /* Paper top and bottom zig-zag tear edges */
        .thermal-slip::before {
            content: "";
            position: absolute;
            top: -5px; left: 0; right: 0; height: 5px;
            background: linear-gradient(135deg, #fff 5px, transparent 0), linear-gradient(-135deg, #fff 5px, transparent 0);
            background-size: 10px 10px;
        }
        .thermal-slip::after {
            content: "";
            position: absolute;
            bottom: -5px; left: 0; right: 0; height: 5px;
            background: linear-gradient(45deg, #fff 5px, transparent 0), linear-gradient(-45deg, #fff 5px, transparent 0);
            background-size: 10px 10px;
        }

        .slip-header { text-align: center; margin-bottom: 12px; }
        .slip-title { font-size: 19px; font-weight: 900; letter-spacing: 1px; margin: 0 0 4px; }
        .slip-subtitle { font-size: 14px; font-weight: 800; margin: 0 0 4px; }
        
        .table-head {
            display: grid;
            grid-template-columns: 32px 1fr 60px 70px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 0;
            border-bottom: 1px dashed #000;
            margin-bottom: 6px;
        }
        .table-head span:last-child, .table-head span:nth-child(3) { text-align: right; }

        .line {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 1.5px 0;
            font-size: 12px;
        }
        .line.sub { font-size: 11px; color: #111; }
        .line.bold { font-weight: 800; }
        .line .num { font-variant-numeric: tabular-nums; text-align: right; }

        .section-header {
            font-weight: 800;
            font-size: 12.5px;
            margin: 10px 0 3px;
            text-decoration: underline;
        }
        
        .separator-single {
            border: 0;
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }
        .separator-solid {
            border: 0;
            border-bottom: 1px solid #000;
            margin: 4px 0;
        }
        .separator-double {
            border: 0;
            border-bottom: 3px double #000;
            margin: 6px 0;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-top: 36px;
            text-align: center;
            font-size: 11px;
        }
        .sig-line {
            padding-top: 36px;
            border-top: 1px dotted #000;
        }

        @media print {
            body { background: #fff !important; }
            .toolbar { display: none !important; }
            .paper-container { padding: 0 !important; }
            .thermal-slip {
                width: 78mm !important;
                box-shadow: none !important;
                padding: 4mm 2mm 6mm !important;
                margin: 0 !important;
            }
            .thermal-slip::before, .thermal-slip::after { display: none !important; }
            @page {
                size: 80mm auto;
                margin: 0mm;
            }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button type="button" class="btn-print" onclick="window.print()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/></svg>
        พิมพ์ใบปิดกะ (Print)
    </button>
    <button type="button" class="btn-close" onclick="window.close()">
        ปิดหน้าต่าง
    </button>
</div>

<div class="paper-container">
    <main class="thermal-slip">
        {{-- Shop Header --}}
        <div class="slip-header">
            <h1 class="slip-title">{{ \App\Models\AppSetting::company('name_th') ?: 'POP STAR SHOP' }}</h1>
            <div class="slip-subtitle">ใบสรุปกะ / ใบรับเงิน</div>
            <div style="font-size:11px;">สาขา: {{ $shift->branch?->name_th ?? $shift->branch?->code }} ({{ $shift->branch?->code ?? '-' }})</div>
            <div style="font-size:10.5px;">เครื่อง POS: {{ $shift->terminal?->code ?? 'POS-01' }} &middot; เลขกะ: {{ $shift->shift_no }}</div>
        </div>

        {{-- Table Head / Columns --}}
        <div class="table-head">
            <span>Qty</span>
            <span>รายการ</span>
            <span>ต่อหน่วย</span>
            <span>ราคา</span>
        </div>

        {{-- Sales Section --}}
        <div class="section-header">รายการขาย</div>

        <div class="line sub">
            <span>บิลแรก :</span>
            <span class="num">{{ $firstBill?->receipt_no ?? '-' }} {{ $firstBill?->created_at?->format('d/m/Y H:i') }}</span>
        </div>
        <div class="line sub">
            <span>สุดท้าย :</span>
            <span class="num">{{ $lastBill?->receipt_no ?? '-' }} {{ $lastBill?->created_at?->format('d/m/Y H:i') }}</span>
        </div>
        
        <div style="font-weight:700;font-size:11.5px;margin-top:4px;">** บิลที่ปิดเรียบร้อย</div>
        <div class="line">
            <span>จำนวนบิล</span>
            <span class="num bold">{{ number_format($billCount) }}</span>
        </div>
        <div class="line">
            <span>ยอดขายก่อนลด</span>
            <span class="num">{{ number_format($grossSales, 2) }}</span>
        </div>
        <div class="line">
            <span>ลดบาทแต่ละรายการ</span>
            <span class="num">{{ number_format($discountAmount, 2) }}</span>
        </div>
        <div class="separator-solid"></div>
        <div class="line">
            <span>หลังลดแต่ละรายการ</span>
            <span class="num">{{ number_format($grossSales - $discountAmount, 2) }}</span>
        </div>
        <div class="line">
            <span>ปัดเศษ</span>
            <span class="num">{{ number_format($netSales - ($grossSales - $discountAmount), 2) }}</span>
        </div>
        <div class="separator-solid"></div>
        <div class="line bold" style="font-size:13.5px;">
            <span>ยอดรวม</span>
            <span class="num">{{ number_format($netSales, 2) }}</span>
        </div>
        <div class="line sub">
            <span>ยอดขายไม่มีภพ.</span>
            <span class="num">{{ number_format($nonTaxableSales, 2) }}</span>
        </div>
        <div class="line sub">
            <span>ยอดขายมีภพ.</span>
            <span class="num">{{ number_format($taxableSales, 2) }}</span>
        </div>
        <div class="line sub">
            <span>ยอดภพ. (VAT)</span>
            <span class="num">{{ number_format($vatAmount, 2) }}</span>
        </div>
        <div class="separator-single"></div>

        {{-- Payment Breakdown Section --}}
        <div class="section-header">รายการชำระ</div>
        @if($totals['cash'] > 0 || ($totals['transfer'] == 0 && $totals['credit_card'] == 0))
        <div class="line">
            <span>เงินสด</span>
            <span class="num">{{ number_format($totals['cash'], 2) }}</span>
        </div>
        @endif
        @if($totals['transfer'] > 0)
        <div class="line">
            <span>QR / โอนเงิน</span>
            <span class="num">{{ number_format($totals['transfer'], 2) }}</span>
        </div>
        @endif
        @if($totals['credit_card'] > 0)
        <div class="line">
            <span>บัตรเครดิต</span>
            <span class="num">{{ number_format($totals['credit_card'], 2) }}</span>
        </div>
        @endif
        @if($totals['cheque'] > 0)
        <div class="line">
            <span>เช็ค</span>
            <span class="num">{{ number_format($totals['cheque'], 2) }}</span>
        </div>
        @endif
        <div class="separator-solid"></div>
        <div class="line bold">
            <span>ยอดชำระ</span>
            <span class="num">{{ number_format($totals['cash'] + $totals['transfer'] + $totals['credit_card'] + $totals['cheque'], 2) }}</span>
        </div>
        <div class="separator-double"></div>

        {{-- Cash Audit Section --}}
        <div class="section-header">รายการเงินสด</div>
        <div class="line">
            <span>เงินสดขาย</span>
            <span class="num">{{ number_format($totals['cash'], 2) }}</span>
        </div>
        <div class="line">
            <span>นำส่งเงิน</span>
            <span class="num">{{ $cashMovements['drop'] > 0 ? '-' . number_format($cashMovements['drop'], 2) : '0.00' }}</span>
        </div>
        <div class="line">
            <span>รับเงินเพื่อทอน (ตั้งต้น)</span>
            <span class="num">{{ number_format($shift->opening_cash, 2) }}</span>
        </div>
        @if($cashMovements['cash_in'] > 0)
        <div class="line">
            <span>เงินเพิ่มเข้าลิ้นชัก</span>
            <span class="num">+{{ number_format($cashMovements['cash_in'], 2) }}</span>
        </div>
        @endif
        @if($cashMovements['payout'] > 0)
        <div class="line">
            <span>เบิกจ่ายจากลิ้นชัก</span>
            <span class="num">-{{ number_format($cashMovements['payout'], 2) }}</span>
        </div>
        @endif
        <div class="separator-solid"></div>
        <div class="line bold" style="font-size:13px;">
            <span>ยอดต้องนำส่ง (เงินสดในลิ้นชัก)</span>
            <span class="num">{{ number_format($shift->expected_cash, 2) }}</span>
        </div>
        @if($shift->status === 'closed')
        <div class="line">
            <span>เงินสดนับจริง</span>
            <span class="num bold">{{ number_format($shift->counted_cash, 2) }}</span>
        </div>
        <div class="line" style="font-weight:800; color:{{ (float)$shift->cash_difference === 0.0 ? '#000' : ((float)$shift->cash_difference > 0 ? '#15803d' : '#b91c1c') }};">
            <span>{{ (float)$shift->cash_difference === 0.0 ? 'เงินตรงกะ' : ((float)$shift->cash_difference > 0 ? 'เงินเกิน (+)' : 'เงินขาด (-)') }}</span>
            <span class="num">{{ number_format(abs($shift->cash_difference), 2) }}</span>
        </div>
        @endif
        <div class="separator-double"></div>

        {{-- Voided / Cancelled Bills Section --}}
        @if(count($voidedBills) > 0)
            <div style="font-weight:700;font-size:11.5px;margin-top:6px;">** บิลที่ยกเลิกระหว่างขาย / ย้อนหลัง</div>
            @foreach($voidedBills as $voidBill)
                <div class="line sub">
                    <span>{{ $voidBill->receipt_no }} {{ $voidBill->created_at?->format('H:i') }}@if($voidBill->voided_at)-{{ $voidBill->voided_at?->format('H:i') }}@endif</span>
                    <span class="num">{{ number_format($voidBill->gross_sales, 2) }}</span>
                </div>
            @endforeach
            <div class="separator-single"></div>
        @endif

        {{-- Cashier and Footer info --}}
        <div style="margin-top:10px;font-size:11.5px;">
            <div>Cashier : <strong>{{ $shift->cashierUser?->name ?? $shift->cashier?->name ?? '01' }} ({{ $shift->cashier?->code ?? $shift->cashierUser?->username ?? '01' }})</strong></div>
            <div style="color:#222;">พิมพ์เมื่อ: {{ now()->format('d/m/Y H:i:s') }}</div>
            @if($shift->closed_at)
                <div style="color:#222;">เวลาปิดกะ: {{ $shift->closed_at?->format('d/m/Y H:i:s') }}</div>
            @else
                <div style="color:#d97706;font-weight:700;">(กะยังเปิดอยู่ - ยอดแบบเรียลไทม์)</div>
            @endif
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-line">
                <div>(ลงชื่อแคชเชียร์)</div>
                <div style="font-size:9.5px;margin-top:3px;">ผู้ส่งเงิน</div>
            </div>
            <div class="sig-line">
                <div>(ลงชื่อผู้ตรวจรับ)</div>
                <div style="font-size:9.5px;margin-top:3px;">ผู้จัดการ/บัญชี</div>
            </div>
        </div>

        <div style="text-align:center;margin-top:20px;font-size:10px;color:#555;">
            - จบรายงานสรุปกะ -
        </div>
    </main>
</div>

</body>
</html>
