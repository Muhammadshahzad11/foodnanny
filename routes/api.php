<?php

use App\Http\Controllers\Admin\SettingMenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\OtpController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\PwaController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\AiController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\PusherController;
use App\Http\Controllers\Admin\RefundController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\BenefitController;
use App\Http\Controllers\Admin\CashoutController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CookiesController;
use App\Http\Controllers\Admin\CuisineController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\AnalyticController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FrontendController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PosOfferController;
use App\Http\Controllers\Admin\PosOrderController;
use App\Http\Controllers\Admin\RiderTipController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Admin\TimezoneController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemAddonController;
use App\Http\Controllers\Admin\ItemExtraController;
use App\Http\Controllers\Auth\DeactivateController;
use App\Http\Controllers\Admin\AboutStepsController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\OrderSetupController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\RestaurantTableController;
use App\Http\Controllers\Admin\SmsGatewayController;
use App\Http\Controllers\Admin\AiAgentController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Auth\GuestSignupController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\SettingController;
use App\Http\Controllers\Admin\ActiveOrderController;
use App\Http\Controllers\Admin\CountryCodeController;
use App\Http\Controllers\Admin\DeliveryBoyController;
use App\Http\Controllers\Admin\ItemsReportController;
use App\Http\Controllers\Admin\MenuSectionController;
use App\Http\Controllers\Admin\OnlineOrderController;
use App\Http\Controllers\Admin\PosCategoryController;
use App\Http\Controllers\Admin\ReturnOrderController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ItemCategoryController;
use App\Http\Controllers\Admin\MenuTemplateController;
use App\Http\Controllers\Admin\MyRestaurantController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderTrackerController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\DefaultAccessController;
use App\Http\Controllers\Admin\DeliverySetupController;
use App\Http\Controllers\Admin\ItemAttributeController;
use App\Http\Controllers\Admin\ItemVariationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\AvailableOrderController;
use App\Http\Controllers\Admin\MyOrderDetailsController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\AnalyticSectionController;
use App\Http\Controllers\Admin\CustomerAddressController;
use App\Http\Controllers\Admin\EmployeeAddressController;
use App\Http\Controllers\Admin\OfferRestaurantController;
use App\Http\Controllers\Admin\RestaurantOwnerController;
use App\Http\Controllers\Auth\RestaurantSignupController;
use App\Http\Controllers\Admin\CampaignAndOfferController;
use App\Http\Controllers\Admin\CollectionReportController;
use App\Http\Controllers\Admin\DeliveryBoyOrderController;
use App\Http\Controllers\Admin\PermissionSwitchController;
use App\Http\Controllers\Admin\PushNotificationController;
use App\Http\Controllers\Admin\RestaurantSwitchController;
use App\Http\Controllers\Auth\DeliveryBoySignupController;
use App\Http\Controllers\Admin\NotificationAlertController;
use App\Http\Controllers\Admin\CampaignRestaurantController;
use App\Http\Controllers\Admin\DeliveryBoyAddressController;
use App\Http\Controllers\Admin\TermsAndConditionsController;
use App\Http\Controllers\Admin\CreditBalanceReportController;
use App\Http\Controllers\Admin\AdministratorAddressController;
use App\Http\Controllers\Admin\DeliveryLocationSetupController;
use App\Http\Controllers\Admin\RestaurantOwnerAddressController;
use App\Http\Controllers\Frontend\ItemController as FrontendItemController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\OfferController as FrontendOfferController;
use App\Http\Controllers\Frontend\OrderController as FrontendOrderController;
use App\Http\Controllers\Frontend\CouponController as FrontendCouponController;
use App\Http\Controllers\Frontend\AddressController as FrontendAddressController;
use App\Http\Controllers\Frontend\BenefitController as FrontendBenefitController;
use App\Http\Controllers\Frontend\CookiesController as FrontendCookiesController;
use App\Http\Controllers\Frontend\CuisineController as FrontendCuisineController;
use App\Http\Controllers\Frontend\MessageController as FrontendMessageController;
use App\Http\Controllers\Frontend\CampaignController as FrontendCampaignController;
use App\Http\Controllers\Frontend\FavoriteController as FrontendFavoriteController;
use App\Http\Controllers\Frontend\LanguageController as FrontendLanguageController;
use App\Http\Controllers\Frontend\RiderTipController as FrontendRiderTipController;
use App\Http\Controllers\Frontend\TimeSlotController as FrontendTimeSlotController;
use App\Http\Controllers\Frontend\AboutStepsController as FrontendAboutStepsController;
use App\Http\Controllers\Frontend\RestaurantController as FrontendRestaurantController;
use App\Http\Controllers\Frontend\SubscriberController as FrontendSubscriberController;
use App\Http\Controllers\Frontend\CountryCodeController as FrontendCountryCodeController;
use App\Http\Controllers\Frontend\ItemCategoryController as FrontendItemCategoryController;
use App\Http\Controllers\Frontend\FirebaseTokenController as FrontendFirebaseTokenController;
use App\Http\Controllers\Frontend\PaymentGatewayController as FrontendPaymentGatewayController;
use App\Http\Controllers\Frontend\AutoLocalizationController as FrontendAutoLocalizationController;
use App\Http\Controllers\Frontend\RestaurantReviewController as FrontendRestaurantReviewController;
use App\Http\Controllers\Frontend\DeliveryBoyReviewController as FrontendDeliveryBoyReviewController;
use App\Http\Controllers\Admin\StorageController;

Route::match(['get', 'post'], '/login', function () {
    return response()->json(['errors' => 'unauthenticated'], 401);
})->middleware(['installed', 'apiKey'])->name('login');

Route::middleware(['installed', 'apiKey', 'auth:sanctum'])->post('/broadcasting/auth', function (Request $request) {
    return Broadcast::auth($request);
});

