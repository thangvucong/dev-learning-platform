<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseDiscount;
use App\Models\User;
use App\Repositories\CourseRepository;
use App\Services\CheckoutOrderService;

class CheckoutService
{
    /**
     * @var \App\Repositories\Interfaces\CourseRepositoryInterface
     */
    protected CourseRepository $courseRepository;

    /**
     * @var \App\Services\CheckoutOrderService
     */
    protected CheckoutOrderService $checkoutOrderService;

    public function __construct(
        CourseRepository $courseRepository,
        CheckoutOrderService $checkoutOrderService
    )
    {
        $this->courseRepository = $courseRepository;
        $this->checkoutOrderService = $checkoutOrderService;
    }

    /**
     * Build view data for checkout from a published course id.
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
            $onepay = $this->checkoutOrderService->buildOnepayCheckoutSession($user, $course, $checkout);
            if ($onepay !== null) {
                $viewData['onepay'] = $onepay;
            }
        }

        return $viewData;
    }

    protected function buildCheckoutPresentation(Course $course): array
    {
        $currencySymbol = 'đ';
        $originalAmount = max(0, (int) ($course->original_price ?? 0));
        $saleAmount = $this->resolveCourseSalePrice($course, $originalAmount);
        $discountAmount = max(0, $originalAmount - $saleAmount);
        $amountVnd = (int) round($saleAmount);
        $hasDiscount = $discountAmount > 0;

        return [
            'course_id' => $course->id,
            'title' => $course->title,
            'thumbnail_url' => $course->thumbnail_url,
            'original_amount' => $originalAmount,
            'discount_amount' => $discountAmount,
            'sale_amount' => $saleAmount,
            'amount_vnd' => $amountVnd,
            'is_free' => $amountVnd <= 0,
            'has_discount' => $hasDiscount,
            'currency_symbol' => $currencySymbol,
            'original_price_formatted' => (string) format_price($originalAmount, $currencySymbol),
            'discount_formatted' => (string) format_price($discountAmount, $currencySymbol),
            'list_price_formatted' => (string) format_price($originalAmount, $currencySymbol),
            'total_formatted' => (string) format_price($saleAmount, $currencySymbol),
            'line_price_formatted' => (string) format_price($saleAmount, $currencySymbol),
        ];
    }

    protected function resolveCourseSalePrice(Course $course, int $originalPrice): int
    {
        if ($originalPrice <= 0 || $course->activeDiscounts->isEmpty()) {
            return max(0, $originalPrice);
        }

        return (int) $course->activeDiscounts
            ->map(function ($discount) use ($originalPrice) {
                return $this->applyDiscount($originalPrice, $discount);
            })
            ->filter(function (int $price) {
                return $price >= 0;
            })
            ->min();
    }

    protected function applyDiscount(int $originalPrice, $discount): int
    {
        $amount = (int) $discount->amount;

        if ($discount->type === CourseDiscount::TYPE_PERCENT) {
            return max(0, $originalPrice - (int) round($originalPrice * min($amount, 100) / 100));
        }

        if ($discount->type === CourseDiscount::TYPE_FIXED) {
            return max(0, $originalPrice - $amount);
        }

        if ($discount->type === CourseDiscount::TYPE_FINAL_PRICE) {
            return max(0, min($originalPrice, $amount));
        }

        return $originalPrice;
    }
}
