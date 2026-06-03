<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serviços de Terceiros (Configurações Core)
    |--------------------------------------------------------------------------
    |
    | Este arquivo serve para centralizar as chaves de API e credenciais
    | de todos os serviços externos consumidos pelo IntelbrasTech.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // --- MÓDULOS EXPANDIDOS INTELBRASTECH ---

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI'),
        'tenant' => env('MICROSOFT_TENANT_ID', 'common'),
    ],
    
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'access_token' => env('FIREBASE_ACCESS_TOKEN'),
    ],

];