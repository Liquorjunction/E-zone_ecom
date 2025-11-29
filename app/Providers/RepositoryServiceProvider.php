<?php

namespace App\Providers;

use App\Models\Card;
use App\Repositories\Admin\Interfaces\AdminRepositoryInterface;
use App\Repositories\Admin\Interfaces\CardRepositoryInterface;
use App\Repositories\Admin\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\FaqCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\FaqRepositoryInterface;
use App\Repositories\Admin\Interfaces\PrivacyPolicyRespositoryInterface;
use App\Repositories\Admin\Interfaces\RoleRepositoryInterface;
use App\Repositories\Admin\Interfaces\SliderRepositoryInterface;
use App\Repositories\Admin\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\Admin\Interfaces\TncRepositoryInterface;
use App\Repositories\Admin\Interfaces\TranslationRepositoryInterface;
use App\Repositories\Admin\Repositories\AdminRepository;
use App\Repositories\Admin\Repositories\CardRepository;
use App\Repositories\Admin\Repositories\CategoryRepository;
use App\Repositories\Admin\Repositories\FaqCategoryRepository;
use App\Repositories\Admin\Repositories\FaqRepository;
use App\Repositories\Admin\Repositories\PrivacyPolicyRespository;
use App\Repositories\Admin\Repositories\RoleRepository;
use App\Repositories\Admin\Repositories\SliderRepository;
use App\Repositories\Admin\Repositories\SubCategoryRepository;
use App\Repositories\Admin\Repositories\TncRepository;
use App\Repositories\Admin\Repositories\TranslationRepository;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
