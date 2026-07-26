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
    JournalController,
    LedgerController,
    PaymentMethodController,
    ProfitLossController,
    ShipmentPaymentController,
    ShipmentStatusController,
    SiteController,
    TrialBalanceController,
    VoucherController,
    WarehouseController,
    // ============================================================
    // LANDING MODULE ADMIN CONTROLLERS
    // ============================================================
    HeroSlideController,
    AboutSectionController,
    ServiceController,
    TestimonialController,
    TeamMemberController,
    PricingPlanController,
    FaqController,
    BlogPostController,
    WhyUsSectionController,
    ContactSectionController,
    FooterSectionController,
    LandingSettingController,
    StatController,
    QuoteRequestController,
    ProhibitedItemController
};

use App\Http\Controllers\Api\Public\LandingController;
use App\Http\Controllers\Api\Public\LandingSettingPublicController;
use App\Http\Controllers\Api\Public\QuoteController;
use App\Http\Controllers\CityPublicController;


// ============================================================
// PUBLIC ROUTES (No authentication required)
// ============================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/cities', [CityPublicController::class, 'index']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth:sanctum', 'signed'])
    ->name('verification.verify');


Route::post('/quotes', [QuoteController::class, 'store']);
// ============================================================
// LANDING PAGE PUBLIC ROUTES - DATA
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
    Route::get('/footer', [LandingController::class, 'getFooter']);
    Route::get('/stats', [LandingController::class, 'getStats']);
    Route::get('/prohibited-items', [LandingController::class, 'getProhibitedItems']);
});

// ============================================================
// LANDING PAGE PUBLIC ROUTES - SETTINGS
// ============================================================
Route::prefix('landing-settings')->group(function () {
    Route::get('/', [LandingSettingPublicController::class, 'index']);
});

