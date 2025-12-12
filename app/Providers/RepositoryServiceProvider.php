<?php

namespace App\Providers;

use App\Repositories\Admin\Interfaces\AboutUsRepositoryInterface;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use App\Repositories\Admin\Interfaces\BrandRepositoryInterface;
use App\Repositories\Admin\Interfaces\CardRepositoryInterface;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\FaqCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\FaqRepositoryInterface;
use App\Repositories\Admin\Interfaces\PrivacyPolicyRespositoryInterface;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface;
use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use App\Repositories\Admin\Interfaces\ShippingAndReturnRepositoryInterface;
use App\Repositories\Admin\Interfaces\ShowroomRepositoryInterface;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\TncRepositoryInterface;
use App\Repositories\Admin\Interfaces\TranslationRepositoryInterface;
use App\Repositories\Admin\Interfaces\WarehouseRepositoryInterface;
use App\Repositories\Admin\Repositories\AboutUsRepository;
use App\Repositories\Admin\Repositories\AdminRepository;
use App\Repositories\Admin\Repositories\BrandRepository;
use App\Repositories\Admin\Repositories\CardRepository;
use App\Repositories\Admin\Repositories\CategoryRepository;
use App\Repositories\Admin\Repositories\FaqCategoryRepository;
use App\Repositories\Admin\Repositories\FaqRepository;
use App\Repositories\Admin\Repositories\PrivacyPolicyRespository;
use App\Repositories\Admin\Repositories\ProductRepository;
use App\Repositories\Admin\Repositories\RoleRepository;
use App\Repositories\Admin\Repositories\ShippingAndReturnRepository;
use App\Repositories\Admin\Repositories\ShowroomRepository;
use App\Repositories\Admin\Repositories\SliderRepository;
use App\Repositories\Admin\Repositories\StoreRepository;
use App\Repositories\Admin\Repositories\SubCategoryRepository;
use App\Repositories\Admin\Repositories\TncRepository;
use App\Repositories\Admin\Repositories\TranslationRepository;
use App\Repositories\Admin\Repositories\WarehouseRepository;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Commands\Show;

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

        // Client Repositories
        $this->app->bind(\App\Repositories\Client\Interfaces\AboutUsRepositoryInterface::class, \App\Repositories\Client\Repositories\AboutUsRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\BrandRepositoryInterface::class, \App\Repositories\Client\Repositories\BrandRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\PrivacyPolicyRepositoryInterface::class, \App\Repositories\Client\Repositories\PrivacyPolicyRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ShippingAndReturnsRepositoryInterface::class, \App\Repositories\Client\Repositories\ShippingAndReturnsRepository::class);
        $this->app->bind(\App\Repositories\Client\Interfaces\ProductRepositoryInterface::class, \App\Repositories\Client\Repositories\ProductRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
