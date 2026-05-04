<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;

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


Route::middleware(['auth'])->group(function () {

    // Nhóm các Route dành riêng cho Admin
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
            // Route::resource('courses', AdminCourseController::class);
        });
});
require __DIR__.'/auth.php';
