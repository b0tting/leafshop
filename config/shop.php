<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default session "driver" that will be used on
    | requests. By default, we will use the lightweight native driver but
    | you may specify any of the other wonderful drivers provided here.
    |
    | Supported: "file", "cookie", "database", "apc",
    |            "memcached", "redis", "array"
    |
    */

    'shopmail' => env('SHOP_MAIL', 'info@shop.text'),
    'shopcontactmail' => env('SHOP_CONTACTMAIL', 'info@shop.text'),
    'iban' => env('SHOP_IBAN', '123123'),
    'bic' => env('SHOP_BIC', '123123'),
    'bank' => env('SHOP_BANK', 'big bank'),
    'bankname' => env('SHOP_NAME', '123123'),
    'billaddress' => env('SHOP_ADDRESS', '123123'),
];