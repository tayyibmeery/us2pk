<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\Admin\{
    DashboardController,
    UserController,
    ShipmentController,
    ConsolidationController,
    CategoryController,
    WeightDiscountController,
    SettingController,
    StatisticsController,
    InvoiceController,
    RevenueController,
    DebtorController,
    CityController,
    InternationalCourierController,
    LocalCourierController,
    StoreController,
    PageController,
    FinancialController,
    PaymentMethodController,
    ShipmentPaymentController,
    ShipmentStatusController,
    SiteController,
    SubCategoryController,
    SubSubCategoryController,
    WarehouseController
};
use App\Http\Controllers\CityPublicController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/cities', [CityPublicController::class, 'index']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['verified'])->group(function () {
        Route::get('/user/profile', [UserDashboardController::class, 'profile']);
        Route::put('/user/profile', [UserDashboardController::class, 'updateProfile']);
        Route::put('/user/avatar', [UserDashboardController::class, 'updateAvatar']);
        Route::get('/user/shipments', [UserDashboardController::class, 'shipments']);
        Route::get('/user/prohibited-items', [UserDashboardController::class, 'prohibitedItems']);
        Route::post('/user/change-password', [UserDashboardController::class, 'changePassword']);
    });
});

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);

    // ✅ Shipment custom routes MUST come BEFORE apiResource
    Route::get('/shipments/generate-shipment-code', [ShipmentController::class, 'generateShipmentCode']);
    Route::get('/shipments/fetch-customer', [ShipmentController::class, 'fetchCustomer']);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);

    // ✅ apiResource AFTER custom routes
    Route::apiResource('shipments', ShipmentController::class);

    Route::prefix('shipments/{shipment}')->group(function () {
        Route::get('payments', [ShipmentPaymentController::class, 'index']);
        Route::post('payments', [ShipmentPaymentController::class, 'store']);
    });
    Route::prefix('shipment-payments')->group(function () {
        Route::get('{payment}', [ShipmentPaymentController::class, 'show']);
        Route::put('{payment}', [ShipmentPaymentController::class, 'update']);
        Route::delete('{payment}', [ShipmentPaymentController::class, 'destroy']);
    });



    // Consolidations — custom before apiResource
    Route::get('consolidations/shipmentsJson', [ConsolidationController::class, 'shipmentsJson']);
    Route::get('consolidations/shipment', [ConsolidationController::class, 'shipmentDetails']);
    Route::apiResource('consolidations', ConsolidationController::class);

    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('international-couriers', InternationalCourierController::class);
    Route::apiResource('local-couriers', LocalCourierController::class);
    Route::apiResource('payment-methods', PaymentMethodController::class);
    Route::apiResource('sites', SiteController::class);
    Route::apiResource('shipment-statuses', ShipmentStatusController::class);
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

    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('revenues', RevenueController::class);
    Route::get('revenues/total', [RevenueController::class, 'total']);
    Route::apiResource('debtors', DebtorController::class);

    Route::get('financial/pl', [FinancialController::class, 'profitAndLoss']);
    Route::get('financial/trial-balance', [FinancialController::class, 'trialBalance']);
});
