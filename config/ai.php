<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver AI untuk Pembuat Soal
    |--------------------------------------------------------------------------
    | mock   = simulasi lokal, tanpa internet & tanpa API key (default)
    | gemini = Google Gemini (butuh GEMINI_API_KEY pada .env)
    | groq   = Groq / Llama (butuh GROQ_API_KEY pada .env)
    |
    */

    'driver' => env('AI_DRIVER', 'mock'),

    // Batas maksimal soal per permintaan (kontrol biaya & beban API)
    'max_per_request' => (int) env('AI_MAX_PER_REQUEST', 12),

    // Timeout request ke provider AI (detik)
    'timeout' => (int) env('AI_TIMEOUT', 45),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY', ''),
        'model' => env('GROQ_MODEL', 'openai/gpt-oss-120b'),
    ],

];
