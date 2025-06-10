<?php

if (!function_exists('cart_count')) {
    function cart_count()
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}

if (!function_exists('format_price')) {
    function format_price($price, $currency = '€')
    {
        return number_format($price, 2, ',', ' ') . ' ' . $currency;
    }
}
