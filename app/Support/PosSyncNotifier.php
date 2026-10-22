<?php

namespace App\Support;

use App\Models\AppSetting;

class PosSyncNotifier
{
    public const SETTING_KEY = 'pos_price_catalog_version';

    /**
     * Notify that prices, promotions, or flash sales have changed.
     * Updates catalog version timestamp.
     */
    public static function notifyChange(): void
    {
        AppSetting::set(self::SETTING_KEY, (string) now()->timestamp);
    }

    /**
     * Get current catalog version timestamp.
     */
    public static function getVersion(): int
    {
        return (int) (AppSetting::get(self::SETTING_KEY, '0') ?: '0');
    }
}
