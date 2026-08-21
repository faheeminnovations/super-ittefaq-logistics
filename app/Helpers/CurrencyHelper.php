<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class CurrencyHelper
{
    /**
     * Format amount with PKR currency symbol
     */
    public static function formatPKR($amount)
    {
        return 'PKR ' . number_format($amount, 2);
    }

    /**
     * Format amount specifically as PKR (ensures PKR is always used)
     */
    public static function formatAsPKR($amount)
    {
        return 'PKR ' . number_format((float)$amount, 2);
    }

    /**
     * Format amount specifically as pkr (lowercase version)
     */
    public static function formatAsPKRLower($amount)
    {
        return 'pkr ' . number_format((float)$amount, 2);
    }

    /**
     * Get currency symbol based on currency code
     */
    public static function getCurrencySymbol($currency = null)
    {
        $currency = $currency ?? self::getCurrentCurrency();
        $currencies = Config::get('currency.currencies', []);
        
        return $currencies[$currency]['symbol'] ?? $currency;
    }

    /**
     * Format amount with specific currency
     */
    public static function formatCurrency($amount, $currency = null)
    {
        // Force PKR for this logistics application
        return self::formatAsPKR($amount);
        
        /* Original code kept for reference if needed in future
        $currency = $currency ?? self::getCurrentCurrency();
        $settings = Config::get("currency.currencies.{$currency}", []);
        
        if (empty($settings)) {
            return $currency . ' ' . number_format($amount, 2);
        }
        
        $symbol = $settings['symbol'] ?? $currency;
        $decimals = $settings['decimals'] ?? 2;
        $position = $settings['position'] ?? 'before';
        $formatted = number_format($amount, $decimals, 
            $settings['decimal_separator'] ?? '.', 
            $settings['thousands_separator'] ?? ','
        );
        
        if ($position === 'before') {
            return $symbol . ' ' . $formatted;
        }
        
        return $formatted . ' ' . $symbol;
        */
    }

    /**
     * Get current currency from settings or config
     */
    public static function getCurrentCurrency()
    {
        // Try to get from database settings first
        try {
            $setting = \DB::table('settings')->first();
            if ($setting && !empty($setting->currency)) {
                return $setting->currency;
            }
        } catch (\Exception $e) {
            // If database query fails, fall back to config
        }
        
        // Fall back to config
        return Config::get('currency.default', 'PKR');
    }

    /**
     * Get current distance unit
     */
    public static function getDistanceUnit()
    {
        // Try to get from database settings first
        try {
            $setting = \DB::table('settings')->first();
            if ($setting && !empty($setting->distance_unit)) {
                return $setting->distance_unit;
            }
        } catch (\Exception $e) {
            // If database query fails, fall back to config
        }
        
        // Fall back to config
        return Config::get('currency.distance_unit', 'Kilometers');
    }
}