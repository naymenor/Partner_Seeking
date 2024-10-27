<?php

use App\Http\Controllers\CustomerMatchingController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomerShortListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use Illuminate\Support\Facades\Auth;
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


//Auth
Route::group(["prefix"=>"home"],function () {
    Route::post('register',[UserAuthController::class,'register']);
    Route::post('login',[UserAuthController::class,'login']);
    Route::post('logout',[UserAuthController::class,'logout'])->middleware('auth:sanctum');

    Route::get('profiles', [CustomerProfileController::class, 'annonindex'])->name('annonmuslist');
    Route::post('profiles/filter', [CustomerProfileController::class, 'annonfilterindex'])->name('annonmusfilterlist');
});

Route::group(["prefix"=>"admin",'middleware' => ['auth:sanctum','roleChecker:admin,null']],function(){
    
    Route::get('user/list', [UserAuthController::class, 'userlist'])->name('userlist');
    Route::get('profile/list', [CustomerProfileController::class, 'index'])->name('customerfull');
    //Route::post('profile/create', [CustomerProfileController::class, 'adminstore'])->name('createCustomer');
    Route::post('profile/customer/update/{id}', [CustomerProfileController::class, 'adminupdate'])->name('adminupdateCustomer');
    Route::get('profile/customer/details/{id}', [CustomerProfileController::class, 'adminsingleshow'])->name('adminsingleCustomer');
    Route::post('profile/customer/status/{id}', [CustomerProfileController::class, 'admininactive'])->name('admindeactiveCustomer');
    Route::post('profile/customer/verification/{id}', [CustomerProfileController::class, 'adminverified'])->name('adminverifiedCustomer');
    
    Route::get('matched', [CustomerMatchingController::class, 'index'])->name('matching');
    Route::put('matched/accept/{id}', [CustomerMatchingController::class, 'adminAppreoval'])->name('matchingadminAppreoval');
    

    Route::resource('/payment', CustomerPaymentController::class);
    
    
    

});
Route::group(["prefix"=>"customer",'middleware' => ['auth:sanctum','roleChecker:admin,customer']],function(){
    Route::get('profile/list', [CustomerProfileController::class, 'prefferedindex'])->name('customerpreffered');
    Route::post('profile/list/filter', [CustomerProfileController::class, 'customerfilteedindex'])->name('customerfiltredlist');
    // Route::post('profile/create', [CustomerProfileController::class, 'customerstore'])->name('createSelfCustomer');
    Route::get('profile/self/details', [CustomerProfileController::class, 'customerselfshow'])->name('showSelfCustomer');
   Route::put('profile/self/update/', [CustomerProfileController::class, 'customerupdate'])->name('customerupdateCustomer');
    Route::get('payment', [CustomerPaymentController::class, 'customerindex'])->name('paymentCustomer');
    Route::post('matching', [CustomerMatchingController::class, 'store'])->name('matchingCustomer');
    Route::get('matching/list', [CustomerMatchingController::class, 'customerSelfindex'])->name('matchingselfCustomer');
    Route::get('matching/request/list', [CustomerMatchingController::class, 'customerSelfRequestindex'])->name('matchingRequestCustomer');
    Route::put('matching/accept/{id}', [CustomerMatchingController::class, 'receiverAppreoval'])->name('matchingreceiverAppreoval');
    
    Route::resource('/shortlist', CustomerShortListController::class);
});

Route::any('{any}', function (Request $request) {
    return response()->json([
        'message' => 'Route not found'
    ], 404);
})->where('any', '.*');