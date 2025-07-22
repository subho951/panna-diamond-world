<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


/* before login */
    Route::match(['get'], '/get-app-setting', [ApiController::class, 'getAppSetting']);
    Route::match(['post'], '/get-static-pages', [ApiController::class, 'getStaticPages']);

    Route::match(['post'], '/signin', [ApiController::class, 'signin']);
    Route::match(['post'], '/signin-with-mobile', [ApiController::class, 'signinWithMobile']);
    Route::match(['post'], '/signin-validate-mobile', [ApiController::class, 'signinValidateMobile']);
    Route::match(['post'], '/resend-otp', [ApiController::class, 'resendOtp']);
    Route::match(['post'], '/forgot-password', [ApiController::class, 'forgotPassword']);
    Route::match(['post'], '/validate-otp', [ApiController::class, 'validateOtp']);
    Route::match(['post'], '/reset-password', [ApiController::class, 'resetPassword']);
/* before login */
/* after login */
    Route::match(['get'], '/signout', [ApiController::class, 'signout']);
    Route::match(['get'], '/dashboard', [ApiController::class, 'dashboard']);
    Route::match(['post'], '/change-password', [ApiController::class, 'changePassword']);
    Route::match(['get'], '/get-profile', [ApiController::class, 'getProfile']);
    Route::match(['get'], '/edit-profile', [ApiController::class, 'editProfile']);
    Route::match(['post'], '/upload-profile-image', [ApiController::class, 'uploadProfileImage']);
    Route::match(['post'], '/update-profile', [ApiController::class, 'updateProfile']);
    Route::match(['get'], '/delete-account', [ApiController::class, 'deleteAccount']);

    Route::match(['get'], '/get-lead-status-list', [ApiController::class, 'getLeadStatusList']);
/* after login */