<?php

use App\Http\Middleware\AuthenticateUser;
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

Route::post('register', 'Auth\AuthController@register');
Route::post('login', 'Auth\AuthController@login');
Route::get('states', 'Admin\StateController@get');
Route::post('verify-contact', 'Auth\AuthController@verifyContact');
Route::post('forgot-password', 'Auth\AuthController@forgotPassword');
Route::post('forgot-password-update', 'Auth\AuthController@forgotPasswordUpdate');
Route::get('states', 'Admin\StateController@get');
Route::get('pin-code-data/{pin_code}', 'Admin\StateController@getPinCodeData');


Route::post('testimonial', 'Admin\TestimonialController@add');
Route::get('testimonial', 'Admin\TestimonialController@get');
Route::get('gallery', 'Admin\GalleryController@get');
Route::post('contact', 'Admin\ContactController@add');

Route::get('package', 'Admin\PackageController@get');
Route::get('package/{id}', 'Admin\PackageController@detail');

Route::middleware([AuthenticateUser::class])->group(function () {
    Route::post('file-upload', 'User\UploadController@uploadFile');
    Route::get('self', 'Auth\AuthController@self');
    Route::put('update-self-data', 'Auth\AuthController@updateSelfData');
    Route::put('update-self-password', 'Auth\AuthController@updateSelfPassword');
    Route::get('logout', 'Auth\AuthController@logout');
});
