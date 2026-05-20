<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Showrooms;
use App\Models\SubCategories;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SiteMapController extends Controller
{
	private const SITEMAP_TTL_HOURS = 24;

	public function index(): BinaryFileResponse
	{
		if ($this->shouldRegenerateSitemap()) {
			$sitemap = Sitemap::create();

			$this->addStaticPages($sitemap);
			$this->addCollectionPages($sitemap);
			$this->addDynamicPages($sitemap);

			$sitemap->writeToFile(public_path('sitemap.xml'));
		}

		return response()->file(public_path('sitemap.xml'), [
			'Content-Type' => 'application/xml; charset=UTF-8',
		]);
	}

	private function shouldRegenerateSitemap(): bool
	{
		$sitemapPath = public_path('sitemap.xml');

		if (! file_exists($sitemapPath)) {
			return true;
		}

		return filemtime($sitemapPath) < Carbon::now()->subHours(self::SITEMAP_TTL_HOURS)->timestamp;
	}

	private function addStaticPages(Sitemap $sitemap): void
	{
		$staticRoutes = [
			'home',
			'about',
			'privacy-policy',
			'faq',
			'terms',
			'shipping-returns',
			'support',
			'showrooms',
			'brands',
			'categories',
			'subcategories',
			'products.index',
			'products.deals',
			'new-arrivals',
			'best-sellers',
			'contact',
		];

		foreach ($staticRoutes as $routeName) {
			if (! Route::has($routeName)) {
				continue;
			}

			$sitemap->add(
				Url::create(
					route($routeName),
					Carbon::now(),
					Url::CHANGE_FREQUENCY_WEEKLY,
					0.8
				)
			);
		}
	}

	private function addCollectionPages(Sitemap $sitemap): void
	{
		foreach (Brand::query()->where('status', 'active')->cursor() as $brand) {
			if (! Route::has('brand.products')) {
				continue;
			}

			$sitemap->add(
				Url::create(
					route('brand.products', ['slug' => $brand->slug]),
					$brand->updated_at ?? Carbon::now(),
					Url::CHANGE_FREQUENCY_WEEKLY,
					0.7
				)
			);
		}

		foreach (Categories::query()->where('status', 'active')->cursor() as $category) {
			if (! Route::has('category.products')) {
				continue;
			}

			$sitemap->add(
				Url::create(
					route('category.products', ['slug' => $category->slug]),
					$category->updated_at ?? Carbon::now(),
					Url::CHANGE_FREQUENCY_WEEKLY,
					0.7
				)
			);
		}

		foreach (SubCategories::query()->where('status', 'active')->cursor() as $subCategory) {
			if (! Route::has('subcategory.products')) {
				continue;
			}

			$sitemap->add(
				Url::create(
					route('subcategory.products', ['slug' => $subCategory->slug]),
					$subCategory->updated_at ?? Carbon::now(),
					Url::CHANGE_FREQUENCY_WEEKLY,
					0.7
				)
			);
		}

		foreach (Showrooms::query()->where('status', 'active')->orderBy('sort_order')->cursor() as $showroom) {
			if (! Route::has('showrooms.show')) {
				continue;
			}

			$sitemap->add(
				Url::create(
					route('showrooms.show', ['slug' => $showroom->slug]),
					$showroom->updated_at ?? Carbon::now(),
					Url::CHANGE_FREQUENCY_MONTHLY,
					0.6
				)
			);
		}
	}

	private function addDynamicPages(Sitemap $sitemap): void
	{
		if (! Route::has('products.detail')) {
			return;
		}

		Product::query()
			->where('status', 'active')
			->select(['id', 'slug', 'updated_at'])
			->cursor()
			->each(function (Product $product) use ($sitemap) {
				$sitemap->add(
					Url::create(
						route('products.detail', ['slug' => $product->slug]),
						$product->updated_at ?? Carbon::now(),
						Url::CHANGE_FREQUENCY_WEEKLY,
						0.9
					)
				);
			});
	}
}
