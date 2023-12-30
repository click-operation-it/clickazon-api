<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\ForgotPasswordController;
use App\Http\Controllers\v1\Auth\VerificationController;
use App\Http\Controllers\v1\Auth\RegisterController;
use App\Http\Controllers\v1\Profile\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["prefix" => "v1"], function () {
    /** Cache */
    Route::get('/clear-cache', function () {
        Artisan::call('optimize:clear');
        return "Base Cache is cleared";
    });


    //Authentication Route
    Route::group(["prefix" => "auth"], function () {
        Route::post('signup', [RegisterController::class, 'store']);
        Route::post('ecommerce/signup', [RegisterController::class, 'ecommerceCustomerSignup']);
        Route::post('ecommerce/validate/email', [LoginController::class, 'validateEcommerceLoginEmail']);
        Route::post('/email/otpverification', [VerificationController::class, 'verifyOTP']);
        Route::get('/twofa', [VerificationController::class, 'update2fa']);
        Route::post('/twofa/update', [VerificationController::class, 'enable2fa']);
        Route::post('recover', [ForgotPasswordController::class, 'recover']);
        Route::post('reset/password', [ForgotPasswordController::class, 'reset']);
        Route::post('password/create', [ForgotPasswordController::class, 'createPassword']);
        Route::post('/email/resend-verification', [RegisterController::class, 'resendCode']);
        Route::post('login', [LoginController::class, 'login']);
        Route::get('logout', [LoginController::class, 'logout']);
    });
    
    Route::group(['prefix' => 'profile', "namespace" => "v1\Profile", 'middleware' => ["auth:api"]], function () {
        Route::post('/', [ProfileController::class, 'updateProfile']);

    });

});