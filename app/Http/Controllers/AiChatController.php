<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AiChatController extends Controller
{
    public function index()
    {
        // สรุปข้อมูลภาพรวมเบื้องต้นสำหรับหน้าแดชบอร์ด AI
        $lowStockCount = 0;
        try {
            $lowStockCount = DB::table('stock_balances')->where('on_hand_qty', '<', 0)->count();
        } catch (\Exception $e) {}

        $today = date('Y-m-d');
        $salesToday = 0;
        try {
            $salesToday = DB::table('pos_orders')->whereDate('created_at', $today)->sum('net_amount');
        } catch (\Exception $e) {}

        return view('ai-assistant.index', [
            'lowStockCount' => $lowStockCount,
            'salesToday' => $salesToday
        ]);
    }

    public function chat(Request $request)
    {
        // Support both old 'message' and new 'messages' format
        $request->validate([
            'message' => 'nullable|string',
            'messages' => 'nullable|array'
        ]);
        
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            // Fallback for when config is cached and env() returns null
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                if (preg_match('/^GEMINI_API_KEY=(.*)$/m', $envContent, $matches)) {
                    $apiKey = trim($matches[1]);
                    $apiKey = trim($apiKey, '"\''); 
                }
            }
        }
        
        if (!$apiKey) {
            return response()->json([
                'reply' => 'ยังไม่ได้ตั้งค่า GEMINI_API_KEY ในระบบครับ'
            ]);
        }

        // --- ดึงข้อมูลจริงจากระบบ ERP เพื่อป้อนเป็น Context ให้ AI ป้องกันการมโนข้อมูล ---
        $sysContext = "";
        try {
            // 1.1 สต๊อกติดลบ
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
                $sysContext .= "สถานะปัจจุบันไม่มีสินค้าสต๊อกติดลบเลย ";
            }

            // 1.2 สินค้าที่มีสต๊อกน้อยที่สุด (Lowest on-hand stock)
            $lowestStock = DB::table('stock_balances as sb')
                ->join('products as p', 'p.id', '=', 'sb.product_id')
                ->join('warehouse_locations as wl', 'wl.id', '=', 'sb.warehouse_location_id')
                ->orderBy('sb.on_hand_qty', 'asc')
                ->select('p.name_th', 'sb.on_hand_qty', 'wl.name as location_name')
                ->limit(8)
                ->get();
            if ($lowestStock->count() > 0) {
                $sysContext .= " | สินค้าที่มีสต๊อกน้อยที่สุด 8 อันดับแรก: ";
                foreach ($lowestStock as $item) {
                    $sysContext .= "{$item->name_th} ({$item->on_hand_qty} ชิ้น ที่ {$item->location_name}), ";
                }
            }

            // 1.3 ยอดรวมสินค้าและมูลค่าสต๊อกสินค้าทั้งหมด
            $totalProductsCount = DB::table('products')->count();
            $inventoryValuation = DB::table('stock_balances as sb')
                ->join('products as p', 'p.id', '=', 'sb.product_id')
                ->where('sb.on_hand_qty', '>', 0)
                ->sum(DB::raw('sb.on_hand_qty * CASE WHEN p.average_cost > 0 THEN p.average_cost ELSE COALESCE(p.last_purchase_cost, 0) END'));
            $totalStockQty = DB::table('stock_balances')->where('on_hand_qty', '>', 0)->sum('on_hand_qty');
            $sysContext .= " | สินค้าทั้งหมดในระบบ: {$totalProductsCount} รายการ | สต๊อกคงเหลือรวม: " . number_format($totalStockQty, 0) . " ชิ้น (มูลค่าต้นทุนสินค้าในคลังรวม: ฿" . number_format($inventoryValuation, 2) . ") ";

            // 2. ข้อมูลสรุปยอดขายหน้าร้าน (วันนี้, 7 วัน, 20 วัน, 30 วัน พร้อมต้นทุนและกำไรจริง)
            $today = date('Y-m-d');
            $salesToday = DB::table('pos_receipts')
                ->where('status', 'completed')
                ->whereDate('receipt_date', $today)
                ->sum('net_sales');
            $ordersTodayCount = DB::table('pos_receipts')
                ->where('status', 'completed')
                ->whereDate('receipt_date', $today)
                ->count();
            $sysContext .= " | สรุปยอดขายหน้าร้านวันนี้ ({$today}): ฿" . number_format($salesToday, 2) . " ({$ordersTodayCount} บิล) ";

            // ยอดขาย 7 วันล่าสุด
            $sevenDaysAgo = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $sales7Days = DB::table('pos_receipts')
                ->where('status', 'completed')
                ->where('receipt_date', '>=', $sevenDaysAgo)
                ->sum('net_sales');
            $orders7DaysCount = DB::table('pos_receipts')
                ->where('status', 'completed')
                ->where('receipt_date', '>=', $sevenDaysAgo)
                ->count();
            $sysContext .= " | ยอดขายรวม 7 วันล่าสุด: ฿" . number_format($sales7Days, 2) . " ({$orders7DaysCount} บิล) ";

            // ยอดขายและต้นทุน/กำไร ย้อนหลัง 20 วันล่าสุด
            $twentyDaysAgo = date('Y-m-d 00:00:00', strtotime('-20 days'));
            $costProfit20 = DB::table('pos_receipt_items as pri')
                ->join('pos_receipts as pr', 'pr.id', '=', 'pri.pos_receipt_id')
                ->leftJoin('products as p', 'p.id', '=', 'pri.product_id')
                ->where('pr.status', 'completed')
                ->where('pr.receipt_date', '>=', $twentyDaysAgo)
                ->select(
                    DB::raw('SUM(pri.net_amount) as total_revenue'),
                    DB::raw('COUNT(DISTINCT pr.id) as total_bills'),
                    DB::raw('SUM(pri.qty * CASE WHEN p.average_cost > 0 THEN p.average_cost ELSE COALESCE(p.last_purchase_cost, 0) END) as total_cost')
                )->first();
            $rev20 = (float)($costProfit20->total_revenue ?? 0);
            $cost20 = (float)($costProfit20->total_cost ?? 0);
            $profit20 = $rev20 - $cost20;
            $bills20 = (int)($costProfit20->total_bills ?? 0);
            $sysContext .= " | [สรุปยอดขาย 20 วันย้อนหลัง]: ยอดขายรวม ฿" . number_format($rev20, 2) . " ({$bills20} บิล), ต้นทุนสินค้าขาย (COGS): ฿" . number_format($cost20, 2) . ", กำไรขั้นต้น (Gross Profit): ฿" . number_format($profit20, 2) . " ";

            // ประวัติยอดขายรายวันย้อนหลัง 30 วัน (Daily Sales History)
            $thirtyDaysAgo = date('Y-m-d 00:00:00', strtotime('-30 days'));
            $dailySales = DB::table('pos_receipts')
                ->select(DB::raw('DATE(receipt_date) as sale_date'), DB::raw('SUM(net_sales) as total_sales'), DB::raw('COUNT(*) as total_bills'))
                ->where('status', 'completed')
                ->where('receipt_date', '>=', $thirtyDaysAgo)
                ->groupBy(DB::raw('DATE(receipt_date)'))
                ->orderBy('sale_date', 'desc')
                ->get();

            if ($dailySales->count() > 0) {
                $sysContext .= " | ประวัติยอดขายรายวัน (ย้อนหลัง 30 วันล่าสุด): ";
                $salesList = [];
                $total30Days = 0;
                $bills30Days = 0;
                foreach ($dailySales as $row) {
                    $salesList[] = "{$row->sale_date}: ฿" . number_format($row->total_sales, 2) . " ({$row->total_bills} บิล)";
                    $total30Days += $row->total_sales;
                    $bills30Days += $row->total_bills;
                }
                $sysContext .= implode(", ", $salesList) . " [รวม 30 วัน: ฿" . number_format($total30Days, 2) . " ({$bills30Days} บิล)] ";
            } else {
                $sysContext .= " | ยังไม่มีประวัติบิลขายในช่วง 30 วันที่ผ่านมา ";
            }

            // 3. สินค้าขายดีที่สุด 5 อันดับแรก (Top 5 Best-Selling Products)
            $topSelling = DB::table('pos_receipt_items as pri')
                ->join('products as p', 'p.id', '=', 'pri.product_id')
                ->select('p.name_th', DB::raw('SUM(pri.qty) as total_qty'), DB::raw('SUM(pri.net_amount) as total_revenue'))
                ->groupBy('pri.product_id', 'p.name_th')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();
            if ($topSelling->count() > 0) {
                $sysContext .= " | สินค้าขายดี 5 อันดับแรกตลอดกาล: ";
                $topList = [];
                foreach ($topSelling as $idx => $item) {
                    $rank = $idx + 1;
                    $topList[] = "อันดับ {$rank}. {$item->name_th} (ขายได้ " . number_format($item->total_qty) . " ชิ้น, ยอดรวม ฿" . number_format($item->total_revenue, 2) . ")";
                }
                $sysContext .= implode(", ", $topList) . " ";
            }

            // 4. สรุปสาขา (Branches)
            $branches = DB::table('branches')->select('id', 'name')->limit(10)->get();
            if ($branches->count() > 0) {
                $sysContext .= " | สาขาในระบบ: " . $branches->pluck('name')->implode(', ') . " ";
            }

            // 5. ใบสั่งซื้อ PO ล่าสุด
            $pendingPo = DB::table('purchase_orders')
                ->whereIn('status', ['draft', 'pending', 'approved'])
                ->orderBy('id', 'desc')
                ->limit(5)
                ->get(['id', 'po_number', 'supplier_name', 'total_amount', 'status']);
            if ($pendingPo->count() > 0) {
                $sysContext .= " | เอกสาร PO ล่าสุด: ";
                foreach ($pendingPo as $po) {
                    $sysContext .= "PO #{$po->po_number} ({$po->supplier_name} ฿" . number_format($po->total_amount, 2) . " สถานะ: {$po->status}), ";
                }
            }

        } catch (\Exception $e) {
            $sysContext .= "(ดึงข้อมูลสรุปเบื้องต้นได้บางส่วน: " . $e->getMessage() . ") ";
        }
        // ----------------------------------------------------------------

        // Build contents array for Gemini (เอาเฉพาะ 6 ข้อความล่าสุด เพื่อความเร็วและป้องกัน payload ใหญ่เกินไป)
        $contents = [];
        if ($request->has('messages') && is_array($request->messages)) {
            $rawMessages = array_slice($request->messages, -6);
            foreach ($rawMessages as $msg) {
                $text = trim($msg['content'] ?? '');
                if ($text === '') continue;
                // Map frontend roles to Gemini roles
                $role = (isset($msg['role']) && $msg['role'] === 'ai') ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $text]
                    ]
                ];
            }

            // กฎของ Gemini: ข้อความแรกใน contents ต้องเป็น role 'user'
            while (!empty($contents) && $contents[0]['role'] !== 'user') {
                array_shift($contents);
            }
        }

        // Fallback if empty or only 'message' is sent
        if (empty($contents)) {
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $request->message ?: 'สวัสดี']
                ]
            ];
        }

        $systemInstructionText = <<<EOT
