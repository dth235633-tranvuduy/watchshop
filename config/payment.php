<?php

return [
    'bank' => [
        'bank_id' => env('BANK_ID', 'MB'),
        'account_no' => env('BANK_ACCOUNT_NO', ''),
        'account_name' => env('BANK_ACCOUNT_NAME', ''),
        'template' => env('BANK_QR_TEMPLATE', 'compact2'),
    ],
];
