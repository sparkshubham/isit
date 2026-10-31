<?php

use Illuminate\Support\Facades\Route;

use Carbon\Carbon;

use App\Models\User;

use Illuminate\Http\Request;
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

Route::get('/verfiy-mail/{verifyKey}', 'HomeController@verfiyMail')->name('verfiy-mail');
Route::get('/verfiy-phone/{verifyKey}', 'HomeController@verfiyPhone')->name('verfiy-phone');

Route::get('/privacy-policy', 'HomeController@privacyPolicy')->name('privacy-policy');
Route::get('/terms-of-uses', 'HomeController@termsOfUse')->name('terms-of-uses');
Route::get('/about-us', 'HomeController@aboutUs')->name('about-us');
Route::get('/home', 'frontend\DashboardController@getPostsBoardNew')->name('home');
Route::get('/support', 'HomeController@support')->name('support');
Route::post('/support-user-web', 'HomeController@supportUser')->name('support-user-web');


// ['middleware' => ['auth']]
// Route::group(function () {
    Route::get('/agora-chat', 'AgoraVideoController@index');
    Route::post('/agora/token', 'AgoraVideoController@token')->name('agora-token');
    Route::post('/agora/call-user', 'AgoraVideoController@callUser');
    Route::get('/agora-events', 'AgoraVideoController@getAvailableEvent')->name('event-live');

// });

// Route::get('terms-of-use', 'frontend\DashboardController@termsOfUse')->name('terms-of-use');
// Route::get('privacy-policy', 'frontend\DashboardController@privacyPolicy')->name('privacy-policy');

Route::group(['prefix'=> '','namespace'=>'frontend\Auth'], function(){   
    Route::get('/', ['as' => 'login', 'uses' => 'LoginController@showLoginForm'])->name('front-login');
    $url = request()->url();
    $pos = strpos($url, '/admin-panel');
    if($pos === true){
        Route::get('admin-panel/login', function () {    
            return redirect('/admin-panel/login');
        })->name('admin-login');
    }else{
        Route::get('/login', function () {    
            return redirect('/');
        })->name('login');

    }    
    Route::get('refreshcaptcha', 'LoginController@refreshCaptcha');
    Route::post('login', [ 'uses' => 'LoginController@login'])->name('front-login');
    Route::get('logout', ['as' => 'logout', 'uses' => 'LoginController@logout'])->name('front-logout');
    Route::get('register', [ 'uses' => 'LoginController@frontRegister'])->name('front-register');
    Route::post('postregister', [ 'uses' => 'LoginController@frontPostRegister'])->name('postregister');
    Route::get('verify/email/{email}', [ 'uses' => 'LoginController@verification'])->name('front-verify-email');
    Route::get('verify/phone/{phone}', [ 'uses' => 'LoginController@verificationPhone'])->name('front-verify-phone');

    Route::get('resend_verify/{email}', [ 'uses' => 'LoginController@resendVerification'])->name('front-resend-verify');
    Route::get('resend_verify_phone/{phone}', [ 'uses' => 'LoginController@resendVerificationPhone'])->name('front-resend-verify-phone');
  
    Route::post('verifypost', [ 'uses' => 'LoginController@postVerify'])->name('verifypost');
    Route::post('verifypostphone', [ 'uses' => 'LoginController@postVerifyphone'])->name('verifypostphone');

   
    
    // Route::get('/verfiy-mail/{verifyKey}', 'LoginController@verfiyMail')->name('verfiy-mail');
    
    Route::get('password/forgot', 'LoginController@passwordForgot')->name('password-forgot');
    Route::get('password/reset', ['as' => 'password.reset', 'uses' => 'ForgotPasswordController@showLinkRequestForm']);
    
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm');
    Route::post('password/email', ['as' => 'password.email', 'uses' => 'ForgotPasswordController@sendResetLinkEmail']);
    Route::post('password/reset', ['as' => 'password.reset', 'uses' => 'ResetPasswordController@reset']);
    Route::get('link-expired', 'LoginController@passwordLinkExpired');
    
    Route::get('set-password/{email}', [ 'uses' => 'LoginController@showSetForm'])->name('front-set-password');

});

