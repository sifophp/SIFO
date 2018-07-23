<?php

function twig_filter_currency()
{
    return new \Twig_Filter(
        'currency', function (
        $amount,
        $currency = 'EUR',
        $tag = ''
    ) {
        $currency_symbols = array(
            'EUR' => '€',
            'GBP' => '£',
            'USD' => '$'
        );

        $currency_symbol = $currency;

        if (array_key_exists($currency, $currency_symbols))
        {
            $currency_symbol = $currency_symbols[$currency];
        }

        if (!empty($tag))
        {
            $currency_symbol = '<' . $tag . '>' . $currency_symbol . '</' . $tag . '>';
        }

        switch ($currency)
        {
            case 'USD':
            case 'GBP':
            {
                return $currency_symbol . number_format($amount, 2);
            }

            default:
            {
                return number_format($amount, 2, ',', '.') . $currency_symbol;
            }
        }
    }
    );
}
