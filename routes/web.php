<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use  App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\OnePayController;
use App\Http\Controllers\Auth\EmailOtpAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;

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

Route::post('/payment/onepay/ipn', [OnePayController::class, 'ipn'])
    ->name('payment.onepay.ipn');

Route::get('/payment/onepay/return', [OnePayController::class, 'handleReturn'])
    ->name('payment.onepay.return');

Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/status', [OrderStatusController::class, 'show'])->name('orders.status');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/payment/onepay/start', [OnePayController::class, 'start'])->name('payment.onepay.start');
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

        Route::get('/api/dashboard-stats', [AdminDashboardController::class, 'getStats'])->name('api.stats');

   Route::group(['prefix' => 'classes'], function () {
    Route::get('/', [AdminClassController::class, 'index'])->name('classes.managerClasses');
    Route::get('/api/list', [AdminClassController::class, 'getListData'])->name('classes.api.list');
});
            // Quản lý người dùng 
            // Route::get('/users', [UserController::class, 'index'])->name('users.index');
            
            // Quản lý khóa học 
        Route::group(['prefix' => 'courses'], function () {
              
                Route::get('/', [AdminCourseController::class, 'index'])->name('courses.managerCourses');
                Route::post('/', [AdminCourseController::class, 'store'])->name('courses.store');
                Route::get('/api/list', [AdminCourseController::class, 'getListData'])->name('courses.api.list');
                // Route::delete('/api/delete/{id}', [AdminCourseController::class, 'destroy'])->name('courses.api.delete');
            });
        });
     
});
require __DIR__.'/auth.php';
