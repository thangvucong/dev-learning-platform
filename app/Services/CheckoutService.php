<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Repositories\CourseRepository;

class CheckoutService
{
    /**
     * @var \App\Repositories\CourseRepository
     */
    protected CourseRepository $courseRepository;

    /**
     * @var \App\Services\CheckoutOrderService
     */
    protected CheckoutOrderService $checkoutOrderService;

    /**
     * Create a new service instance.
     *
     * @param  \App\Repositories\CourseRepository  $courseRepository
     * @param  \App\Services\CheckoutOrderService  $checkoutOrderService
     */
    public function __construct(CourseRepository $courseRepository, CheckoutOrderService $checkoutOrderService)
    {
        $this->courseRepository = $courseRepository;
        $this->checkoutOrderService = $checkoutOrderService;
    }

    /**
     * Build view data for checkout from a published course id (prices from DB only).
     *
     * @param  int  $courseId
     * @param  \App\Models\User|null  $user
     * @return array<string, mixed>
     */
    public function buildCheckoutViewData(int $courseId, ?User $user): array
    {
        $course = $this->courseRepository->findPublishedCourseForCheckout($courseId);
        $checkout = $this->buildCheckoutPresentation($course);

        $viewData = [
            'checkout' => $checkout,
        ];

        if ($user !== null) {
            $sepay = $this->checkoutOrderService->buildSepayCheckoutSession($user, $course, $checkout);
            if ($sepay !== null) {
                $viewData['sepay'] = $sepay;
            }
        }

        return $viewData;
    }

    /**
     * Map course and active price row to a presentation array for Blade.
     *
     * @param  \App\Models\Course  $course
     * @return array<string, mixed>
     */
    protected function buildCheckoutPresentation(Course $course): array
    {
        $activePrice = $course->prices->first();
        $currencySymbol = 'đ';

        if ($activePrice && $activePrice->relationLoaded('currency') && $activePrice->currency) {
            $symbol = $activePrice->currency->symbol;
            if ($symbol !== null && $symbol !== '') {
                $currencySymbol = $symbol;
            }
        }

        $isFree = (bool) $course->is_free;
        $saleAmount = 0.0;
        $listAmount = 0.0;
        $hasDiscount = false;
        $discountPercent = null;
        $currencyId = null;
        $amountVnd = 0;

        if (!$isFree && $activePrice) {
            $saleAmount = (float) $activePrice->price;
            $currencyId = (int) $activePrice->currency_id;
            $amountVnd = (int) round($saleAmount);
            $compare = (float) ($activePrice->compare_price ?? 0);
            $listAmount = ($compare > $saleAmount) ? $compare : $saleAmount;
            $hasDiscount = $compare > $saleAmount && $saleAmount >= 0;

            if ($hasDiscount && $compare > 0) {
                $discountPercent = (int) round((($compare - $saleAmount) / $compare) * 100);
            }
        }

        $savingsAmount = $hasDiscount ? max(0.0, $listAmount - $saleAmount) : 0.0;

        return [
            'course_id' => $course->id,
            'title' => $course->title,
            'thumbnail_url' => $course->thumbnail_url,
            'is_free' => $isFree,
            'currency_id' => $currencyId,
            'sale_amount' => $saleAmount,
            'amount_vnd' => $amountVnd,
            'currency_symbol' => $currencySymbol,
            'list_price_formatted' => $isFree
                ? format_price(0, $currencySymbol)
                : format_price($listAmount, $currencySymbol),
            'discount_amount_formatted' => $isFree || !$hasDiscount
                ? format_price(0, $currencySymbol)
                : format_price($savingsAmount, $currencySymbol),
            'has_discount' => !$isFree && $hasDiscount,
            'discount_percent' => $discountPercent,
            'discount_percent_label' => ($hasDiscount && $discountPercent !== null)
                ? ('-' . $discountPercent . '%')
                : null,
            'total_formatted' => $isFree
                ? 'Miễn phí'
                : (string) format_price($saleAmount, $currencySymbol),
            'line_price_formatted' => $isFree
                ? 'Miễn phí'
                : (string) format_price($saleAmount, $currencySymbol),
        ];
    }
}