Route::prefix('auth')->middleware(['installed', 'apiKey', 'localization'])->name('auth.')->namespace('Auth')->group(function () {
    Route::post('/is-auth', [LoginController::class, 'isAuth']);
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login-email');
    Route::post('/login-phone', [LoginController::class, 'phoneLogin'])->middleware('throttle:login-phone');;

    Route::prefix('signup')->name('signup.')->group(function () {
        Route::post('/phone', [SignupController::class, 'phone'])->middleware('throttle:otp-send');
        Route::post('/verify', [SignupController::class, 'verify'])->middleware('throttle:otp-verify');
        Route::post('/register', [SignupController::class, 'register'])->middleware('throttle:request-verify');
    });

    Route::prefix('signup-restaurant')->name('signup-restaurant.')->group(function () {
        Route::post('/phone', [RestaurantSignupController::class, 'phone'])->middleware('throttle:otp-send');
        Route::post('/verify', [RestaurantSignupController::class, 'verify'])->middleware('throttle:otp-verify');
        Route::post('/verify-owner', [RestaurantSignupController::class, 'owner'])->middleware('throttle:request-verify');
        Route::post('/register-restaurant', [RestaurantSignupController::class, 'register'])->middleware('throttle:request-verify');
    });

    Route::prefix('signup-delivery-boy')->name('signup-delivery-boy.')->group(function () {
        Route::post('/phone', [DeliveryBoySignupController::class, 'phone'])->middleware('throttle:otp-send');
        Route::post('/verify', [DeliveryBoySignupController::class, 'verify'])->middleware('throttle:otp-verify');
        Route::post('/register-delivery-boy', [DeliveryBoySignupController::class, 'register'])->middleware('throttle:request-verify');
    });

    Route::prefix('guest-signup')->name('guest-signup.')->group(function () {
        Route::post('/phone', [GuestSignupController::class, 'phone'])->middleware('throttle:otp-send');
        Route::post('/verify', [GuestSignupController::class, 'verify'])->middleware('throttle:otp-verify');
    });

    Route::prefix('forgot-password')->name('forgot-password.')->group(function () {
        Route::post('/', [ForgotPasswordController::class, 'forgotPassword'])->middleware('throttle:forgot-password');
        Route::post('/verify-code', [ForgotPasswordController::class, 'verifyCode'])->middleware('throttle:verify-code');
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->middleware('throttle:request-verify');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('verify.api')->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::post('/delete-account', [DeactivateController::class, 'deleteAccount']);
        });
    });
});

Route::prefix('profile')->name('profile.')->middleware(['installed', 'apiKey', 'auth:sanctum', 'localization'])->group(function () {
    Route::get('/', [ProfileController::class, 'profile']);
    Route::match(['put', 'patch'], '/', [ProfileController::class, 'update']);
    Route::match(['put', 'patch'], '/change-password', [ProfileController::class, 'changePassword']);
    Route::post('/change-image', [ProfileController::class, 'changeImage']);
});

