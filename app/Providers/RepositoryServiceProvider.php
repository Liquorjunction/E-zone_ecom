<?php

namespace App\Providers;

use App\Repositories\Admin\Interfaces\AboutUsRepositoryInterface;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use App\Repositories\Admin\Interfaces\BrandRepositoryInterface;
use App\Repositories\Admin\Interfaces\CouponRepositoryInterface;
use App\Repositories\Admin\Interfaces\CouponUsageRepositoryInterface;
use App\Repositories\Admin\Interfaces\CardRepositoryInterface;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\DiscountRepositoryInterface;
use App\Repositories\Admin\Interfaces\DiscountTargetRepositoryInterface;
use App\Repositories\Admin\Interfaces\FaqCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\FaqRepositoryInterface;
use App\Repositories\Admin\Interfaces\InventoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface;
use App\Repositories\Admin\Interfaces\ManageCustomerRepositoryInterface;
use App\Repositories\Admin\Interfaces\NewsletterRepositoryInterface;
use App\Repositories\Admin\Interfaces\OrderRepositoryInterface;
use App\Repositories\Admin\Interfaces\PrivacyPolicyRespositoryInterface;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface;
use App\Repositories\Admin\Interfaces\QuickStatRepositoryInterface;
use App\Repositories\Admin\Interfaces\ReviewRepositoryInterface;
use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use App\Repositories\Admin\Interfaces\ShippingAndReturnRepositoryInterface;
use App\Repositories\Admin\Interfaces\ShowroomRepositoryInterface;
use App\Repositories\Admin\Interfaces\SiteSettingsRepositoryInterface;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface;
use App\Repositories\Admin\Interfaces\StockReportRepositoryInterface;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\TncRepositoryInterface;
use App\Repositories\Admin\Interfaces\TranslationRepositoryInterface;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\Admin\Interfaces\WhyShopWithUsRepositoryInterface;
use App\Repositories\Admin\Repositories\AboutUsRepository;
use App\Repositories\Admin\Repositories\AdminRepository;
use App\Repositories\Admin\Repositories\BrandRepository;
use App\Repositories\Admin\Repositories\CouponRepository;
use App\Repositories\Admin\Repositories\CouponUsageRepository;
use App\Repositories\Admin\Repositories\CardRepository;
use App\Repositories\Admin\Repositories\CategoryRepository;
use App\Repositories\Admin\Repositories\DiscountRepository;
use App\Repositories\Admin\Repositories\DiscountTargetRepository;
use App\Repositories\Admin\Repositories\FaqCategoryRepository;
use App\Repositories\Admin\Repositories\FaqRepository;
use App\Repositories\Admin\Repositories\InventoryRepository;
use App\Repositories\Admin\Repositories\InventoryTransactionsRepository;
use App\Repositories\Admin\Repositories\ManageCustomerRepository;
use App\Repositories\Admin\Repositories\NewsletterRepository;
use App\Repositories\Admin\Repositories\OrderRepository;
use App\Repositories\Admin\Repositories\PrivacyPolicyRespository;
use App\Repositories\Admin\Repositories\ProductRepository;
use App\Repositories\Admin\Repositories\QuickStatRepository;
use App\Repositories\Admin\Repositories\ReviewRepository;
use App\Repositories\Admin\Repositories\RoleRepository;
use App\Repositories\Admin\Repositories\ShippingAndReturnRepository;
use App\Repositories\Admin\Repositories\ShowroomRepository;
use App\Repositories\Admin\Repositories\SiteSettingsRepository;
use App\Repositories\Admin\Repositories\SliderRepository;
use App\Repositories\Admin\Repositories\StockReportRepository;
use App\Repositories\Admin\Repositories\StoreRepository;
use App\Repositories\Admin\Repositories\SubCategoryRepository;
use App\Repositories\Admin\Repositories\TncRepository;
use App\Repositories\Admin\Repositories\TranslationRepository;
use App\Repositories\Admin\Repositories\WarehouseRepository;
use App\Repositories\Admin\Repositories\WhyShopWithUsRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(SliderRepositoryInterface::class, SliderRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(CardRepositoryInterface::class, CardRepository::class);
        $this->app->bind(PrivacyPolicyRespositoryInterface::class, PrivacyPolicyRespository::class);
        $this->app->bind(TranslationRepositoryInterface::class, TranslationRepository::class);
        $this->app->bind(FaqCategoryRepositoryInterface::class, FaqCategoryRepository::class);
        $this->app->bind(FaqRepositoryInterface::class, FaqRepository::class);
        $this->app->bind(TncRepositoryInterface::class, TncRepository::class);
        $this->app->bind(ShippingAndReturnRepositoryInterface::class, ShippingAndReturnRepository::class);
        $this->app->bind(AboutUsRepositoryInterface::class, AboutUsRepository::class);
        $this->app->bind(ShowroomRepositoryInterface::class, ShowroomRepository::class);
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(StoreRepositoryInterface::class, StoreRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(InventoryTransactionsRepositoryInterface::class, InventoryTransactionsRepository::class);
        $this->app->bind(DiscountTargetRepositoryInterface::class, DiscountTargetRepository::class);
        $this->app->bind(DiscountRepositoryInterface::class, DiscountRepository::class);
        $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
        $this->app->bind(CouponUsageRepositoryInterface::class, CouponUsageRepository::class);
        $this->app->bind(QuickStatRepositoryInterface::class, QuickStatRepository::class);
        $this->app->bind(WhyShopWithUsRepositoryInterface::class, WhyShopWithUsRepository::class);
        $this->app->bind(StockReportRepositoryInterface::class, StockReportRepository::class);
        $this->app->bind(ManageCustomerRepositoryInterface::class, ManageCustomerRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(NewsletterRepositoryInterface::class, NewsletterRepository::class);
        $this->app->bind(SiteSettingsRepositoryInterface::class, SiteSettingsRepository::class);

        // Client Repositories
        $this->app->bind(\App\Repositories\Client\Interfaces\AuthRepositoryInterface::class, \App\Repositories\Client\Repositories\AuthRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\AboutUsRepositoryInterface::class, \App\Repositories\Client\Repositories\AboutUsRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\BrandRepositoryInterface::class, \App\Repositories\Client\Repositories\BrandRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\HomeRepositoryInterface::class, \App\Repositories\Client\Repositories\HomeRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\PrivacyPolicyRepositoryInterface::class, \App\Repositories\Client\Repositories\PrivacyPolicyRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ShippingAndReturnsRepositoryInterface::class, \App\Repositories\Client\Repositories\ShippingAndReturnsRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ProductRepositoryInterface::class, \App\Repositories\Client\Repositories\ProductRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\CartRepositoryInterface::class, \App\Repositories\Client\Repositories\CartRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\OrderRepositoryInterface::class, \App\Repositories\Client\Repositories\OrderRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\AddressRepositoryInterface::class, \App\Repositories\Client\Repositories\AddressRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\WishlistRepositoryInterface::class, \App\Repositories\Client\Repositories\WishlistRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\SecurityRepositoryInterface::class, \App\Repositories\Client\Repositories\SecurityRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\DiscountRepositoryInterface::class, \App\Repositories\Client\Repositories\DiscountRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\FaqRepositoryInterface::class, \App\Repositories\Client\Repositories\FaqRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\WhyShopWithUsRepositoryInterface::class, \App\Repositories\Client\Repositories\WhyShopWithUsRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\QuickStatRepositoryInterface::class, \App\Repositories\Client\Repositories\QuickStatRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ProfileRepositoryInterface::class, \App\Repositories\Client\Repositories\ProfileRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\CartRepositoryInterface::class, \App\Repositories\Client\Repositories\CartRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ReviewRepositoryInterface::class, \App\Repositories\Client\Repositories\ReviewRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\NewsletterRepositoryInterface::class, \App\Repositories\Client\Repositories\NewsletterRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ShowroomRepositoryInterface::class, \App\Repositories\Client\Repositories\ShowroomRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
