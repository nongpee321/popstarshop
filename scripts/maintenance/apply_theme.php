<?php
$file = dirname(__DIR__, 2).'/resources/views/pos/index.blade.php';
$content = file_get_contents($file);

// Replace Root variables
$content = preg_replace('/:root\s*\{[^}]+\}/s', ':root {
    --pos-bg: #f3f4f6;
    --pos-panel: #ffffff;
    --pos-panel-2: #f9fafb;
    --pos-card: #ffffff;
    --pos-card-2: #f9fafb;
    --pos-border: #e5e7eb;
    --pos-text: #111827;
    --pos-muted: #6b7280;
    --pos-green: #10b981;
    --pos-blue: #3b82f6;
    --pos-red: #ef4444;
    --pos-amber: #f59e0b;
    --pos-cyan: #06b6d4;
}', $content);

// Replace all hardcoded dark backgrounds
$content = str_replace('rgba(17,28,46,.94)', '#ffffff', $content);
$content = str_replace('rgba(17,28,46,.96)', '#ffffff', $content);
$content = str_replace('rgba(17,28,46,.82)', '#ffffff', $content);
$content = str_replace('rgba(7,17,31,.55)', '#f3f4f6', $content);
$content = str_replace('rgba(7,17,31,.42)', '#f9fafb', $content);
$content = str_replace('rgba(15,23,42,.44)', '#ffffff', $content);
$content = str_replace('rgba(30,41,59,.42)', '#f9fafb', $content);
$content = str_replace('rgba(15,23,42,.35)', '#f9fafb', $content);
$content = str_replace('rgba(15,23,42,.7)', '#ffffff', $content);
$content = str_replace('rgba(15,23,42,.75)', '#ffffff', $content);
$content = str_replace('rgba(5,10,20,.7)', 'rgba(0,0,0,0.5)', $content);

// Cart Header gradient
$content = preg_replace('/background:\s*linear-gradient\(180deg,\s*#0d1b2f,\s*#0a1424\);/i', 'background: #f9fafb;', $content);

// Product Card gradient
$content = preg_replace('/background:\s*linear-gradient\(180deg,\s*rgba\(32,49,73,\.98\),\s*rgba\(24,38,59,\.98\)\);/i', 'background: #ffffff;', $content);

// Text colors that were light
$content = str_replace('color: #f1f5f9;', 'color: #111827;', $content);
$content = str_replace('color: #e2e8f0;', 'color: #374151;', $content);
$content = str_replace('color: #93c5fd;', 'color: #6b7280;', $content);
$content = str_replace('color: #dbeafe;', 'color: #111827;', $content);
$content = str_replace('color: #f8fafc;', 'color: #111827;', $content);
$content = str_replace('color: #0f172a;', 'color: #111827;', $content);

// Subtle backgrounds
$content = str_replace('background: rgba(148,163,184,.16);', 'background: #e5e7eb;', $content);
$content = str_replace('background: rgba(148,163,184,.18);', 'background: #e5e7eb;', $content);

// Border colors
$content = str_replace('border: 1.5px solid rgba(148,163,184,.18);', 'border: 1px solid #e5e7eb;', $content);

// Box shadows
$content = preg_replace('/box-shadow:\s*0\s+10px\s+30px\s+rgba\(2,8,23,\.28\);/i', 'box-shadow: 0 1px 3px rgba(0,0,0,0.1);', $content);
$content = preg_replace('/box-shadow:\s*0\s+18px\s+44px\s+rgba\(2,8,23,\.30\);/i', 'box-shadow: 0 4px 6px rgba(0,0,0,0.1);', $content);
$content = preg_replace('/box-shadow:\s*0\s+18px\s+44px\s+rgba\(2,8,23,\.22\);/i', 'box-shadow: 0 4px 6px rgba(0,0,0,0.1);', $content);
$content = preg_replace('/box-shadow:\s*0\s+10px\s+24px\s+rgba\(2,8,23,\.20\);/i', 'box-shadow: 0 1px 2px rgba(0,0,0,0.05);', $content);
$content = preg_replace('/box-shadow:\s*0\s+-16px\s+38px\s+rgba\(2,8,23,\.32\);/i', 'box-shadow: 0 -4px 6px rgba(0,0,0,0.05);', $content);

// Action buttons
$content = preg_replace('/background:\s*linear-gradient\(135deg,#475569,#334155\);/i', 'background: #f3f4f6; color: #111827;', $content);

// Fix product SKU color
$content = preg_replace('/\.product-sku \{\s*font-size:\s*10\.5px;\s*color:\s*#6b7280;/i', '.product-sku { font-size: 11px; color: #6b7280;', $content);

// Ensure the body background is correctly mapped to our variable
$content = preg_replace('/body\s*\{\s*font-family:[^}]+}/', "body {\n            font-family: var(--pos-ui-font);\n            background: var(--pos-bg);\n            color: var(--pos-text);\n            font-size: 14px;\n        }", $content);

// Write back
file_put_contents($file, $content);
echo "Modern Light Theme Applied successfully!";
