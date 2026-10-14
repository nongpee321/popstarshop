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
            $sysContext .= "(ไม่สามารถดึงข้อมูลระบบเพิ่มเติมได้) ";
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

        $modelsToTry = ['gemini-3.5-flash', 'gemini-3.8-flash'];
        $lastError = null;

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $apiKey,
                ])
                    ->timeout(20)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                        'systemInstruction' => [
                            'parts' => [
                                ['text' => 'คุณคือ POP STAR AI ผู้ช่วยอัจฉริยะประจำระบบ ERP ของ บริษัท ป๊อบสตาร์ฟู้ดเทรดดิ้ง จำกัด (ธุรกิจอาหารแช่แข็ง ชาบู หมูกระทะ ขยายสาขา ค้าส่งและปลีก) 
กฎสำคัญในการตอบ:
1. ตอบอ้างอิงจากข้อมูลจริงของระบบเท่านั้น ห้ามเดาหรือสมมติตัวเลขเองเด็ดขาด
2. ตอบสั้น กระชับ สุภาพ เป็นมิตร และตรงประเด็นที่สุด
3. บริบทข้อมูลจริงเรียลไทม์ในระบบตอนนี้คือ: ' . $sysContext]
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
                \Log::warning("Gemini API Error with model {$model}: " . $lastError);
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                \Log::warning("Gemini Connection Exception with model {$model}: " . $lastError);
            }
        }

        \Log::error('All Gemini Models Failed. Last Error: ' . $lastError);
        return response()->json([
            'reply' => 'ขออภัย ระบบ AI ขัดข้องชั่วคราว ไม่สามารถประมวลผลคำตอบได้ กรุณาลองใหม่อีกครั้ง'
        ], 500);
    }
}
