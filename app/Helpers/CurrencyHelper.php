<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Moneda principal del sistema
     */
    public static function getCurrency(): string
    {
        return 'MXN';
    }

    /**
     * Locale para formateo de moneda
     */
    public static function getLocale(): string
    {
        return 'es_MX';
    }

    /**
     * Símbolo de la moneda
     */
    public static function getCurrencySymbol(): string
    {
        return '$';
    }

    /**
     * Formatea un valor numérico como moneda mexicana
     */
    public static function format(float $amount): string
    {
        return '$' . number_format($amount, 2, '.', ',');
    }

    /**
     * Formatea un valor con el formato completo de pesos mexicanos
     */
    public static function formatWithCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2, '.', ',') . ' MXN';
    }

    /**
     * Convierte string a float limpiando formato de moneda
     */
    public static function parseAmount(string $value): float
    {
        // Remover símbolos de moneda y comas
        $cleaned = preg_replace('/[^\d.]/', '', $value);
        return (float) $cleaned;
    }
}
