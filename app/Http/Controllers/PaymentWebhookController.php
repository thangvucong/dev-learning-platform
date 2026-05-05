<?php

namespace App\Http\Controllers;

use App\Services\SePay\SepayWebhookPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    /**
     * SePay bank transfer webhook (JSON).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\SePay\SepayWebhookPaymentService  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function sepay(Request $request, SepayWebhookPaymentService $service): JsonResponse
    {
        $service->assertValidApiKey($request);

        $payload = $request->all();
        $result = $service->processPayload(is_array($payload) ? $payload : []);

        return response()->json($result, 200);
    }
}
