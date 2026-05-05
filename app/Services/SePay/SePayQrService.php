<?php

namespace App\Services\SePay;

/**
 * Build VietQR-style image URLs via SePay public QR endpoint (no API secret in URL).
 *
 * @see https://developer.sepay.vn/vi/sepay-webhooks/tao-qr-va-form-thanh-toan
 */
class SePayQrService
{
    /**
     * Build QR image URL for bank transfer (amount VND, description URL-encoded).
     *
     * @param  string  $accountNumber
     * @param  string  $bankCode  Bank code for qr.sepay.vn (e.g. Vietcombank, MBBank)
     * @param  int  $amountVnd
     * @param  string  $transferDescription  Exact transfer content (must match webhook matching rules)
     * @return string
     */
    public function buildQrImageUrl(string $accountNumber, string $bankCode, int $amountVnd, string $transferDescription): string
    {
        $base = rtrim(config('sepay.qr.base_url'), '?&');

        $query = http_build_query([
            'acc' => $accountNumber,
            'bank' => $bankCode,
            'amount' => $amountVnd,
            'des' => $transferDescription,
        ], '', '&', PHP_QUERY_RFC3986);

        return $base . '?' . $query;
    }
}