// ============================================================
// QUOTE REQUEST PUBLIC ROUTE
// ============================================================
Route::post('/quotes', [QuoteRequestController::class, 'store']);

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
    Route::get('/shipments/generate-shipment-code', [ShipmentController::class, 'generateShipmentCode']);
    Route::post('/shipments/bulk-status', [ShipmentController::class, 'updateBulkStatus']);
    Route::get('/shipments/fetch-customer', [ShipmentController::class, 'fetchCustomer']);
    Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);
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

    // ============================================================
    // LANDING PAGES - 14 MODULES (Full CRUD)
    // ============================================================

    // 1. Hero Slides
    Route::prefix('hero-slides')->group(function () {
        Route::get('/', [HeroSlideController::class, 'index']);
        Route::post('/', [HeroSlideController::class, 'store']);
        Route::get('/{heroSlide}', [HeroSlideController::class, 'show']);
        Route::put('/{heroSlide}', [HeroSlideController::class, 'update']);
        Route::delete('/{heroSlide}', [HeroSlideController::class, 'destroy']);
        Route::post('/reorder', [HeroSlideController::class, 'reorder']);
        Route::post('/bulk-status', [HeroSlideController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [HeroSlideController::class, 'bulkDelete']);
    });

    // 2. About Sections
    Route::prefix('about-sections')->group(function () {
        Route::get('/', [AboutSectionController::class, 'index']);
        Route::post('/', [AboutSectionController::class, 'store']);
        Route::get('/{aboutSection}', [AboutSectionController::class, 'show']);
        Route::put('/{aboutSection}', [AboutSectionController::class, 'update']);
        Route::delete('/{aboutSection}', [AboutSectionController::class, 'destroy']);
        Route::delete('/bulk-delete', [AboutSectionController::class, 'bulkDelete']);
    });

    // 3. Services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']);
        Route::post('/', [ServiceController::class, 'store']);
        Route::get('/{service}', [ServiceController::class, 'show']);
        Route::put('/{service}', [ServiceController::class, 'update']);
        Route::delete('/{service}', [ServiceController::class, 'destroy']);
        Route::post('/reorder', [ServiceController::class, 'reorder']);
        Route::post('/bulk-status', [ServiceController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [ServiceController::class, 'bulkDelete']);
    });

    // 4. Testimonials
    Route::prefix('testimonials')->group(function () {
        Route::get('/', [TestimonialController::class, 'index']);
        Route::post('/', [TestimonialController::class, 'store']);
        Route::get('/{testimonial}', [TestimonialController::class, 'show']);
        Route::put('/{testimonial}', [TestimonialController::class, 'update']);
        Route::delete('/{testimonial}', [TestimonialController::class, 'destroy']);
        Route::post('/reorder', [TestimonialController::class, 'reorder']);
        Route::post('/bulk-status', [TestimonialController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [TestimonialController::class, 'bulkDelete']);
    });

    // 5. Team Members
    Route::prefix('team-members')->group(function () {
        Route::get('/', [TeamMemberController::class, 'index']);
        Route::post('/', [TeamMemberController::class, 'store']);
        Route::get('/{teamMember}', [TeamMemberController::class, 'show']);
        Route::put('/{teamMember}', [TeamMemberController::class, 'update']);
        Route::delete('/{teamMember}', [TeamMemberController::class, 'destroy']);
        Route::post('/reorder', [TeamMemberController::class, 'reorder']);
        Route::post('/bulk-status', [TeamMemberController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [TeamMemberController::class, 'bulkDelete']);
    });

    // 6. Pricing Plans
    Route::prefix('pricing-plans')->group(function () {
        Route::get('/', [PricingPlanController::class, 'index']);
        Route::post('/', [PricingPlanController::class, 'store']);
        Route::get('/{pricingPlan}', [PricingPlanController::class, 'show']);
        Route::put('/{pricingPlan}', [PricingPlanController::class, 'update']);
        Route::delete('/{pricingPlan}', [PricingPlanController::class, 'destroy']);
        Route::post('/reorder', [PricingPlanController::class, 'reorder']);
        Route::post('/bulk-status', [PricingPlanController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [PricingPlanController::class, 'bulkDelete']);
    });

    // 7. FAQs
    Route::prefix('faqs')->group(function () {
        Route::get('/', [FaqController::class, 'index']);
        Route::post('/', [FaqController::class, 'store']);
        Route::get('/{faq}', [FaqController::class, 'show']);
        Route::put('/{faq}', [FaqController::class, 'update']);
        Route::delete('/{faq}', [FaqController::class, 'destroy']);
        Route::post('/reorder', [FaqController::class, 'reorder']);
        Route::post('/bulk-status', [FaqController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [FaqController::class, 'bulkDelete']);
    });

    // 8. Blog Posts
    Route::prefix('blog-posts')->group(function () {
        Route::get('/', [BlogPostController::class, 'index']);
        Route::post('/', [BlogPostController::class, 'store']);
        Route::get('/{blogPost}', [BlogPostController::class, 'show']);
        Route::put('/{blogPost}', [BlogPostController::class, 'update']); // <-- CHANGED FROM POST TO PUT
        Route::delete('/{blogPost}', [BlogPostController::class, 'destroy']);
        Route::post('/bulk-status', [BlogPostController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [BlogPostController::class, 'bulkDelete']);
    });

    // 9. Why Us Sections
    Route::prefix('why-us-sections')->group(function () {
        Route::get('/', [WhyUsSectionController::class, 'index']);
        Route::post('/', [WhyUsSectionController::class, 'store']);
        Route::get('/{whyUsSection}', [WhyUsSectionController::class, 'show']);
        Route::put('/{whyUsSection}', [WhyUsSectionController::class, 'update']);
        Route::delete('/{whyUsSection}', [WhyUsSectionController::class, 'destroy']);
    });

    // 10. Contact Sections
    Route::prefix('contact-sections')->group(function () {
        Route::get('/', [ContactSectionController::class, 'index']);
        Route::post('/', [ContactSectionController::class, 'store']);
        Route::get('/{contactSection}', [ContactSectionController::class, 'show']);
        Route::put('/{contactSection}', [ContactSectionController::class, 'update']);
        Route::delete('/{contactSection}', [ContactSectionController::class, 'destroy']);
    });

    // 11. Footer Sections
    Route::prefix('footer-sections')->group(function () {
        Route::get('/', [FooterSectionController::class, 'index']);
        Route::post('/', [FooterSectionController::class, 'store']);
        Route::get('/{footerSection}', [FooterSectionController::class, 'show']);
        Route::put('/{footerSection}', [FooterSectionController::class, 'update']);
        Route::delete('/{footerSection}', [FooterSectionController::class, 'destroy']);
    });

    // 12. Statistics
    Route::prefix('stats')->group(function () {
        Route::get('/', [StatController::class, 'index']);
        Route::post('/', [StatController::class, 'store']);
        Route::get('/{stat}', [StatController::class, 'show']);
        Route::put('/{stat}', [StatController::class, 'update']);
        Route::delete('/{stat}', [StatController::class, 'destroy']);
    });

    // 13. Quote Requests
    Route::prefix('quote-requests')->group(function () {
        Route::get('/', [QuoteRequestController::class, 'index']);
        Route::get('/stats', [QuoteRequestController::class, 'stats']);
        Route::get('/export', [QuoteRequestController::class, 'export']);
        Route::get('/{quoteRequest}', [QuoteRequestController::class, 'show']);
        Route::delete('/{quoteRequest}', [QuoteRequestController::class, 'destroy']);
        Route::post('/{quoteRequest}/update-status', [QuoteRequestController::class, 'updateStatus']);
        Route::delete('/bulk-delete', [QuoteRequestController::class, 'bulkDelete']);
    });

    // 14. Prohibited Items
    Route::prefix('prohibited-items')->group(function () {
        Route::get('/', [ProhibitedItemController::class, 'index']);
        Route::post('/', [ProhibitedItemController::class, 'store']);
        Route::get('/categories', [ProhibitedItemController::class, 'getCategories']);
        Route::get('/severity-options', [ProhibitedItemController::class, 'getSeverityOptions']);
        Route::get('/export', [ProhibitedItemController::class, 'export']);
        Route::get('/{prohibitedItem}', [ProhibitedItemController::class, 'show']);
        Route::put('/{prohibitedItem}', [ProhibitedItemController::class, 'update']);
        Route::delete('/{prohibitedItem}', [ProhibitedItemController::class, 'destroy']);
        Route::post('/reorder', [ProhibitedItemController::class, 'reorder']);
        Route::post('/bulk-status', [ProhibitedItemController::class, 'bulkStatus']);
        Route::delete('/bulk-delete', [ProhibitedItemController::class, 'bulkDelete']);
    });

    // ============================================================
    // LANDING SETTINGS - ADMIN ROUTES
    // ============================================================
    Route::prefix('landing-settings')->group(function () {
        Route::get('/', [LandingSettingController::class, 'index']);
        Route::get('/{landingSetting}', [LandingSettingController::class, 'show']);
        Route::put('/{landingSetting}', [LandingSettingController::class, 'update']);
        Route::post('/bulk-update', [LandingSettingController::class, 'bulkUpdate']);
        Route::post('/reset', [LandingSettingController::class, 'reset']);
    });
});