คุณคือ "POP STAR AI" ผู้ช่วยอัจฉริยะและที่ปรึกษาการบริหาร ประจำระบบ ERP ของ บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด (ธุรกิจอาหารแช่แข็ง ชาบู หมูกระทะ วัตถุดิบขยายสาขา ค้าส่งและปลีก)

ขีดความสามารถและความฉลาดของคุณ (ตอบได้ครอบคลุมทุกเรื่อง):
1. **วิเคราะห์ยอดขายและประวัติย้อนหลัง**: คุณมีข้อมูลยอดขายจริงย้อนหลังถึง 30 วัน (รวมทั้ง 7 วัน, 20 วัน, 30 วัน), สินค้าขายดี 5 อันดับแรก, ยอดขายวันนี้ ให้สรุป ตัวเลข เปรียบเทียบแนวโน้ม และคำนวณกำไร/การเติบโตให้ผู้ใช้ได้ทันที อย่าปฏิเสธว่าไม่มีข้อมูล
2. **วิเคราะห์สต๊อกสินค้าและต้นทุน**: คุณมีข้อมูลสต๊อกคงเหลือรวม, มูลค่าต้นทุนสินค้าในคลังรวม, สินค้าสต๊อกติดลบ, สินค้าสต๊อกเหลือน้อย พร้อมให้คำแนะนำเรื่องการสั่งซื้อ (Reorder Point), การกระจายสต๊อกไปยังสาขาต่างๆ
3. **จัดซื้อ (Purchasing & POs)**: ดูสถานะใบสั่งซื้อ PO ล่าสุด แนะนำการวางแผนสั่งซื้อกับซัพพลายเออร์
4. **การบริหารจัดการและการใช้งานระบบ ERP**: ให้คำปรึกษาได้ทุกฟังก์ชัน ทั้งระบบ POS, การรับเข้า/เบิกออกสินค้า, การปรับปรุงสต๊อก, การโอนย้ายสินค้าระหว่างคลัง, การคำนวณต้นทุน FIFO/Moving Average
5. **วิเคราะห์และแนะนำเชิงธุรกิจ (Business Consultant)**: ช่วยคิดโปรโมชั่น จัดเซ็ตหมูกระทะ ชาบู วางแผนเพิ่มยอดขาย และลดการสูญเสียของอาหารแช่แข็ง

