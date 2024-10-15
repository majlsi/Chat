<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['prefix' => 'v1'],
    function () {

        //sign in & register
        Route::post('authenticate', 'Auth\AuthenticateController@authenticate');
        Route::post('authenticate/invalidate', 'Auth\AuthenticateController@invalidate');
        Route::post('authenticate/register', 'Auth\AuthenticateController@register');
      
        //Social login
        Route::get('social-callback/{provider}', 'Auth\AuthenticateController@handleSocialCallback');
        Route::post('social-login', 'Auth\AuthenticateController@socialLogin');

        //Forget password
        Route::post('authenticate/forget-password', 'Auth\Password\ForgotPasswordController@getResetToken');
        Route::post('authenticate/reset-password', 'Auth\Password\ResetPasswordController@reset');
        Route::post('authenticate/valid-code', 'Auth\Password\ResetPasswordController@codeValid');

        //Verify user
        Route::post('authenticate/verify-account', 'Auth\AuthenticateController@verifyUser');
        Route::post('authenticate/send-confirmation-code', 'Auth\AuthenticateController@resendVerificationCode');

        //Image upload
        Route::post('upload', 'UploadController@uploadImage');
        Route::post('upload-attachment', 'UploadController@uploadFile');

    });

Route::group(['prefix' => 'v1', 'middleware' => ['jwt.customAuth']],
    function () {

        //users
        Route::get('authenticate/user', 'Auth\AuthenticateController@getAuthenticatedUser');
        Route::post('users/filtered-list', 'UserController@getPagedList');
        
        Route::resource('users', 'UserController');
        Route::resource('chat-rooms', 'ChatRoomController');

        //Roles & access
        Route::post('roles/filtered-list', 'RoleController@getPagedList');
        Route::resource('roles', 'RoleController');
        
        Route::group(['prefix' => 'userAccess'],
            function () {
                Route::get('users/rights', 'RoleController@getRoleRights');
                Route::get('roles/modules-rights', 'RoleController@getModulesRights');
                Route::get('roles/CanAccess/{rightId}', 'RoleController@CanAccess');
            });
   
        // chat rooms
        Route::resource('chat-rooms', 'ChatRoomController');
        Route::post('chat-rooms/{room_id}/add-users','ChatRoomController@addUsersToChatRoom');
        Route::delete('chat-rooms/{room_id}/chat-room-users/{id}','ChatRoomController@deleteUserFromChatRoom');
        Route::post('chat-rooms/{room_id}/check-user-exist','ChatRoomController@checkUserExistInChatRoom');
       
        Route::post('chat-rooms/{room_id}/close-room','ChatRoomController@closeChatRoom');
        Route::post('chat-rooms/filtered-list', 'ChatRoomController@getPagedList');
        Route::post('chat-rooms/{id}/attachments/filtered-list', 'ChatRoomController@getChatRoomAttachments');

        // chat messages
        Route::resource('chat-messages', 'ChatRoomMessageController');
        Route::post('chat-messages/attachment', 'ChatRoomMessageController@sendAttachmentMessage');
        Route::post('chat-messages/filtered-list', 'ChatRoomMessageController@getPagedList');

    });
