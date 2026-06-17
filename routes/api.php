<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\CityController as SitiesController;
use App\Http\Controllers\Api\Admin\{
    DashboardController,
    UserController,
    ShipmentController,
    ConsolidationController,
    AddressController,
    CategoryController,
    WeightDiscountController,
    SettingController,
    StatisticsController,
    InvoiceController,
    RevenueController,
    DebtorController,
    CityController,
    StoreController,
    PageController,
    FinancialController,
    SubCategoryController,
    SubSubCategoryController
};
use App\Http\Controllers\CityPublicController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);




Route::get('/public/cities', [CityPublicController::class, 'index']);
// Email verification (Laravel built-in)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Authenticated (user & admin)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User dashboard (must be verified and approved)
    Route::middleware(['verified'])->group(function () {
        Route::get('/user/profile', [UserDashboardController::class, 'profile']);
        Route::put('/user/profile', [UserDashboardController::class, 'updateProfile']);
        Route::put('/user/avatar', [UserDashboardController::class, 'updateAvatar']);
        Route::get('/user/shipments', [UserDashboardController::class, 'shipments']);
        Route::get('/user/prohibited-items', [UserDashboardController::class, 'prohibitedItems']);
        Route::post('/user/change-password', [UserDashboardController::class, 'changePassword']);
    });
});

// Admin routes (must be admin)
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/{user}/status', [UserController::class, 'updateStatus']);

    // Shipments
    Route::apiResource('shipments', ShipmentController::class);
    Route::get('shipments/fetch-customer', [ShipmentController::class, 'fetchCustomer']);

    // Consolidations
    Route::apiResource('consolidations', ConsolidationController::class);

    // Addresses, Weight Discounts, Settings, Cities, Stores, Pages
    Route::apiResource('addresses', AddressController::class);
    Route::apiResource('weight-discounts', WeightDiscountController::class);
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('cities', CityController::class);
    Route::apiResource('stores', StoreController::class);
    Route::apiResource('pages', PageController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('sub-categories', SubCategoryController::class);
    Route::apiResource('sub-sub-categories', SubSubCategoryController::class);

    // Statistics
    Route::get('statistics/top-cities', [StatisticsController::class, 'topCities']);
    Route::get('statistics/active-users', [StatisticsController::class, 'activeUsers']);
    Route::get('statistics/city-wise-business', [StatisticsController::class, 'cityWiseBusiness']);
    Route::get('statistics/shipments', [StatisticsController::class, 'shipmentsStats']);
    Route::get('statistics/delivery-time', [StatisticsController::class, 'deliveryTime']);
    Route::get('statistics/debtors-balance', [StatisticsController::class, 'debtorsBalance']);

    // Invoices, Revenues, Debtors
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('revenues', RevenueController::class);
    Route::get('revenues/total', [RevenueController::class, 'total']);
    Route::apiResource('debtors', DebtorController::class);

    // Financial
    Route::get('financial/pl', [FinancialController::class, 'profitAndLoss']);
    Route::get('financial/trial-balance', [FinancialController::class, 'trialBalance']);
});
