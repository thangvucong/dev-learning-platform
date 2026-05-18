<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Repositories\OrderRepository;
use App\Services\CheckoutService;
use App\Services\OnePay\OnePayGatewayService;
use App\Services\OnePay\OnePayPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OnePayController extends Controller
{
    /**
     * Redirect authenticated user to OnePay payment gateway.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\CheckoutService  $checkoutService
     * @param  \App\Repositories\OrderRepository  $orderRepository
     * @param  \App\Services\OnePay\OnePayGatewayService  $gatewayService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start(
        Request $request,
        CheckoutService $checkoutService,
        OrderRepository $orderRepository,
        OnePayGatewayService $gatewayService
    ): RedirectResponse {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'method' => ['required', 'in:' . OnePayGatewayService::METHOD_INTERNATIONAL . ',' . OnePayGatewayService::METHOD_DOMESTIC],
        ]);

        $user = $request->user();
        $courseId = (int) $validated['course_id'];
        if ($user->enrolledCourses()->whereKey($courseId)->exists() || $orderRepository->hasPaidOrderForCourse((int) $user->id, $courseId)) {
            /** @var \App\Models\Course|null $course */
            $course = Course::query()->select(['id', 'slug'])->find($courseId);

            toastr('Bạn đã sở hữu khóa học này.', 'error');

            return redirect()->route('courses.show', ['slug' => (string) optional($course)->slug]);
        }

        $checkout = $checkoutService->buildCheckoutViewData((int) $validated['course_id'], null)['checkout'];
        if (!empty($checkout['is_free']) || (int) $checkout['amount_vnd'] <= 0) {
            return back()->with('onepay_error', 'Khóa học miễn phí hoặc số tiền không hợp lệ.');
        }

        $method = (string) $validated['method'];
        $saleAmount = (float) $checkout['sale_amount'];

        $order = $orderRepository->findPendingOnepayOrderForCourse((int) $user->id, $courseId, $method);
        if (!$order || (float) $order->total_amount !== $saleAmount) {
            $order = $orderRepository->createPendingOnepayOrderWithCourse(
                (int) $user->id,
                $saleAmount,
                $courseId,
                $saleAmount,
                $method
            );
        }

        if (!$order->payment_reference) {
            $order->forceFill([
                'payment_reference' => $gatewayService->generateMerchantTxnRef((int) $order->id, $method),
            ])->save();
            $order->refresh();
        }

        $amountMinor = (int) round((float) $order->total_amount * 100);
        $payload = $gatewayService->makePurchasePayload(
            $request,
            (string) $order->payment_reference,
            $amountMinor,
            'ORDER_' . $order->id
        );

        try {
            $redirectUrl = $gatewayService->createPurchaseRedirectUrl($method, $payload);
            Log::info('OnePay start redirect generated', [
                'order_id' => $order->id,
                'payment_method' => $method,
                'sandbox' => (bool) config('onepay.sandbox', true),
                'merchant' => (string) config('onepay.merchant'),
                'merch_txn_ref' => (string) $order->payment_reference,
                'redirect_url' => $redirectUrl,
            ]);
        } catch (\Throwable $e) {
            Log::error('OnePay start failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('onepay_error', 'Không thể khởi tạo thanh toán OnePay. Vui lòng thử lại.');
        }

        return redirect()->away($redirectUrl);
    }

    /**
     * Browser return URL after OnePay payment attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\OnePay\OnePayPaymentService  $paymentService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleReturn(Request $request, OnePayPaymentService $paymentService): RedirectResponse
    {
        Log::info('OnePay return received', [
            'vpc_MerchTxnRef' => (string) $request->input('vpc_MerchTxnRef', ''),
            'vpc_TxnResponseCode' => (string) $request->input('vpc_TxnResponseCode', ''),
            'vpc_TransactionNo' => (string) $request->input('vpc_TransactionNo', ''),
        ]);

        $result = $paymentService->handleReturnPayload($request->all());
        $order = $result['order'];

        if ($result['success'] && $order) {
            return redirect()->route('checkout.success', ['order' => $order->id]);
        }

        if ($order) {
            return redirect()->route('checkout', ['course_id' => $order->course_id])
                ->with('onepay_error', $result['message']);
        }

        return redirect()->route('home')
            ->with('onepay_error', 'Không xác định được đơn hàng OnePay.');
    }

    /**
     * Server-to-server IPN callback from OnePay.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\OnePay\OnePayPaymentService  $paymentService
     * @return \Illuminate\Http\Response
     */
    public function ipn(Request $request, OnePayPaymentService $paymentService): Response
    {
        Log::info('OnePay IPN received', [
            'vpc_MerchTxnRef' => (string) $request->input('vpc_MerchTxnRef', ''),
            'vpc_TxnResponseCode' => (string) $request->input('vpc_TxnResponseCode', ''),
            'vpc_TransactionNo' => (string) $request->input('vpc_TransactionNo', ''),
        ]);

        $result = $paymentService->handleIpnPayload($request->all());
        $body = $result['success'] ? 'responsecode=1&desc=confirm-success' : 'responsecode=0&desc=confirm-failed';

        return response($body, 200)->header('Content-Type', 'text/plain');
    }
}
