<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Common\TableController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqSubCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CampaignTypeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\LeadHeaderController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UploadLeadController;
use App\Http\Controllers\IndividualLeadController;
use App\Http\Controllers\LeadListController;
use App\Http\Controllers\FeedbackTagController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WpApiController;

// Route::get('/', function () {
//     return view('welcome');
// });

// GET route – to display the page
Route::get('/test-email-function', [AuthController::class, 'showEmailTestPage']);
Route::get('/page-content/{id}', [UserController::class, 'page']);
Route::get('/delete-account-request', [UserController::class, 'deleteAccountRequest']);
Route::post('/delete-account-update', [UserController::class, 'deleteaccount']);

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
    /* Masters */
        /* country */
            Route::get('country/list', [CountryController::class, 'list']);
            Route::match(['get', 'post'], 'country/add', [CountryController::class, 'add']);
            Route::match(['get', 'post'], 'country/edit/{id}', [CountryController::class, 'edit']);
            Route::get('country/delete/{id}', [CountryController::class, 'delete']);
            Route::get('country/change-status/{id}', [CountryController::class, 'change_status']);
        /* country */
        /* state */
            Route::get('state/list', [StateController::class, 'list']);
            Route::match(['get', 'post'], 'state/add', [StateController::class, 'add']);
            Route::match(['get', 'post'], 'state/edit/{id}', [StateController::class, 'edit']);
            Route::get('state/delete/{id}', [StateController::class, 'delete']);
            Route::get('state/change-status/{id}', [StateController::class, 'change_status']);
        /* state */
        /* city */
            Route::get('city/list', [CityController::class, 'list']);
            Route::match(['get', 'post'], 'city/add', [CityController::class, 'add']);
            Route::match(['get', 'post'], 'city/edit/{id}', [CityController::class, 'edit']);
            Route::get('city/delete/{id}', [CityController::class, 'delete']);
            Route::get('city/change-status/{id}', [CityController::class, 'change_status']);
            Route::get('/states/{country_id}', [CityController::class, 'getStatesByCountry'])->name('getStatesByCountry');
        /* city */
        /* campaign type */
            Route::get('campaign-type/list', [CampaignTypeController::class, 'list']);
            Route::match(['get', 'post'], 'campaign-type/add', [CampaignTypeController::class, 'add']);
            Route::match(['get', 'post'], 'campaign-type/edit/{id}', [CampaignTypeController::class, 'edit']);
            Route::get('campaign-type/delete/{id}', [CampaignTypeController::class, 'delete']);
            Route::get('campaign-type/change-status/{id}', [CampaignTypeController::class, 'change_status']);
        /* campaign type */
        /* campaign */
            Route::get('campaign/list', [CampaignController::class, 'list']);
            Route::match(['get', 'post'], 'campaign/add', [CampaignController::class, 'add']);
            Route::match(['get', 'post'], 'campaign/edit/{id}', [CampaignController::class, 'edit']);
            Route::get('campaign/delete/{id}', [CampaignController::class, 'delete']);
            Route::get('campaign/change-status/{id}', [CampaignController::class, 'change_status']);
        /* campaign */
        /* source */
            Route::get('source/list', [SourceController::class, 'list']);
            Route::match(['get', 'post'], 'source/add', [SourceController::class, 'add']);
            Route::match(['get', 'post'], 'source/edit/{id}', [SourceController::class, 'edit']);
            Route::get('source/delete/{id}', [SourceController::class, 'delete']);
            Route::get('source/change-status/{id}', [SourceController::class, 'change_status']);
        /* source */
        /* lead header */
            Route::get('lead-header/list', [LeadHeaderController::class, 'list']);
            Route::match(['get', 'post'], 'lead-header/add', [LeadHeaderController::class, 'add']);
            Route::match(['get', 'post'], 'lead-header/edit/{id}', [LeadHeaderController::class, 'edit']);
            Route::get('lead-header/delete/{id}', [LeadHeaderController::class, 'delete']);
            Route::get('lead-header/change-status/{id}', [LeadHeaderController::class, 'change_status']);
        /* lead header */
        /* lead status */
            Route::get('lead-status/list', [LeadStatusController::class, 'list']);
            Route::match(['get', 'post'], 'lead-status/add', [LeadStatusController::class, 'add']);
            Route::match(['get', 'post'], 'lead-status/edit/{id}', [LeadStatusController::class, 'edit']);
            Route::get('lead-status/delete/{id}', [LeadStatusController::class, 'delete']);
            Route::get('lead-status/change-status/{id}', [LeadStatusController::class, 'change_status']);
        /* lead status */
        /* mood */
            Route::get('mood/list', [MoodController::class, 'list']);
            Route::match(['get', 'post'], 'mood/add', [MoodController::class, 'add']);
            Route::match(['get', 'post'], 'mood/edit/{id}', [MoodController::class, 'edit']);
            Route::get('mood/delete/{id}', [MoodController::class, 'delete']);
            Route::get('mood/change-status/{id}', [MoodController::class, 'change_status']);
        /* mood */
    /* Masters */
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
    /* page */
        Route::get('page/list', [PageController::class, 'list']);
        Route::match(['get', 'post'], 'page/add', [PageController::class, 'add']);
        Route::match(['get', 'post'], 'page/edit/{id}', [PageController::class, 'edit']);
        Route::get('page/delete/{id}', [PageController::class, 'delete']);
        Route::get('page/change-status/{id}', [PageController::class, 'change_status']);
    /* page */
    /* branch */
        Route::get('branch/list', [BranchController::class, 'list']);
        Route::match(['get', 'post'], 'branch/add', [BranchController::class, 'add']);
        Route::match(['get', 'post'], 'branch/edit/{id}', [BranchController::class, 'edit']);
        Route::get('branch/delete/{id}', [BranchController::class, 'delete']);
        Route::get('branch/change-status/{id}', [BranchController::class, 'change_status']);
    /* branch */
    /* Upload  Lead */
        Route::match(['get', 'post'], 'upload-lead', [UploadLeadController::class, 'list']);
        Route::post( 'upload-lead/preview', [UploadLeadController::class, 'preview']);
        Route::post( 'upload-lead/store', [UploadLeadController::class, 'store']);
        Route::get( 'upload-lead/cancel-upload/{tempFile}', [UploadLeadController::class, 'cancelUpload']);
        Route::get('upload-lead/delete/{id}', [UploadLeadController::class, 'delete']);
        Route::get('upload-lead/csv-download/{id}', [UploadLeadController::class, 'csvDownload']);
        Route::post( 'upload-lead/fetch-telecaller', [UploadLeadController::class, 'fetchTelecaller']);
        Route::post( 'upload-lead/fetch-campaign', [UploadLeadController::class, 'fetchCampaign']);
    /* Upload  Lead */
    /* Individual Lead */
        Route::match(['get', 'post'], 'individual-lead/add', [IndividualLeadController::class, 'add']);
        Route::post( 'individual-lead/fetch-telecaller', [IndividualLeadController::class, 'fetchTelecaller']);
        Route::post( 'individual-lead/fetch-campaign', [IndividualLeadController::class, 'fetchCampaign']);
        Route::post( 'individual-lead/fetch-state', [IndividualLeadController::class, 'fetchState']);
        Route::post( 'individual-lead/fetch-phone-code', [IndividualLeadController::class, 'fetchPhoneCode']);
    /* Individual Lead */

    /* Lead List */
        Route::get('lead-list', [LeadListController::class, 'list']);
        Route::match(['get', 'post'],'lead-list/edit/{id}', [LeadListController::class, 'edit']);
        Route::get('lead-list/delete/{id}', [LeadListController::class, 'delete']);
        Route::get('lead-list/change-status/{id}', [LeadListController::class, 'change_status']);
        Route::get('lead-list/fetch-lead-status', [LeadListController::class, 'fetchLeadStatus']);
        Route::get('lead-list/fetch-call-purpose', [LeadListController::class, 'fetchCallPurpose']);
        Route::get('lead-list/fetch-mood', [LeadListController::class, 'fetchMood']);
        Route::get('lead-list/fetch-feedback-tag', [LeadListController::class, 'fetchFeedbackTag']);
        Route::post('lead-list/update-lead-status', [LeadListController::class, 'updateLeadStatus']);
        Route::post('lead-list/fetch-lead-history', [LeadListController::class, 'fetchLeadHistory']);
        Route::post('lead-list/fetch-lead-detail', [LeadListController::class, 'fetchLeadDetail']);
        Route::post('lead-list/fetch-lead-added-updated', [LeadListController::class, 'fetchLeadAddedUpdated']);
        Route::post('lead-list/fetch-lead-activity-count', [LeadListController::class, 'fetchLeadActivityCount']);
        Route::get('lead-list/view-lead/{id}', [LeadListController::class, 'viewLead']);
        Route::post('lead-list/get-lead-call-data', [LeadListController::class, 'getLeadCallData']);
        Route::post('lead-list/individual-lead-transfer-modal-data', [LeadListController::class, 'individualLeadTransferModalData']);
        Route::post('lead-list/individual-lead-transfer', [LeadListController::class, 'individualLeadTransfer']);
        Route::post('lead-list/bulk-lead-transfer', [LeadListController::class, 'bulkLeadTransfer']);
        Route::post('lead-list/fetch-branch-wise-telecaller', [LeadListController::class, 'fetchBranchWiseTelecaller']);
        Route::post('lead-list/fetch-parent-wise-child-status', [LeadListController::class, 'fetchParentWiseChildStatus']);
        Route::post( 'lead-list/fetch-campaign', [LeadListController::class, 'fetchCampaign']);
        Route::match(['get', 'post'],'lead-list/export-all-leads-as-csv', [LeadListController::class, 'exportAllLeadsAsCSV']);
    /* Lead List */

    /* Feedback Tags */
        Route::get('feedback-tag/list',[FeedbackTagController::class, 'list']);
        Route::match(['get','post'], 'feedback-tag/add', [FeedbackTagController::class, 'add']);
        Route::match(['get','post'], 'feedback-tag/edit/{id}', [FeedbackTagController::class, 'edit']);
        Route::get('feedback-tag/delete/{id}', [FeedbackTagController::class, 'delete']);
        Route::get('feedback-tag/change-status/{id}', [FeedbackTagController::class, 'change_status']);
    /* Feedback Tags */

    /* Purpose */
        Route::get('purpose/list',[PurposeController::class, 'list']);
        Route::match(['get','post'], 'purpose/add', [PurposeController::class, 'add']);
        Route::match(['get','post'], 'purpose/edit/{id}', [PurposeController::class, 'edit']);
        Route::get('purpose/delete/{id}', [PurposeController::class, 'delete']);
        Route::get('purpose/change-status/{id}', [PurposeController::class, 'change_status']);
    /* Purpose */



    /* Report */
        Route::get('activity-report', [ReportController::class, 'activityReport']);
        Route::match(['get', 'post'],'activity-report-modal', [ReportController::class, 'activityReportModal']);
        Route::get('assign-report', [ReportController::class, 'assignReport']);

        Route::get('activity-report-new', [ReportController::class, 'activityReportNew']);

    /* Report */



    
});


/* WP API testing */
Route::match(['get', 'post'], 'wp-message', [WpApiController::class, 'wpMessage']);
/* WP API testing */