Route::group(['prefix'=> '','namespace'=>'frontend','middleware' => ['auth:web','web','checkstatus']], function() {


   
    Route::get('/dashboard', 'GeneralPostController@create');
    Route::get('/change-password', 'ProfileController@showChangePasswordForm')->name('frontend-changePassword-form');
    Route::post('/changePassword', 'ProfileController@changePassword')->name('frontend-changePassword');
    Route::post('/profileUpdate', 'ProfileController@profileUpdate')->name('frontend-profile-update');   
    Route::get('/profile', 'ProfileController@index')->name('profile');
    Route::get('/help', 'DashboardController@help')->name('help');

    
    Route::post('/help-user-web', 'DashboardController@helpUser')->name('help-user-web');
    Route::post('/report-post-user', 'DashboardController@reportPost')->name('report-post-user');
    Route::get('/notification', 'DashboardController@notification')->name('notification');


    
    //post index routes 
    // Route::get('/post', 'GeneralPostController@index')->name('post-index');
    Route::post('/general-post/save', 'GeneralPostController@gernalPostStore')->name('general-post-save');
    Route::post('/upload-post-media', 'GeneralPostController@uploadPostMedia')->name('upload-post-media');
    Route::post('/upload-post-media2', 'GeneralPostController@storeMedia')->name('upload-post-media2');
    Route::post('/get-post', 'GeneralPostController@getPost')->name('get-post');

    
    

    Route::get('/explore', 'ExplorePostController@index')->name('explore');
    Route::get('/explore-view/{post_id}', 'ExplorePostController@view')->name('explore-view');
    Route::get('/anointed', 'AnointedPostController@index')->name('anointed');
    Route::post('/anointed-post/save', 'AnointedPostController@store')->name('anointed-post-save');
    
    //Search User
    Route::get('/search-user', 'ProfileController@searchUserList')->name('search-user');
    
    // profile User
    // Route::get('/my-profile/{userId}', 'ProfileController@myProfile')->name('my-profile');
    Route::get('/my-profile', 'ProfileController@myProfile')->name('my-profile');
    Route::get('/user-profile/{user_id}', 'ProfileController@userProfile')->name('user-profile');
    Route::Post('/public-visibility-change', 'ProfileController@publicVisibilityChange')->name('public-visibility-change');
  


     // Follower unfollower List
    Route::get('/follower-unfollower/{user_id}/{type}', 'ProfileController@followUnfollowList')->name('follower-unfollower');
    Route::post('/follower-list', 'ProfileController@followerList')->name('follower-list');
    Route::post('/following-list', 'ProfileController@followingList')->name('following-list');
    Route::post('/follow-user', 'ProfileController@followUser')->name('follow-user');
    Route::post('/unfollow-user', 'ProfileController@unfollowUser')->name('unfollow-user');


    // Pending List Method
    Route::get('/pending-request', 'ProfileController@pendingRequest')->name('pending-request');  
    Route::post('/accept-following-request', 'ProfileController@acceptFollowRequest')->name('accept-following-request');
    Route::post('/decline-following-request', 'ProfileController@declineFollowRequest')->name('decline-following-request');
   
    
     // List Blocked Users
    Route::get('/blocked-users', 'ProfileController@getBlockUser')->name('blocked-users');
    Route::post('/block-user', 'ProfileController@blockUser')->name('block-user');
    Route::post('/unblock-user', 'ProfileController@UnblockUser')->name('unblock-user');


    ///Like
    Route::post('/like-post', 'GeneralPostController@likePost')->name('like-post');
    Route::post('/unlike-post', 'GeneralPostController@unlikePost')->name('unlike-post');
    Route::post('/like-user-list', 'GeneralPostController@getLikedUserList')->name('like-user-list');


    //comment post
    Route::post('/comment-post', 'GeneralPostController@commentPost')->name('comment-post');
    Route::post('/remove-comment', 'GeneralPostController@removeCommentPost')->name('remove-comment');
    // Route::get('/comment-list', 'GeneralPostController@getPostCommentListing')->name('comment-list');
    Route::post('/comment-list', 'GeneralPostController@getPostCommentListing')->name('comment-list');

    Route::post('/replied-comment-list', 'GeneralPostController@getRepliedComments')->name('replied-comment-list');

    Route::post('/like-comment', 'GeneralPostController@likeComment')->name('like-comment');
    Route::post('/unlike-comment', 'GeneralPostController@unlikeComment')->name('unlike-comment');
    Route::post('/comment-like-user-list', 'GeneralPostController@getCommentLikedUserList')->name('comment-like-user-list');
    
    //
    
    Route::post('/delete-post', 'GeneralPostController@deletePost')->name('delete-post');
    Route::post('/save-post', 'GeneralPostController@savePost')->name('save-post');
    Route::post('/remove-save-post', 'GeneralPostController@unsavePost')->name('remove-save-post');
   /******* Event */
    // Route::get('/event-list', 'EventController@index')->name('event-list');
    
    Route::get('/event-list-my', 'EventController@index')->name('event-list-my');
    Route::get('/event-list-other', 'EventController@getPostboardEvent')->name('event-list-other');
    Route::post('/get-event', 'EventController@getEvent')->name('get-event');
    Route::get('/create-event-popup', 'EventController@createPopup')->name('create-event-popup');

    
    Route::get('/event-view/{event_id}', 'EventController@view')->name('event-view');
  



    Route::post('/create-event', 'EventController@createEvent')->name('create-event');
    Route::post('/delete-event', 'EventController@deleteEvent')->name('delete-event');
    Route::post('/save-event', 'EventController@saveEvent')->name('save-event');
    Route::post('/remove-save-event', 'EventController@unsaveEvent')->name('remove-save-event');
    Route::post('/get-saved-events', 'EventController@getSavedEvents')->name('get-saved-events');
    /******* Event */
    

    /******* Store/Product Controller**/
    // Route::get('/store', 'StoreController@index')->name('store');
    Route::get('/store-my', 'StoreController@index')->name('store-my');
    Route::get('/store-other', 'StoreController@getPostboardProduct')->name('store-other');



    Route::post('/create-edit-product', 'StoreController@createEditProduct')->name('create-edit-product');
    Route::post('/create-product', 'StoreController@createProduct')->name('create-product');
    Route::get('/product-view/{product_id}', 'StoreController@view')->name('product-view');


    
    Route::post('/delete-product', 'StoreController@deleteProduct')->name('delete-product');
    Route::post('/product-status', 'StoreController@productStatus')->name('product-status');
   
    /******* Product */

    //Post Board
    Route::get('/posts', 'DashboardController@getPostsBoardNew')->name('post-index');
    Route::get('/post-board', 'DashboardController@getPostsBoardNew')->name('post-board');



    // Under Construction route
    
    Route::post('/upload-file-S3', 'ProfileController@uploadFileS3')->name('upload-file-S3');
    // Route::get('/message', 'MessageController@index')->name('message.index');
    Route::post('/branch-io-share', 'ProfileController@branchIoUrl');



    Route::get('/agora-view', function (Request $request) {
       
        $response['post_id']= $request->post_id;
        $view['broadcast'] = view('frontend.agora.broadcast',$response)->render();
        $view['audience'] = view('frontend.agora.audience',$response)->render();
        return response()->json($view);
        
    });
    

    Route::get('/video-call', function () {
        return view('frontend.message.video');
    });

Route::post('/user-go-live', 'ProfileController@userGoLive')->name('user-go-live');
Route::get('/user-stop-live', 'ProfileController@userStopLive')->name('user-stop-live');

Route::post('/update-user-live-count', 'ProfileController@updateUserLiveCount')->name('update-user-live-count');

Route::post('/send-web-notification', 'ProfileController@sendWebNotification')->name('send-web-notification');
Route::post('/store-token-web', 'ProfileController@storeToken')->name('store.token');



Route::post('/send-busy-notification', 'ProfileController@sendBusyNotification')->name('send-busy-notification');

Route::post('/start-agora-recording', 'ProfileController@startAgoraRecording')->name('start-agora-recording');
Route::post('/stop-agora-recording', 'ProfileController@stopAgoraRecording')->name('stop-agora-recording');
Route::get('/live-user-list', 'ProfileController@getUserLiveList')->name('live-user-list');


Route::get('/agora-view-test', function () {
    return view('frontend.common.video-test');
});





});


Route::fallback(function(){
    return view('frontend.common.404');
});