<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Branding
    |--------------------------------------------------------------------------
    |
    | Configure your application's name, logo, and branding elements.
    |
    */

    'org_name' => env('APP_BRAND_ORG_NAME', 'CUH'),

    'product_name' => env('APP_BRAND_PRODUCT_NAME', 'Asset Management System'),

    'short_name' => env('APP_BRAND_SHORT_NAME', 'CUH'),

    'logo' => env('APP_BRAND_LOGO', null), // Path to logo file in public/storage or public/images

    'logo_alt' => env('APP_BRAND_LOGO_ALT', 'Logo'),

    'favicon' => env('APP_BRAND_FAVICON', null), // Path to favicon

    'show_logo_in_sidebar' => env('APP_BRAND_SHOW_LOGO_SIDEBAR', true),

    'show_logo_in_navbar' => env('APP_BRAND_SHOW_LOGO_NAVBAR', false),

];
