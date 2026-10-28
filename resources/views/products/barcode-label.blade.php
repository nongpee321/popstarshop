<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EAN-13 สินค้า {{ $product->sku_code }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 16px; color: #111827; font-family: Tahoma, sans-serif; background: #eef2f7; }
        .toolbar { max-width: 520px; margin: 0 auto 12px; display: flex; justify-content: center; flex-wrap: wrap; gap: 8px; }
        .toolbar button { border: 0; border-radius: 7px; padding: 9px 16px; color: #fff; background: #2563eb; font-weight: 700; cursor: pointer; }
        .toolbar button:disabled { opacity: .55; cursor: wait; }
        .toolbar a { color: #334155; padding: 9px 12px; text-decoration: none; }
        .copy-number { width: 150px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; font: 700 15px monospace; letter-spacing: .08em; text-align: center; }
        .copy-status { min-height: 18px; margin: 0 auto 10px; text-align: center; font-size: 12px; }
        .golabel-guide { max-width: 520px; margin: 0 auto 14px; padding: 10px 14px; border: 1px solid #bfdbfe; border-radius: 8px; color: #1e3a8a; background: #eff6ff; font-size: 13px; line-height: 1.55; }
        .golabel-guide strong { color: #1d4ed8; }
        .label { width: 50mm; height: 40mm; margin: 0 auto; padding: 2mm; display: flex; flex-direction: column; align-items: center; justify-content: space-around; overflow: hidden; background: #fff; text-align: center; }
        .name { width: 100%; max-height: 8mm; overflow: hidden; font-size: 8pt; font-weight: 700; line-height: 1.15; }
        #product-barcode { width: 34mm; height: 19mm; }
        #product-barcode svg { width: 34mm; height: 19mm; display: block; }
        .barcode-digits { font: 9pt/1 monospace; letter-spacing: .04em; }
        .error { display: none; color: #b91c1c; font: 10pt Tahoma, sans-serif; }
        @page { size: 50mm 40mm; margin: 0; }
        @media print {
            body { width: 50mm; height: 40mm; padding: 0; background: #fff; }
            .toolbar, .copy-status, .golabel-guide, .error { display: none !important; }
            .label { margin: 0; page-break-after: avoid; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <label for="ean13-number">เลข EAN-13</label>
        <input id="ean13-number" class="copy-number" type="text" value="{{ $barcode }}" readonly>
        <button id="copy-number" type="button">คัดลอกเลข EAN-13</button>
        <button id="print-label" type="button" disabled onclick="window.print()">พิมพ์ตัวอย่าง</button>
        <a href="#" onclick="window.close(); return false;">ปิด</a>
    </div>
    <p id="copy-status" class="copy-status" role="status" aria-live="polite">คัดลอกเลข EAN-13 แล้วไป GoLabel เลือก 1D Barcode แบบ EAN-13 และวางเลขในช่องข้อมูล</p>
    <aside class="golabel-guide">
        <strong>ตั้งขนาดใน GoLabel II สำหรับฉลากเล็ก:</strong>
        เลือกแท่งบาร์โค้ดแล้วเปิดคุณสมบัติ ตั้งความกว้างทั้งสัญลักษณ์ประมาณ <strong>32–34 มม.</strong>
        และความสูงแท่งอย่างน้อย <strong>18 มม.</strong> เปิดแสดงเลขใต้แท่งและเว้นขอบซ้าย/ขวาอย่างน้อย 2.5 มม.
        อย่าบีบเฉพาะแนวนอนหรือย่อจนแท่งบางมาก หลังปรับให้พิมพ์ทดสอบแล้วยิงใส่ Notepad ต้องได้เลข 13 หลักครบ
    </aside>
    <main class="label">
        <div id="product-barcode" role="img" aria-label="EAN-13 barcode {{ $barcode }}"></div>
        <div class="barcode-digits">{{ substr($barcode, 0, 1) }} {{ substr($barcode, 1, 6) }} {{ substr($barcode, 7, 6) }}</div>
        <div class="name">{{ $product->name_th }}</div>
    </main>
    <p id="barcode-error" class="error">สร้างบาร์โค้ด EAN-13 ไม่สำเร็จ กรุณาลองเปิดหน้านี้ใหม่</p>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const value = @json($barcode);
            const leftPatterns = ['0001101','0011001','0010011','0111101','0100011','0110001','0101111','0111011','0110111','0001011'];
            const evenPatterns = ['0100111','0110011','0011011','0100001','0011101','0111001','0000101','0010001','0001001','0010111'];
            const rightPatterns = ['1110010','1100110','1101100','1000010','1011100','1001110','1010000','1000100','1001000','1110100'];
            const parity = ['LLLLLL','LLGLGG','LLGGLG','LLGGGL','LGLLGG','LGGLLG','LGGGLL','LGLGLG','LGLGGL','LGGLGL'];

            try {
                if (!/^\d{13}$/.test(value)) throw new Error('EAN-13 must contain thirteen digits');

                const leftDigits = value.slice(1, 7);
                const rightDigits = value.slice(7);
                let data = '101';
                for (let index = 0; index < leftDigits.length; index++) {
                    const digit = Number(leftDigits[index]);
                    data += parity[Number(value[0])][index] === 'L' ? leftPatterns[digit] : evenPatterns[digit];
                }
                data += '01010';
                for (const digit of rightDigits) data += rightPatterns[Number(digit)];
                data += '101';

                const quietZone = '0000000000';
                const modules = quietZone + data + quietZone;
                const bars = [];
                for (let index = 0; index < data.length; index++) {
                    if (data[index] !== '1') continue;
                    const isGuard = index < 3 || (index >= 45 && index < 50) || index >= 92;
                    bars.push(`<rect x="${index + quietZone.length}" y="0" width="1" height="${isGuard ? 62 : 56}"/>`);
                }
                document.getElementById('product-barcode').innerHTML =
                    `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${modules.length} 64" preserveAspectRatio="none" aria-hidden="true">${bars.join('')}</svg>`;
                document.getElementById('print-label').disabled = false;
            } catch (error) {
                document.getElementById('barcode-error').style.display = 'block';
                console.error('Could not generate EAN-13 barcode:', error);
            }

            document.getElementById('copy-number').addEventListener('click', async function () {
                const status = document.getElementById('copy-status');
                try {
                    await navigator.clipboard.writeText(value);
                    status.textContent = `คัดลอกเลข EAN-13 ${value} แล้ว — วางในช่องข้อมูลของบาร์โค้ด EAN-13 ใน GoLabel`;
                    status.style.color = '#15803d';
                } catch (error) {
                    const input = document.getElementById('ean13-number');
                    input.select();
                    status.textContent = 'เลือกเลขในช่องแล้ว กด Ctrl+C จากนั้นวางในช่องข้อมูล EAN-13 ของ GoLabel';
                    status.style.color = '#b91c1c';
                    console.error('Could not copy EAN-13 number:', error);
                }
            });
        });
    </script>
</body>
</html>
