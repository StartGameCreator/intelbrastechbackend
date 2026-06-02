return [
    // ... mantenha os serviços padrões do arquivo (mail, ses, etc) e adicione no final do array:

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