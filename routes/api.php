<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\Admin\{
    AccountController,
    DashboardController,
    UserController,

    ShipmentController,
    ConsolidationController,
    WeightDiscountController,
    StatisticsController,
    InvoiceController,
    DebtorController,
    CityController,
    InternationalCourierController,
    LocalCourierController,
    PageController,
    JournalController,
    LedgerController,
    PaymentMethodController,
    ProfitLossController,
    ShipmentPaymentController,
    ShipmentStatusController,
    SiteController,
    TrialBalanceController,
    VoucherController,
    WarehouseController
};

use App\Http\Controllers\Api\Public\LandingController;
use App\Http\Controllers\CityPublicController;


// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::prefix('landing')->group(function () {
    Route::get('/', [LandingController::class, 'index']);
    Route::get('/section/{type}', [LandingController::class, 'getSection']);
    Route::get('/hero', [LandingController::class, 'getHero']);
    Route::get('/services', [LandingController::class, 'getServices']);
    Route::get('/testimonials', [LandingController::class, 'getTestimonials']);
    Route::get('/team', [LandingController::class, 'getTeam']);
    Route::get('/pricing', [LandingController::class, 'getPricing']);
    Route::get('/about', [LandingController::class, 'getAbout']);
    Route::get('/faq', [LandingController::class, 'getFaq']);
    Route::get('/whyus', [LandingController::class, 'getWhyUs']);
    Route::get('/blog', [LandingController::class, 'getBlog']);
    Route::get('/contact', [LandingController::class, 'getContact']);
    Route::get('/stats', [LandingController::class, 'getStats']);
});

// routes/api.php - Add these to admin routes

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // ... existing routes

    // Pages routes
    Route::prefix('pages')->group(function () {
        Route::get('/types', [PageController::class, 'getTypes']);
        Route::post('/upload-image', [PageController::class, 'uploadImage']);
        Route::post('/reorder', [PageController::class, 'reorder']);
        Route::post('/bulk-delete', [PageController::class, 'bulkDelete']);
        Route::post('/bulk-status', [PageController::class, 'bulkStatus']);
        Route::delete('/{page}/image', [PageController::class, 'deleteImage']);
    });

    Route::apiResource('pages', PageController::class);
});
// ============================================================
// PUBLIC ROUTES (No authentication required)
// ============================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/cities', [CityPublicController::class, 'index']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed'])
    ->name('verification.verify');




// ============================================================
// AUTHENTICATED ROUTES (Requires authentication)
// ============================================================
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});



// ============================================================
// USER ROUTES (Requires authentication & email verification)
// ============================================================
Route::middleware(['auth:sanctum', 'verified'])->prefix('user')->group(function () {
    // Profile
    Route::get('/profile', [UserDashboardController::class, 'profile']);
    Route::put('/profile', [UserDashboardController::class, 'updateProfile']);
    Route::put('/avatar', [UserDashboardController::class, 'updateAvatar']);
    Route::post('/change-password', [UserDashboardController::class, 'changePassword']);

    // Dashboard
    Route::get('/dashboard/stats', [UserDashboardController::class, 'dashboardStats']);

    // Shipments
    Route::get('/shipments', [UserDashboardController::class, 'shipments']);
    Route::get('/shipments/{id}', [UserDashboardController::class, 'shipmentDetails']);
    Route::get('/track/{trackingNumber}', [UserDashboardController::class, 'trackShipment']);

    // Other
    Route::get('/prohibited-items', [UserDashboardController::class, 'prohibitedItems']);
});

