<?php

return [
    'api_key'       => env('DEV-I2nRxqZX4WE3IwdGAaD4OxVivJMjpmVX28z5gEue'),
    'private_key'   => env('C7CSX-ds62g-l2IXl-KqYbx-w5YcE'),
    'merchant_code' => env('T24264'),
    'is_production'  => env('TRIPAY_IS_PRODUCTION', false),
    'api_url'       => env('TRIPAY_IS_PRODUCTION', false) 
                        ? 'https://tripay.co.id/api/' 
                        : 'https://tripay.co.id/api-sandbox/',
];