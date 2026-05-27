<?php

namespace App\Services\AI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
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
        return $this->generateContent($prompt, true);
    }

    /**
     * @param  string  $prompt
     * @return string
     */
    public function generateText(string $prompt): string
    {
        return $this->generateContent($prompt, false);
    }

    /**
     * @param  string  $prompt
     * @param  bool  $jsonResponse
     * @return string
     */
    protected function generateContent(string $prompt, bool $jsonResponse): string
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

        $generationConfig = [
            'temperature' => $jsonResponse ? 0.1 : 0.3,
            'topP' => $jsonResponse ? 0.1 : 0.8,
        ];

        if ($jsonResponse) {
            $generationConfig['responseMimeType'] = 'application/json';
        }

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
                    'generationConfig' => $generationConfig,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new RuntimeException($this->sanitizeExceptionMessage($e), 0, $e);
        }

        $body = json_decode((string) $response->getBody(), true);
        $text = data_get($body, 'candidates.0.content.parts.0.text');
        if (!is_string($text) || trim($text) === '') {
            throw new RuntimeException('Gemini response did not include JSON text.');
        }

        return trim($text);
    }

    protected function sanitizeExceptionMessage(GuzzleException $e): string
    {
        if ($e instanceof RequestException && $e->hasResponse()) {
            return 'Gemini request failed with HTTP status ' . $e->getResponse()->getStatusCode() . '.';
        }

        return 'Gemini request failed before receiving a valid response.';
    }
}
