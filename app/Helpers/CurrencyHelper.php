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
