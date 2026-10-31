<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
/--------------------------------------------------------------------------
/ Admin Routes With Out Login
/---------------------------------------------------------------------------
*/
// echo "front";exit;

/*--------------------------------------------------------------------------
/ Admin Routes With Out Login
/---------------------------------------------------------------------------

// /*
// /--------------------------------------------------------------------------
// / front Routes Routes After Login
// /---------------------------------------------------------------------------
// */
Route::group(['prefix'=> 'front-panel','namespace'=>'backend','middleware' => ['auth:front_user','front_user']], function() {
    // front Dashboard
    Route::get('/dashboard', 'DashboardController@index');
    Route::post('/dashboard/change-date', 'DashboardController@changeDate');
    // front chagne password
    Route::get('/change-password', 'HomeController@showChangePasswordForm');
    Route::post('/changePassword', 'HomeController@changePassword')->name('backend.changePassword');
    // front chagne password
    Route::get('/change-profile{id?}', 'HomeController@showChangeProfileForm');
    Route::post('/changeProfile', 'HomeController@changeProfile')->name('backend.changeProfile');
    // front User
    Route::get('/users/add', 'UserController@create');
    Route::get('/users/view/{id?}', 'UserController@show');
    Route::get('/users/delete/{id?}', 'UserController@destroy');
    Route::get('/users', 'UserController@index');
    Route::get('/users/data', 'UserController@data');
    Route::post('/users/change-status', 'UserController@changeStatus');
    // front CMS Pages
    Route::get('/cms-page', 'CmsController@index');
    Route::get('/cms-page/data', 'CmsController@data');
    Route::get('/cms-page/add', 'CmsController@create');
    Route::get('/cms-page/edit/{id?}', 'CmsController@create');
    Route::post('/cms-page/save', 'CmsController@store');
    Route::post('/cms-page/change-status', 'CmsController@changeStatus');
    Route::get('/cms-page/delete/{id?}', 'CmsController@destroy');
    Route::get('/cms-page/view/{id?}', 'CmsController@show');
    // front Daily Devotional Post
    Route::get('/front-post', 'FrontPostController@index');
    Route::get('/front-post/data', 'FrontPostController@data');
    Route::get('/front-post/add', 'FrontPostController@create');
    Route::get('/front-post/edit/{id?}', 'FrontPostController@create');
    Route::post('/front-post/save', 'FrontPostController@store');
    Route::post('/front-post/change-status', 'FrontPostController@changeStatus');
    Route::get('/front-post/delete/{id?}', 'FrontPostController@destroy');
    Route::post('/front-post/upload-image', 'FrontPostController@uploadImage');
    Route::get('/front-post/view/{id?}', 'FrontPostController@show');
    // front Reportd Post
    Route::get('/report-post', 'ReportPostController@index');
    Route::get('/report-post/data', 'ReportPostController@data');
    Route::get('/report-post/add', 'ReportPostController@create');
    Route::get('/report-post/edit/{id?}', 'ReportPostController@create');
    Route::post('/report-post/save', 'ReportPostController@store');
    Route::post('/report-post/change-status', 'ReportPostController@changeStatus');
    Route::get('/report-post/delete/{id?}', 'ReportPostController@destroy');
    Route::get('/report-post/view/{id?}', 'ReportPostController@show');
    Route::post('/report-post/change-status', 'ReportPostController@changeStatus');
    // front Help User
    Route::get('/help-user', 'HelpUserController@index');
    Route::get('/help-user/data', 'HelpUserController@data');
    Route::get('/help-user/add', 'HelpUserController@create');
    Route::get('/help-user/edit/{id?}', 'HelpUserController@create');
    Route::post('/help-user/save', 'HelpUserController@store');
    Route::post('/help-user/change-status', 'HelpUserController@changeStatus');
    Route::get('/help-user/delete/{id?}', 'HelpUserController@destroy');
    Route::get('/help-user/view/{id?}', 'HelpUserController@show');
    Route::post('/help-user/change-status', 'HelpUserController@changeStatus');
    // front General Post
    Route::get('/general-post', 'GeneralPostController@index');
    Route::get('/general-post/data', 'GeneralPostController@data');
    Route::get('/general-post/add', 'GeneralPostController@create');
    Route::get('/general-post/edit/{id?}', 'GeneralPostController@create');
    Route::post('/general-post/save', 'GeneralPostController@store');
    Route::post('/general-post/change-status', 'GeneralPostController@changeStatus');
    Route::get('/general-post/delete/{id?}', 'GeneralPostController@destroy');
    Route::post('/general-post/upload-image', 'GeneralPostController@uploadImage');
    Route::get('/general-post/view/{id?}', 'GeneralPostController@show');
    // front General Post
    Route::get('/anointed-post', 'AnointedPostController@index');
    Route::get('/anointed-post/data', 'AnointedPostController@data');
    Route::get('/anointed-post/add', 'AnointedPostController@create');
    Route::get('/anointed-post/edit/{id?}', 'AnointedPostController@create');
    Route::post('/anointed-post/save', 'AnointedPostController@store');
    Route::post('/anointed-post/change-status', 'AnointedPostController@changeStatus');
    Route::get('/anointed-post/delete/{id?}', 'AnointedPostController@destroy');
    Route::post('/anointed-post/upload-image', 'AnointedPostController@uploadImage');
    Route::get('/anointed-post/view/{id?}', 'AnointedPostController@show');
    // front Event
    Route::get('/event', 'EventController@index');
    Route::get('/event/data', 'EventController@data');
    Route::get('/event/add', 'EventController@create');
    Route::get('/event/edit/{id?}', 'EventController@create');
    Route::post('/event/save', 'EventController@store');
    Route::post('/event/change-status', 'EventController@changeStatus');
    Route::get('/event/delete/{id?}', 'EventController@destroy');
    Route::get('/event/view/{id?}', 'EventController@show');
    Route::post('/event/change-status', 'EventController@changeStatus');
});
Route::fallback(function(){
	return getResponse(113,401);
});
