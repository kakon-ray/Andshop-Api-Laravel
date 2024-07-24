<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientDashboard;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\VendorManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/reset_password', [PasswordResetRequestController::class, 'reset_password_submit'])->name('reset_password');
Route::get('reset/password/{token}', [PasswordResetRequestController::class, 'show_reset_password_form'])->name('reset.password');
Route::post('/new-password', [PasswordResetRequestController::class, 'new_password_submit'])->name('new.password.submit');


//admin login and registration and check
Route::post('/sign_up', [AdminAuthController::class, 'regisign_upster'])->name('sign_up');
Route::post('/user_login', [AdminAuthController::class, 'login'])->name('user_login');


// verification email
Route::post('/email-verified', [RegController::class, 'email_verified'])->name('email_verified');


Route::group(['middleware' => ['jwt.role:admin', 'jwt.auth']], function ($router) {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');


    Route::post('/category/add', [CategoryController::class, 'category_store']);
    Route::get('/category/show', [CategoryController::class, 'category_show']);
    Route::post('/category/edit', [CategoryController::class, 'category_edit']);
    Route::get('/category/delete/{id}', [CategoryController::class, 'category_delete']);


    Route::post('/subcategory/add', [SubcategoryController::class, 'sub_category_store']);
    Route::get('/subcategory/show', [SubcategoryController::class, 'sub_category_show']);
    Route::post('/subcategory/edit', [SubcategoryController::class, 'sub_category_edit']);
    Route::get('/subcategory/delete/{id}', [SubcategoryController::class, 'sub_category_delete']);


    // only api created
    Route::post('/role/request/accepted', [VendorManagement::class, 'user_request_personal_info_accepted']);
    
    // only api created
    Route::post('/role/request/cancel', [VendorManagement::class, 'user_request_personal_info_cancel']);

    // only api created
    Route::get('/product/manage', [VendorManagement::class, 'product_manage']);

    // only api created
    Route::post('/product/approved', [VendorManagement::class, 'product_approved']);

    // only api created
    Route::post('/product/cancel', [VendorManagement::class, 'product_cancel']);
});



// ================ user or vendor authentication ========================== 


// user or vendor login and registration and check
Route::post('/user/sign_up', [RegController::class, 'regisign_upster'])->name('user.sign_up');
Route::post('/user/user_login', [ClientDashboard::class, 'login'])->name('user.user_login');


Route::group(['middleware' => ['jwt.role:userbasic', 'jwt.auth']], function ($router) {

    // user management
    Route::get('/user/show/{id}', [ClientDashboard::class, 'user_show']);
    Route::post('/user/update', [ClientDashboard::class, 'user_update']);
    Route::post('/user/role/request/submit', [ClientDashboard::class, 'user_request_personal_info_submit']);


    // only api created
    Route::post('/product/add', [ProductController::class, 'product_add']);
    // only api created
    Route::get('/specific/product/show/{vendor_id}', [ProductController::class, 'specific_product_show']);
    // only api created
    Route::post('/product/edit', [ProductController::class, 'product_edit']);
    // only api created
    Route::get('/product/delete/{id}', [ProductController::class, 'delete_product']);
});










// google and facebook login
Route::get('auth/{provider}', [SocialiteController::class, 'loginSocial'])
    ->middleware(['web']);

Route::get('auth/{provider}/callback', [SocialiteController::class, 'callbackSocial'])
    ->middleware(['web']);



    // php artisan serve --host 192.168.5.239 --port 8000    
    // 192.168.5.239 my ip
    // ipconfig (See My IP)