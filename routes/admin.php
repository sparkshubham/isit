<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\SendPushNotificationJob;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
/--------------------------------------------------------------------------
/ Admin Routes With Out Login
/---------------------------------------------------------------------------
*/
// echo "admin";exit;
Route::group(['prefix'=> '/admin-panel','namespace'=>'backend\Auth'], function() {
    // Login Routes...
    
    Route::get('/', ['as' => 'admin-panel.login', 'uses' => 'LoginController@showLoginForm'])->name('admin-login');

    Route::get('/login', function () {
        return redirect('/admin-panel');
    })->name('login');
    Route::get('refreshcaptcha', 'LoginController@refreshCaptcha');
    Route::post('login', [ 'uses' => 'LoginController@login'])->name('admin-login');
    Route::get('logout', ['as' => 'admin-panel.logout', 'uses' => 'LoginController@logout'])->name('admin-logout');
    Route::post('register', [ 'uses' => 'LoginController@login'])->name('admin-register');

    // Password Reset Routes...
    Route::get('password/reset', ['as' => 'admin-panel.password.reset', 'uses' => 'ForgotPasswordController@showLinkRequestForm']);
    Route::post('password/email', ['as' => 'admin-panel.password.email', 'uses' => 'ForgotPasswordController@sendResetLinkEmail']);
    Route::get('password/reset/{token}', ['as' => 'admin-panel.password.reset.token', 'uses' => 'ResetPasswordController@showResetForm']);
    Route::post('password/reset', [ 'uses' => 'ResetPasswordController@reset']);
    Route::get('link-expired', 'LoginController@passwordLinkExpired');
});

