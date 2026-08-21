<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This option controls the default currency used throughout the application.
    | Currently set to PKR (Pakistani Rupee) as per requirements.
    |
    */

    'default' => env('CURRENCY_DEFAULT', 'PKR'),

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Currency-specific settings including symbols, formatting, and locale.
    |
    */

    'currencies' => [
        'PKR' => [
            'symbol' => 'PKR',
            'position' => 'before', // 'before' or 'after'
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'decimals' => 2,
            'locale' => 'en_PK',
        ],
        'USD' => [
            'symbol' => '$',
            'position' => 'before',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'decimals' => 2,
            'locale' => 'en_US',
        ],
        'GBP' => [
            'symbol' => '£',
            'position' => 'before',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'decimals' => 2,
            'locale' => 'en_GB',
        ],
        'EUR' => [
            'symbol' => '€',
            'position' => 'before',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'decimals' => 2,
            'locale' => 'de_DE',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Distance Unit
    |--------------------------------------------------------------------------
    |
    | Default distance unit for the application. Set to Kilometers for Pakistan.
    |
    */

    'distance_unit' => env('DISTANCE_UNIT', 'Kilometers'),
];