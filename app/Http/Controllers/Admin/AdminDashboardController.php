<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\JsonResponse;


class AdminDashboardController extends Controller {
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService) {
        $this->dashboardService = $dashboardService;
    }

    public function getStats(): JsonResponse {
        try {
            $data = $this->dashboardService->getDashboardData();
            
            return response()->json([
                'success' => true,
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống : ' . $e->getMessage()
            ], 500);
        }
    }
}