Route::prefix('admin')->name('admin.')->middleware(['installed', 'apiKey', 'localization', 'auth:sanctum'])->group(function () {
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index']);
    });

    Route::prefix('default-access')->name('default-access.')->group(function () {
        Route::get('/', [DefaultAccessController::class, 'index']);
    });

    Route::prefix('restaurant-switch')->name('restaurant-switch.')->group(function () {
        Route::get('/', [RestaurantSwitchController::class, 'index']);
        Route::post('/switch', [RestaurantSwitchController::class, 'switch']);
    });

    Route::prefix('permission-switch')->name('permission-switch.')->group(function () {
        Route::post('/', [PermissionSwitchController::class, 'index']);
    });

    Route::prefix('country-code')->name('country-code.')->group(function () {
        Route::get('/', [CountryCodeController::class, 'index']);
        Route::get('/show/{country}', [CountryCodeController::class, 'show']);
        Route::post('/find', [CountryCodeController::class, 'find']);
    });

    Route::prefix('timezone')->name('timezone.')->group(function () {
        Route::get('/', [TimezoneController::class, 'index']);
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::post('/admin-overview', [DashboardController::class, 'adminOverview']);
        Route::get('/admin-collection-balance', [DashboardController::class, 'adminCollectionBalance']);
        Route::post('/admin-sales-summary', [DashboardController::class, 'adminSalesSummary']);
        Route::post('/admin-orders-summary', [DashboardController::class, 'adminOrdersSummary']);
        Route::get('/admin-revenue', [DashboardController::class, 'adminRevenue']);
        Route::get('/admin-top-customers', [DashboardController::class, 'adminTopCustomers']);
        Route::get('/admin-top-delivery-boys', [DashboardController::class, 'adminTopDeliveryBoys']);
        Route::get('/admin-most-popular-restaurants', [DashboardController::class, 'adminMostPopularRestaurants']);
        Route::post('/restaurant-owner-overview', [DashboardController::class, 'restaurantOwnerOverview']);
        Route::post('/restaurant-owner-customer-stats', [DashboardController::class, 'restaurantOwnerCustomerStats']);
        Route::get('/restaurant-owner-most-popular-items', [DashboardController::class, 'restaurantOwnerMostPopularItems']);
        Route::post('/delivery-boy-overview', [DashboardController::class, 'deliveryBoyOverview']);
        Route::get('/delivery-boy-payout-balance', [DashboardController::class, 'deliveryBoyPayoutBalance']);
        Route::get('/delivery-boy-collection-balance', [DashboardController::class, 'deliveryBoyCollectionBalance']);
        Route::get('/delivery-boy-active-orders', [DashboardController::class, 'deliveryBoyActiveOrders']);
        Route::post('/other-overview', [DashboardController::class, 'otherOverview']);
    });

    Route::prefix('offer')->name('offer.')->group(function () {
        Route::get('/', [OfferController::class, 'index']);
        Route::get('/show/{offer}', [OfferController::class, 'show']);
        Route::post('/', [OfferController::class, 'store']);
        Route::post('/{offer}', [OfferController::class, 'update']);
        Route::delete('/{offer}', [OfferController::class, 'destroy']);
        Route::get('/export', [OfferController::class, 'export']);
        Route::post('/change-thumbnail/{offer}', [OfferController::class, 'changeThumbnail']);
        Route::post('/change-cover/{offer}', [OfferController::class, 'changeCover']);
        Route::post('/translations/{offer}', [OfferController::class, 'saveTranslations']);

        Route::get('/restaurant/{offer}', [OfferRestaurantController::class, 'index']);
        Route::post('/restaurant/{offer}', [OfferRestaurantController::class, 'store']);
        Route::delete('/restaurant/{offer}/{offerRestaurant}', [OfferRestaurantController::class, 'destroy']);
        Route::post('/restaurant/verify/{offer}/{offerRestaurant}', [OfferRestaurantController::class, 'verify']);
    });

    Route::prefix('coupon')->name('coupon.')->group(function () {
        Route::get('/', [CouponController::class, 'index']);
        Route::get('/show/{coupon}', [CouponController::class, 'show']);
        Route::post('/', [CouponController::class, 'store']);
        Route::post('/{coupon}', [CouponController::class, 'update']);
        Route::delete('/{coupon}', [CouponController::class, 'destroy']);
        Route::get('/export', [CouponController::class, 'export']);
        Route::post('/translations/{coupon}', [CouponController::class, 'saveTranslations']);
    });

    Route::prefix('item')->name('item.')->group(function () {
        Route::get('/', [ItemController::class, 'index']);
        Route::get('/show/{item}', [ItemController::class, 'show']);
        Route::post('/', [ItemController::class, 'store']);
        Route::post('/{item}', [ItemController::class, 'update']);
        Route::delete('/{item}', [ItemController::class, 'destroy']);
        Route::post('/change-image/{item}', [ItemController::class, 'changeImage']);
        Route::post('/translations/{item}', [ItemController::class, 'saveTranslations']);
        Route::get('/export', [ItemController::class, 'export']);

        Route::get('/variation/{item}', [ItemVariationController::class, 'index']);
        Route::get('/variation/group-by-attribute/{item}', [ItemVariationController::class, 'listGroupByAttribute']);
        Route::post('/variation/{item}', [ItemVariationController::class, 'store']);
        Route::post('/variation/{item}/{itemVariation}', [ItemVariationController::class, 'update']);
        Route::delete('/variation/{item}/{itemVariation}', [ItemVariationController::class, 'destroy']);
        Route::get('/variation/{item}/show/{itemVariation}', [ItemVariationController::class, 'show']);

        Route::get('/extra/{item}', [ItemExtraController::class, 'index']);
        Route::post('/extra/{item}', [ItemExtraController::class, 'store']);
        Route::post('/extra/{item}/{itemExtra}', [ItemExtraController::class, 'update']);
        Route::delete('/extra/{item}/{itemExtra}', [ItemExtraController::class, 'destroy']);
        Route::get('/extra/{item}/show/{itemExtra}', [ItemExtraController::class, 'show']);

        Route::get('/addon/{item}', [ItemAddonController::class, 'index']);
        Route::post('/addon/{item}', [ItemAddonController::class, 'store']);
        Route::delete('/addon/{item}/{itemAddon}', [ItemAddonController::class, 'destroy']);
    });

    Route::prefix('my-order')->name('my-order.')->group(function () {
        Route::get('/show/{user}/{order}', [MyOrderDetailsController::class, 'orderDetails']);
    });

    Route::prefix('order-tracker')->name('order-tracker.')->group(function () {
        Route::post('/', [OrderTrackerController::class, 'index']);
    });

    Route::prefix('administrator')->name('administrator.')->group(function () {
        Route::get('/', [AdministratorController::class, 'index']);
        Route::get('/show/{administrator}', [AdministratorController::class, 'show']);
        Route::post('/', [AdministratorController::class, 'store']);
        Route::match(['post', 'put', 'patch'], '/{administrator}', [AdministratorController::class, 'update']);
        Route::delete('/{administrator}', [AdministratorController::class, 'destroy']);

        Route::get('/export', [AdministratorController::class, 'export']);
        Route::post('/change-password/{administrator}', [AdministratorController::class, 'changePassword']);
        Route::post('/change-image/{administrator}', [AdministratorController::class, 'changeImage']);

        Route::get('/my-order/{administrator}', [AdministratorController::class, 'myOrder']);

        Route::prefix('address')->name('address.')->group(function () {
            Route::get('/{administrator}', [AdministratorAddressController::class, 'index']);
            Route::get('/show/{administrator}/{address}', [AdministratorAddressController::class, 'show']);
            Route::post('/{administrator}', [AdministratorAddressController::class, 'store']);
            Route::match(['put', 'patch'], '/{administrator}/{address}', [AdministratorAddressController::class, 'update']);
            Route::delete('/{administrator}/{address}', [AdministratorAddressController::class, 'destroy']);
        });
    });

    Route::prefix('table')->name('table.')->group(function () {
        Route::get('/', [RestaurantTableController::class, 'index']);
        Route::post('/', [RestaurantTableController::class, 'store']);
        Route::get('/show/{restaurantTable}', [RestaurantTableController::class, 'show']);
        Route::match(['put', 'patch'], '/{restaurantTable}', [RestaurantTableController::class, 'update']);
        Route::delete('/{restaurantTable}', [RestaurantTableController::class, 'destroy']);
        Route::post('/change-status/{restaurantTable}', [RestaurantTableController::class, 'changeStatus']);
    });

    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/show/{employee}', [EmployeeController::class, 'show']);
        Route::match(['put', 'patch'], '/{employee}', [EmployeeController::class, 'update']);
        Route::delete('/{employee}', [EmployeeController::class, 'destroy']);

        Route::get('/export', [EmployeeController::class, 'export']);
        Route::post('/change-password/{employee}', [EmployeeController::class, 'changePassword']);
        Route::post('/change-image/{employee}', [EmployeeController::class, 'changeImage']);

        Route::get('/all-employee-and-admin', [EmployeeController::class, 'allEmployeeAndAdmin']);
        Route::get('/employee-and-admin-show/{employee}', [EmployeeController::class, 'employeeAndAdminShow']);

        Route::get('/my-order/{employee}', [EmployeeController::class, 'myOrder']);

        Route::prefix('address')->name('address.')->group(function () {
            Route::get('/{employee}', [EmployeeAddressController::class, 'index']);
            Route::get('/show/{employee}/{address}', [EmployeeAddressController::class, 'show']);
            Route::post('/{employee}', [EmployeeAddressController::class, 'store']);
            Route::match(['put', 'patch'], '/{employee}/{address}', [EmployeeAddressController::class, 'update']);
            Route::delete('/{employee}/{address}', [EmployeeAddressController::class, 'destroy']);
        });
    });

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/show/{customer}', [CustomerController::class, 'show']);
        Route::match(['post', 'put', 'patch'], '/{customer}', [CustomerController::class, 'update']);
        Route::delete('/{customer}', [CustomerController::class, 'destroy']);

        Route::get('/export', [CustomerController::class, 'export']);
        Route::post('/change-password/{customer}', [CustomerController::class, 'changePassword']);
        Route::post('/change-image/{customer}', [CustomerController::class, 'changeImage']);
        Route::get('/all-customer', [CustomerController::class, 'allCustomer']);

        Route::get('/my-order/{customer}', [CustomerController::class, 'myOrder']);

        Route::get('/address/{customer}', [CustomerAddressController::class, 'index']);
        Route::get('/address/show/{customer}/{address}', [CustomerAddressController::class, 'show']);
        Route::post('/address/{customer}', [CustomerAddressController::class, 'store']);
        Route::match(['put', 'patch'], '/address/{customer}/{address}', [CustomerAddressController::class, 'update']);
        Route::delete('/address/{customer}/{address}', [CustomerAddressController::class, 'destroy']);
    });

    Route::prefix('delivery-boy')->name('delivery-boy.')->group(function () {
        Route::get('/', [DeliveryBoyController::class, 'index']);
        Route::post('/', [DeliveryBoyController::class, 'store']);
        Route::get('/show/{deliveryBoy}', [DeliveryBoyController::class, 'show']);
        Route::match(['put', 'patch'], '/{deliveryBoy}', [DeliveryBoyController::class, 'update']);
        Route::delete('/{deliveryBoy}', [DeliveryBoyController::class, 'destroy']);
        Route::get('/all-delivery-boy', [DeliveryBoyController::class, 'allDeliveryBoy']);
        Route::get('/export', [DeliveryBoyController::class, 'export']);
        Route::post('/change-password/{deliveryBoy}', [DeliveryBoyController::class, 'changePassword']);
        Route::post('/change-image/{deliveryBoy}', [DeliveryBoyController::class, 'changeImage']);
        Route::get('/my-order/{deliveryBoy}', [DeliveryBoyController::class, 'myOrder']);
        Route::get('/delivery-boy-statement', [DeliveryBoyController::class, 'deliveryBoyStatement']);
        Route::get('/delivery-boy-collection', [DeliveryBoyController::class, 'deliveryBoyCollection']);

        Route::prefix('delivered-order')->name('delivered-order.')->group(function () {
            Route::get('/{deliveryBoy}', [DeliveryBoyOrderController::class, 'deliveredOrder']);
            Route::get('/show/{deliveryBoy}/{order}', [DeliveryBoyOrderController::class, 'deliveredOrderDetails']);
        });

        Route::prefix('address')->name('address.')->group(function () {
            Route::get('/{deliveryBoy}', [DeliveryBoyAddressController::class, 'index']);
            Route::get('/show/{deliveryBoy}/{address}', [DeliveryBoyAddressController::class, 'show']);
            Route::post('/{deliveryBoy}', [DeliveryBoyAddressController::class, 'store']);
            Route::match(['put', 'patch'], '/{deliveryBoy}/{address}', [DeliveryBoyAddressController::class, 'update']);
            Route::delete('/{deliveryBoy}/{address}', [DeliveryBoyAddressController::class, 'destroy']);
        });
    });

    Route::prefix('restaurant-owner')->name('restaurant-owner.')->group(function () {
        Route::get('/', [RestaurantOwnerController::class, 'index']);
        Route::post('/', [RestaurantOwnerController::class, 'store']);
        Route::get('/show/{restaurantOwner}', [RestaurantOwnerController::class, 'show']);
        Route::match(['post', 'put', 'patch'], '/{restaurantOwner}', [RestaurantOwnerController::class, 'update']);
        Route::delete('/{restaurantOwner}', [RestaurantOwnerController::class, 'destroy']);
        Route::get('/export', [RestaurantOwnerController::class, 'export']);
        Route::post('/change-password/{restaurantOwner}', [RestaurantOwnerController::class, 'changePassword']);
        Route::post('/change-image/{restaurantOwner}', [RestaurantOwnerController::class, 'changeImage']);
        Route::get('/my-order/{restaurantOwner}', [RestaurantOwnerController::class, 'myOrder']);

        Route::get('/address/{restaurantOwner}', [RestaurantOwnerAddressController::class, 'index']);
        Route::get('/address/show/{restaurantOwner}/{address}', [RestaurantOwnerAddressController::class, 'show']);
        Route::post('/address/{restaurantOwner}', [RestaurantOwnerAddressController::class, 'store']);
        Route::match(['put', 'patch'], '/address/{restaurantOwner}/{address}', [RestaurantOwnerAddressController::class, 'update']);
        Route::delete('/address/{restaurantOwner}/{address}', [RestaurantOwnerAddressController::class, 'destroy']);
    });

    Route::prefix('sales-report')->name('sales-report.')->group(function () {
        Route::get('/', [SalesReportController::class, 'index']);
        Route::get('/export', [SalesReportController::class, 'export']);
    });

    Route::prefix('pos-category')->name('pos-category.')->group(function () {
        Route::get('/', [PosCategoryController::class, 'index']);
    });

    Route::prefix('pos-offer')->name('pos-offer.')->group(function () {
        Route::get('/', [PosOfferController::class, 'index']);
    });

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::post('/', [PosController::class, 'store']);
    });

    Route::prefix('pos-order')->name('posOrder.')->group(function () {
        Route::get('/', [PosOrderController::class, 'index']);
        Route::get('show/{order}', [PosOrderController::class, 'show']);
        Route::delete('/{order}', [PosOrderController::class, 'destroy']);
        Route::get('/export', [PosOrderController::class, 'export']);
        Route::post('/change-status/{order}', [PosOrderController::class, 'changeStatus']);
    });

    Route::prefix('items-report')->name('items-report.')->group(function () {
        Route::get('/', [ItemsReportController::class, 'index']);
        Route::get('/export', [ItemsReportController::class, 'export']);
    });

    Route::prefix('online-order')->name('onlineOrder.')->group(function () {
        Route::get('/', [OnlineOrderController::class, 'index']);
        Route::get('/show/{order}', [OnlineOrderController::class, 'show']);
        Route::get('/export', [OnlineOrderController::class, 'export']);
        Route::post('/change-status/{order}', [OnlineOrderController::class, 'changeStatus']);
        Route::post('/add-token/{order}', [OnlineOrderController::class, 'addToken']);
    });

    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::get('/', [RestaurantController::class, 'index']);
        Route::get('/all-restaurant', [RestaurantController::class, 'allRestaurant']);
        Route::get('/show/{restaurant}', [RestaurantController::class, 'show']);
        Route::post('/', [RestaurantController::class, 'store']);
        Route::match(['put', 'patch'], '/{restaurant}', [RestaurantController::class, 'update']);
        Route::delete('/{restaurant}', [RestaurantController::class, 'destroy']);
        Route::get('/export', [RestaurantController::class, 'export']);
        Route::post('/change-image/{restaurant}', [RestaurantController::class, 'changeImage']);
        Route::post('/change-logo/{restaurant}', [RestaurantController::class, 'changeLogo']);
        Route::post('/user/{restaurant}', [RestaurantController::class, 'userStore']);
    });

    Route::prefix('payout')->name('payout.')->group(function () {
        Route::get('/', [PayoutController::class, 'index']);
        Route::post('/', [PayoutController::class, 'store']);
        Route::get('/show/{payout}', [PayoutController::class, 'show']);
        Route::get('/export', [PayoutController::class, 'export']);
        Route::get('/check/amount', [PayoutController::class, 'amount']);
        Route::delete('/{payout}', [PayoutController::class, 'destroy']);
    });

    Route::prefix('available-order')->name('availableOrder.')->group(function () {
        Route::get('/', [AvailableOrderController::class, 'index']);
        Route::get('/export', [AvailableOrderController::class, 'export']);
        Route::get('/change-status/{order}', [AvailableOrderController::class, 'changeStatus']);
    });

    Route::prefix('transaction')->name('transaction.')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/export', [TransactionController::class, 'export']);
    });

    Route::prefix('voucher')->name('voucher.')->group(function () {
        Route::get('/', [VoucherController::class, 'index']);
        Route::get('/show/{voucher}', [VoucherController::class, 'show']);
        Route::post('/', [VoucherController::class, 'store']);
        Route::match(['post', 'put', 'patch'], '/{voucher}', [VoucherController::class, 'update']);
        Route::delete('/{voucher}', [VoucherController::class, 'destroy']);
        Route::get('/export', [VoucherController::class, 'export']);
        Route::post('/translations/{voucher}', [VoucherController::class, 'saveTranslations']);
    });

    Route::prefix('collection')->name('collection.')->group(function () {
        Route::get('/', [CollectionController::class, 'index']);
        Route::post('/', [CollectionController::class, 'store']);
        Route::delete('/{collection}', [CollectionController::class, 'destroy']);
        Route::get('/export', [CollectionController::class, 'export']);
    });

    Route::prefix('cashout')->name('cashout.')->group(function () {
        Route::get('/', [CashoutController::class, 'index']);
        Route::post('/', [CashoutController::class, 'store']);
        Route::get('/show/{cashout}', [CashoutController::class, 'show']);
        Route::delete('/{cashout}', [CashoutController::class, 'destroy']);
        Route::get('/export', [CashoutController::class, 'export']);
    });

    Route::prefix('collection-report')->name('collection-report.')->group(function () {
        Route::get('/', [CollectionReportController::class, 'index']);
        Route::get('/export', [CollectionReportController::class, 'export']);
    });

    Route::prefix('credit-balance-report')->name('credit-balance-report.')->group(function () {
        Route::get('/', [CreditBalanceReportController::class, 'index']);
        Route::get('/export', [CreditBalanceReportController::class, 'export']);
    });

    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', [SubscriberController::class, 'index']);
        Route::delete('/{subscriber}', [SubscriberController::class, 'destroy']);
        Route::get('/export', [SubscriberController::class, 'export']);
        Route::post('/send-email', [SubscriberController::class, 'sendEmail']);
    });

    Route::prefix('message')->name('message.')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/show/{order}', [MessageController::class, 'show']);
        Route::post('/', [MessageController::class, 'store']);
    });

    Route::prefix('active-order')->name('activeOrder.')->group(function () {
        Route::get('/', [ActiveOrderController::class, 'index']);
        Route::get('/show/{order}', [ActiveOrderController::class, 'show']);
        Route::get('/export', [ActiveOrderController::class, 'export']);
        Route::get('/change-status/{order}', [ActiveOrderController::class, 'changeStatus']);
        Route::get('/received-status/{order}', [ActiveOrderController::class, 'receivedStatus']);
    });

    Route::prefix('push-notification')->name('push-notification.')->group(function () {
        Route::get('/', [PushNotificationController::class, 'index']);
        Route::post('/', [PushNotificationController::class, 'store']);
        Route::get('/show/{pushNotification}', [PushNotificationController::class, 'show']);
        Route::delete('/{pushNotification}', [PushNotificationController::class, 'destroy']);
        Route::get('/export', [PushNotificationController::class, 'export']);
    });

    Route::prefix('campaign')->name('campaign.')->group(function () {
        Route::get('/', [CampaignController::class, 'index']);
        Route::get('/show/{campaign}', [CampaignController::class, 'show']);
        Route::post('/', [CampaignController::class, 'store']);
        Route::post('/{campaign}', [CampaignController::class, 'update']);
        Route::delete('/{campaign}', [CampaignController::class, 'destroy']);
        Route::get('/export', [CampaignController::class, 'export']);
        Route::post('/change-thumbnail/{campaign}', [CampaignController::class, 'changeThumbnail']);
        Route::post('/change-cover/{campaign}', [CampaignController::class, 'changeCover']);
        Route::post('/translations/{campaign}', [CampaignController::class, 'saveTranslations']);

        Route::get('/restaurant/{campaign}', [CampaignRestaurantController::class, 'index']);
        Route::post('/restaurant/{campaign}', [CampaignRestaurantController::class, 'store']);
        Route::delete('/restaurant/{campaign}/{campaignRestaurant}', [CampaignRestaurantController::class, 'destroy']);
        Route::post('/restaurant/verify/{campaign}/{campaignRestaurant}', [CampaignRestaurantController::class, 'verify']);
    });

    Route::prefix('campaign-and-offer')->name('campaign-and-offer.')->group(function () {
        Route::prefix('campaign')->name('campaign.')->group(function () {
            Route::get('/', [CampaignAndOfferController::class, 'campaignList']);
            Route::get('/show/{campaign}', [CampaignAndOfferController::class, 'showCampaign']);
            Route::post('/apply/{campaign}', [CampaignAndOfferController::class, 'applyCampaign']);
            Route::delete('/{campaign}', [CampaignAndOfferController::class, 'leaveCampaign']);
            Route::get('/export', [CampaignAndOfferController::class, 'exportCampaign']);
        });

        Route::prefix('offer')->name('offer.')->group(function () {
            Route::get('/', [CampaignAndOfferController::class, 'offerList']);
            Route::get('/show/{offer}', [CampaignAndOfferController::class, 'showOffer']);
            Route::post('/apply/{offer}', [CampaignAndOfferController::class, 'applyOffer']);
            Route::delete('/{offer}', [CampaignAndOfferController::class, 'leaveOffer']);
            Route::get('/export', [CampaignAndOfferController::class, 'exportOffer']);
        });
    });

    Route::prefix('return-order')->name('return-order.')->group(function () {
        Route::get('/', [ReturnOrderController::class, 'index']);
        Route::get('/export', [ReturnOrderController::class, 'export']);
        Route::post('/', [ReturnOrderController::class, 'store']);
        Route::delete('/{order}', [ReturnOrderController::class, 'destroy']);
        Route::get('/show/{order}', [ReturnOrderController::class, 'show']);
    });

    Route::prefix('refund')->name('refund.')->group(function () {
        Route::get('/', [RefundController::class, 'index']);
        Route::get('/export', [RefundController::class, 'export']);
        Route::post('/', [RefundController::class, 'store']);
        Route::delete('/{refund}', [RefundController::class, 'destroy']);
    });

    Route::prefix('review')->name('review.')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::get('/export', [ReviewController::class, 'export']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::post('/{review}', [ReviewController::class, 'update']);
        Route::get('/show/{review}', [ReviewController::class, 'show']);
        Route::delete('/{review}', [ReviewController::class, 'destroy']);
    });

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/status', [AiController::class, 'status']);
        Route::post('/name', [AiController::class, 'name']);
        Route::post('/description', [AiController::class, 'description']);
        Route::post('/chat', [AiController::class, 'chat']);
        Route::post('/chat-response/{aiChatHistory}', [AiController::class, 'chatResponse']);
        Route::get('/chat-history', [AiController::class, 'chatHistory']);;
        Route::delete('/chat-history', [AiController::class, 'deleteChatHistory']);
    });

    Route::prefix('system-setting')->name('system-setting.')->group(function () {
        Route::prefix('company')->name('company.')->group(function () {
            Route::get('/', [CompanyController::class, 'index']);
            Route::match(['put', 'patch'], '/', [CompanyController::class, 'update']);
        });

        Route::prefix('site')->name('site.')->group(function () {
            Route::get('/', [SiteController::class, 'index']);
            Route::match(['put', 'patch'], '/', [SiteController::class, 'update']);
        });

        Route::prefix('frontend')->name('frontend.')->group(function () {
            Route::get('/', [FrontendController::class, 'index']);
            Route::post('/', [FrontendController::class, 'update']);
            Route::get('/translations', [FrontendController::class, 'getTranslations']);
            Route::post('/translations', [FrontendController::class, 'storeTranslations']);
        });

        Route::prefix('terms-and-conditions')->name('terms-and-conditions.')->group(function () {
            Route::get('/', [TermsAndConditionsController::class, 'index']);
            Route::match(['put', 'patch'], '/', [TermsAndConditionsController::class, 'update']);
        });

        Route::prefix('mail')->name('mail.')->group(function () {
            Route::get('/', [MailController::class, 'index']);
            Route::match(['put', 'patch'], '/', [MailController::class, 'update']);
        });

        Route::prefix('delivery-setup')->name('delivery-setup.')->group(function () {
            Route::get('/', [DeliverySetupController::class, 'index']);
            Route::match(['put', 'patch'], '/', [DeliverySetupController::class, 'update']);
        });

        Route::prefix('otp')->name('otp.')->group(function () {
            Route::get('/', [OtpController::class, 'index']);
            Route::match(['put', 'patch'], '/', [OtpController::class, 'update']);
        });

        Route::prefix('notification')->name('notification.')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('/update', [NotificationController::class, 'update']);
        });

        Route::prefix('notification-alert')->name('notification-alert.')->group(function () {
            Route::get('/', [NotificationAlertController::class, 'index']);
            Route::post('/update', [NotificationAlertController::class, 'update']);
        });

        Route::prefix('pusher')->name('pusher.')->group(function () {
            Route::get('/', [PusherController::class, 'index']);
            Route::post('/update', [PusherController::class, 'update']);
        });

        Route::prefix('social-media')->name('social-media.')->group(function () {
            Route::get('/', [SocialMediaController::class, 'index']);
            Route::match(['put', 'patch'], '/', [SocialMediaController::class, 'update']);
        });

        Route::prefix('cookies')->name('cookies.')->group(function () {
            Route::get('/', [CookiesController::class, 'index']);
            Route::match(['put', 'patch'], '/', [CookiesController::class, 'update']);
        });

        Route::prefix('analytic')->name('analytic.')->group(function () {
            Route::get('/', [AnalyticController::class, 'index']);
            Route::get('/show/{analytic}', [AnalyticController::class, 'show']);
            Route::post('/', [AnalyticController::class, 'store']);
            Route::post('/{analytic}', [AnalyticController::class, 'update']);
            Route::delete('/{analytic}', [AnalyticController::class, 'destroy']);
        });

        Route::prefix('analytic-section')->name('analytic-section.')->group(function () {
            Route::get('/{analytic}', [AnalyticSectionController::class, 'index']);
            Route::post('/{analytic}', [AnalyticSectionController::class, 'store']);
            Route::post('/{analytic}/{analyticSection}', [AnalyticSectionController::class, 'update']);
            Route::delete('/{analytic}/{analyticSection}', [AnalyticSectionController::class, 'destroy']);
        });

        Route::prefix('theme')->name('theme.')->group(function () {
            Route::get('/', [ThemeController::class, 'index']);
            Route::post('/', [ThemeController::class, 'update']);
        });

        Route::prefix('currency')->name('currency.')->group(function () {
            Route::get('/', [CurrencyController::class, 'index']);
            Route::post('/', [CurrencyController::class, 'store']);
            Route::post('update/{currency}', [CurrencyController::class, 'update']);
            Route::delete('/{currency}', [CurrencyController::class, 'destroy']);
        });

        Route::prefix('rider-tip')->name('rider-tip.')->group(function () {
            Route::get('/', [RiderTipController::class, 'index']);
            Route::post('/', [RiderTipController::class, 'store']);
            Route::post('/update/{riderTip}', [RiderTipController::class, 'update']);
            Route::delete('/{riderTip}', [RiderTipController::class, 'destroy']);
        });

        Route::prefix('cuisine')->name('cuisine.')->group(function () {
            Route::get('/', [CuisineController::class, 'index']);
            Route::post('/', [CuisineController::class, 'store']);
            Route::get('/show/{cuisine}', [CuisineController::class, 'show']);
            Route::post('/update/{cuisine}', [CuisineController::class, 'update']);
            Route::delete('/{cuisine}', [CuisineController::class, 'destroy']);
            Route::post('/sort', [CuisineController::class, 'sort']);
            Route::get('/all-cuisine', [CuisineController::class, 'allCuisine']);
        });

        Route::prefix('about-steps')->name('about-steps.')->group(function () {
            Route::get('/', [AboutStepsController::class, 'index']);
            Route::get('/show/{aboutStep}', [AboutStepsController::class, 'show']);
            Route::post('/', [AboutStepsController::class, 'store']);
            Route::post('/update/{aboutStep}', [AboutStepsController::class, 'update']);
            Route::delete('/{aboutStep}', [AboutStepsController::class, 'destroy']);
            Route::post('/sort', [AboutStepsController::class, 'sort']);
        });

        Route::prefix('benefit')->name('benefit.')->group(function () {
            Route::get('/', [BenefitController::class, 'index']);
            Route::get('/show/{benefit}', [BenefitController::class, 'show']);
            Route::post('/', [BenefitController::class, 'store']);
            Route::post('/update/{benefit}', [BenefitController::class, 'update']);
            Route::delete('/{benefit}', [BenefitController::class, 'destroy']);
            Route::post('/sort', [BenefitController::class, 'sort']);
        });

        Route::prefix('menu-section')->name('menu-section.')->group(function () {
            Route::get('/', [MenuSectionController::class, 'index']);
        });

        Route::prefix('menu-template')->name('menu-template.')->group(function () {
            Route::get('/', [MenuTemplateController::class, 'index']);
        });

        Route::prefix('page')->name('page.')->group(function () {
            Route::get('/', [PageController::class, 'index']);
            Route::get('/show/{page}', [PageController::class, 'show']);
            Route::post('/', [PageController::class, 'store']);
            Route::post('/{page}', [PageController::class, 'update']);
            Route::delete('/{page}', [PageController::class, 'destroy']);
        });

        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/show/{role}', [RoleController::class, 'show']);
            Route::post('/{role}', [RoleController::class, 'update']);
            Route::delete('/{role}', [RoleController::class, 'destroy']);
        });

        Route::prefix('permission')->name('permission.')->group(function () {
            Route::get('/{role}', [PermissionController::class, 'index']);
            Route::post('/{role}', [PermissionController::class, 'update']);
        });

        Route::prefix('language')->name('language.')->group(function () {
            Route::get('/', [LanguageController::class, 'index']);
            Route::post('/', [LanguageController::class, 'store']);
            Route::get('/show/{language}', [LanguageController::class, 'show']);
            Route::post('/update/{language}', [LanguageController::class, 'update']);
            Route::delete('/{language}', [LanguageController::class, 'destroy']);

            Route::get('/file-list/{language:code}', [LanguageController::class, 'fileList']);
            Route::post('/file-text', [LanguageController::class, 'fileText']);
            Route::post('/file-text/store', [LanguageController::class, 'fileTextStore']);
        });

        Route::prefix('sms-gateway')->name('sms-gateway.')->group(function () {
            Route::get('/', [SmsGatewayController::class, 'index']);
            Route::post('update/', [SmsGatewayController::class, 'update']);
        });

        Route::prefix('storage')->name('storage.')->group(function () {
            Route::get('/', [StorageController::class, 'index']);
            Route::post('update/', [StorageController::class, 'update']);
        });

        Route::prefix('ai-agent')->name('ai-agent.')->group(function () {
            Route::get('/', [AiAgentController::class, 'index']);
            Route::post('update/', [AiAgentController::class, 'update']);
        });

        Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
            Route::get('/', [PaymentGatewayController::class, 'index']);
            Route::post('update/', [PaymentGatewayController::class, 'update']);
        });

        Route::prefix('pwa')->name('pwa')->group(function () {
            Route::get('/', [PwaController::class, 'index']);
            Route::post('/', [PwaController::class, 'update']);
        });

        Route::prefix('menu')->name('menu')->group(function () {
            Route::get('/', [SettingMenuController::class, 'systemMenu']);;
        });
    });

    Route::prefix('restaurant-setting')->name('restaurant-setting.')->group(function () {
        Route::get('/default-restaurant', [MyRestaurantController::class, 'defaultRestaurant']);
        Route::prefix('my-restaurant')->name('my-restaurant.')->group(function () {
            Route::get('/', [MyRestaurantController::class, 'index']);
            Route::post('/', [MyRestaurantController::class, 'update']);
            Route::post('/change-image', [MyRestaurantController::class, 'changeImage']);
            Route::post('/change-logo', [MyRestaurantController::class, 'changeLogo']);
            Route::post('/current-status', [MyRestaurantController::class, 'currentStatus']);
        });

        Route::prefix('order-setup')->name('order-setup.')->group(function () {
            Route::get('/', [OrderSetupController::class, 'index']);
            Route::post('/', [OrderSetupController::class, 'store']);
        });

        Route::prefix('time-slot')->name('time-slot.')->group(function () {
            Route::get('/', [TimeSlotController::class, 'index']);
            Route::post('/', [TimeSlotController::class, 'store']);
            Route::delete('/{timeSlot}', [TimeSlotController::class, 'destroy']);
        });
        Route::prefix('item-category')->name('item-category.')->group(function () {
            Route::get('/', [ItemCategoryController::class, 'index']);
            Route::get('/show/{itemCategory}', [ItemCategoryController::class, 'show']);
            Route::post('/', [ItemCategoryController::class, 'store']);
            Route::post('/update/{itemCategory}', [ItemCategoryController::class, 'update']);
            Route::delete('/{itemCategory}', [ItemCategoryController::class, 'destroy']);
            Route::post('/sort', [ItemCategoryController::class, 'sort']);
            Route::post('/translations/{itemCategory}', [ItemCategoryController::class, 'saveTranslations']);
        });

        Route::prefix('item-attribute')->name('item-attribute.')->group(function () {
            Route::get('/', [ItemAttributeController::class, 'index']);
            Route::post('/', [ItemAttributeController::class, 'store']);
            Route::post('/update/{itemAttribute}', [ItemAttributeController::class, 'update']);
            Route::delete('/{itemAttribute}', [ItemAttributeController::class, 'destroy']);
        });

        Route::prefix('tax')->name('tax.')->group(function () {
            Route::get('/', [TaxController::class, 'index']);
            Route::post('/', [TaxController::class, 'store']);
            Route::post('/update/{tax}', [TaxController::class, 'update']);
            Route::delete('/{tax}', [TaxController::class, 'destroy']);
        });

        Route::prefix('menu')->name('menu')->group(function () {
            Route::get('/', [SettingMenuController::class, 'restaurantMenu']);
        });
    });

    Route::prefix('delivery-boy-setting')->name('delivery-boy-setting.')->group(function () {
        Route::prefix('delivery-location-setup')->name('delivery-location-setup.')->group(function () {
            Route::get('/', [DeliveryLocationSetupController::class, 'index']);
            Route::post('/', [DeliveryLocationSetupController::class, 'store']);
        });

        Route::prefix('menu')->name('menu')->group(function () {
            Route::get('/', [SettingMenuController::class, 'deliveryBoyMenu']);
        });
    });
});

