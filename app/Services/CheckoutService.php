<?php

namespace App\Services;

use App\Models\Course;
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

    /**
     * Map course price into checkout presentation (no course_prices/discount).
     *
     * @param  \App\Models\Course  $course
     * @return array<string, mixed>
     */
    protected function buildCheckoutPresentation(Course $course): array
    {
        $currencySymbol = 'đ';
        $coursePrice = $course->price !== null ? (float) $course->price : 0.0;
        $saleAmount = max(0.0, $coursePrice);
        $amountVnd = (int) round($saleAmount);

        return [
            'course_id' => $course->id,
            'title' => $course->title,
            'thumbnail_url' => $course->thumbnail_url,
            'sale_amount' => $saleAmount,
            'amount_vnd' => $amountVnd,
            'currency_symbol' => $currencySymbol,
            'list_price_formatted' => (string) format_price($saleAmount, $currencySymbol),
            'total_formatted' => (string) format_price($saleAmount, $currencySymbol),
            'line_price_formatted' => (string) format_price($saleAmount, $currencySymbol),
        ];
    }
}
