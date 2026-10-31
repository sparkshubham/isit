<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix'=> '/v1','name'=>'api.','namespace'=>'Api\V1','middleware' => ['api.beforefilter']], function() {
	Route::post('/register', 'UserController@register')->name('api.register');
    Route::post('/login', 'UserController@login')->name('api.login');
    Route::post('/resend-verification-mail ', 'UserController@resendVerificationMail')->name('api.resend-verification-mail ');
    Route::post('/password-reset', 'UserController@sendForgotPasswordLink')->name('api.password-reset');
    Route::post('/validate-reset-token', 'UserController@validateResetCode')->name('api.validate-reset-token');
    Route::post('/update-password', 'UserController@updatePassword')->name('api.update-password');
    Route::post('/update-password', 'UserController@updatePassword')->name('api.update-password');
    Route::get('/global-setting', 'UserController@globalSettings')->name('api.global-setting');
    Route::get('/get-app-version', 'UserController@getAppVersion')->name('api.get-app-version');
    Route::post('/get-twilio-callback', 'UserController@getTwilioCallBack')->name('api.get-twilio-callback');
    Route::post('/send-notification-test', 'UserController@sendNotificationTest')->name('api.send-notification-test');
    Route::post('/send-busy-notification', 'UserController@sendBusyNotification')->name('api.send-busy-notification');
    Route::get('/send-notification-event', 'ProductController@sendNotificationEvent')->name('api.send-notification-event');
    Route::post('/misscall-notification', 'UserController@misscallNotification')->name('api.misscall-notification');
    Route::get('/hobbies', 'UserController@getHobbies')->name('api.hobbies');
});

