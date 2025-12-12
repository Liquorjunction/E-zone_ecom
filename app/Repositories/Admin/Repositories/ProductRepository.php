<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Repositories\Admin\Interfaces\ProductRepositoryInterface;
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

    public function createProduct($request, $data = null)
    {
        try {
            // 1. Prepare Data
            $data = $data ?? $request->validated();
            $data['status'] = $data['status'] ?? 'inactive';

            // Validate translatable fields before saving
            $this->validateTranslatableFields($data);

            // Normalize Dropdowns
            $this->normalizeForeignKeys($data);
            if (empty($data['sku'])) {
                $data['sku'] = 'TEMP-'.uniqid();
            }

            // Handle Cover Image
            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('products', 'public');
            }

            // 2. Create Product
            $product = Product::create($data);            // 3. Generate Real SKU (EZ-XXX-0000)
            $this->generateAndSaveSku($product, $data);

            // 4. Handle Gallery Images (Only if request has files)
            if ($request instanceof \Illuminate\Http\Request) {
                $this->handleGalleryImages($product, $request);
            }

            // 5. Handle Variants
            $variants = $data['variants'] ?? ($request instanceof \Illuminate\Http\Request ? $request->input('variants', []) : []);
            $this->handleVariants($product, $variants);

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.createProduct FAILED: '.$e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    public function updateProduct($id, $request, $data = null)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $data ?? $request->validated();

            // Security: Don't allow changing SKU or ID
            unset($data['sku'], $data['id']);

            $this->normalizeForeignKeys($data);

            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                    Storage::disk('public')->delete($product->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            }

            $product->update($data);

            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('image_path')) {
                foreach ($request->file('image_path') as $image) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $image->store('product_images', 'public'),
                        'sort_order' => 0,
                    ]);
                }
            }

            if (isset($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            return $product;

        } catch (\Exception $e) {
            Log::error('ProductRepository.updateProduct: '.$e->getMessage());
            throw $e;
        }
    }

    private function syncVariants(Product $product, array $variantsData)
    {
        $existingVariantIds = $product->productVariants()->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($variantsData as $variant) {
            $variant = $this->normalizeVariantTranslatable($variant);

            if (! empty($variant['id']) && in_array($variant['id'], $existingVariantIds)) {
                $submittedIds[] = $variant['id'];
                ProductVariant::where('id', $variant['id'])->update([
                    'variant_name' => $variant['variant_name'],
                    'price' => $variant['price'],
                    'attributes' => $variant['attributes'] ?? null,
                    'status' => $variant['status'] ?? 'active',
                ]);
            } else {
                // Auto-generate variant SKU derived from product SKU
                $suffix = strtoupper(Str::random(4));
                $newSku = $product->sku . '-V-' . $suffix;

                $newVar = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variant['variant_name'],
                    'sku' => $newSku,
                    'price' => $variant['price'],
                    'attributes' => $variant['attributes'] ?? null,
                    'status' => $variant['status'] ?? 'active',
                ]);
                $submittedIds[] = $newVar->id;
            }
        }

        $idsToDelete = array_diff($existingVariantIds, $submittedIds);
        if (! empty($idsToDelete)) {
            ProductVariant::whereIn('id', $idsToDelete)->delete();
        }
    }

    private function normalizeForeignKeys(array &$data)
    {
        if (isset($data['brand']) && ! isset($data['brand_id'])) {
            $data['brand_id'] = $data['brand'];
        }
        if (isset($data['category']) && ! isset($data['category_id'])) {
            $data['category_id'] = $data['category'];
        }
        if (isset($data['sub_category']) && ! isset($data['sub_category_id'])) {
            $data['sub_category_id'] = $data['sub_category'];
        }
    }

    private function generateAndSaveSku(Product $product, array $data)
    {
        $namePart = is_array($data['name']) ? ($data['name']['en'] ?? reset($data['name'])) : $data['name'];
        $cleanName = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namePart), 0, 3));
        $paddedId = str_pad($product->id, 4, '0', STR_PAD_LEFT);

        $finalSku = "EZ-{$cleanName}-{$paddedId}";
        $product->update(['sku' => $finalSku]);
    }

    private function handleGalleryImages(Product $product, $request)
    {
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $image->store('product_images', 'public'),
                    'sort_order' => 0,
                ]);
            }
        }
    }

    private function handleVariants(Product $product, array $variantsData)
    {
        if (empty($variantsData)) {
            return;
        }

        foreach ($variantsData as $index => $variantData) {
            $variantData = $this->normalizeVariantTranslatable($variantData, $index);

            if (! empty($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'variant_name' => $variantData['variant_name'],
                        'price' => $variantData['price'],
                        'status' => $variantData['status'] ?? $variant->status,
                        'attributes' => $variantData['attributes'] ?? null,
                    ]);
                }
            } else {
                // Auto-generate variant SKU derived from product SKU
                $suffix = strtoupper(Str::random(4));
                $variantSku = $product->sku . '-V-' . $suffix;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variantData['variant_name'],
                    'sku' => $variantSku,
                    'price' => $variantData['price'],
                    'attributes' => $variantData['attributes'] ?? null,
                    'status' => $variantData['status'] ?? 'active',
                ]);
            }
        }
    }

    private function normalizeVariantTranslatable(array $variantData, int $index = 0): array
    {
        // Accept both 'name' and 'variant_name' keys; convert to translatable array
        if (! isset($variantData['variant_name'])) {
            if (isset($variantData['name'])) {
                $variantData['variant_name'] = $variantData['name'];
            }
        }

        if (isset($variantData['variant_name']) && ! is_array($variantData['variant_name'])) {
            $variantData['variant_name'] = ['en' => $variantData['variant_name']];
        }

        if (isset($variantData['name']) && is_array($variantData['name'])) {
            foreach ($variantData['name'] as $localeKey => $value) {
                if (! isset($variantData['variant_name'][$localeKey])) {
                    $variantData['variant_name'][$localeKey] = $value;
                }
            }
        }

        // DO NOT fill missing locales here - translation service already handled it
        // Only validate English is present
        if (empty($variantData['variant_name']['en'])) {
            Log::warning("Variant {$index} missing English variant_name during normalization");
        }

        return $variantData;
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            ProductVariant::where('product_id', $id)->delete();
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
            $product->delete();
        } catch (\Exception $e) {
            Log::error('ProductRepository.deleteProduct: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateProductStatus($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->status = $product->status == 'active' ? 'inactive' : 'active';
            $product->save();

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.updateProductStatus: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateProductBestseller($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->is_bestseller = !$product->is_bestseller;
            $product->save();

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.updateProductBestseller: '.$e->getMessage());
            throw $e;
        }
    }

    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    public function getDataforDataTable($request = null)
    {
        return Product::with(['brand', 'category'])
            ->select('id', 'name', 'status', 'is_bestseller', 'cover_image', 'brand_id', 'category_id')
            ->latest()
            ->get();
    }

    /**
     * Validate translatable fields to ensure data integrity
     * Prevents Spatie Translatable from removing important English content
     *
     * @param array $data
     * @return void
     * @throws \Exception if critical validation fails
     */
    private function validateTranslatableFields(array &$data): void
    {
        // Required translatable field: name must have English value
        if (!isset($data['name']['en']) || empty(trim($data['name']['en']))) {
            Log::error("Validation failed: Product name (English) is required");
            throw new \Exception("Product name in English is required");
        }

        // Clean up empty translatable fields to prevent Spatie issues
        // BUT provide default empty JSON for nullable fields to satisfy database constraints
        $translatableFields = ['name', 'description', 'warranty', 'dimensions'];

        foreach ($translatableFields as $field) {
            if (isset($data[$field])) {
                // If field is an empty array, replace with empty JSON object {} to satisfy DB constraint
                if (is_array($data[$field]) && empty($data[$field])) {
                    // For translatable fields in Spatie, use empty object that will be JSON encoded
                    $data[$field] = (object)[];  // Empty JSON object: {}
                    continue;
                }

                // If field is an array, validate and clean up empty locale values
                if (is_array($data[$field])) {
                    foreach ($data[$field] as $locale => $value) {
                        // Remove keys with empty string values (but keep null for optional fields)
                        if ($value === '') {
                            unset($data[$field][$locale]);
                        }
                    }

                    // If all locales were removed, set to empty object to satisfy DB constraint
                    if (empty($data[$field])) {
                        $data[$field] = (object)[];  // Empty JSON object: {}
                    }
                }
            } else {
                // Field not provided at all - set to empty JSON object for optional fields
                // name is required so we skip it
                if ($field !== 'name') {
                    $data[$field] = (object)[];  // Empty JSON object: {}
                }
            }
        }

        Log::info("Translatable fields validation completed successfully");
    }

    /**
     * Verify that translatable fields were saved correctly to database
     * Helps detect issues with Spatie Translatable behavior
     *
     * @param Product $product
     * @return void
     */
    private function verifyTranslatableFieldsSaved(Product $product): void
    {
        Log::info("=== Verifying translatable fields saved to DB ===");

        $translatableFields = ['name', 'description', 'warranty', 'dimensions'];
        $issues = [];

        foreach ($translatableFields as $field) {
            $value = $product->$field;

            if (is_null($value)) {
                continue;
            }

            // Decode JSON to check locales
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $locales = array_keys($decoded);
                    $hasEnglish = isset($decoded['en']);

                    Log::info("Field '{$field}': Locales=" . json_encode($locales) . ", HasEnglish=" . ($hasEnglish ? 'YES' : 'NO'));

                    // Check if English value exists and is not empty
                    if ($hasEnglish) {
                        $enLength = isset($decoded['en']) ? strlen($decoded['en']) : 0;
                        if ($enLength > 0) {
                            Log::info("  ✓ English value present (length: {$enLength})");
                        } else {
                            $issues[] = "Field '{$field}' has empty English value";
                            Log::warning("  ✗ English value is empty!");
                        }
                    } else {
                        // Only warn if other locales exist (translations without source)
                        if (!empty($locales)) {
                            $issues[] = "Field '{$field}' missing English translation (has: " . implode(', ', $locales) . ")";
                            Log::warning("  ✗ Missing English locale! Has: " . json_encode($locales));
                        }
                    }
                } else {
                }
            } else {
                Log::debug("Field '{$field}': " . gettype($value));
            }
        }

        if (!empty($issues)) {
            Log::error("=== CRITICAL: Translatable field issues detected ===");
            foreach ($issues as $issue) {
                Log::error("  - {$issue}");
            }
        } else {
            Log::info("=== All translatable fields verified successfully ===");
        }
    }
}
