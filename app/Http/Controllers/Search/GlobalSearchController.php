<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GlobalSearchController extends Controller
{
    protected GlobalSearchService $searchService;

    public function __construct(GlobalSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Perform global search
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $limit = (int) $request->query('limit', 5);

        // Validate input
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập ít nhất 2 ký tự',
                'data' => null,
            ], 422);
        }

        // Perform search
        $results = $this->searchService->search($query, $limit);

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }
}
