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

            // 1.3 ยอดรวมสินค้าและหมวดหมู่ทั้งหมด
            $totalProductsCount = DB::table('products')->count();
            $sysContext .= " | จำนวนรายการสินค้าทั้งหมดในระบบ: {$totalProductsCount} รายการ ";

            // 2. ข้อมูลสรุปภาพรวมวันนี้ (ยอดขาย, เอกสารรออนุมัติ, สินค้าขายดี)
            $today = date('Y-m-d');
            $salesToday = DB::table('pos_orders')
                ->whereDate('created_at', $today)
                ->sum('net_amount');
            $ordersTodayCount = DB::table('pos_orders')
                ->whereDate('created_at', $today)
                ->count();
            $sysContext .= " | สรุปยอดขายหน้าร้านวันนี้ ({$today}): ฿" . number_format($salesToday, 2) . " จากจำนวน {$ordersTodayCount} บิล ";

            // 3. ใบสั่งซื้อ PO ล่าสุด
            $pendingPo = DB::table('purchase_orders')
                ->whereIn('status', ['draft', 'pending', 'approved'])
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get(['id', 'po_number', 'supplier_name', 'total_amount', 'status']);
            if ($pendingPo->count() > 0) {
                $sysContext .= " | เอกสาร PO ล่าสุด: ";
                foreach ($pendingPo as $po) {
                    $sysContext .= "PO #{$po->po_number} ({$po->supplier_name} ฿" . number_format($po->total_amount, 2) . " สถานะ: {$po->status}), ";
                }
            }

        } catch (\Exception $e) {
            $sysContext .= "(ดึงข้อมูลสรุปเบื้องต้นได้บางส่วน) ";
        }
        // ----------------------------------------------------------------

        // Build contents array for Gemini
        $contents = [];
        if ($request->has('messages') && is_array($request->messages)) {
            foreach ($request->messages as $msg) {
                // Map frontend roles to Gemini roles
                $role = (isset($msg['role']) && $msg['role'] === 'ai') ? 'model' : 'user';
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $msg['content'] ?? '']
                    ]
                ];
            }
        } else {
            // Fallback if only 'message' is sent
            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $request->message ?? '']
                ]
            ];
        }

        $systemInstructionText = <<<EOT
คุณคือ "POP STAR AI" ผู้ช่วยอัจฉริยะประจำระบบ ERP ของ บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด (ดำเนินธุรกิจอาหารแช่แข็ง ชาบู หมูกระทะ วัตถุดิบขยายสาขา ค้าส่งและปลีก)

แนวทางการตอบ:
1. หากผู้ใช้ถามถึงข้อมูลจริงในระบบ (เช่น สต๊อกติดลบ, สต๊อกน้อยสุด, ยอดขายวันนี้, ใบสั่งซื้อ PO): ให้ตอบอ้างอิงจาก "บริบทข้อมูลจริงเรียลไทม์" ด้านล่างนี้เป็นหลัก
2. หากผู้ใช้ถามเรื่องขั้นตอนการทำงาน การโอนย้ายสต็อก การสร้างเอกสาร สูตรคำนวณต้นทุน (เช่น Moving Average หรือ FIFO) การใช้งานระบบ ERP หรือความรู้ทางธุรกิจ: สามารถอธิบาย แนะนำขั้นตอน และให้คำปรึกษาได้อย่างละเอียด ครบถ้วน และชัดเจนทันที โดยไม่ต้องปฏิเสธ
3. ตอบสั้น กระชับ สุภาพ เป็นมิตร จัดฟอร์แมตให้อ่านง่าย มีหัวข้อย่อยหรือ bullet points เมื่อเหมาะสม

บริบทข้อมูลจริงเรียลไทม์ในระบบตอนนี้:
{$sysContext}
EOT;

        $modelsToTry = ['gemini-3.5-flash', 'gemini-3.8-flash', 'gemini-3.5-flash-lite', 'gemini-3.6-flash', 'gemini-3.7-flash'];
        $lastError = null;

        foreach ($modelsToTry as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => $apiKey,
                    ])
                        ->timeout(20)
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
                    $statusCode = $response->status();
                    // If 503 (high demand) or 429, wait briefly before retrying
                    if ($statusCode === 503 || $statusCode === 429) {
                        usleep(300000); // 300ms
                    } else {
                        break; // don't retry if 400 or other fatal error
                    }
                } catch (\Exception $e) {
                    $lastError = $e->getMessage();
                    usleep(300000);
                }
            }
        }



        \Log::error('All Gemini Models Failed. Last Error: ' . $lastError);
        return response()->json([
            'reply' => 'ขออภัย ระบบ AI ขัดข้องชั่วคราว ไม่สามารถประมวลผลคำตอบได้ กรุณาลองใหม่อีกครั้ง'
        ], 500);
    }
}
