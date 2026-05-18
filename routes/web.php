<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use  App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\OnePayController;
use App\Http\Controllers\Auth\EmailOtpAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ClassController as StudentClassController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\Search\GlobalSearchController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MyPostController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUploadController;


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

Route::get('/search', [GlobalSearchController::class, 'index'])->name('search');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/{slug}', [PostController::class, 'show'])->name('posts.show');

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
    Route::middleware(['role:user'])->group(function () {
        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile.edit');
    });

    Route::prefix('posts')
        ->name('posts.')
        ->middleware('auth')
        ->group(function () {
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/', [PostController::class, 'store'])->name('store');
            Route::post('/editor/image', [PostController::class, 'uploadEditorImage'])->name('editor.image');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');

        });

    Route::prefix('my-posts')
        ->name('my-posts.')
        ->middleware('auth')
        ->group(function () {
            Route::get('/', [MyPostController::class, 'index'])->name('index');
            Route::delete('/{postId}', [MyPostController::class, 'destroy'])->name('destroy');
            Route::post('/{postId}/resubmit', [MyPostController::class, 'resubmit'])->name('resubmit');
        });

    Route::get('/dashboard', function () {
        return redirect()->to(\App\Support\AuthRedirect::to(auth()->user()));
    })->middleware('role.redirect')->name('dashboard');

    //  Route dành cho Admin
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.') 
        ->group(function () {
            
            // Trang chủ quản trị
            Route::get('/dashboard', function () {
                return view('components.admin.dashboard');
            })->name('dashboard');

            Route::prefix('posts')->name('posts.')->group(function () {
                Route::get('/', [AdminPostController::class, 'index'])->name('index');
                Route::get('/{id}', [AdminPostController::class, 'show'])->name('show');
                Route::post('/{id}/approve', [AdminPostController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [AdminPostController::class, 'reject'])->name('reject');
                Route::post('/{id}/unpublish', [AdminPostController::class, 'unpublish'])->name('unpublish');
                Route::delete('/{id}', [AdminPostController::class, 'destroy'])->name('destroy');
            });

            Route::post('/uploads/editor-image', [AdminUploadController::class, 'editorImage'])
                ->name('uploads.editor-image');

        Route::get('/api/dashboard-stats', [AdminDashboardController::class, 'getStats'])->name('api.stats');

   Route::group(['prefix' => 'classes'], function () {
    Route::get('/', [AdminClassController::class, 'index'])->name('classes.managerClasses');
    Route::get('/api/list', [AdminClassController::class, 'getListData'])->name('classes.api.list');

            // Admin APIs (JSON) for managing classes and class students
            Route::post('/', [AdminClassController::class, 'store'])->name('classes.store');
            Route::get('/{courseClass}/api/students', function () {
                return response()->json(['message' => 'Not implemented']);
            })->name('classes.api.students');
            Route::post('/{courseClass}/students', [AdminClassController::class, 'addStudents'])->name('classes.students.add');
            Route::post('/{courseClass}/students/import', [AdminClassController::class, 'importStudents'])->name('classes.students.import');
});
            Route::group(['prefix' => 'users'], function () {
                Route::get('/', [AdminUserController::class, 'index'])->name('users.index');
                Route::get('/{user}', [AdminUserController::class, 'show'])->name('users.show');
                Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
                Route::put('/{user}', [AdminUserController::class, 'update'])->name('users.update');
                Route::patch('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggleStatus');
                Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            });
            
            // Quản lý khóa học 
        Route::group(['prefix' => 'courses'], function () {
              
                Route::get('/', [AdminCourseController::class, 'index'])->name('courses.managerCourses');
                Route::post('/', [AdminCourseController::class, 'store'])->name('courses.store');
                Route::get('/api/list', [AdminCourseController::class, 'getListData'])->name('courses.api.list');
                Route::put('/{course}/instructor', [AdminCourseController::class, 'updateInstructor'])->name('courses.updateInstructor');
                // Route::delete('/api/delete/{id}', [AdminCourseController::class, 'destroy'])->name('courses.api.delete');
            });
        });

    Route::middleware(['role:teacher'])
        ->prefix('teacher')
        ->name('teacher.')
        ->group(function () {
        Route::get('/api/schedule', [\App\Http\Controllers\Teacher\TeacherClassController::class, 'getSchedule'])->name('api.schedule');
           Route::get('/dashboard', [\App\Http\Controllers\Teacher\TeacherClassController::class, 'indexView'])->name('dashboard');

            Route::get('/api/classes', [\App\Http\Controllers\Teacher\TeacherClassController::class, 'index'])
            ->name('api.classes');
        });

    Route::middleware(['role:student'])
        ->prefix('user')
        ->name('user.')
        ->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
            Route::get('/schedule', [StudentScheduleController::class, 'index'])->name('schedule.index');
            Route::get('/schedule/data', [StudentScheduleController::class, 'data'])->name('schedule.data');
            Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile.index');
            Route::patch('/profile', [StudentProfileController::class, 'updateProfile'])->name('profile.update');
            Route::post('/profile/avatar', [StudentProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
            Route::patch('/profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.password.update');
            Route::patch('/profile/settings', [StudentProfileController::class, 'updateSettings'])->name('profile.settings.update');
            Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
            Route::get('/courses/{id}', [StudentCourseController::class, 'show'])->name('courses.show');
            Route::get('/classes', [StudentClassController::class, 'index'])->name('classes.index');
            Route::get('/classes/{id}', [StudentClassController::class, 'show'])->name('classes.show');
        });
});
require __DIR__.'/auth.php';