Route::group(['prefix'=> '/v1','name'=>'api.','namespace'=>'Api\V1','middleware' => ['auth:api','api.beforefilter']], function() {    
    Route::get('/profile/{userId}', 'UserController@profile')->name('profile');
    Route::post('/change-password', 'UserController@changePassword')->name('change-password');
    Route::post('/profile', 'UserController@setProfile')->name('profile');
    Route::get('/countries', 'UserController@getCountries')->name('countries');
    Route::get('/categories', 'UserController@getCategories')->name('categories');
    Route::get('/logout', 'UserController@logout')->name('logout');
    Route::get('/public-visibility-update', 'UserController@publicVisibilityUpdate')->name('public-visibility-update');
    Route::get('/get-twillio-access-token', 'UserController@getTwillioAccessToken')->name('get-twillio-access-token');
    Route::get('/check-authentication', 'UserController@checkAuthentication')->name('check-authentication');
    Route::post('/device-token-update', 'UserController@deviceTokenUpdate')->name('device-token-update');
    Route::get('/get-notification-count', 'UserController@getNotificationCount')->name('get-notification-count');
    Route::get('/delete-user', 'UserController@deleteUser')->name('delete-user');
    Route::post('/user-go-live', 'UserController@userGoLive')->name('user-go-live');
    Route::get('/user-stop-live', 'UserController@userStopLive')->name('user-stop-live');
    Route::post('/is-user-live', 'UserController@isUserLive')->name('is-user_live');
    Route::post('/update-user-live-count', 'UserController@updateUserLiveCount')->name('update-user-live-count');
    Route::post('/users-live-list', 'UserController@getUserLiveList')->name('users-live-list');
    Route::post('/send-go-live-notifications', 'UserController@sendGoLiveNotifications')->name('send-go-live-notifications');
    Route::post('/report-user', 'UserController@reportUser')->name('report-user');
    Route::post('/get-contact-detail-user', 'UserController@getContactDetailUser')->name('get-contact-detail-user');

    /************Posts */
    Route::post('/general-post', 'PostController@generalPost')->name('general-post');
    Route::post('/anointed-post', 'PostController@anointedPost')->name('anointed-post');
    Route::post('/upload-post-media', 'PostController@uploadPostMedia')->name('upload-post-media');
    Route::post('/get-posts', 'PostController@getPosts')->name('get-posts');
    Route::post('/get-post', 'PostController@getPost')->name('get-post');
    Route::post('/get-explore-general', 'PostController@getGeneralExplore')->name('get-explore-general');
    Route::post('/get-explore-anointed', 'PostController@getAnointedExplore')->name('get-explore-anointed');
    Route::post('/save-post', 'PostController@savePost')->name('save-post');
    Route::post('/remove-save-post', 'PostController@unsavePost')->name('remove-save-post');
    Route::post('/get-saved-posts', 'PostController@getSavedPosts')->name('get-saved-posts');
    Route::post('/like-post', 'PostController@likePost')->name('like-post');
    Route::post('/unlike-post', 'PostController@unlikePost')->name('unlike-post');
    Route::post('/like-user-list', 'PostController@getLikedUserList')->name('like-user-list');
    Route::post('/get-post-board', 'PostController@getPostsBoard')->name('get-post-board');
    Route::post('/delete-post', 'PostController@deletePost')->name('delete-post');
    Route::post('/comment-post', 'PostController@commentPost')->name('comment-post');
    Route::post('/remove-comment', 'PostController@removeCommentPost')->name('remove-comment');
    Route::post('/comment-list', 'PostController@getPostCommentListing')->name('comment-list');
    Route::post('/replied-comment-list', 'PostController@getRepliedComments')->name('replied-comment-list');
    Route::post('/report-post', 'PostController@reportPost')->name('report-post');
    Route::post('/get-post-board-new', 'PostController@getPostsBoardNew')->name('get-post-board-new');
    Route::post('/like-comment', 'PostController@likeComment')->name('like-comment');
    Route::post('/unlike-comment', 'PostController@unlikeComment')->name('unlike-comment');
    Route::post('/comment-like-user-list', 'PostController@getCommentLikedUserList')->name('comment-like-user-list');
    Route::post('/create-post-with-media', 'PostController@createPostWithMedia')->name('create-post-with-media');
    Route::post('/get-post-board-v2', 'PostController@getPostsBoardV2')->name('get-post-board-v2');
    Route::post('/get-post-board-new-v2', 'PostController@getPostsBoardNewV2')->name('get-post-board-new-v2');
    /************Posts */
    /******* User Follow */
    Route::post('/follow-user', 'UserController@followUser')->name('follow-user');
    Route::post('/unfollow-user', 'UserController@unfollowUser')->name('unfollow-user');
    Route::post('/follower-list', 'UserController@followerList')->name('follower-list');
    Route::post('/following-list', 'UserController@followingList')->name('following-list');
    Route::post('/following-request-list', 'UserController@followingRequestList')->name('following-request-list');
    Route::post('/accept-following-request', 'UserController@acceptFollowRequest')->name('accept-following-request');
    Route::post('/decline-following-request', 'UserController@declineFollowRequest')->name('decline-following-request');
    Route::post('/search-user', 'UserController@searchUserList')->name('search-user');
    /********  User Follow */
    /******* User Block */
    Route::post('/block-user', 'UserController@blockUser')->name('block-user');
    Route::post('/unblock-user', 'UserController@UnblockUser')->name('unblock-user');
    Route::post('/block-user-list', 'UserController@blockUserList')->name('block-user-list');
    /********  User Block */
    /************Articles */
    Route::get('articles', 'ArticleController@index');
    Route::get('articles/{article}', 'ArticleController@show');
    Route::post('articles', 'ArticleController@store');
    Route::put('articles/{article}', 'ArticleController@update');
    Route::delete('articles/{article}', 'ArticleController@delete');
    /************Articles */
    Route::post('/help', 'UserController@helpUser')->name('help-user');
    /******* Product */
    Route::post('/product', 'ProductController@createProduct')->name('product');
    Route::post('/upload-product-media', 'ProductController@uploadProductMedia')->name('upload-product-media');
    Route::post('/get-products', 'ProductController@getProducts')->name('get-products');
    Route::post('/get-product', 'ProductController@getProduct')->name('get-product');
    Route::post('/delete-product', 'ProductController@deleteProduct')->name('delete-product');
    Route::post('/get-postboard-products', 'ProductController@getPostboardProduct')->name('get-postboard-products');
    Route::post('/product-status', 'ProductController@productStatus')->name('product-status');
    /******* Product */
    /******* Event */
    Route::post('/event', 'ProductController@createEvent')->name('event');
    Route::post('/get-events', 'ProductController@getEvents')->name('get-events');
    Route::post('/get-event', 'ProductController@getEvent')->name('get-event');
    Route::post('/delete-event', 'ProductController@deleteEvent')->name('delete-event');
    Route::post('/get-postboard-events', 'ProductController@getPostboardEvent')->name('get-postboard-events');    
    Route::post('/save-event', 'ProductController@saveEvent')->name('save-event');
    Route::post('/remove-save-event', 'ProductController@unsaveEvent')->name('remove-save-event');
    Route::post('/get-saved-events', 'ProductController@getSavedEvents')->name('get-saved-events');
    Route::get('/get-available-event', 'ProductController@getAvailableEvent')->name('get-available-event');
    /******* Event */
    Route::post('/upload-file-S3', 'UserController@uploadFileS3')->name('upload-file-S3');
    Route::post('/notifications-list', 'UserController@getUserNotificationList')->name('notifications-list');
    /******* Stories */
    Route::post('/create-story', 'ProductController@createStory')->name('create-story');
    Route::post('/delete-story', 'ProductController@deleteStory')->name('delete-story');
    Route::post('/user-story-list', 'ProductController@userStoryList')->name('user-story-list');
    Route::post('/like-user-story', 'ProductController@likeUserStory')->name('like-user-story');
    Route::post('/unlike-user-story', 'ProductController@unlikeStory')->name('unlike-user-story');
    Route::post('/get-liked-story-user-list', 'ProductController@getLikedStoryUserList')->name('get-liked-story-user-list');
    Route::post('/view-user-story', 'ProductController@viewUserStory')->name('view-user-story');
    Route::post('/get-viewed-story-user-list', 'ProductController@getViewedStoryUserList')->name('get-viewed-story-user-list');
    Route::post('/user-story-list-v2', 'ProductController@userStoryListV2')->name('user-story-list-v2');
    /************Agora Recording*/
    Route::post('/start-agora-recording', 'UserController@startAgoraRecording')->name('start-agora-recording');
    Route::post('/stop-agora-recording', 'UserController@stopAgoraRecording')->name('stop-agora-recording');
});

Route::fallback(function(){
    
	return getResponse(600,404);
});
