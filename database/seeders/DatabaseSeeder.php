<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CompanyBalanceTableSeeder::class);
        $this->call(MenuTableSeeder::class);
        $this->call(SettingMenuTableSeeder::class);
        $this->call(MenuSectionTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(AdminTableSeeder::class);
        $this->call(CustomerTableSeeder::class);
        $this->call(DeliveryBoyTableSeeder::class);
        $this->call(RolePermissionTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(SiteTableSeeder::class);
        $this->call(FrontendTableSeeder::class);
        $this->call(MailTableSeeder::class);
        $this->call(DeliverySetupTableSeeder::class);
        $this->call(OtpTableSeeder::class);
        $this->call(ThemeTableSeeder::class);
        $this->call(CuisineTableSeeder::class);
        $this->call(RestaurantTableSeeder::class);
        $this->call(RestaurantOwnerTableSeeder::class);
        $this->call(DeliveryBoyTableSeeder2::class);
        $this->call(CustomerTableSeeder2::class);
        $this->call(EmployeeTableSeeder::class);
        $this->call(MenuTemplateTableSeeder::class);
        $this->call(AboutStepsTableSeeder::class);
        $this->call(NotificationTableSeeder::class);
        $this->call(NotificationAlertTableSeeder::class);
        $this->call(PusherTableSeeder::class);
        $this->call(SocialMediaTableSeeder::class);
        $this->call(CookiesTableSeeder::class);
        $this->call(AnalyticTableSeeder::class);
        $this->call(TimeSlotTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(RiderTipTableSeeder::class);
        $this->call(ItemCategoryTableSeeder::class);
        $this->call(ItemAttributeTableSeeder::class);
        $this->call(TaxTableSeeder::class);
        $this->call(PageTableSeeder::class);
        $this->call(TermsAndConditionsTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(PaymentGatewayTableSeederVersionOne::class);
        $this->call(SmsGatewayTableSeeder::class);
        $this->call(AiAgentTableSeeder::class);
        $this->call(AiAgentDataTableSeeder::class);
        $this->call(StorageTableSeeder::class);
        $this->call(PaymentGatewayDataTableSeeder::class);
        $this->call(PwaTableSeeder::class);
        $this->call(BenefitTableSeeder::class);
        $this->call(VoucherTableSeeder::class);
        $this->call(CouponTableSeeder::class);
        $this->call(ItemTableSeeder::class);
        $this->call(ItemAddonTableSeeder::class);
        $this->call(OfferTableSeeder::class);
        $this->call(OfferRestaurantTableSeeder::class);
        $this->call(CampaignTableSeeder::class);
        $this->call(CampaignRestaurantTableSeeder::class);
        $this->call(OrderSetupTableSeeder::class);
        $this->call(DeliveryLocationTableSeeder::class);
        $this->call(SubscriberTableSeeder::class);
        $this->call(ModuleTestDataSeeder::class);
//        $this->call(PushNotificationTableSeeder::class);
//        $this->call(OrderTableSeeder::class);
//        $this->call(ReviewTableSeeder::class);
//        $this->call(RefundTableSeeder::class);
//        $this->call(MessageTableSeeder::class);
//        $this->call(CollectionTableSeeder::class);
//        $this->call(PayoutTableSeeder::class);
//        $this->call(CashoutTableSeeder::class);
//        $this->call(FavoriteTableSeeder::class);
    }
}
