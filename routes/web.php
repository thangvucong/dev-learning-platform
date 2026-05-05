<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use  App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminClassController;

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

    //  Route dành cho Admin
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.') 
        ->group(function () {
            
            // Trang chủ quản trị
            Route::get('/dashboard', function () {
                return view('components.admin.dashboard');
            })->name('dashboard');

        

   Route::group(['prefix' => 'classes'], function () {
    Route::get('/', [AdminClassController::class, 'index'])->name('classes.managerClasses');
    Route::get('/api/list', [AdminClassController::class, 'getListData'])->name('classes.api.list');
});
            // Quản lý người dùng 
            // Route::get('/users', [UserController::class, 'index'])->name('users.index');
            
            // Quản lý khóa học 
        Route::group(['prefix' => 'courses'], function () {
              
                Route::get('/', [AdminCourseController::class, 'index'])->name('courses.managerCourses');
                Route::get('/api/list', [AdminCourseController::class, 'getListData'])->name('courses.api.list');
                // Route::delete('/api/delete/{id}', [AdminCourseController::class, 'destroy'])->name('courses.api.delete');
            });
        });
     
});
require __DIR__.'/auth.php';