ข้อกำหนดสำคัญในการตอบและจัดทำรายงาน (เพื่อให้เอกสารรายงานออกมาเป็นทางการ น่าเชื่อถือ และได้มาตรฐานระดับผู้บริหาร):
๑. เมื่อผู้ใช้ขอรายงาน, สรุปยอดขาย, สรุปสต๊อกสินค้า, การเงิน หรือข้อมูลเชิงสถิติเพื่อนำไปทำรายงาน:
   - **ต้องจัดรูปแบบเป็น "รายงานบทวิเคราะห์เชิงบริหารที่เป็นทางการ (Official Executive Report)"** แบ่งหัวข้อชัดเจน 4 ส่วน:
     - **๑. สรุปสาระสำคัญเชิงบริหาร (Executive Summary)**: สรุปภาพรวม ตัวเลขภาพรวม และผลลัพธ์สำคัญสั้นกระชับ
     - **๒. ข้อมูลตัวเลขและผลการดำเนินงาน (Performance Data)**: **ต้องนำเสนอด้วยตาราง Markdown Table เสมอ** เช่น
       | ลำดับ | รายการ / หมวดหมู่ | จำนวน / หน่วย | ยอดขาย / มูลค่า (บาท) | สัดส่วน / สถานะ |
       | :---: | :--- | :---: | ---: | :---: |
       (ใส่ตัวเลขจริง มีหน่วยกำกับ และใช้ลูกน้ำคั่นหลักพันเสมอ เช่น 18,500.00 บาท)
     - **๓. การวิเคราะห์แนวโน้มและข้อสังเกตสำคัญ (Key Insights & Analysis)**: วิเคราะห์เชิงลึก สินค้าทำกำไร จุดที่ควรระวัง การหมุนเวียนของสินค้า
     - **๔. ข้อเสนอแนะเชิงกลยุทธ์และการปฏิบัติการ (Strategic Recommendations)**: ให้แนวทางปฏิบัติที่เป็นรูปธรรม ชัดเจน ตรงจุด
