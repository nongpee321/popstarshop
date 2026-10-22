<?php

namespace App\Http\Middleware;

use App\Support\RoutePermissions;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Single gate for the whole ERP: guests go to the login page, disabled
 * accounts are logged out, and each module route requires its mapped
 * permission (see RoutePermissions). Runs on every web request.
 */
class ErpAuthorize
{
    private const PUBLIC_ROUTES = [
        'home',
        'login', 'login.attempt', 'logout', 'pos.download', 'python-pos.download',
        'pos.release.latest', 'pos.release.download',
        'pos.activate-token',
        'mfa.challenge', 'mfa.verify',
        'portal',
        'manual',
        'settings.preview',
        'settings.module-preview',
        'settings.pos-designer-preview',
        'settings.receipt-preview',
        'settings.workflows-preview',
        'settings.operations-preview',
        'users.preview',
        'finance.preview',
        'pos.user-preview',
        'products.preview',
        'inventory.preview',
        'sales.preview',
        'sales.preview.documents',
        'sales.preview.quotations',
        'sales.preview.bookings',
        'sales.preview.cash-sales',
        'sales.preview.sale-returns',
        'sales.preview.billing-notes',
        'sales.preview.credit-debit-notes',
        'sales.preview.crm',
        'sales.preview.pipeline',
        'master-data.preview',
        'master-data.preview.master-data-setup',
        'master-data.preview.customers',
        'master-data.preview.suppliers',
        'master-data.preview.members',
        'master-data.preview.warehouse-locations',
        'master-data.preview.price-tables',
        'master-data.preview.scale-prices',
        'master-data.preview.promotions',
        'master-data.preview.flash-sales',
        'master-data.preview.price-tags',
        'master-data.preview.discount-cards',
        'master-data.preview.member-points',
        'master-data.preview.qty-promotions',
        'purchasing.preview.purchase-orders',
        'purchasing.preview.stock-issues-req',
        'purchasing.preview.stock-issues-ret',
        'purchasing.preview.stock-issues-dam',
        'purchasing.preview.production',
        'purchasing.preview.low-stock',
        'reports.preview.sales',
        'reports.preview.purchasing',
        'reports.preview.inventory',
        'reports.preview.payment',
        'reports.preview.tax',
        'reports.preview.legacy',
        'integrations.preview.line',
        'overview.preview.dashboard',
        'overview.preview.controls',
    ];

    private const PASSWORD_CHANGE_ROUTES = ['password.change', 'password.update'];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if (in_array($routeName, self::PUBLIC_ROUTES, true)) {
            return $next($request);
        }

        // ตรวจสอบ POS Device Token (Magic Link หรือ Session ประจำเครื่อง)
        $posToken = $request->query('pos_token') ?: $request->query('token') ?: $request->cookie('pos_device_token');
        if ($posToken && str_starts_with($routeName ?? '', 'pos.')) {
            $device = \App\Models\PosDevice::with(['user', 'branch'])
                ->where('token_hash', \App\Models\PosDevice::hashToken($posToken))
                ->first();

            if ($device && $device->isActive() && $device->user && $device->user->is_active) {
                if (! Auth::check() || Auth::id() !== $device->user_id) {
                    Auth::login($device->user);
                }
                $request->attributes->set('pos_device', $device);
                $device->forceFill([
                    'last_seen_at' => now(),
                    'last_ip' => $request->ip(),
                ])->saveQuietly();

                // จำ token ลง cookie ประจำเครื่องนี้ 365 วัน (httpOnly=false เพื่อให้ JS/Tauri เข้าถึงได้ถ้าจำเป็น)
                \Illuminate\Support\Facades\Cookie::queue('pos_device_token', $posToken, 60 * 24 * 365);
            }
        }

        $user = Auth::user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'กรุณาเข้าสู่ระบบ'], 401)
                : redirect()->guest(route('login'));
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['username' => 'บัญชีนี้ถูกปิดใช้งาน ติดต่อผู้ดูแลระบบ']);
        }

        // ยกเลิกการบังคับเปลี่ยนรหัสผ่านตามที่ผู้ใช้ร้องขอ
        // if ($user->must_change_password && ! in_array($routeName, self::PASSWORD_CHANGE_ROUTES, true)) {
        //     return redirect()->route('password.change');
        // }

        $permission = RoutePermissions::resolve($routeName);
        if ($permission && ! $user->hasPermission($permission)) {
            // ถ้าเป็นพนักงานแคชเชียร์และหลุดไปเข้าหน้าอื่นของ ERP ให้ส่งกลับมาที่หน้า /pos
            if ($user->hasPermission('pos.sell') && ! str_starts_with($routeName ?? '', 'pos.')) {
                return redirect()->route('pos.index')->with('warning', 'คุณมีสิทธิ์เข้าใช้งานเฉพาะระบบขายหน้าร้าน (POS) เท่านั้น');
            }
            abort(403, 'บัญชีของคุณไม่มีสิทธิ์ใช้เมนูนี้ ติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์เพิ่ม');
        }

        // หากผู้ใช้มีเฉพาะสิทธิ์ POS และพยายามเข้าหน้า Dashboard หรือเมนูอื่นๆ ที่ไม่มีสิทธิ์เฉพาะ
        if ($routeName === 'dashboard' && $user->hasPermission('pos.sell') && ! $user->hasPermission('settings.manage') && ! $user->hasPermission('reports.view') && ! $user->hasPermission('accounting.view')) {
            return redirect()->route('pos.index');
        }

        return $next($request);
    }
}
