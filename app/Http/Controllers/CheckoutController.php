<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * @var \App\Services\CheckoutService
     */
    protected CheckoutService $checkoutService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\CheckoutService  $checkoutService
     */
    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Show checkout for a published course (prices loaded from DB).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ]);

        $viewData = $this->checkoutService->buildCheckoutViewData(
            (int) $validated['course_id'],
            $request->user()
        );

        return view('pages.checkout.index', $viewData);
    }

    /**
     * Thank-you page after SePay QR payment is confirmed (server-side status = paid).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function success(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) $request->user()->id) {
            abort(403);
        }

        if ($order->status !== 'paid') {
            $order->loadMissing('items');
            $courseId = optional($order->items->first())->course_id;

            return redirect()->route('checkout', array_filter([
                'course_id' => $courseId,
            ]));
        }

        $order->loadMissing(['items.course']);

        return view('pages.checkout.success', [
            'order' => $order,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
