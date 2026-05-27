<?php

namespace App\Services\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class GeminiClient
{
    protected Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?: new Client([
            'timeout' => (int) config('ai.moderation.timeout', 20),
        ]);
    }

    /**
     * @param  string  $prompt
     * @return string
     */
    public function generateJson(string $prompt): string
    {
        $apiKey = (string) config('ai.gemini.api_key');
        if ($apiKey === '') {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        $model = trim((string) config('ai.gemini.model', 'gemini-1.5-flash'));
        $baseUri = trim((string) config('ai.gemini.base_uri', 'https://generativelanguage.googleapis.com/v1beta/'));
        if ($baseUri === '') {
            $baseUri = 'https://generativelanguage.googleapis.com/v1beta/';
        }

        $endpoint = rtrim($baseUri, '/') . '/models/' . rawurlencode($model) . ':generateContent';

        try {
            $response = $this->client->post($endpoint, [
                'query' => ['key' => $apiKey],
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topP' => 0.1,
                        'responseMimeType' => 'application/json',
                    ],
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Gemini request failed: ' . $e->getMessage(), 0, $e);
        }

        $body = json_decode((string) $response->getBody(), true);
        $text = data_get($body, 'candidates.0.content.parts.0.text');
        if (!is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini response did not include JSON text.');
        }

        return trim($text);
    }
}