// ============================================================
// ADMIN ROUTES (Requires authentication & admin role)
// ============================================================
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // ============================================================
    // DASHBOARD
    // ============================================================
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ============================================================
    // USERS
    // ============================================================
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::post('/users/{user}/status', [UserController::class, 'updateStatus']);
    Route::get('/users/search', [ShipmentController::class, 'searchUsers']);


    // ============================================================
    // SHIPMENTS
    // ============================================================
    // Custom routes (must come before apiResource)
    Route::get('/shipments/generate-shipment-code', [ShipmentController::class, 'generateShipmentCode']);
    Route::post('/shipments/bulk-status', [ShipmentController::class, 'updateBulkStatus']);
    Route::get('/shipments/fetch-customer', [ShipmentController::class, 'fetchCustomer']);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);

    // API Resource
    Route::apiResource('shipments', ShipmentController::class);

    // Shipment Payments
    Route::prefix('shipments/{shipment}')->group(function () {
        Route::get('payments', [ShipmentPaymentController::class, 'index']);
        Route::post('payments', [ShipmentPaymentController::class, 'store']);
    });
    Route::prefix('shipment-payments')->group(function () {
        Route::get('{payment}', [ShipmentPaymentController::class, 'show']);
        Route::put('{payment}', [ShipmentPaymentController::class, 'update']);
        Route::delete('{payment}', [ShipmentPaymentController::class, 'destroy']);
    });

    // ============================================================
    // CONSOLIDATIONS
    // ============================================================
    Route::get('consolidations/shipmentsJson', [ConsolidationController::class, 'shipmentsJson']);
    Route::get('consolidations/shipment', [ConsolidationController::class, 'shipmentDetails']);
    Route::apiResource('consolidations', ConsolidationController::class);

    // ============================================================
    // STATISTICS
    // ============================================================
    Route::get('statistics/top-cities', [StatisticsController::class, 'topCities']);
    Route::get('statistics/active-users', [StatisticsController::class, 'activeUsers']);
    Route::get('statistics/city-wise-business', [StatisticsController::class, 'cityWiseBusiness']);
    Route::get('statistics/shipments', [StatisticsController::class, 'shipmentsStats']);
    Route::get('statistics/delivery-time', [StatisticsController::class, 'deliveryTime']);
    Route::get('statistics/debtors-balance', [StatisticsController::class, 'debtorsBalance']);

    // ============================================================
    // FINANCIAL - ACCOUNTS
    // ============================================================
    Route::apiResource('accounts', AccountController::class);
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus']);

    // ============================================================
    // FINANCIAL - VOUCHERS
    // ============================================================
    Route::apiResource('vouchers', VoucherController::class);
    Route::post('vouchers/{voucher}/approve', [VoucherController::class, 'approve']);
    Route::get('/vouchers/by-number/{voucher_no}', [VoucherController::class, 'showByNumber']);

    // ============================================================
    // FINANCIAL - REPORTS
    // ============================================================
    Route::get('journal', [JournalController::class, 'index']);
    Route::get('/ledger', [LedgerController::class, 'index']);
    Route::get('trial-balance', [TrialBalanceController::class, 'index']);

    // ============================================================
    // FINANCIAL - P&L
    // ============================================================
    Route::prefix('pandl')->group(function () {
        Route::get('since-inception', [ProfitLossController::class, 'sinceInception']);
        Route::get('yearly', [ProfitLossController::class, 'yearly']);
        Route::get('quarterly', [ProfitLossController::class, 'quarterly']);
        Route::get('monthly', [ProfitLossController::class, 'monthly']);
        Route::get('balance-sheet', [ProfitLossController::class, 'balanceSheet']);
        Route::get('balance-sheet/today', [ProfitLossController::class, 'balanceSheetToday']);
        Route::get('balance-sheet/yearly', [ProfitLossController::class, 'balanceSheetYearly']);
    });

    // ============================================================
    // FINANCIAL - INVOICES
    // ============================================================
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::get('/stats', [InvoiceController::class, 'stats']);
        Route::get('/{invoice}', [InvoiceController::class, 'show']);
        Route::get('/{invoice}/download', [InvoiceController::class, 'download']);
        Route::get('/{invoice}/print', [InvoiceController::class, 'print']);
        Route::post('/', [InvoiceController::class, 'store']);
        Route::put('/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy']);
        Route::post('/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid']);
    });

    // ============================================================
    // FINANCIAL - DEBTORS
    // ============================================================
    Route::get('debtors', [DebtorController::class, 'index']);
    Route::get('debtors/{id}', [DebtorController::class, 'show']);
    Route::post('debtors/{id}/payment', [DebtorController::class, 'recordPayment']);
    Route::get('debtors/stats', [DebtorController::class, 'stats']);
    Route::get('debtors/export', [DebtorController::class, 'export']);
    Route::post('debtors/sync', [DebtorController::class, 'syncFromInvoices']);

    // ============================================================
    // LOOKUP TABLES (Directory & Setup)
    // ============================================================
    Route::apiResource('cities', CityController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('international-couriers', InternationalCourierController::class);
    Route::apiResource('local-couriers', LocalCourierController::class);
    Route::apiResource('payment-methods', PaymentMethodController::class);
    Route::apiResource('sites', SiteController::class);
    Route::apiResource('shipment-statuses', ShipmentStatusController::class);
    Route::apiResource('weight-discounts', WeightDiscountController::class);


});
