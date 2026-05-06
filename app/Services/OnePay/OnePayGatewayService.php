<?php

namespace App\Services\OnePay;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Omnipay\Omnipay;
use RuntimeException;

class OnePayGatewayService
{
    public const METHOD_INTERNATIONAL = 'onepay_int_card';
    public const METHOD_DOMESTIC = 'onepay_dom_card';

    /**
     * Build purchase request and return redirect URL.
     *
     * @param  string  $paymentMethod
     * @param  array<string, mixed>  $payload
     * @return string
     */
    public function createPurchaseRedirectUrl(string $paymentMethod, array $payload): string
    {
        $gateway = $this->makeGateway($paymentMethod);
        $response = $gateway->purchase($payload)->send();

        if (!$response->isRedirect()) {
            throw new RuntimeException((string) $response->getMessage());
        }

        if (!method_exists($response, 'getRedirectUrl')) {
            throw new RuntimeException('OnePay response does not contain redirect URL.');
        }

        return (string) $response->getRedirectUrl();
    }

    /**
     * Validate browser return payload.
     *
     * @param  string  $paymentMethod
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function completePurchase(string $paymentMethod, array $payload): array
    {
        $response = $this->makeGateway($paymentMethod)->completePurchase($payload)->send();

        return [
            'is_successful' => $response->isSuccessful(),
            'code' => (string) $response->getCode(),
            'message' => (string) $response->getMessage(),
            'data' => $this->normalizeData($response->getData()),
        ];
    }

    /**
     * Validate IPN payload from OnePay.
     *
     * @param  string  $paymentMethod
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function notification(string $paymentMethod, array $payload): array
    {
        $gateway = $this->makeGateway($paymentMethod);
        if (!method_exists($gateway, 'notification')) {
            throw new RuntimeException('OnePay gateway does not support notification.');
        }

        $response = $gateway->notification($payload)->send();

        return [
            'is_successful' => $response->isSuccessful(),
            'code' => (string) $response->getCode(),
            'message' => (string) $response->getMessage(),
            'data' => $this->normalizeData($response->getData()),
        ];
    }

    /**
     * Resolve OnePay method from merchant transaction reference prefix.
     *
     * @param  string  $merchantTxnRef
     * @return string|null
     */
    public function resolveMethodFromReference(string $merchantTxnRef): ?string
    {
        if (strpos($merchantTxnRef, 'OPI_') === 0) {
            return self::METHOD_INTERNATIONAL;
        }

        if (strpos($merchantTxnRef, 'OPD_') === 0) {
            return self::METHOD_DOMESTIC;
        }

        return null;
    }

    /**
     * Generate merchant transaction reference for OnePay.
     *
     * @param  int  $orderId
     * @param  string  $paymentMethod
     * @return string
     */
    public function generateMerchantTxnRef(int $orderId, string $paymentMethod): string
    {
        $prefix = $paymentMethod === self::METHOD_DOMESTIC ? 'OPD' : 'OPI';

        return sprintf('%s_ORDER_%d_%s', $prefix, $orderId, now()->format('YmdHis'));
    }

    /**
     * Build base OnePay payload for purchase.
     *
     * @param  Request  $request
     * @param  string  $merchantTxnRef
     * @param  int  $amountMinor
     * @param  string  $orderInfo
     * @return array<string, mixed>
     */
    public function makePurchasePayload(
        Request $request,
        string $merchantTxnRef,
        int $amountMinor,
        string $orderInfo
    ): array {
        return [
            'vpc_MerchTxnRef' => $merchantTxnRef,
            'vpc_ReturnURL' => (string) config('onepay.return_url'),
            'vpc_TicketNo' => (string) ($request->ip() ?: '127.0.0.1'),
            'vpc_Amount' => (string) $amountMinor,
            'vpc_OrderInfo' => $orderInfo,
            'vpc_Locale' => (string) config('onepay.locale', 'vn'),
            'vpc_Currency' => (string) config('onepay.currency', 'VND'),
            'AgainLink' => url()->previous(),
        ];
    }

    /**
     * @param  string  $paymentMethod
     * @return object
     */
    protected function makeGateway(string $paymentMethod): object
    {
        $gatewayName = $paymentMethod === self::METHOD_DOMESTIC
            ? 'OnePay_Domestic'
            : 'OnePay_International';

        /** @var object $gateway */
        $gateway = Omnipay::create($gatewayName);
        $gateway->initialize([
            'vpc_Merchant' => (string) config('onepay.merchant'),
            'vpc_AccessCode' => (string) config('onepay.access_code'),
            'vpc_User' => (string) config('onepay.user'),
            'vpc_Password' => (string) config('onepay.password'),
            'vpc_HashKey' => (string) config('onepay.hash_key'),
        ]);
        if (method_exists($gateway, 'setTestMode')) {
            $gateway->setTestMode((bool) config('onepay.sandbox', true));
        }

        return $gateway;
    }

    /**
     * @param  mixed  $data
     * @return array<string, mixed>
     */
    protected function normalizeData($data): array
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        return is_array($data) ? $data : [];
    }
}