Route::prefix('frontend')->name('frontend.')->middleware(['installed', 'apiKey', 'localization'])->group(function () {
    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
    });

    Route::prefix('auto-localization')->name('auto-localization.')->group(function () {
        Route::get('/', [FrontendAutoLocalizationController::class, 'index']);
    });

    Route::prefix('device-token')->name('device-token.')->middleware(['auth:sanctum'])->group(function () {
        Route::post('/web', [FrontendFirebaseTokenController::class, 'webToken']);
        Route::post('/mobile', [FrontendFirebaseTokenController::class, 'deviceToken']);
    });

    Route::prefix('country-code')->name('country-code.')->group(function () {
        Route::get('/', [FrontendCountryCodeController::class, 'index']);
        Route::get('/show/{country}', [FrontendCountryCodeController::class, 'show']);
        Route::post('/find', [FrontendCountryCodeController::class, 'find']);
    });

    Route::prefix('cookies')->name('cookies.')->group(function () {
        Route::get('/', [FrontendCookiesController::class, 'get']);
        Route::post('/', [FrontendCookiesController::class, 'set']);
    });

    Route::prefix('page')->name('page.')->group(function () {
        Route::get('/', [FrontendPageController::class, 'index']);
        Route::get('/show/{page:slug}', [FrontendPageController::class, 'show']);
        Route::get('/page-info/{page}', [FrontendPageController::class, 'show']);
    });

    Route::prefix('language')->name('language.')->group(function () {
        Route::get('/', [FrontendLanguageController::class, 'index']);
        Route::get('/show/{language}', [FrontendLanguageController::class, 'show']);
    });

    Route::prefix('about-step')->name('about-steps.')->group(function () {
        Route::get('/', [FrontendAboutStepsController::class, 'index']);
    });

    Route::prefix('benefit')->name('benefit.')->group(function () {
        Route::get('/', [FrontendBenefitController::class, 'index']);
    });

    Route::prefix('subscriber')->name('subscriber.')->group(function () {
        Route::post('/', [FrontendSubscriberController::class, 'store']);
    });

    Route::prefix('cuisine')->name('cuisine.')->group(function () {
        Route::get('/', [FrontendCuisineController::class, 'index']);
    });

    Route::prefix('address')->name('address.')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [FrontendAddressController::class, 'index']);
        Route::get('/show/{address}', [FrontendAddressController::class, 'show']);
        Route::post('/', [FrontendAddressController::class, 'store']);
        Route::match(['put', 'patch'], '/{address}', [FrontendAddressController::class, 'update']);
        Route::delete('/{address}', [FrontendAddressController::class, 'destroy']);
    });

    Route::prefix('restaurant')->name('restaurant.')->group(function () {
        Route::get('/', [FrontendRestaurantController::class, 'index']);
        Route::get('/show/{restaurant:slug}', [FrontendRestaurantController::class, 'show']);
        Route::get('/show-by-id/{restaurant}', [FrontendRestaurantController::class, 'show']);
        Route::get('restaurant-by-lat-long-radius', [FrontendRestaurantController::class, 'restaurantByLatLongRadius']);
        Route::get('favorite', [FrontendRestaurantController::class, 'favorite'])->middleware(['auth:sanctum']);
    });

    Route::prefix('offer')->name('offer.')->group(function () {
        Route::get('/', [FrontendOfferController::class, 'activeMultiOffer']);
        Route::get('/single', [FrontendOfferController::class, 'activeSingleOffer']);
        Route::get('/show/{offer:slug}/', [FrontendOfferController::class, 'activeShowOffer']);
        Route::get('/find', [FrontendOfferController::class, 'activeOfferFind']);
        Route::post('/check/{restaurant}', [FrontendOfferController::class, 'activeOfferCheck']);
    });

    Route::prefix('campaign')->name('campaign.')->group(function () {
        Route::get('/', [FrontendCampaignController::class, 'index']);
        Route::get('/show/{campaign:slug}/', [FrontendCampaignController::class, 'show']);
    });

    Route::prefix('item-category')->name('item-category.')->group(function () {
        Route::get('/', [FrontendItemCategoryController::class, 'index']);
        Route::get('/show/{itemCategory:slug}', [FrontendItemCategoryController::class, 'show']);
        Route::get('/items/{restaurant:slug}', [FrontendItemCategoryController::class, 'categoryWiseItems']);
    });

    Route::prefix('coupon')->name('coupon.')->group(function () {
        Route::get('/{restaurant}', [FrontendCouponController::class, 'index']);
        Route::get('/{frontendCoupon}/show', [FrontendCouponController::class, 'show']);
        Route::post('/{restaurant}/coupon-checking', [FrontendCouponController::class, 'couponChecking']);
    });

    Route::prefix('item')->name('item.')->group(function () {
        Route::get('/', [FrontendItemController::class, 'index']);
        Route::get('/search-items/{restaurant:slug}/', [FrontendItemController::class, 'searchItems']);
        Route::get('/show/{frontendItem:slug}', [FrontendItemController::class, 'show']);
    });

    Route::prefix('favorite')->middleware(['auth:sanctum'])->name('favorite.')->group(function () {
        Route::get('/', [FrontendFavoriteController::class, 'index']);
        Route::post('/toggle', [FrontendFavoriteController::class, 'toggle']);
    });

    Route::prefix('time-slot')->name('time-slot.')->group(function () {
        Route::get('/today/{restaurant}', [FrontendTimeSlotController::class, 'todayTimeSlot']);
        Route::get('/tomorrow/{restaurant}', [FrontendTimeSlotController::class, 'tomorrowTimeSlot']);
    });

    Route::prefix('payment-gateway')->name('payment-gateway.')->group(function () {
        Route::get('/', [FrontendPaymentGatewayController::class, 'index']);
    });

    Route::prefix('rider-tip')->name('rider-tip.')->group(function () {
        Route::get('/', [FrontendRiderTipController::class, 'index']);
    });

    Route::prefix('order')->name('order.')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/', [FrontendOrderController::class, 'index']);
        Route::post('/', [FrontendOrderController::class, 'store']);
        Route::get('/show/{frontendOrder}', [FrontendOrderController::class, 'show']);
        Route::post('/cancel/{frontendOrder}', [FrontendOrderController::class, 'cancel']);
    });

    Route::prefix('restaurant-review')->name('restaurant-review.')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/{order}', [FrontendRestaurantReviewController::class, 'show']);
        Route::post('/{order}', [FrontendRestaurantReviewController::class, 'store']);
    });

    Route::prefix('delivery-boy-review')->name('delivery-boy-review.')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/{order}', [FrontendDeliveryBoyReviewController::class, 'show']);
        Route::post('/{order}', [FrontendDeliveryBoyReviewController::class, 'store']);
    });

    Route::prefix('message')->name('message.')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/show/{order}', [FrontendMessageController::class, 'show']);
        Route::post('/', [FrontendMessageController::class, 'store']);
    });
});
