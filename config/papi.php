<?php
// ============================================================
// config/papi.php
// ============================================================
return [
    'api_key'      => env('PAPI_API_KEY'),
    'api_url'      => env('PAPI_API_URL', 'https://app.papi.mg'),
    'success_url'  => env('PAPI_SUCCESS_URL'),
    'failure_url'  => env('PAPI_FAILURE_URL'),
    'callback_url' => env('PAPI_CALLBACK_URL'),
];

