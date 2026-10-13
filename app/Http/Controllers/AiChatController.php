<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class AiChatController extends Controller
{
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
        } catch (\Exception $e) {
            $sysContext .= "(ไม่สามารถดึงข้อมูลสต๊อกได้) ";
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

        try {
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(15)
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $apiKey, [
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => 'คุณคือผู้ช่วย AI ประจำระบบ ERP ของ POP STAR SHOP (ขายส่งอาหารแช่แข็ง หมูกระทะ ชาบู) กฎสำคัญในการตอบ: 1. ตอบอ้างอิงจากข้อมูลจริงของระบบเท่านั้น ห้ามเดาหรือสมมติข้อมูลตัวเลขเองเด็ดขาด 2. ตอบสั้น กระชับ ตรงประเด็นที่สุด 3. ข้อมูลเรียลไทม์ในระบบตอนนี้คือ: ' . $sysContext]
                        ]
                    ],
                    'contents' => $contents
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'ไม่มีคำตอบจากระบบ';
                return response()->json(['reply' => $reply]);
            }

            return response()->json([
                'reply' => 'ขออภัย เกิดข้อผิดพลาดจาก Google Gemini: ' . $response->body()
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'ขออภัย ระบบเครือข่ายขัดข้อง: ' . $e->getMessage()
            ], 500);
        }
    }
}
