<?php

namespace App\Http\Controllers;

use App\Services\AI\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected ChatbotService $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'page_type' => ['required', 'string', 'in:home,posts_index,post_detail,course_detail,generic'],
            'page_ref' => ['nullable', 'string', 'max:255'],
            'history' => ['nullable', 'array', 'max:6'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:1000'],
        ]);

        $result = $this->chatbotService->reply(
            (string) $validated['message'],
            (string) $validated['page_type'],
            isset($validated['page_ref']) ? (string) $validated['page_ref'] : null,
            $validated['history'] ?? []
        );

        return response()->json($result, $result['success'] ? 200 : 503);
    }
}
