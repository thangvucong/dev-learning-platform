<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\Auth\EmailOtpAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use  App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

Route::post('/payment/webhook', [PaymentWebhookController::class, 'sepay'])
    ->name('payment.webhook.sepay');

Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/status', [OrderStatusController::class, 'show'])->name('orders.status');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

Route::post('/auth/send-code', [EmailOtpAuthController::class, 'sendCode'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('auth.send-code');

Route::post('/auth/verify-code', [EmailOtpAuthController::class, 'verifyCode'])
    ->middleware(['guest', 'throttle:12,1'])
    ->name('auth.verify-code');

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])
    ->middleware('guest')
    ->name('auth.google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('auth.google.callback');


Route::middleware(['auth'])->group(function () {

    //  Route dành cho Admin
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.') 
        ->group(function () {
            
            // Trang chủ quản trị
            Route::get('/dashboard', function () {
                return view('components.admin.dashboard');
            })->name('dashboard');

        


            // Quản lý người dùng 
            // Route::get('/users', [UserController::class, 'index'])->name('users.index');
            
            // Quản lý khóa học 
            Route::resource('courses', AdminCourseController::class);
        });
});
require __DIR__.'/auth.php';
