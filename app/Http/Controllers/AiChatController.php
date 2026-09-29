<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                            ['text' => 'คุณคือผู้ช่วย AI ประจำระบบ ERP ของ POP STAR SHOP (ขายส่งอาหารแช่แข็ง หมูกระทะ ชาบู) กฎสำคัญในการตอบ: 1. ตอบเฉพาะเรื่องที่เกี่ยวกับ "ระบบ ERP, สต๊อกสินค้า, การขาย และ POP STAR SHOP" เท่านั้น 2. บังคับให้ "ตอบสั้น กระชับ ตรงประเด็นที่สุด" อ่านแล้วเข้าใจทันที ห้ามร่ายยาวเด็ดขาด 3. ถ้านอกเรื่องให้ปฏิเสธสั้นๆ ว่า "ขออภัยครับ ตอบได้เฉพาะเรื่องระบบและบริษัทเท่านั้น"']
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
