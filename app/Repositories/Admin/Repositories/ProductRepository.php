<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts()
    {
        return Product::all();
    }

    public function getAllActiveProducts()
    {
        return Product::where('status', 'active')->get();
    }

    public function getProductVariants($productId)
    {
        return ProductVariant::where('product_id', $productId)->where('status', 'active')->get();
    }

    public function getProductVarientCount($productId)
    {
        $count = ProductVariant::where('product_id', $productId)->count();
        dd($count);
    }

    public function getProductById($id)
    {
        return Product::with(['productVariants.images', 'productVariants.inventory'])->findOrFail($id);
    }

    public function getDataforDataTable($request = null)
    {
        $query = Product::with([
            'brand',
            'category',
            'productVariants' => function ($q) {
                $q->withCount('orderItems');
            }
        ])
            ->withCount([
                'productVariantsByProductId as productVariants_count' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->addSelect([
                'products.id',
                'products.name',
                'products.status',
                'products.is_bestseller',
                'products.cover_image',
                'products.brand_id',
                'products.category_id',
                'products.sku',
            ])
            ->selectRaw('COALESCE((
            SELECT MIN(pv.price)
            FROM product_variants pv
            WHERE pv.product_id = products.id
            AND pv.status = "active"
            AND pv.deleted_at IS NULL
        ), 0) as min_price')
            ->latest();

        $results = $query->get();
        return $results;
    }


    public function createProduct($request, $data = null)
    {
        try {
            $data = $data ?? $request->validated();
            $data['status'] = $data['status'] ?? 'inactive';
            $data['is_bestseller'] = $request->has('is_bestseller') ? 1 : 0;
            $data['price'] = 0;
            $this->validateTranslatableFields($data);
            $this->normalizeForeignKeys($data);
            if (empty($data['sku'])) {
                $data['sku'] = 'TEMP-' . uniqid();
            }

            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            } elseif (isset($data['cover_image']) && is_string($data['cover_image'])) {
                $data['cover_image'] = $this->handleImportedFile($data['cover_image'], 'products/covers');
            }

            $galleryPaths = [];
            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $galleryPaths[] = $file->store('products/gallery', 'public');
                }
            }

            $product = Product::create($data);
            $this->generateAndSaveSku($product, $data);

            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('variants')) {
                $variantFiles = $request->file('variants');
                if (!empty($variantFiles) && is_array($variantFiles)) {
                    foreach ($variantFiles as $idx => $filesForVariant) {
                        if (is_array($filesForVariant) && isset($filesForVariant['images'])) {
                            if (!isset($data['variants'][$idx])) $data['variants'][$idx] = [];
                            $data['variants'][$idx]['images'] = $filesForVariant['images'];
                        }
                    }
                }
            }
            if (!empty($data['variants']) && is_array($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            if (!empty($galleryPaths)) {
                $preferredVariantId = null;
                if ($request instanceof \Illuminate\Http\Request) {
                    $pref = $request->input('gallery_images_variant_id');
                    if (!empty($pref) && is_numeric($pref)) {
                        $preferredVariantId = (int)$pref;
                        if (!ProductVariant::where('id', $preferredVariantId)->where('product_id', $product->id)->exists()) {
                            $preferredVariantId = null;
                        }
                    }
                }

                if (is_null($preferredVariantId)) {
                    $firstVariant = $product->productVariants()->first();
                    $preferredVariantId = $firstVariant ? $firstVariant->id : null;
                }

                foreach ($galleryPaths as $p) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $preferredVariantId,
                        'image_path' => $p,
                        'sort_order' => 0,
                    ]);
                }
            }

            if ($product->productVariants()->count() === 0) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => ['en' => 'Default'],
                    'sku' => $product->sku . '-V-DEF',
                    'price' => $data['price'] ?? 0,
                    'attributes' => ['en' => []],
                    'status' => 'active',
                ]);
            }
            return $product;
        } catch (\Exception $e) {
            $summary = [
                'name_en' => $data['name']['en'] ?? null,
                'brand_id' => $data['brand_id'] ?? ($data['brand'] ?? null),
                'category_id' => $data['category_id'] ?? ($data['category'] ?? null),
                'variants' => isset($data['variants']) ? count($data['variants']) : 0,
            ];
            Log::error('ProductRepository.createProduct FAILED', array_merge(['exception' => $e->getMessage()], $summary));
            throw new \Exception('Failed to create product');
        }
    }

    public function updateProduct($id, $request, $data = null)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $data ?? $request->validated();
            unset($data['sku'], $data['id']);
            $this->normalizeForeignKeys($data);

            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                    Storage::disk('public')->delete($product->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            } elseif (isset($data['cover_image']) && is_string($data['cover_image'])) {
                $data['cover_image'] = $this->handleImportedFile($data['cover_image'], 'products/covers');
            }

            if ($request instanceof \Illuminate\Http\Request) {
                $existingImages = ProductImage::forProduct($product->id)->pluck('image_path')->toArray();
                $newImages = [];

                if ($request->hasFile('gallery_images')) {
                    foreach ($request->file('gallery_images') as $file) {
                        $newImages[] = $file->store('products/gallery', 'public');
                    }
                }

                $imagesToKeep = $request->input('gallery_images_existing', []);
                $imagesToDelete = array_diff($existingImages, $imagesToKeep);

                if (!empty($imagesToDelete)) {
                    $images = ProductImage::where('product_id', $product->id)->whereIn('image_path', $imagesToDelete)->get();
                    foreach ($images as $oldImg) {
                        if ($oldImg->image_path && Storage::disk('public')->exists($oldImg->image_path)) {
                            Storage::disk('public')->delete($oldImg->image_path);
                        }
                        $oldImg->delete();
                    }
                }

                $preferredVariantId = null;
                if ($request instanceof \Illuminate\Http\Request) {
                    $pref = $request->input('gallery_images_variant_id');
                    if (!empty($pref) && is_numeric($pref)) {
                        $preferredVariantId = (int)$pref;
                        if (!ProductVariant::where('id', $preferredVariantId)->where('product_id', $product->id)->exists()) {
                            $preferredVariantId = null;
                        }
                    }
                }
                if (is_null($preferredVariantId)) {
                    $firstVariant = $product->productVariants()->first();
                    $preferredVariantId = $firstVariant ? $firstVariant->id : null;
                }

                foreach ($newImages as $p) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $preferredVariantId,
                        'image_path' => $p,
                        'sort_order' => 0,
                    ]);
                }
            }

            $product->update($data);

            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('variants')) {
                $variantFiles = $request->file('variants');
                if (!empty($variantFiles) && is_array($variantFiles)) {
                    foreach ($variantFiles as $idx => $filesForVariant) {
                        if (is_array($filesForVariant) && isset($filesForVariant['images'])) {
                            if (!isset($data['variants'][$idx])) $data['variants'][$idx] = [];
                            $data['variants'][$idx]['images'] = $filesForVariant['images'];
                        }
                    }
                }
            }
            if (isset($data['variants']) && is_array($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            if ($product->productVariants()->count() === 0) {
                $defaultVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => ['en' => 'Default'],
                    'sku' => $product->sku . '-V-DEF',
                    'price' => $data['price'] ?? 0,
                    'attributes' => ['en' => []],
                    'status' => 'active',
                ]);
                $this->syncVariants($product, [$defaultVariant->id => []]);
            }
            return $product;
        } catch (\Exception $e) {
            $summary = [
                'product_id' => $id,
                'name_en' => $data['name']['en'] ?? null,
                'variants' => isset($data['variants']) ? count($data['variants']) : 0,
            ];
            Log::error('ProductRepository.updateProduct FAILED', array_merge(['exception' => $e->getMessage()], $summary));
            throw new \Exception('Failed to update product');
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);

            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                Storage::disk('public')->delete($product->cover_image);
            }

            $product->productVariants()->delete();

            $product->delete();
        } catch (\Exception $e) {
            Log::error('ProductRepository.deleteProduct: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';

        Log::info("Updating product ID {$id} status to {$newStatus}");

        $product->update(['status' => $newStatus]);

        $product->productVariants()->update(['status' => $newStatus]);

        return $product;
    }

    public function updateProductBestseller($id)
    {
        $product = Product::findOrFail($id);
        $newState = !$product->is_bestseller;

        Log::info("Updating product ID {$id} bestseller status to " . ($newState ? 'true' : 'false'));

        $product->update(['is_bestseller' => $newState]);

        $product->productVariants()->update(['is_bestseller' => $newState]);

        return $product;
    }

    public function updateProductVariantStatus($variantId)
    {
        $variant = ProductVariant::findOrFail($variantId);
        $newStatus = $variant->status === 'active' ? 'inactive' : 'active';

        $variant->update(['status' => $newStatus]);

        Log::info("Updated variant ID {$variantId} status to {$newStatus}");
        $product = $variant->product;

        $hasActiveVariants = $product->productVariants()->where('status', 'active')->exists();
        $product->update(['status' => $hasActiveVariants ? 'active' : 'inactive']);

        return $variant;
    }

    public function syncProductStatus($productId)
    {
        $product = Product::findOrFail($productId);
        $hasActive = $product->productVariants()->where('status', 'active')->exists();
        $product->status = $hasActive ? 'active' : 'inactive';
        Log::info("Syncing product ID {$productId} status to " . ($hasActive ? 'active' : 'inactive'));
        $product->save();
        return $product;
    }

    public function updateProductVariantBestseller($variantId)
    {
        $variant = ProductVariant::findOrFail($variantId);
        $newState = !$variant->is_bestseller;

        $variant->update(['is_bestseller' => $newState]);

        $product = $variant->product;

        $hasBestsellerVariant = $product->productVariants()->where('is_bestseller', true)->exists();
        $product->update(['is_bestseller' => $hasBestsellerVariant]);

        return $variant;
    }

    public function syncProductBestseller($productId)
    {
        $product = Product::findOrFail($productId);
        $hasBestseller = $product->productVariants()->where('is_bestseller', 1)->exists();
        $product->is_bestseller = $hasBestseller ? 1 : 0;
        Log::info("Syncing product ID {$productId} bestseller status to " . ($hasBestseller ? 'true' : 'false'));
        $product->save();
        return $product;
    }

    private function syncVariants(Product $product, array $variantsData)
    {
        $existingVariants = $product->productVariants()->get();
        $existingIds = $existingVariants->pluck('id')->toArray();
        $processedIds = [];
        $variantCounter = 1;

        foreach ($variantsData as $key => $data) {
            if (empty($data) || (!isset($data['variant_name']) && !isset($data['name']))) {
                continue;
            }
            $data = $this->normalizeVariantData($data, is_numeric($key) ? (int)$key : 0);

            if (isset($data['attributes']) && is_array($data['attributes'])) {
                foreach (['en', 'fr', 'ar'] as $ln) {
                    if (isset($data['attributes'][$ln]) && is_string($data['attributes'][$ln])) {
                        $decoded = json_decode($data['attributes'][$ln], true);
                        if (is_array($decoded)) {
                            $data['attributes'][$ln] = $decoded;
                        } else {
                            $data['attributes'][$ln] = [['key' => 'value', 'value' => $data['attributes'][$ln]]];
                        }
                    }
                }
            }

            if (isset($data['id']) && in_array($data['id'], $existingIds)) {
                $variant = ProductVariant::find($data['id']);
                if ($variant) {
                    $logPayload = [
                        'action' => 'update_variant',
                        'variant_id' => $variant->id,
                        'variant_name_en' => $data['variant_name']['en'] ?? ($data['name']['en'] ?? null),
                        'attributes_counts' => [
                            'en' => isset($data['attributes']['en']) ? count($data['attributes']['en']) : 0,
                            'fr' => isset($data['attributes']['fr']) ? count($data['attributes']['fr']) : 0,
                            'ar' => isset($data['attributes']['ar']) ? count($data['attributes']['ar']) : 0,
                        ],
                        'attributes_sample' => [
                            'en' => $this->extractSampleAttribute($data['attributes']['en'] ?? []),
                            'fr' => $this->extractSampleAttribute($data['attributes']['fr'] ?? []),
                            'ar' => $this->extractSampleAttribute($data['attributes']['ar'] ?? []),
                        ],
                    ];
                    Log::info('ProductRepository.syncVariants variant payload before update', $logPayload);

                    $variant->update([
                        'variant_name'  => $data['variant_name'],
                        'price'         => $data['price'],
                        'attributes'    => $data['attributes'] ?? null,
                        'status'        => $data['status'] ?? 'active',
                        'is_bestseller' => (bool)($data['is_bestseller'] ?? $variant->is_bestseller ?? false),
                    ]);

                    $processedIds[] = $variant->id;

                    if (!empty($data['images'])) {
                        $this->processVariantImages($product, $variant, $data['images']);
                    }
                }
            } else {
                if (empty($data['sku'])) {
                    $suffix = strtoupper(Str::random(4));
                    $data['sku'] = $product->sku . '-V-' . $suffix . '-' . $variantCounter;
                }

                if (isset($data['attributes']['en']) && (empty($data['attributes']['fr']) || empty($data['attributes']['ar']))) {
                    try {
                        $translator = app(\App\Services\AutoTranslationService::class);
                        $mini = ['variants' => [$data]];
                        $miniTranslated = $translator->prepareProductData($mini);
                        if (!empty($miniTranslated['variants'][0]['attributes'])) {
                            $data['attributes'] = $miniTranslated['variants'][0]['attributes'];
                        }
                    } catch (\Exception $e) {
                        Log::warning('Auto-translate fallback failed for variant attributes: ' . $e->getMessage());
                    }
                }

                $logPayload = [
                    'action' => 'create_variant',
                    'product_id' => $product->id,
                    'variant_name_en' => $data['variant_name']['en'] ?? ($data['name']['en'] ?? null),
                    'attributes_counts' => [
                        'en' => isset($data['attributes']['en']) ? count($data['attributes']['en']) : 0,
                        'fr' => isset($data['attributes']['fr']) ? count($data['attributes']['fr']) : 0,
                        'ar' => isset($data['attributes']['ar']) ? count($data['attributes']['ar']) : 0,
                    ],
                    'attributes_sample' => [
                        'en' => $this->extractSampleAttribute($data['attributes']['en'] ?? []),
                        'fr' => $this->extractSampleAttribute($data['attributes']['fr'] ?? []),
                        'ar' => $this->extractSampleAttribute($data['attributes']['ar'] ?? []),
                    ],
                ];
                Log::info('ProductRepository.syncVariants variant payload before create', $logPayload);

                $variant = ProductVariant::create([
                    'product_id'     => $product->id,
                    'variant_name'   => $data['variant_name'],
                    'sku'            => $data['sku'],
                    'price'          => $data['price'],
                    'attributes'     => $data['attributes'] ?? null,
                    'status'         => $data['status'] ?? 'active',
                    'is_bestseller'  => (bool)($data['is_bestseller'] ?? false),
                ]);
                $processedIds[] = $variant->id;
                if (!empty($data['images'])) {
                    $this->processVariantImages($product, $variant, $data['images']);
                }
                $variantCounter++;
            }
        }

        $idsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($idsToDelete)) {
            foreach ($existingVariants as $variant) {
                if (in_array($variant->id, $idsToDelete)) {
                    foreach ($variant->images as $img) {
                        if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                            Storage::disk('public')->delete($img->image_path);
                        }
                        $img->delete();
                    }

                    foreach ($variant->inventory as $inv) {
                        $inv->delete();
                    }
                    $variant->delete();
                }
            }
        }
    }

    private function processVariantImages(Product $product, ProductVariant $variant, $images)
    {
        if (!is_array($images)) {
            $images = [$images];
        }

        foreach ($images as $imageInput) {
            $path = null;

            if ($imageInput instanceof UploadedFile) {
                $path = $imageInput->store('products/variants', 'public');
            } elseif (is_string($imageInput) && !empty($imageInput)) {
                $path = $this->handleImportedFile($imageInput, 'products/variants');
            }

            if ($path) {
                ProductImage::create([
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant->id,
                    'image_path'         => $path,
                    'sort_order'         => 0,
                ]);
            }
        }
    }

    private function extractSampleAttribute(array $attrs): ?string
    {
        if (empty($attrs)) return null;
        $first = $attrs[0] ?? null;
        if (!$first) return null;
        $val = $first['value'] ?? (is_string($first) ? $first : null);
        if (is_array($val)) $val = json_encode($val, JSON_UNESCAPED_UNICODE);
        if (!is_string($val)) $val = (string)$val;
        $val = trim($val);
        if (mb_strlen($val) > 60) {
            return mb_substr($val, 0, 57) . '...';
        }
        return $val;
    }

    private function handleImportedFile($sourcePath, $targetDir)
    {
        if (Storage::disk('public')->exists($sourcePath)) {
            return $sourcePath;
        }

        if (file_exists($sourcePath)) {
            $realSource = realpath($sourcePath);
            $allowedBase = realpath(storage_path('app/temp_imports')) ?: null;
            if ($allowedBase && $realSource && strpos($realSource, $allowedBase) === 0) {
                $filename = basename($realSource);
                $targetPath = $targetDir . '/' . Str::random(5) . '_' . $filename;
                Storage::disk('public')->put($targetPath, file_get_contents($realSource));
                return $targetPath;
            }
            Log::warning('handleImportedFile: attempted import from disallowed path', ['source' => $sourcePath]);
            return null;
        }

        return null;
    }

    private function normalizeVariantData(array $data, int $index = 0): array
    {
        if (!isset($data['variant_name'])) {
            if (isset($data['name'])) {
                $data['variant_name'] = $data['name'];
            } else {
                $data['variant_name'] = ['en' => 'Variant ' . ($index + 1)];
            }
        }

        if (!is_array($data['variant_name'])) {
            $data['variant_name'] = ['en' => $data['variant_name']];
        }

        foreach (['en', 'fr', 'ar'] as $lang) {
            if (!isset($data['variant_name'][$lang])) {
                $data['variant_name'][$lang] = '';
            }
        }

        if (isset($data['attributes'])) {
            $cleanAttributes = [];
            foreach (['en', 'fr', 'ar'] as $lang) {
                if (isset($data['attributes'][$lang])) {
                    $attrs = $data['attributes'][$lang];
                    if (is_string($attrs)) {
                        $decoded = json_decode($attrs, true);
                        if (is_array($decoded)) {
                            $attrs = $decoded;
                        } else {
                            $attrs = [$attrs];
                        }
                    }

                    $normalized = [];
                    foreach ($attrs as $k => $item) {
                        if (is_array($item) && (isset($item['key']) || isset($item['value']))) {
                            $normalized[] = $item;
                        } elseif (is_string($item)) {
                            $decoded = json_decode($item, true);
                            if (is_array($decoded) && (isset($decoded['key']) || isset($decoded['value']))) {
                                $normalized[] = $decoded;
                            } else {
                                $normalized[] = ['key' => (string)$k, 'value' => $item];
                            }
                        } else {
                            $normalized[] = ['key' => (string)$k, 'value' => (string)$item];
                        }
                    }
                    $cleanAttributes[$lang] = array_values($normalized);
                }
            }

            foreach (['en', 'fr', 'ar'] as $lang) {
                if (!isset($cleanAttributes[$lang])) {
                    $cleanAttributes[$lang] = [];
                }
            }
            $data['attributes'] = $cleanAttributes;
        }

        return $data;
    }

    private function ensureTranslatableKeys(array &$field, $default = ''): void
    {
        if (!is_array($field)) {
            $value = $field;
            $field = ['en' => $value];
        }
        foreach (['en', 'fr', 'ar'] as $lang) {
            if (!array_key_exists($lang, $field)) {
                $field[$lang] = $default;
            }
        }
    }

    private function generateAndSaveSku(Product $product, array $data)
    {
        $namePart = $product->getTranslation('name', 'en');
        $cleanName = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namePart), 0, 3));
        $paddedId = str_pad($product->id, 4, '0', STR_PAD_LEFT);
        $finalSku = "EZ-{$cleanName}-{$paddedId}";

        if (str_starts_with($product->sku, 'TEMP-')) {
            $product->update(['sku' => $finalSku]);
        }
    }

    private function normalizeForeignKeys(array &$data)
    {
        if (isset($data['brand']) && !isset($data['brand_id'])) $data['brand_id'] = $data['brand'];
        if (isset($data['category']) && !isset($data['category_id'])) $data['category_id'] = $data['category'];
        if (isset($data['sub_category']) && !isset($data['sub_category_id'])) $data['sub_category_id'] = $data['sub_category'];
    }

    private function validateTranslatableFields(array &$data): void
    {
        if (!isset($data['name']['en']) || empty(trim($data['name']['en']))) {
            throw new \Exception('Product name in English is required');
        }

        $fields = ['description', 'warranty', 'dimensions', 'specifications'];
        foreach ($fields as $field) {
            if (!isset($data[$field])) $data[$field] = [];
            $this->ensureTranslatableKeys($data[$field], "");
        }

        if (isset($data['name'])) {
            $this->ensureTranslatableKeys($data['name'], '');
        }
    }
}
