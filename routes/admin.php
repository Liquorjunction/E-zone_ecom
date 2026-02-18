<?php

use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CouponUsageController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InventoryTransactionsController;
use App\Http\Controllers\Admin\ManageCustomerController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\QuickStatController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShippingAndReturnController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StockReportController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TncController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\WhyShopWithUsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Translation Routes
        Route::post('/translate-content', [TranslationController::class, 'translate'])->name('translate.content');
        Route::post('/translate-batch', [TranslationController::class, 'translateBatch'])->name('translate.batch');

        // User Management routes (Admins)
        Route::prefix('user-management')->name('user-management.')->group(function () {
            Route::get('/', [AdminAuthController::class, 'index'])->name('index')->middleware('can:user management.read,admin');
            Route::get('/data', [AdminAuthController::class, 'data'])->name('data')->middleware('can:user management.read,admin');
            Route::get('/create', [AdminAuthController::class, 'create'])->name('create')->middleware('can:user management.create,admin');
            Route::post('/', [AdminAuthController::class, 'store'])->name('store')->middleware('can:user management.create,admin');
            Route::get('/edit', [AdminAuthController::class, 'edit'])->name('edit')->middleware('can:user management.update,admin');
            Route::get('/show', [AdminAuthController::class, 'show'])->name('view')->middleware('can:user management.read,admin');
            Route::put('/update', [AdminAuthController::class, 'update'])->name('update')->middleware('can:user management.update,admin');
            Route::patch('/toggle-status', [AdminAuthController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:user management.update,admin');
        });

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });

        // Roles & Permissions routes
        Route::prefix('roles-permissions')->name('roles-permissions.')->middleware('can:role.read,admin')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index'); // permission: role.read
            Route::get('/data', [RoleController::class, 'data'])->name('data');
            Route::get('/show', [RoleController::class, 'show'])->name('show')->middleware('can:role.read,admin');
            Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('can:role.create,admin');
            Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('can:role.create,admin');
            Route::get('/edit', [RoleController::class, 'edit'])->name('edit')->middleware('can:role.update,admin');
            Route::put('/update', [RoleController::class, 'update'])->name('update')->middleware('can:role.update,admin');
            Route::delete('/destroy', [RoleController::class, 'destroy'])->name('destroy')->middleware('can:role.delete,admin');
        });

        // Hero-Slider routes
        Route::prefix('hero-slider')->name('hero-slider.')->middleware('can:slider.read,admin')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('index'); // permission: slider.read
            Route::get('/data', [SliderController::class, 'data'])->name('data');
            Route::get('/create', [SliderController::class, 'create'])->name('create')->middleware('can:slider.create,admin');
            Route::post('/', [SliderController::class, 'store'])->name('store')->middleware('can:slider.create,admin');
            Route::get('/edit', [SliderController::class, 'edit'])->name('edit')->middleware('can:slider.update,admin');
            Route::put('/update', [SliderController::class, 'update'])->name('update')->middleware('can:slider.update,admin');
            Route::delete('/destroy', [SliderController::class, 'destroy'])->name('destroy')->middleware('can:slider.delete,admin');
            Route::post('/batch-destroy', [SliderController::class, 'batchDestroy'])->name('batchDestroy')->middleware('can:slider.delete,admin');
            Route::patch('/toggle-status', [SliderController::class, 'updateStatus'])->name('toggleStatus')->middleware('can:slider.update,admin');
            Route::post('/batch-update-status', [SliderController::class, 'batchUpdateStatus'])->name('batchUpdateStatus')->middleware('can:slider.update,admin');
        });

        // Categories & Subcategories routes
        Route::prefix('category-subcategory')->name('category-subcategory.')->group(function () {

            // Separate Index Pages
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');

            // --- Categories Routes ---
            Route::prefix('categories')->name('categories.')->middleware('can:category.read,admin')->group(function () {
                Route::get('/data', [CategoryController::class, 'data'])->name('data');
                Route::get('/create', [CategoryController::class, 'create'])->name('create')->middleware('can:category.create,admin');
                Route::post('/', [CategoryController::class, 'store'])->name('store')->middleware('can:category.create,admin');
                Route::get('/edit', [CategoryController::class, 'edit'])->name('edit')->middleware('can:category.update,admin');
                Route::put('/update', [CategoryController::class, 'update'])->name('update')->middleware('can:category.update,admin');
                Route::delete('/destroy', [CategoryController::class, 'destroy'])->name('destroy')->middleware('can:category.delete,admin');
                Route::patch('/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:category.update,admin');
            });

            // --- SubCategories Routes ---
            Route::prefix('subcategories')->name('subcategories.')->middleware('can:subcategory.read,admin')->group(function () {
                Route::get('/data', [SubCategoryController::class, 'data'])->name('data');
                Route::get('/create', [SubCategoryController::class, 'create'])->name('create')->middleware('can:subcategory.create,admin');
                Route::post('/', [SubCategoryController::class, 'store'])->name('store')->middleware('can:subcategory.create,admin');
                Route::get('/edit', [SubCategoryController::class, 'edit'])->name('edit')->middleware('can:subcategory.update,admin');
                Route::put('/update', [SubCategoryController::class, 'update'])->name('update')->middleware('can:subcategory.update,admin');
                Route::delete('/destroy', [SubCategoryController::class, 'destroy'])->name('destroy')->middleware('can:subcategory.delete,admin');
                Route::patch('/toggle-status', [SubCategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:subcategory.update,admin');
            });
        });

        // Card Routes
        Route::prefix('cards')->name('cards.')->middleware('can:card.read,admin')->group(function () {
            Route::get('/', [CardController::class, 'index'])->name('index'); // permission: card.read
            Route::get('/data', [CardController::class, 'data'])->name('data');
            Route::get('/create', [CardController::class, 'create'])->name('create')->middleware('can:card.create,admin');
            Route::post('/', [CardController::class, 'store'])->name('store')->middleware('can:card.create,admin');
            Route::get('/edit', [CardController::class, 'edit'])->name('edit')->middleware('can:card.update,admin');
            Route::put('/update', [CardController::class, 'update'])->name('update')->middleware('can:card.update,admin');
            Route::delete('/destroy', [CardController::class, 'destroy'])->name('destroy')->middleware('can:card.delete,admin');
            Route::patch('/toggle-status', [CardController::class, 'status'])->name('toggleStatus')->middleware('can:card.update,admin');
        });

        // Privacy Policy Routes
        Route::prefix('privacy-policy')->name('privacy-policy.')->middleware('can:privacy_policies.read,admin')->group(function () {
            Route::get('/', [PrivacyPolicyController::class, 'index'])->name('index');
            Route::get('/data', [PrivacyPolicyController::class, 'data'])->name('data');
            Route::get('/create', [PrivacyPolicyController::class, 'create'])->name('create')->middleware('can:privacy_policies.create,admin');
            Route::post('/', [PrivacyPolicyController::class, 'store'])->name('store')->middleware('can:privacy_policies.create,admin');
            Route::get('/edit', [PrivacyPolicyController::class, 'edit'])->name('edit')->middleware('can:privacy_policies.update,admin');
            Route::put('/update', [PrivacyPolicyController::class, 'update'])->name('update')->middleware('can:privacy_policies.update,admin');
            Route::delete('/destroy', [PrivacyPolicyController::class, 'destroy'])->name('destroy')->middleware('can:privacy_policies.delete,admin');
            Route::patch('/toggle-status', [PrivacyPolicyController::class, 'updateStatus'])->name('toggleStatus')->middleware('can:privacy_policies.update,admin');
        });

        // FAQ Category Routes
        Route::prefix('faq-categories')->name('faq-categories.')->middleware('can:faqs.read,admin')->group(function () {
            Route::get('/', [FaqCategoryController::class, 'index'])->name('index');
            Route::get('/data', [FaqCategoryController::class, 'data'])->name('data');
            Route::get('/create', [FaqCategoryController::class, 'create'])->name('create')->middleware('can:faqs.create,admin');
            Route::post('/', [FaqCategoryController::class, 'store'])->name('store')->middleware('can:faqs.create,admin');
            Route::get('/edit', [FaqCategoryController::class, 'edit'])->name('edit')->middleware('can:faqs.update,admin');
            Route::put('/update', [FaqCategoryController::class, 'update'])->name('update')->middleware('can:faqs.update,admin');
            Route::delete('/destroy', [FaqCategoryController::class, 'destroy'])->name('destroy')->middleware('can:faqs.delete,admin');
            Route::patch('/toggle-status', [FaqCategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:faqs.update,admin');
        });

        // FAQ Routes
        Route::prefix('faqs')->name('faqs.')->middleware('can:faqs.read,admin')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::get('/data', [FaqController::class, 'data'])->name('data');
            Route::get('/create', [FaqController::class, 'create'])->name('create')->middleware('can:faqs.create,admin');
            Route::post('/', [FaqController::class, 'store'])->name('store')->middleware('can:faqs.create,admin');
            Route::get('/edit', [FaqController::class, 'edit'])->name('edit')->middleware('can:faqs.update,admin');
            Route::put('/update', [FaqController::class, 'update'])->name('update')->middleware('can:faqs.update,admin');
            Route::delete('/destroy', [FaqController::class, 'destroy'])->name('destroy')->middleware('can:faqs.delete,admin');
            Route::patch('/toggle-status', [FaqController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:faqs.update,admin');
        });

        // Terms and Conditions Routes
        Route::prefix('tncs')->name('tncs.')->middleware('can:tnc.read,admin')->group(function () {
            Route::get('/', [TncController::class, 'index'])->name('index');
            Route::get('/data', [TncController::class, 'data'])->name('data');
            Route::get('/create', [TncController::class, 'create'])->name('create')->middleware('can:tnc.create,admin');
            Route::post('/', [TncController::class, 'store'])->name('store')->middleware('can:tnc.create,admin');
            Route::get('/edit', [TncController::class, 'edit'])->name('edit')->middleware('can:tnc.update,admin');
            Route::put('/update', [TncController::class, 'update'])->name('update')->middleware('can:tnc.update,admin');
            Route::delete('/destroy', [TncController::class, 'destroy'])->name('destroy')->middleware('can:tnc.delete,admin');
            Route::patch('/toggle-status', [TncController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:tnc.update,admin');
        });

        // Shipping & Return Routes
        Route::group(['prefix' => 'shipping-return', 'as' => 'shipping-return.'], function () {
            Route::get('/', [ShippingAndReturnController::class, 'index'])->name('index')->can('shipping_return.read');
            Route::get('/create', [ShippingAndReturnController::class, 'create'])->name('create')->can('shipping_return.create');
            Route::post('/store', [ShippingAndReturnController::class, 'store'])->name('store')->can('shipping_return.create');
            Route::get('/{id}/edit', [ShippingAndReturnController::class, 'edit'])->name('edit')->can('shipping_return.update');
            Route::put('/update', [ShippingAndReturnController::class, 'update'])->name('update')->can('shipping_return.update');
            Route::delete('/destroy', [ShippingAndReturnController::class, 'destroy'])->name('destroy')->can('shipping_return.delete');
            Route::get('/data', [ShippingAndReturnController::class, 'data'])->name('data')->can('shipping_return.read');
            Route::patch('/status/{id}', [ShippingAndReturnController::class, 'status'])->name('toggleStatus')->can('shipping_return.update');
        });

        // About Us Routes
        Route::prefix('about-us')->name('about_us.')->middleware('can:about_us.read,admin')->group(function () {
            Route::get('/', [AboutUsController::class, 'index'])->name('index'); // permission: about_us.read
            Route::get('/data', [AboutUsController::class, 'data'])->name('data');
            Route::get('/create', [AboutUsController::class, 'create'])->name('create')->middleware('can:about_us.create,admin');
            Route::post('/', [AboutUsController::class, 'store'])->name('store')->middleware('can:about_us.create,admin');
            Route::get('/edit', [AboutUsController::class, 'edit'])->name('edit')->middleware('can:about_us.update,admin');
            Route::put('/update', [AboutUsController::class, 'update'])->name('update')->middleware('can:about_us.update,admin');
            Route::delete('/destroy', [AboutUsController::class, 'destroy'])->name('destroy')->middleware('can:about_us.delete,admin');
            Route::patch('/toggle-status', [AboutUsController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:about_us.update,admin');
        });

        // Showroom Routes
        Route::prefix('showrooms')->name('showrooms.')->middleware('can:showrooms.read,admin')->group(function () {
            Route::get('/', [ShowroomController::class, 'index'])->name('index'); // permission: showrooms.read
            Route::get('/data', [ShowroomController::class, 'data'])->name('data');
            Route::get('/show', [ShowroomController::class, 'show'])->name('show')->middleware('can:showrooms.read,admin');
            Route::get('/create', [ShowroomController::class, 'create'])->name('create')->middleware('can:showrooms.create,admin');
            Route::post('/', [ShowroomController::class, 'store'])->name('store')->middleware('can:showrooms.create,admin');
            Route::get('/edit', [ShowroomController::class, 'edit'])->name('edit')->middleware('can:showrooms.update,admin');
            Route::put('/update', [ShowroomController::class, 'update'])->name('update')->middleware('can:showrooms.update,admin');
            Route::delete('/destroy', [ShowroomController::class, 'destroy'])->name('destroy')->middleware('can:showrooms.delete,admin');
            Route::patch('/toggle-status', [ShowroomController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:showrooms.update,admin');
        });

        // Brands Routes
        Route::prefix('brands')->name('brands.')->middleware('can:brands.read,admin')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index'); // permission: brands.read
            Route::get('/data', [BrandController::class, 'data'])->name('data');
            Route::get('/create', [BrandController::class, 'create'])->name('create')->middleware('can:brands.create,admin');
            Route::post('/', [BrandController::class, 'store'])->name('store')->middleware('can:brands.create,admin');
            Route::get('/edit', [BrandController::class, 'edit'])->name('edit')->middleware('can:brands.update,admin');
            Route::put('/update', [BrandController::class, 'update'])->name('update')->middleware('can:brands.update,admin');
            Route::delete('/destroy', [BrandController::class, 'destroy'])->name('destroy')->middleware('can:brands.delete,admin');
            Route::patch('/toggle-status', [BrandController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:brands.update,admin');
        });

        // Product Routes
        Route::prefix('products')->name('products.')->middleware('can:products.read,admin')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index'); // permission: products.read
            Route::get('/data', [ProductController::class, 'dataTable'])->name('data');
            Route::get('/create', [ProductController::class, 'create'])->name('create')->middleware('can:products.create,admin');
            Route::post('/', [ProductController::class, 'store'])->name('store')->middleware('can:products.create,admin');
            Route::get('/edit', [ProductController::class, 'edit'])->name('edit')->middleware('can:products.update,admin');
            Route::put('/update', [ProductController::class, 'update'])->name('update')->middleware('can:products.update,admin');
            Route::delete('/destroy', [ProductController::class, 'destroy'])->name('destroy')->middleware('can:products.delete,admin');
            Route::patch('/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:products.update,admin');
            Route::patch('/toggle-bestseller', [ProductController::class, 'toggleBestseller'])->name('toggleBestseller')->middleware('can:products.update,admin');
            Route::patch('/variant-toggle-status', [ProductController::class, 'toggleVariantStatus'])->name('variantToggleStatus')->middleware('can:products.update,admin');
            Route::patch('/variant-toggle-bestseller', [ProductController::class, 'toggleVariantBestseller'])->name('variantToggleBestseller')->middleware('can:products.update,admin');
            Route::patch('/variant-destroy', [ProductController::class, 'destroyVariant'])->name('variantDestroy')->middleware('can:products.delete,admin');
            Route::get('/show', [ProductController::class, 'show'])->name('show')->middleware('can:products.read,admin');
            Route::post('/import', [ProductController::class, 'import'])->name('import')->middleware('can:products.create,admin');
            Route::get('/template', [ProductController::class, 'downloadTemplate'])->name('template');
            Route::get('import/progress/{batchId}', [ProductController::class, 'importProgress'])->name('import.progress');
            Route::post('import', [ProductController::class, 'import'])->name('import');
            Route::get('/import/report/{batchId}', [ProductController::class, 'importReport'])->middleware('can:products.create,admin')->name('import.report');
        });

        // Store Routes
        Route::prefix('stores')->name('stores.')->middleware('can:store.read,admin')->group(function () {
            Route::get('/', [StoreController::class, 'index'])->name('index'); // permission: store.read
            Route::get('/data', [StoreController::class, 'getDataForDataTable'])->name('data');
            Route::get('/show', [StoreController::class, 'show'])->name('show')->middleware('can:store.read,admin');
            Route::get('/list', [StoreController::class, 'getAllStores'])->name('list');
            Route::get('/create', [StoreController::class, 'create'])->name('create')->middleware('can:store.create,admin');
            Route::post('/', [StoreController::class, 'store'])->name('store')->middleware('can:store.create,admin');
            Route::get('/edit', [StoreController::class, 'edit'])->name('edit')->middleware('can:store.update,admin');
            Route::put('/update', [StoreController::class, 'update'])->name('update')->middleware('can:store.update,admin');
            Route::delete('/destroy', [StoreController::class, 'destroy'])->name('destroy')->middleware('can:store.delete,admin');
            Route::patch('/toggle-status', [StoreController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:store.update,admin');
        });

        // Warehouse Routes
        Route::prefix('warehouses')->name('warehouses.')->middleware('can:warehouse.read,admin')->group(function () {
            Route::get('/', [WarehouseController::class, 'index'])->name('index'); // permission: warehouse.read
            Route::get('/data', [WarehouseController::class, 'getDataForDataTable'])->name('data');
            Route::get('/show', [WarehouseController::class, 'show'])->name('show')->middleware('can:warehouse.read,admin');
            Route::get('/list', [WarehouseController::class, 'getAllWarehouses'])->name('list');
            Route::get('/create', [WarehouseController::class, 'create'])->name('create')->middleware('can:warehouse.create,admin');
            Route::post('/', [WarehouseController::class, 'store'])->name('store')->middleware('can:warehouse.create,admin');
            Route::get('/edit', [WarehouseController::class, 'edit'])->name('edit')->middleware('can:warehouse.update,admin');
            Route::put('/update', [WarehouseController::class, 'update'])->name('update')->middleware('can:warehouse.update,admin');
            Route::delete('/destroy', [WarehouseController::class, 'destroy'])->name('destroy')->middleware('can:warehouse.delete,admin');
            Route::patch('/toggle-status', [WarehouseController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:warehouse.update,admin');
        });

        // Inventory Routes
        Route::prefix('inventory')->name('inventory.')->middleware('can:inventories.read,admin')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('/data', [InventoryController::class, 'dataTable'])->name('data');
            Route::get('/get-variants', [InventoryController::class, 'getVariants'])->name('get-variants');
            Route::get('/create', [InventoryController::class, 'create'])->name('create')->middleware('can:inventories.create,admin');
            Route::post('/store', [InventoryController::class, 'store'])->name('store')->middleware('can:inventories.create,admin');
            Route::get('/edit', [InventoryController::class, 'edit'])->name('edit')->middleware('can:inventories.update,admin');
            Route::put('/update', [InventoryController::class, 'update'])->name('update')->middleware('can:inventories.update,admin');
            Route::get('/show', [InventoryController::class, 'show'])->name('show')->middleware('can:inventories.read,admin');
            Route::delete('/destroy', [InventoryController::class, 'destroy'])->name('destroy')->middleware('can:inventories.delete,admin');
        });

        // Inventory Transactions Routes
        Route::prefix('inventory-transactions')->name('inventory-transactions.')->middleware('can:inventory-transaction.read,admin')->group(function () {
            Route::get('/', [InventoryTransactionsController::class, 'index'])->name('index');
            Route::get('/data', [InventoryTransactionsController::class, 'getDataTable'])->name('data');
        });

        // Order Routes
        Route::prefix('orders')->name('orders.')->middleware('can:order.read,admin')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index'); // permission: order.read
            Route::get('/data', [OrderController::class, 'dataTable'])->name('data');
            Route::get('/show', [OrderController::class, 'show'])->name('show')->middleware('can:order.read,admin');
            Route::post('/update-order-status', [OrderController::class, 'updateOrderStatus'])->name('update-order-status')->middleware('can:order.update,admin');
            Route::get('/edit', [OrderController::class, 'edit'])->name('edit')->middleware('can:order.update,admin');
            Route::post('/cancel', [OrderController::class, 'cancelOrder'])->name('cancel')->middleware('can:order.update,admin');
                Route::get('/list', [OrderController::class, 'list'])->name('list');
                Route::get('/items', [OrderController::class, 'items'])->name('items'); // query param: ?order_id=
                Route::get('/order-items/inventories', [OrderController::class, 'orderItemInventories'])->name('orderItem.inventories'); // query param: ?order_item_id=
        });

        // Dispatch Routes
        Route::prefix('dispatch')->name('dispatch.')->middleware('can:order.update,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DispatchController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\DispatchController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\Admin\DispatchController::class, 'store'])->name('store');
            Route::get('/edit', [\App\Http\Controllers\Admin\DispatchController::class, 'edit'])->name('edit');
            Route::put('/update', [\App\Http\Controllers\Admin\DispatchController::class, 'update'])->name('update');
            Route::delete('/destroy', [\App\Http\Controllers\Admin\DispatchController::class, 'destroy'])->name('destroy');
            Route::get('/data', [\App\Http\Controllers\Admin\DispatchController::class, 'data'])->name('data');
        });

        // Review Routes
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/pending', [ReviewController::class, 'pending'])->name('pending');
            Route::get('/show', [ReviewController::class, 'show'])->name('show');
            Route::post('/approve', [ReviewController::class, 'approve'])->name('approve');
            Route::post('/reject', [ReviewController::class, 'reject'])->name('reject');
            Route::delete('/destroy', [ReviewController::class, 'destroy'])->name('destroy');
        });

        // Discount Routes
        Route::prefix('discounts')->name('discounts.')->middleware('can:discount.read,admin')->group(function () {

            Route::get('/', [DiscountController::class, 'index'])->name('index');
            Route::get('/datatable', [DiscountController::class, 'datatable'])->name('datatable');
            Route::get('/show', [DiscountController::class, 'show'])->name('show')->middleware('can:discount.read,admin');
            Route::get('/create', [DiscountController::class, 'create'])->name('create')->middleware('can:discount.create,admin');
            Route::post('/', [DiscountController::class, 'store'])->name('store')->middleware('can:discount.create,admin');
            Route::get('/edit', [DiscountController::class, 'edit'])->name('edit')->middleware('can:discount.update,admin');
            Route::put('/update', [DiscountController::class, 'update'])->name('update')->middleware('can:discount.update,admin');
            Route::delete('/destroy', [DiscountController::class, 'destroy'])->name('destroy')->middleware('can:discount.delete,admin');
            Route::patch('/toggle-status', [DiscountController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:discount.update,admin');
            Route::prefix('targets')->name('targets.')->group(function () {
                Route::post('/', [DiscountController::class, 'storeTarget'])->name('store')->middleware('can:discount.create,admin');
                Route::put('/update', [DiscountController::class, 'updateTarget'])->name('update')->middleware('can:discount.update,admin');
                Route::delete('/destroy', [DiscountController::class, 'destroyTarget'])->name('destroy')->middleware('can:discount.delete,admin');
                Route::patch('/toggle-status', [DiscountController::class, 'toggleChildStatus'])->name('toggleStatus')->middleware('can:discount.update,admin');
            });
        });

        // Coupon Routes
        Route::prefix('coupons')->name('coupons.')->middleware('can:coupon.read,admin')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index'); // permission: coupon.read
            Route::get('/data', [CouponController::class, 'data'])->name('data');
            Route::get('/show', [CouponController::class, 'show'])->name('show')->middleware('can:coupon.read,admin');
            Route::get('/create', [CouponController::class, 'create'])->name('create')->middleware('can:coupon.create,admin');
            Route::post('/', [CouponController::class, 'store'])->name('store')->middleware('can:coupon.create,admin');
            Route::get('/edit', [CouponController::class, 'edit'])->name('edit')->middleware('can:coupon.update,admin');
            Route::put('/update', [CouponController::class, 'update'])->name('update')->middleware('can:coupon.update,admin');
            Route::delete('/destroy', [CouponController::class, 'destroy'])->name('destroy')->middleware('can:coupon.delete,admin');
            Route::patch('/update-status', [CouponController::class, 'updateStatus'])->name('updateStatus')->middleware('can:coupon.update,admin');
        });

        Route::prefix('coupon-usage')->name('coupon-usage.')->middleware('can:coupon.read,admin')->group(function () {
            Route::get('/', [CouponUsageController::class, 'index'])->name('index');
            Route::get('/data', [CouponUsageController::class, 'data'])->name('data');
        });

        // Quick Stats Routes
        Route::prefix('quick-stats')->name('quick-stats.')->middleware('can:quick_stats.read,admin')->group(function () {
            Route::get('/', [QuickStatController::class, 'index'])->name('index'); // permission: quick_stats.read
            Route::get('/data', [QuickStatController::class, 'data'])->name('data');
            Route::get('/create', [QuickStatController::class, 'create'])->name('create')->middleware('can:quick_stats.create,admin');
            Route::post('/', [QuickStatController::class, 'store'])->name('store')->middleware('can:quick_stats.create,admin');
            Route::get('/edit', [QuickStatController::class, 'edit'])->name('edit')->middleware('can:quick_stats.update,admin');
            Route::put('/update', [QuickStatController::class, 'update'])->name('update')->middleware('can:quick_stats.update,admin');
            Route::delete('/destroy', [QuickStatController::class, 'destroy'])->name('destroy')->middleware('can:quick_stats.delete,admin');
            Route::patch('/update-status', [QuickStatController::class, 'updateStatus'])->name('updateStatus')->middleware('can:quick_stats.update,admin');
        });

        // Why Shop With Us Routes
        Route::prefix('whyshopwithus')->name('whyshopwithus.')->middleware('can:why_choose_us.read,admin')->group(function () {
            Route::get('/', [WhyShopWithUsController::class, 'index'])->name('index'); // permission: why_choose_us.read
            Route::get('/data', [WhyShopWithUsController::class, 'dataTable'])->name('data');
            Route::get('/create', [WhyShopWithUsController::class, 'create'])->name('create')->middleware('can:why_choose_us.create,admin');
            Route::post('/', [WhyShopWithUsController::class, 'store'])->name('store')->middleware('can:why_choose_us.create,admin');
            Route::get('/edit', [WhyShopWithUsController::class, 'edit'])->name('edit')->middleware('can:why_choose_us.update,admin');
            Route::put('/update', [WhyShopWithUsController::class, 'update'])->name('update')->middleware('can:why_choose_us.update,admin');
            Route::delete('/destroy', [WhyShopWithUsController::class, 'destroy'])->name('destroy')->middleware('can:why_choose_us.delete,admin');
            Route::patch('/toggle-status', [WhyShopWithUsController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:why_choose_us.update,admin');
        });

        // Manage Customers Routes
        Route::prefix('customers')->name('customers.')->middleware('can:manageCustomers.read,admin')->group(function () {
            Route::get('/', [ManageCustomerController::class, 'index'])->name('index'); // permission: manageCustomers.read
            Route::get('/data', [ManageCustomerController::class, 'data'])->name('data');
            Route::get('/show', [ManageCustomerController::class, 'show'])->name('show')->middleware('can:manageCustomers.read,admin');
            Route::get('/edit', [ManageCustomerController::class, 'edit'])->name('edit')->middleware('can:manageCustomers.update,admin');
            Route::put('/update', [ManageCustomerController::class, 'update'])->name('update')->middleware('can:manageCustomers.update,admin');
            Route::delete('/destroy', [ManageCustomerController::class, 'destroy'])->name('destroy')->middleware('can:manageCustomers.delete,admin');
            Route::delete('/batch-destroy', [ManageCustomerController::class, 'batchDestroy'])->name('batchDestroy')->middleware('can:manageCustomers.delete,admin');
            Route::patch('/toggle-status', [ManageCustomerController::class, 'updateCustomerStatus'])->name('toggleStatus')->middleware('can:manageCustomers.update,admin');
            Route::post('/batch-update-status', [ManageCustomerController::class, 'batchUpdateCustomerStatus'])->name('batchUpdateStatus')->middleware('can:manageCustomers.update,admin');
        });

        // Reviews Routes
        Route::prefix('reviews')->name('reviews.')->middleware('can:reviews.read,admin')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index'); // permission: reviews.read
            Route::get('/data', [ReviewController::class, 'data'])->name('data');
            Route::get('/show', [ReviewController::class, 'show'])->name('show')->middleware('can:reviews.read,admin');
            Route::post('/approve', [ReviewController::class, 'approve'])->name('approve')->middleware('can:reviews.update,admin');
            Route::post('/reject', [ReviewController::class, 'reject'])->name('reject')->middleware('can:reviews.update,admin');
            Route::delete('/destroy', [ReviewController::class, 'destroy'])->name('destroy')->middleware('can:reviews.delete,admin');
        });

        // Newsletter Subscribers Routes
        Route::prefix('newsletter')->name('newsletter-subscribers.')->middleware('can:newsletter.read,admin')->group(function () {
            Route::get('/', [NewsletterController::class, 'index'])->name('index');
            Route::get('/data', [NewsletterController::class, 'data'])->name('data');
            Route::patch('/toggle-status', [NewsletterController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:newsletter.update,admin'); // permission: newsletter.update
            Route::delete('/destroy', [NewsletterController::class, 'destroy'])->name('destroy')->middleware('can:newsletter.delete,admin');
        });

        // Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/stock', [StockReportController::class, 'index'])->name('stock');
        });

        // Site Settings Routes
        Route::prefix('site-settings')->name('site-settings.')->middleware('can:site_status.read,admin')->group(function () {
            Route::get('/', [SiteSettingController::class, 'index'])->name('index'); // permission: site_status.read
            Route::post('/', [SiteSettingController::class, 'store'])->name('store')->middleware('can:site_status.create,admin');
            Route::put('/', [SiteSettingController::class, 'update'])->name('update')->middleware('can:site_status.update,admin');
            Route::post('/toggle-status', [SiteSettingController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:site_status.update,admin');
            Route::get('/status', [SiteSettingController::class, 'getStatus'])->name('status');
        });
    });
});
