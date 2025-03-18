<?php
/**
 * General helper functions for WebSchedulr
 */

if (!function_exists('asset_url')) {
    /**
     * Generate URL for assets
     * 
     * @param string $path Asset path
     * @return string Full asset URL
     */
    function asset_url($path) {
        $baseUrl = Config::BASE_URL;
        return rtrim($baseUrl, '/') . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('site_url')) {
    /**
     * Generate URL for site pages
     * 
     * @param string $path Site path
     * @return string Full site URL
     */
    function site_url($path = '') {
        $baseUrl = Config::BASE_URL;
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another URL
     * 
     * @param string $url URL to redirect to
     * @return void
     */
    function redirect($url) {
        header('Location: ' . site_url($url));
        exit;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date
     * 
     * @param string|int $date Date string or timestamp
     * @param string $format Format string
     * @return string Formatted date
     */
    function format_date($date, $format = 'M j, Y') {
        if (is_numeric($date)) {
            $timestamp = $date;
        } else {
            $timestamp = strtotime($date);
        }
        
        return date($format, $timestamp);
    }
}

if (!function_exists('format_time')) {
    /**
     * Format a time
     * 
     * @param string|int $time Time string or timestamp
     * @param string $format Format string
     * @return string Formatted time
     */
    function format_time($time, $format = 'g:i A') {
        if (is_numeric($time)) {
            $timestamp = $time;
        } else {
            $timestamp = strtotime($time);
        }
        
        return date($format, $timestamp);
    }
}

if (!function_exists('format_money')) {
    /**
     * Format an amount as money
     * 
     * @param float $amount Amount to format
     * @param string $currency Currency code
     * @return string Formatted amount
     */
    function format_money($amount, $currency = 'ZAR') {
        $symbol = 'R';
        
        switch ($currency) {
            case 'USD':
                $symbol = '$';
                break;
            case 'EUR':
                $symbol = '€';
                break;
            case 'GBP':
                $symbol = '£';
                break;
        }
        
        return $symbol . ' ' . number_format($amount, 2);
    }
}
