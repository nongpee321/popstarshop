import re
file_path = r"d:\pop-erp-food\ERPPOP-main\app\Http\Controllers\AiChatController.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# I need to add DB facade and inject context
if "use Illuminate\Support\Facades\DB;" not in content:
    content = content.replace("use Illuminate\Support\Facades\Http;", "use Illuminate\Support\Facades\Http;\nuse Illuminate\Support\Facades\DB;")

injection_code = """        if (!$apiKey) {
            return response()->json([
                'reply' => 'ยังไม่ได้ตั้งค่า GEMINI_API_KEY ในระบบครับ'
            ]);
        }

        // --- ดึงข้อมูลจริงจากระบบ ERP เพื่อป้อนเป็น Context ให้ AI ป้องกันการมโนข้อมูล ---
        $sysContext = "";
        try {
            // 1. สต๊อกติดลบ
            $lowStock = DB::table('stock_balances as sb')
                ->join('products as p', 'p.id', '=', 'sb.product_id')
                ->join('warehouse_locations as wl', 'wl.id', '=', 'sb.warehouse_location_id')
                ->where('sb.on_hand_qty', '<', 0)
                ->orderBy('sb.on_hand_qty')
                ->select('p.name_th', 'sb.on_hand_qty', 'wl.name as location_name')
                ->limit(10)
                ->get();
                
            if ($lowStock->count() > 0) {
                $sysContext .= "สถานะสต๊อกติดลบปัจจุบันมี " . $lowStock->count() . " รายการ (อันดับต้นๆ): ";
                foreach ($lowStock as $item) {
                    $sysContext .= "{$item->name_th} ({$item->on_hand_qty} ชิ้น ที่ {$item->location_name}), ";
                }
            } else {
                $sysContext .= "สถานะปัจจุบันไม่มีสต๊อกติดลบเลย ";
            }
        } catch (\Exception $e) {
            $sysContext .= "(ไม่สามารถดึงข้อมูลสต๊อกได้) ";
        }
        // ----------------------------------------------------------------"""

content = content.replace("""        if (!$apiKey) {
            return response()->json([
                'reply' => 'ยังไม่ได้ตั้งค่า GEMINI_API_KEY ในระบบครับ'
            ]);
        }""", injection_code)

old_system_prompt = "'text' => 'คุณคือผู้ช่วย AI ประจำระบบ ERP ของ POP STAR SHOP (ขายส่งอาหารแช่แข็ง หมูกระทะ ชาบู) กฎสำคัญในการตอบ: 1. ตอบเฉพาะเรื่องที่เกี่ยวกับ \"ระบบ ERP, สต๊อกสินค้า, การขาย และ POP STAR SHOP\" เท่านั้น 2. บังคับให้ \"ตอบสั้น กระชับ ตรงประเด็นที่สุด\" อ่านแล้วเข้าใจทันที ห้ามร่ายยาวเด็ดขาด 3. ถ้านอกเรื่องให้ปฏิเสธสั้นๆ ว่า \"ขออภัยครับ ตอบได้เฉพาะเรื่องระบบและบริษัทเท่านั้น\"'"
new_system_prompt = "'text' => 'คุณคือผู้ช่วย AI ประจำระบบ ERP ของ POP STAR SHOP (ขายส่งอาหารแช่แข็ง หมูกระทะ ชาบู) กฎสำคัญในการตอบ: 1. ตอบอ้างอิงจากข้อมูลจริงของระบบเท่านั้น ห้ามเดาหรือสมมติข้อมูลตัวเลขเองเด็ดขาด 2. ตอบสั้น กระชับ ตรงประเด็นที่สุด 3. ข้อมูลเรียลไทม์ในระบบตอนนี้คือ: ' . $sysContext"

content = content.replace(old_system_prompt, new_system_prompt)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Injected real DB context into AiChatController.")