// /*
// /--------------------------------------------------------------------------
// / Admin Routes Routes After Login
// /---------------------------------------------------------------------------
// */
Route::group(['prefix'=> 'admin-panel','namespace'=>'backend','middleware' => ['auth:admin_user','admin_user']], function() {
    // Admin Dashboard
    Route::get('/dashboard', 'DashboardController@index');
    Route::post('/dashboard/change-date', 'DashboardController@changeDate');
    
    // Admin chagne password
    Route::get('/change-password', 'HomeController@showChangePasswordForm');
    Route::post('/changePassword', 'HomeController@changePassword')->name('backend.changePassword');

    // Admin chagne password
    Route::get('/change-profile{id?}', 'HomeController@showChangeProfileForm');
    Route::post('/changeProfile', 'HomeController@changeProfile')->name('backend.changeProfile');

    // Admin User
    Route::get('/users/add', 'UserController@create');
    Route::get('/users/view/{id?}', 'UserController@show');
    Route::get('/users/delete/{id?}', 'UserController@destroy');
    Route::get('/users', 'UserController@index');
    Route::get('/users/data', 'UserController@data');
    Route::post('/users/change-status', 'UserController@changeStatus');
    Route::post('/users/change-star-status', 'UserController@changeStarStatus');
    Route::post('/users/change-verify-status', 'UserController@changeVerifyStatus');    

    // Admin CMS Pages
    Route::get('/cms-page', 'CmsController@index');
    Route::get('/cms-page/data', 'CmsController@data');
    Route::get('/cms-page/add', 'CmsController@create');
    Route::get('/cms-page/edit/{id?}', 'CmsController@create');
    Route::post('/cms-page/save', 'CmsController@store');
    Route::post('/cms-page/change-status', 'CmsController@changeStatus');
    Route::get('/cms-page/delete/{id?}', 'CmsController@destroy');
    Route::get('/cms-page/view/{id?}', 'CmsController@show');

    // Admin Daily Devotional Post
    Route::get('/admin-post', 'AdminPostController@index');
    Route::get('/admin-post/data', 'AdminPostController@data');
    Route::get('/admin-post/add', 'AdminPostController@create');
    Route::get('/admin-post/edit/{id?}', 'AdminPostController@create');
    Route::post('/admin-post/save', 'AdminPostController@store');
    Route::post('/admin-post/change-status', 'AdminPostController@changeStatus');
    Route::get('/admin-post/delete/{id?}', 'AdminPostController@destroy');
    Route::post('/admin-post/upload-image', 'AdminPostController@uploadImage');
    Route::get('/admin-post/view/{id?}', 'AdminPostController@show');

    // Admin Reportd Post
    Route::get('/report-post', 'ReportPostController@index');
    Route::get('/report-post/data', 'ReportPostController@data');
    Route::get('/report-post/add', 'ReportPostController@create');
    Route::get('/report-post/edit/{id?}', 'ReportPostController@create');
    Route::post('/report-post/save', 'ReportPostController@store');
    Route::post('/report-post/change-status', 'ReportPostController@changeStatus');
    Route::get('/report-post/delete/{id?}', 'ReportPostController@destroy');
    Route::get('/report-post/view/{id?}', 'ReportPostController@show');
    Route::post('/report-post/change-status', 'ReportPostController@changeStatus');

    // Admin Help User
    Route::get('/help-user', 'HelpUserController@index');
    Route::get('/help-user/data', 'HelpUserController@data');
    Route::get('/help-user/add', 'HelpUserController@create');
    Route::get('/help-user/edit/{id?}', 'HelpUserController@create');
    Route::post('/help-user/save', 'HelpUserController@store');
    Route::post('/help-user/change-status', 'HelpUserController@changeStatus');
    Route::get('/help-user/delete/{id?}', 'HelpUserController@destroy');
    Route::get('/help-user/view/{id?}', 'HelpUserController@show');
    Route::post('/help-user/change-status', 'HelpUserController@changeStatus');

    // Admin General Post
    Route::get('/general-post', 'GeneralPostController@index');
    Route::get('/general-post/data', 'GeneralPostController@data');
    Route::get('/general-post/add', 'GeneralPostController@create');
    Route::get('/general-post/edit/{id?}', 'GeneralPostController@create');
    Route::post('/general-post/save', 'GeneralPostController@store');
    Route::post('/general-post/change-status', 'GeneralPostController@changeStatus');
    Route::get('/general-post/delete/{id?}', 'GeneralPostController@destroy');
    Route::post('/general-post/upload-image', 'GeneralPostController@uploadImage');
    Route::get('/general-post/view/{id?}', 'GeneralPostController@show');

    // Admin General Post
    Route::get('/anointed-post', 'AnointedPostController@index');
    Route::get('/anointed-post/data', 'AnointedPostController@data');
    Route::get('/anointed-post/add', 'AnointedPostController@create');
    Route::get('/anointed-post/edit/{id?}', 'AnointedPostController@create');
    Route::post('/anointed-post/save', 'AnointedPostController@store');
    Route::post('/anointed-post/change-status', 'AnointedPostController@changeStatus');
    Route::get('/anointed-post/delete/{id?}', 'AnointedPostController@destroy');
    Route::post('/anointed-post/upload-image', 'AnointedPostController@uploadImage');
    Route::get('/anointed-post/view/{id?}', 'AnointedPostController@show');

    // Admin Event
    Route::get('/event', 'EventController@index');
    Route::get('/event/data', 'EventController@data');
    Route::get('/event/add', 'EventController@create');
    Route::get('/event/edit/{id?}', 'EventController@create');
    Route::post('/event/save', 'EventController@store');
    Route::post('/event/change-status', 'EventController@changeStatus');
    Route::get('/event/delete/{id?}', 'EventController@destroy');
    Route::get('/event/view/{id?}', 'EventController@show');
    Route::post('/event/change-status', 'EventController@changeStatus');

    // Admin App Version
    Route::get('/app-version', 'AppVersionController@index');
    Route::get('/app-version/data', 'AppVersionController@data');
    Route::get('/app-version/add-version', 'AppVersionController@create');
    Route::get('/app-version/edit/{id?}', 'AppVersionController@create');
    Route::post('/app-version/save', 'AppVersionController@store');
    Route::post('/app-version/change-status', 'AppVersionController@changeStatus');
    Route::get('/app-version/delete/{id?}', 'AppVersionController@destroy');
    Route::get('/app-version/view/{id?}', 'AppVersionController@show');
    Route::get('/app-version/global-setting', 'AppVersionController@globalSetting');
    Route::post('/app-version/global-setting-save', 'AppVersionController@globalSettingSave');
    Route::get('/app-version/send-notification', 'AppVersionController@sendNotification');

    // Admin Notifications
    Route::get('/admin-notification', 'AdminNotificationController@index');
    Route::get('/admin-notification/data', 'AdminNotificationController@data');
    Route::get('/admin-notification/add', 'AdminNotificationController@create');
    Route::get('/admin-notification/edit/{id?}', 'AdminNotificationController@create');
    Route::post('/admin-notification/save', 'AdminNotificationController@store');
    Route::post('/admin-notification/change-status', 'AdminNotificationController@changeStatus');
    Route::get('/admin-notification/delete/{id?}', 'AdminNotificationController@destroy');
    Route::get('/admin-notification/view/{id?}', 'AdminNotificationController@show');


});

Route::fallback(function(){
	return getResponse(113,401);
});
