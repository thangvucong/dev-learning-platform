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
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\Teacher\ClassController as TeacherClassesController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\MaterialController as TeacherMaterialController;
use App\Http\Controllers\Teacher\ScheduleController as TeacherScheduleController;
use App\Http\Controllers\Search\GlobalSearchController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MyPostController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminUploadController;
use App\Http\Controllers\ChatbotController;
use App\Support\AuthRedirect;


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
Route::post('/chatbot/message', [ChatbotController::class, 'message'])
    ->middleware('throttle:20,1')
    ->name('chatbot.message');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

Route::post('/payment/onepay/ipn', [OnePayController::class, 'ipn'])
    ->name('payment.onepay.ipn');

Route::get('/payment/onepay/return', [OnePayController::class, 'handleReturn'])
    ->name('payment.onepay.return');

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

Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/status', [OrderStatusController::class, 'show'])
        ->middleware('permission:view own orders')
        ->name('orders.status');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])
        ->middleware('permission:view own orders')
        ->name('checkout.success');
    Route::post('/payment/onepay/start', [OnePayController::class, 'start'])
        ->middleware('permission:checkout courses')
        ->name('payment.onepay.start');

    Route::middleware(['role:student'])->group(function () {
        Route::get('/profile', [StudentProfileController::class, 'index'])->name('profile.edit');
    });

    Route::prefix('posts')
        ->name('posts.')
        ->middleware('permission:create posts')
        ->group(function () {
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/', [PostController::class, 'store'])->name('store');
            Route::post('/editor/image', [PostController::class, 'uploadEditorImage'])
                ->middleware('permission:upload editor images')
                ->name('editor.image');
            Route::get('/{post}/edit', [PostController::class, 'edit'])
                ->middleware('permission:manage own posts')
                ->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])
                ->middleware('permission:manage own posts')
                ->name('update');
    });

    Route::prefix('my-posts')
        ->name('my-posts.')
        ->middleware('permission:manage own posts')
        ->group(function () {
            Route::get('/', [MyPostController::class, 'index'])->name('index');
            Route::delete('/{postId}', [MyPostController::class, 'destroy'])->name('destroy');
            Route::post('/{postId}/resubmit', [MyPostController::class, 'resubmit'])->name('resubmit');
    });

    Route::get('/dashboard', function () {
        return redirect()->to(AuthRedirect::to(auth()->user()));
    })->middleware('role.redirect')->name('dashboard');

    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.') 
        ->group(function () {
            
            Route::get('/dashboard', function () {
                return view('components.admin.dashboard');
            })->name('dashboard');

            Route::prefix('posts')->name('posts.')->middleware('permission:manage posts')->group(function () {
                Route::get('/', [AdminPostController::class, 'index'])->name('index');
                Route::get('/{id}', [AdminPostController::class, 'show'])->name('show');
                Route::post('/{id}/approve', [AdminPostController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [AdminPostController::class, 'reject'])->name('reject');
                Route::post('/{id}/unpublish', [AdminPostController::class, 'unpublish'])->name('unpublish');
                Route::delete('/{id}', [AdminPostController::class, 'destroy'])->name('destroy');
            });

            Route::post('/uploads/editor-image', [AdminUploadController::class, 'editorImage'])
                ->middleware('permission:upload editor images')
                ->name('uploads.editor-image');

            Route::get('/api/dashboard-stats', [AdminDashboardController::class, 'getStats'])->name('api.stats');

            Route::group(['prefix' => 'classes', 'middleware' => 'permission:manage classes'], function () {
                Route::get('/', [AdminClassController::class, 'index'])->name('classes.managerClasses');
                Route::get('/api/list', [AdminClassController::class, 'getListData'])->name('classes.api.list');

                Route::post('/', [AdminClassController::class, 'store'])->name('classes.store');
                Route::get('/{courseClass}/api/students', function () {
                    return response()->json(['message' => 'Not implemented']);
                })->name('classes.api.students');
                Route::post('/{courseClass}/students', [AdminClassController::class, 'addStudents'])->name('classes.students.add');
                Route::post('/{courseClass}/students/import', [AdminClassController::class, 'importStudents'])->name('classes.students.import');
            });

            Route::group(['prefix' => 'users', 'middleware' => 'permission:manage users'], function () {
                Route::get('/', [AdminUserController::class, 'index'])->name('users.index');
                Route::get('/{user}', [AdminUserController::class, 'show'])->name('users.show');
                Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
                Route::put('/{user}', [AdminUserController::class, 'update'])->name('users.update');
                Route::patch('/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggleStatus');
                Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            });

            Route::group(['prefix' => 'courses', 'middleware' => 'permission:manage courses'], function () {

                Route::get('/', [AdminCourseController::class, 'index'])->name('courses.managerCourses');
                Route::post('/', [AdminCourseController::class, 'store'])->name('courses.store');
                Route::get('/api/list', [AdminCourseController::class, 'getListData'])->name('courses.api.list');
                Route::put('/{course}/instructor', [AdminCourseController::class, 'updateInstructor'])->name('courses.updateInstructor');
            });
    });

    Route::middleware(['role:instructor'])
        ->prefix('teacher')
        ->name('teacher.')
        ->group(function () {
            Route::get('/schedule', [TeacherScheduleController::class, 'index'])->name('schedule.index');
            Route::get('/schedule/data', [TeacherScheduleController::class, 'data'])->name('schedule.data');
            Route::post('/schedule/classes/{courseClass}/sessions', [TeacherScheduleController::class, 'ensureAttendanceSession'])->name('schedule.sessions.ensure');
            Route::get('/schedule/sessions/{classSession}/attendance', [TeacherScheduleController::class, 'attendance'])->name('schedule.attendance');
            Route::put('/schedule/sessions/{classSession}/attendance/{student}', [TeacherScheduleController::class, 'updateAttendance'])->name('schedule.attendance.update');
            Route::put('/schedule/sessions/{classSession}/attendance', [TeacherScheduleController::class, 'bulkAttendance'])->name('schedule.attendance.bulk');
            Route::get('/schedule/sessions/{classSession}/assignments', [TeacherScheduleController::class, 'assignments'])->name('schedule.assignments');
            Route::post('/schedule/sessions/{classSession}/assignments', [TeacherScheduleController::class, 'storeAssignment'])->name('schedule.assignments.store');
            Route::get('/schedule/assignments/{sessionAssignment}/submissions', [TeacherScheduleController::class, 'assignmentSubmissions'])->name('schedule.assignments.submissions');
            Route::put('/schedule/submissions/{assignmentSubmission}/grade', [TeacherScheduleController::class, 'gradeSubmission'])->name('schedule.submissions.grade');
            Route::get('/materials', [TeacherMaterialController::class, 'index'])->name('materials.index');
            Route::get('/materials/data', [TeacherMaterialController::class, 'data'])->name('materials.data');
            Route::post('/materials', [TeacherMaterialController::class, 'store'])->name('materials.store');
            Route::delete('/materials/{learningMaterial}', [TeacherMaterialController::class, 'destroy'])->name('materials.destroy');
            Route::get('/materials/{learningMaterial}/download', [TeacherMaterialController::class, 'download'])->name('materials.download');
            Route::get('/classes/{courseClass}/sessions/options', [TeacherMaterialController::class, 'classSessions'])->name('materials.class-sessions');
            Route::get('/classes', [TeacherClassesController::class, 'index'])->name('classes.index');
            Route::get('/classes/{courseClass}', [TeacherClassesController::class, 'show'])->name('classes.show');
            Route::get('/classes/{courseClass}/students/export', [TeacherClassesController::class, 'exportStudents'])->name('classes.students.export');
            Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware(['role:student'])
        ->prefix('user')
        ->name('user.')
        ->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
            Route::get('/schedule', [StudentScheduleController::class, 'index'])->name('schedule.index');
            Route::get('/schedule/data', [StudentScheduleController::class, 'data'])->name('schedule.data');
            Route::get('/schedule/sessions/{classSession}/assignments', [StudentScheduleController::class, 'assignments'])->name('schedule.assignments');
            Route::post('/schedule/assignments/{sessionAssignment}/submission', [StudentScheduleController::class, 'submitAssignment'])->name('schedule.assignments.submit');
            Route::get('/materials', [StudentMaterialController::class, 'index'])->name('materials.index');
            Route::get('/materials/data', [StudentMaterialController::class, 'data'])->name('materials.data');
            Route::get('/materials/{learningMaterial}/download', [StudentMaterialController::class, 'download'])->name('materials.download');
            Route::get('/classes/{courseClass}/sessions/options', [StudentMaterialController::class, 'classSessions'])->name('materials.class-sessions');
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

Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

require __DIR__.'/auth.php';
