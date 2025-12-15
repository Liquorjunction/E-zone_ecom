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
    // --- Interface Implementation Methods ---

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

    public function getProductById($id)
    {
        // Eager load inventory and images for editing
        return Product::with(['productVariants.images', 'productVariants.inventory'])->findOrFail($id);
    }

    public function getDataforDataTable($request = null)
    {
        return Product::with(['brand', 'category'])
            ->select('id', 'name', 'status', 'is_bestseller', 'cover_image', 'brand_id', 'category_id', 'sku')
            ->selectRaw('COALESCE((
                SELECT MIN(price)
                FROM product_variants
                WHERE product_variants.product_id = products.id
                AND product_variants.status = "active"
            ), 0) as min_price')
            ->latest()
            ->get();
    }

    // --- Core CRUD Operations ---

    public function createProduct($request, $data = null)
    {
        try {
            // 1. Prepare Data
            $data = $data ?? $request->validated();
            $data['status'] = $data['status'] ?? 'inactive';
            $data['is_bestseller'] = $request->has('is_bestseller') ? 1 : 0;
            $data['price'] = 0;
            $this->validateTranslatableFields($data);
            $this->normalizeForeignKeys($data);
            if (empty($data['sku'])) {
                $data['sku'] = 'TEMP-' . uniqid();
            }
            // Handle Cover Image
            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            } elseif (isset($data['cover_image']) && is_string($data['cover_image'])) {
                $data['cover_image'] = $this->handleImportedFile($data['cover_image'], 'products/covers');
            }
            // Handle Gallery Images uploads (collect paths; will persist to product_images table after creating product)
            $galleryPaths = [];
            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $galleryPaths[] = $file->store('products/gallery', 'public');
                }
            }

            // 2. Create Product
            $product = Product::create($data);
            $this->generateAndSaveSku($product, $data);
            // 3. Handle Variants
            // 3. Handle Variants
            // Map uploaded files for variants into the $data['variants'] array so syncVariants can process them
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
            // 4. Persist gallery images into product_images table (main product images)
            if (!empty($galleryPaths)) {
                // Prefer explicit variant id from request/data if provided
                $preferredVariantId = null;
                if ($request instanceof \Illuminate\Http\Request) {
                    $pref = $request->input('gallery_images_variant_id');
                    if (!empty($pref) && is_numeric($pref)) {
                        $preferredVariantId = (int)$pref;
                        // verify belongs to this product
                        if (!ProductVariant::where('id', $preferredVariantId)->where('product_id', $product->id)->exists()) {
                            $preferredVariantId = null;
                        }
                    }
                }

                // If no explicit variant requested, attach to first variant if any
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
            // 5. Ensure at least one sellable variant exists
            if ($product->productVariants()->count() === 0) {
                $defaultVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => ['en' => 'Default'],
                    'sku' => $product->sku . '-V-DEF',
                    'price' => $data['price'] ?? 0,
                    'attributes' => ['en' => []],
                    'status' => 'active',
                ]);
                Inventory::create([
                    'product_variant_id' => $defaultVariant->id,
                    'quantity' => 1,
                    'status' => 'active'
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
            // Update Cover Image (Delete old if new uploaded)
            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                // Delete old cover image
                if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                    Storage::disk('public')->delete($product->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            } elseif (isset($data['cover_image']) && is_string($data['cover_image'])) {
                $data['cover_image'] = $this->handleImportedFile($data['cover_image'], 'products/covers');
            }

            // Handle Gallery Images: add new, keep existing, remove deleted
            if ($request instanceof \Illuminate\Http\Request) {
                // Use ProductImage DB entries for gallery images rather than a non-existent product->gallery_images property
                $existingImages = ProductImage::forProduct($product->id)->pluck('image_path')->toArray();
                $newImages = [];

                // Handle new uploads
                if ($request->hasFile('gallery_images')) {
                    // Optionally remove previous product-level images if client expects replacement
                    foreach ($request->file('gallery_images') as $file) {
                        $newImages[] = $file->store('products/gallery', 'public');
                    }
                }

                // The request should send `gallery_images_existing` paths to keep
                $imagesToKeep = $request->input('gallery_images_existing', []);
                $imagesToDelete = array_diff($existingImages, $imagesToKeep);

                // Delete removed images from storage and DB
                if (!empty($imagesToDelete)) {
                    $images = ProductImage::where('product_id', $product->id)->whereIn('image_path', $imagesToDelete)->get();
                    foreach ($images as $oldImg) {
                        if ($oldImg->image_path && Storage::disk('public')->exists($oldImg->image_path)) {
                            Storage::disk('public')->delete($oldImg->image_path);
                        }
                        $oldImg->delete();
                    }
                }

                // Persist newly uploaded images — attach to requested variant or default to first variant
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
            // Sync Variants
            // Map uploaded files for variants into the $data['variants'] array so syncVariants can process them
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
            // Ensure at least one sellable variant exists after update
            if ($product->productVariants()->count() === 0) {
                $defaultVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => ['en' => 'Default'],
                    'sku' => $product->sku . '-V-DEF',
                    'price' => $data['price'] ?? 0,
                    'attributes' => ['en' => []],
                    'status' => 'active',
                ]);
                Inventory::create([
                    'product_variant_id' => $defaultVariant->id,
                    'quantity' => 1,
                    'status' => 'active'
                ]);
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

            // Delete all associated images (both product images and variant images)
            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            // Delete cover image
            if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                Storage::disk('public')->delete($product->cover_image);
            }

            // Delete variants and their inventories (cascade delete will handle this)
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
        $product->status = $product->status == 'active' ? 'inactive' : 'active';
        $product->save();
        return $product;
    }

    public function updateProductBestseller($id)
    {
        $product = Product::findOrFail($id);
        $product->is_bestseller = !$product->is_bestseller;
        $product->save();
        return $product;
    }

    // --- Variant Logic ---

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

            // Ensure attributes locales are arrays (decode JSON strings submitted from frontend)
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                foreach (['en','fr','ar'] as $ln) {
                    if (isset($data['attributes'][$ln]) && is_string($data['attributes'][$ln])) {
                        $decoded = json_decode($data['attributes'][$ln], true);
                        if (is_array($decoded)) {
                            $data['attributes'][$ln] = $decoded;
                        } else {
                            // keep as single item array
                            $data['attributes'][$ln] = [['key' => 'value', 'value' => $data['attributes'][$ln]]];
                        }
                    }
                }
            }

            // Update
            if (isset($data['id']) && in_array($data['id'], $existingIds)) {
                $variant = ProductVariant::find($data['id']);
                    if ($variant) {
                        // Log sanitized variant payload before update (no sensitive/full dumps)
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
                            'variant_name' => $data['variant_name'],
                            'price'        => $data['price'],
                            'attributes'   => $data['attributes'] ?? null,
                            'status'       => $data['status'] ?? 'active',
                        ]);
                    // Inventory
                    if (isset($data['quantity'])) {
                        $inventory = $variant->inventory()->first();
                        if ($inventory) {
                            $inventory->update(['quantity' => $data['quantity']]);
                        } else {
                            Inventory::create([
                                'product_variant_id' => $variant->id,
                                'quantity' => $data['quantity'],
                                'status' => 'active'
                            ]);
                        }
                    }
                    $processedIds[] = $variant->id;
                    // Images
                    if (!empty($data['images'])) {
                        $this->processVariantImages($product, $variant, $data['images']);
                    }
                }
            } else {
                // Create
                if (empty($data['sku'])) {
                    $suffix = strtoupper(Str::random(4));
                    $data['sku'] = $product->sku . '-V-' . $suffix . '-' . $variantCounter;
                }
                // If target locales for attributes are missing, attempt a quick server-side translation
                if (isset($data['attributes']['en']) && (empty($data['attributes']['fr']) || empty($data['attributes']['ar']))) {
                    try {
                        $translator = app(\App\Services\AutoTranslationService::class);
                        // Prepare minimal payload shape expected by the service
                        $mini = ['variants' => [$data]];
                        $miniTranslated = $translator->prepareProductData($mini);
                        if (!empty($miniTranslated['variants'][0]['attributes'])) {
                            $data['attributes'] = $miniTranslated['variants'][0]['attributes'];
                        }
                    } catch (\Exception $e) {
                        Log::warning('Auto-translate fallback failed for variant attributes: ' . $e->getMessage());
                    }
                }

                // Log sanitized variant payload before create
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
                    'product_id'   => $product->id,
                    'variant_name' => $data['variant_name'],
                    'sku'          => $data['sku'],
                    'price'        => $data['price'],
                    'attributes'   => $data['attributes'] ?? null,
                    'status'       => $data['status'] ?? 'active',
                ]);
                Inventory::create([
                    'product_variant_id' => $variant->id,
                    'quantity'           => $data['quantity'] ?? 0,
                    'status'             => 'active',
                ]);
                $processedIds[] = $variant->id;
                if (!empty($data['images'])) {
                    $this->processVariantImages($product, $variant, $data['images']);
                }
                $variantCounter++;
            }
        }
        // Delete removed variants
        $idsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($idsToDelete)) {
            foreach ($existingVariants as $variant) {
                if (in_array($variant->id, $idsToDelete)) {
                    // Delete images
                    foreach ($variant->images as $img) {
                        if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                            Storage::disk('public')->delete($img->image_path);
                        }
                        $img->delete();
                    }
                    // Delete inventory
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

    /**
     * Extract a short sample string from attributes array for safe logging.
     */
    private function extractSampleAttribute(array $attrs): ?string
    {
        if (empty($attrs)) return null;
        $first = $attrs[0] ?? null;
        if (!$first) return null;
        $val = $first['value'] ?? (is_string($first) ? $first : null);
        if (is_array($val)) $val = json_encode($val, JSON_UNESCAPED_UNICODE);
        if (!is_string($val)) $val = (string)$val;
        // Trim and shorten
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

        // Only allow copying from a controlled import temporary directory to avoid arbitrary file reads.
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
        // Ensure all language keys exist for variant_name
        foreach (['en', 'fr', 'ar'] as $lang) {
            if (!isset($data['variant_name'][$lang])) {
                $data['variant_name'][$lang] = '';
            }
        }

        if (isset($data['attributes'])) {
            $cleanAttributes = [];
            // Handle translatable attributes structure
            foreach (['en', 'fr', 'ar'] as $lang) {
                if (isset($data['attributes'][$lang])) {
                    $attrs = $data['attributes'][$lang];
                    if (is_string($attrs)) {
                        // Decode JSON string from form
                        $decoded = json_decode($attrs, true);
                        if (is_array($decoded)) {
                            $attrs = $decoded;
                        } else {
                            // Maybe it's a single serialized entry
                            $attrs = [$attrs];
                        }
                    }
                    // Normalize to array of ['key'=>..., 'value'=>...]
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
            // Ensure attributes have keys for each language (empty arrays if missing)
            foreach (['en', 'fr', 'ar'] as $lang) {
                if (!isset($cleanAttributes[$lang])) {
                    $cleanAttributes[$lang] = [];
                }
            }
            $data['attributes'] = $cleanAttributes;
        }

        return $data;
    }

    /**
     * Ensure translatable fields have entries for each supported language.
     * Leaves existing values intact but adds missing keys with default value.
     */
    private function ensureTranslatableKeys(array &$field, $default = ''): void
    {
        if (!is_array($field)) {
            // If it's a string, assume English content
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
            // Ensure each translatable field has en, fr, ar keys
            $this->ensureTranslatableKeys($data[$field], "");
        }
        // Ensure name has all language keys as well
        if (isset($data['name'])) {
            $this->ensureTranslatableKeys($data['name'], '');
        }
    }
}
