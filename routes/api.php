<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\Admin\{
    AccountController,
    AccountingReportController,
    DashboardController,
    UserController,
    ShipmentController,
    ConsolidationController,

    WeightDiscountController,

    StatisticsController,
    InvoiceController,
    RevenueController,
    DebtorController,
    CityController,
    EmployeeController,


    InternationalCourierController,
    LocalCourierController,

    PageController,
    FinancialController,
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
use App\Http\Controllers\CityPublicController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/public/cities', [CityPublicController::class, 'index']);

Route::get('public/landing', [PageController::class, 'publicLanding']);

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
    Route::post('/shipments/bulk-status', [ShipmentController::class, 'updateBulkStatus']);
    Route::get('/users/search', [ShipmentController::class, 'searchUsers']);
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







    // Employees
    Route::apiResource('employees', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('employees/{employee}', [EmployeeController::class, 'show']);





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

    Route::apiResource('cities', CityController::class);

    Route::apiResource('pages', PageController::class);


    // Statistics
    Route::get('statistics/top-cities', [StatisticsController::class, 'topCities']);
    Route::get('statistics/active-users', [StatisticsController::class, 'activeUsers']);
    Route::get('statistics/city-wise-business', [StatisticsController::class, 'cityWiseBusiness']);
    Route::get('statistics/shipments', [StatisticsController::class, 'shipmentsStats']);
    Route::get('statistics/delivery-time', [StatisticsController::class, 'deliveryTime']);
    Route::get('statistics/debtors-balance', [StatisticsController::class, 'debtorsBalance']);

    // ✅ INVOICES - IMPORTANT: Must be before apiResource
    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'index']);
        Route::get('/stats', [InvoiceController::class, 'stats']);
        Route::get('/{invoice}', [InvoiceController::class, 'show']);
        Route::get('/{invoice}/download', [InvoiceController::class, 'download']);  // ✅ Download PDF
        Route::get('/{invoice}/print', [InvoiceController::class, 'print']);        // ✅ Print data
        Route::post('/', [InvoiceController::class, 'store']);
        Route::put('/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy']);
        Route::post('/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid']);
    });

    Route::apiResource('revenues', RevenueController::class);
    Route::get('revenues/total', [RevenueController::class, 'total']);
    // Debtors
    Route::get('debtors', [DebtorController::class, 'index']);
    Route::get('debtors/{id}', [DebtorController::class, 'show']);
    Route::post('debtors', [DebtorController::class, 'store']);
    Route::put('debtors/{id}', [DebtorController::class, 'update']);
    Route::delete('debtors/{id}', [DebtorController::class, 'destroy']);
    Route::post('debtors/{id}/payment', [DebtorController::class, 'recordPayment']);
    Route::get('debtors/stats', [DebtorController::class, 'stats']);
    Route::get('debtors/export', [DebtorController::class, 'export']);
    Route::post('debtors/sync', [DebtorController::class, 'syncFromInvoices']);

    Route::get('financial/pl', [FinancialController::class, 'profitAndLoss']);
    Route::get('financial/trial-balance', [FinancialController::class, 'trialBalance']);



    Route::apiResource('accounts', AccountController::class);
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus']);

    Route::apiResource('vouchers', VoucherController::class);
    Route::post('vouchers/{voucher}/approve', [VoucherController::class, 'approve']);
    Route::get('/vouchers/by-number/{voucher_no}', [VoucherController::class, 'showByNumber']);

    Route::get('journal', [JournalController::class, 'index']);
    Route::get('/ledger', [LedgerController::class, 'index']);
    Route::get('trial-balance', [TrialBalanceController::class, 'index']);

    Route::prefix('pandl')->group(function () {
        Route::get('since-inception', [ProfitLossController::class, 'sinceInception']);
        Route::get('yearly', [ProfitLossController::class, 'yearly']);
        Route::get('quarterly', [ProfitLossController::class, 'quarterly']);
        Route::get('monthly', [ProfitLossController::class, 'monthly']);
        Route::get('balance-sheet', [ProfitLossController::class, 'balanceSheet']);
        Route::get('balance-sheet/today', [ProfitLossController::class, 'balanceSheetToday']);
        Route::get('balance-sheet/yearly', [ProfitLossController::class, 'balanceSheetYearly']);
    });
});
