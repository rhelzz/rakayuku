<?php

if (!function_exists('formatRupiah')) {
    /**
     * Format number to Rupiah currency
     *
     * @param float|int $amount
     * @param bool $prefix
     * @return string
     */
    function formatRupiah($amount, $prefix = true)
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $prefix ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('parseRupiah')) {
    /**
     * Strip formatting from Rupiah string to get numeric value
     *
     * @param string $rupiah
     * @return float
     */
    function parseRupiah($rupiah)
    {
        return (float) str_replace(['Rp', '.', ' ', ','], '', $rupiah);
    }
}

if (!function_exists('formatQty')) {
    /**
     * Format quantity adaptively:
     * - Integer values display without decimals: 10
     * - Decimal values display with trimmed decimals: 10,5 or 10,75
     *
     * @param float|int $value
     * @return string
     */
    function formatQty($value): string
    {
        if ($value == intval($value)) {
            return number_format($value, 0, ',', '.');
        }
        return rtrim(rtrim(number_format($value, 2, ',', '.'), '0'), ',');
    }
}
