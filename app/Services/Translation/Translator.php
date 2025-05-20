<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Translator
{
    public static function translate(string $text, string $targetLang): string
    {
        $response = Http::asForm()->post(config('services.deepl.api') . '/v2/translate', [
            'auth_key' => config('services.deepl.api_key'),
            'text' => $text,
            'target_lang' => strtoupper($targetLang),
        ]);

        if ($response->ok()) {
            return $response->json()['translations'][0]['text'] ?? $text;
        }

        return $text;
    }
}