๒. ระดับภาษาและบุคลิก:
   - ใช้ภาษาทางการ สุภาพ ถูกต้อง หนักแน่น เหมาะสมกับเอกสารรายงานของคณะผู้บริหาร
   - แทนตัวเองว่า "POP STAR AI" หรือ "ระบบ"
   - หากเป็นการพูดคุยทั่วไปหรือถามคำถามสั้น ให้ตอบอย่างกระชับ สุภาพ และช่วยเหลืออย่างเต็มที่

บริบทข้อมูลจริงเรียลไทม์ในระบบตอนนี้:
{$sysContext}
EOT;

        // จัดลำดับโมเดลที่ตอบสนองเร็วและเสถียรที่สุดขึ้นก่อน (เช่น gemini-3.5-flash-lite ใช้เวลาเพียง ~1 วิ)
        $modelsToTry = ['gemini-3.5-flash-lite', 'gemini-3.1-flash-lite', 'gemini-3.5-flash', 'gemini-3.6-flash'];
        $lastError = null;

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])
                    ->timeout(8)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                        'systemInstruction' => [
                            'parts' => [
                                ['text' => $systemInstructionText]
                            ]
                        ],
                        'contents' => $contents
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'ไม่มีคำตอบจากระบบ';
                    
                    // สกัดข้อมูล Token Usage จาก Google Gemini
                    $usageMetadata = $data['usageMetadata'] ?? [];
                    $promptTokens = $usageMetadata['promptTokenCount'] ?? 0;
                    $candidatesTokens = $usageMetadata['candidatesTokenCount'] ?? 0;
                    $totalTokens = $usageMetadata['totalTokens'] ?? ($promptTokens + $candidatesTokens);
                    
                    // เพดานโควตา Context Window คือ 1,000,000 Tokens
                    $quotaLimit = 1000000;
                    $remainingTokens = max(0, $quotaLimit - $totalTokens);

                    return response()->json([
                        'reply' => $reply,
                        'tokens' => [
                            'prompt_tokens' => $promptTokens,
                            'completion_tokens' => $candidatesTokens,
                            'total_tokens' => $totalTokens,
                            'quota_limit' => $quotaLimit,
                            'remaining_tokens' => $remainingTokens
                        ]
                    ]);
                }

                $lastError = $response->body();
                \Log::warning("Gemini model {$model} returned status {$response->status()}: {$lastError}");
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                \Log::warning("Gemini model {$model} exception: {$lastError}");
            }
        }

        \Log::error('All Gemini Models Failed. Last Error: ' . $lastError);
        return response()->json([
            'reply' => 'ขออภัย ระบบ AI ขัดข้องชั่วคราว ไม่สามารถประมวลผลคำตอบได้ กรุณาลองใหม่อีกครั้ง'
        ], 500);
    }
}
