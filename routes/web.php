<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Common\TableController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqSubCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PageController;

// Route::get('/', function () {
//     return view('welcome');
// });

// GET route – to display the page
Route::get('/test-email-function', [AuthController::class, 'showEmailTestPage']);

// POST route – to send the email
Route::post('/test-email-function', [AuthController::class, 'testEmailFunction']);

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('signin', [AuthController::class, 'login'])->name('signin');
Route::match(['get','post'],'/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotpassword');
Route::match(['get','post'],'/validate-otp/{id}', [AuthController::class, 'validateOtp'])->name('validateotp');
Route::match(['get','post'],'/resend-otp/{id}', [AuthController::class, 'resendOtp']);
Route::match(['get','post'],'/reset-password/{id}', [AuthController::class, 'resetPassword'])->name('resetpassword');

Route::get('/table/fetch', [TableController::class, 'fetch']);
Route::get('/table/export', [TableController::class, 'export']);

Route::middleware(['auth'])->group(function () {
	Route::get('dashboard', [AuthController::class, 'dashboard']);
	Route::get('logout', [AuthController::class, 'logout']);
	Route::get('email-logs', [AuthController::class, 'emailLogs']);
    Route::match(['get','post'],'/email-logs/details/{email}', [AuthController::class, 'emailLogsDetails']);
    Route::get('login-logs', [AuthController::class, 'loginLogs']);
    Route::get('user-activity-logs', [AuthController::class, 'userActivityLogs']);
    Route::match(['get','post'], '/common-delete-image/{id1}/{id2}/{id3}/{id4}/{id5}', [AuthController::class, 'commonDeleteImage']);
    /* setting */
        Route::get('settings', [AuthController::class, 'settings']);
        Route::post('profile-settings', [AuthController::class, 'profile_settings']);
        Route::post('general-settings', [AuthController::class, 'general_settings']);
        Route::post('change-password', [AuthController::class, 'change_password']);
        Route::post('email-settings', [AuthController::class, 'email_settings']);
        Route::get('test-email', [AuthController::class, 'testEmail']);
        Route::post('email-template', [AuthController::class, 'email_template']);
        Route::post('sms-settings', [AuthController::class, 'sms_settings']);
        Route::post('footer-settings', [AuthController::class, 'footer_settings']);
        Route::post('seo-settings', [AuthController::class, 'seo_settings']);
        Route::post('payment-settings', [AuthController::class, 'payment_settings']);
    /* setting */
    /* access & permission */
        /* modules */
            Route::get('module/list', [ModuleController::class, 'list']);
            Route::match(['get', 'post'], 'module/add', [ModuleController::class, 'add']);
            Route::match(['get', 'post'], 'module/edit/{id}', [ModuleController::class, 'edit']);
            Route::get('module/delete/{id}', [ModuleController::class, 'delete']);
            Route::get('module/change-status/{id}', [ModuleController::class, 'change_status']);
        /* modules */
        /* roles */
            Route::get('role/list', [RoleController::class, 'list']);
            Route::match(['get', 'post'], 'role/add', [RoleController::class, 'add']);
            Route::match(['get', 'post'], 'role/edit/{id}', [RoleController::class, 'edit']);
            Route::get('role/delete/{id}', [RoleController::class, 'delete']);
            Route::get('role/change-status/{id}', [RoleController::class, 'change_status']);
        /* roles */
        /* admin users */
            Route::get('admin-user/list', [AdminUserController::class, 'list']);
            Route::match(['get', 'post'], 'admin-user/add', [AdminUserController::class, 'add']);
            Route::match(['get', 'post'], 'admin-user/edit/{id}', [AdminUserController::class, 'edit']);
            Route::get('admin-user/delete/{id}', [AdminUserController::class, 'delete']);
            Route::get('admin-user/change-status/{id}', [AdminUserController::class, 'change_status']);
        /* admin users */
    /* access & permission */
    /* industry */
        Route::get('industry/list', [IndustryController::class, 'list']);
        Route::match(['get', 'post'], 'industry/add', [IndustryController::class, 'add']);
        Route::match(['get', 'post'], 'industry/edit/{id}', [IndustryController::class, 'edit']);
        Route::get('industry/delete/{id}', [IndustryController::class, 'delete']);
        Route::get('industry/change-status/{id}', [IndustryController::class, 'change_status']);
    /* industry */
    /* company */
        Route::get('company/list', [CompanyController::class, 'list']);
        Route::match(['get', 'post'], 'company/add', [CompanyController::class, 'add']);
        Route::match(['get', 'post'], 'company/edit/{id}', [CompanyController::class, 'edit']);
        Route::get('company/delete/{id}', [CompanyController::class, 'delete']);
        Route::get('company/change-status/{id}', [CompanyController::class, 'change_status']);
        Route::match(['get', 'post'], 'company/subcriptions/{id}', [CompanyController::class, 'subcriptions']);
    /* company */
    /* FAQs */
        /* faq category */
            Route::get('faq-category/list', [FaqCategoryController::class, 'list']);
            Route::match(['get', 'post'], 'faq-category/add', [FaqCategoryController::class, 'add']);
            Route::match(['get', 'post'], 'faq-category/edit/{id}', [FaqCategoryController::class, 'edit']);
            Route::get('faq-category/delete/{id}', [FaqCategoryController::class, 'delete']);
            Route::get('faq-category/change-status/{id}', [FaqCategoryController::class, 'change_status']);
        /* faq category */
        /* faq sub category */
            Route::get('faq-sub-category/list', [FaqSubCategoryController::class, 'list']);
            Route::match(['get', 'post'], 'faq-sub-category/add', [FaqSubCategoryController::class, 'add']);
            Route::match(['get', 'post'], 'faq-sub-category/edit/{id}', [FaqSubCategoryController::class, 'edit']);
            Route::get('faq-sub-category/delete/{id}', [FaqSubCategoryController::class, 'delete']);
            Route::get('faq-sub-category/change-status/{id}', [FaqSubCategoryController::class, 'change_status']);
        /* faq sub category */
        /* faq */
            Route::get('faq/list', [FaqController::class, 'list']);
            Route::match(['get', 'post'], 'faq/add', [FaqController::class, 'add']);
            Route::match(['get', 'post'], 'faq/edit/{id}', [FaqController::class, 'edit']);
            Route::get('faq/delete/{id}', [FaqController::class, 'delete']);
            Route::get('faq/change-status/{id}', [FaqController::class, 'change_status']);
        /* faq */
    /* FAQs */
    /* package */
        Route::get('package/list', [PackageController::class, 'list']);
        Route::match(['get', 'post'], 'package/add', [PackageController::class, 'add']);
        Route::match(['get', 'post'], 'package/edit/{id}', [PackageController::class, 'edit']);
        Route::get('package/delete/{id}', [PackageController::class, 'delete']);
        Route::get('package/change-status/{id}', [PackageController::class, 'change_status']);
    /* package */
    /* page */
        Route::get('page/list', [PageController::class, 'list']);
        Route::match(['get', 'post'], 'page/add', [PageController::class, 'add']);
        Route::match(['get', 'post'], 'page/edit/{id}', [PageController::class, 'edit']);
        Route::get('page/delete/{id}', [PageController::class, 'delete']);
        Route::get('page/change-status/{id}', [PageController::class, 'change_status']);
    /* page */
});
