<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Poll order payment status for checkout UX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        $order->refresh();

        return response()->json([
            'status' => $order->status,
        ]);
    }
}
