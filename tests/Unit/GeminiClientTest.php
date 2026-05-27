<?php

namespace Tests\Unit;

use App\Services\AI\GeminiClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

class GeminiClientTest extends TestCase
{
    public function test_it_sanitizes_gemini_request_errors(): void
    {
        config()->set('ai.gemini.api_key', 'SECRET_KEY_SHOULD_NOT_LEAK');
        config()->set('ai.gemini.model', 'gemini-1.5-flash');

        $request = new Request(
            'POST',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=SECRET_KEY_SHOULD_NOT_LEAK'
        );

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('post')
            ->once()
            ->andThrow(RequestException::create($request, new Response(403)));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Gemini request failed with HTTP status 403.');

        try {
            (new GeminiClient($client))->generateText('Xin chào');
        } catch (\RuntimeException $exception) {
            $this->assertStringNotContainsString('SECRET_KEY_SHOULD_NOT_LEAK', $exception->getMessage());
            $this->assertStringNotContainsString('?key=', $exception->getMessage());
            throw $exception;
        }
    }
}
