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

    public function createProduct($request, $data = null)
    {
        try {
            // 1. Prepare Data
            $data = $data ?? $request->validated();
            $data['status'] = $data['status'] ?? 'inactive';

            Log::info("===== ProductRepository.createProduct START =====");
            Log::info("Input data keys: " . json_encode(array_keys($data)));

            // Validate translatable fields before saving
            $this->validateTranslatableFields($data);

            // Normalize Dropdowns (Handle 'brand' key if passed instead of 'brand_id')
            $this->normalizeForeignKeys($data);

            // ------------------------------------------------------------
            // CRITICAL FIX: Temporary SKU to prevent SQL Error 1364
            // ------------------------------------------------------------
            if (empty($data['sku'])) {
                $data['sku'] = 'TEMP-'.uniqid();
            }

            // Handle Cover Image
            // Check if request is actually an HTTP request with a file
            if ($request instanceof \Illuminate\Http\Request && $request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('products', 'public');
            }
            // If importing, we might just leave cover_image null or handle URLs here
            // elseif (isset($data['cover_image_url'])) { ... }


            // CRITICAL DEBUG: Log description structure right before create
            if (isset($data['description'])) {
                $descKeys = is_array($data['description']) ? array_keys($data['description']) : 'NOT_ARRAY';
                Log::critical("BEFORE CREATE - description locales: " . json_encode($descKeys));
                if (is_array($data['description']) && isset($data['description']['en'])) {
                    Log::critical("BEFORE CREATE - description.en length: " . strlen($data['description']['en']));
                    Log::critical("BEFORE CREATE - description.en preview: " . substr($data['description']['en'], 0, 50) . '...');
                } else {
                    Log::critical("BEFORE CREATE - description.en: MISSING!");
                }
            } else {
                Log::critical("BEFORE CREATE - description field: NOT SET");
            }

            // 2. Create Product
            Log::info("Calling Product::create...");
            $product = Product::create($data);

            Log::info("Product created with ID: {$product->id}");

            // Verify translatable data was saved correctly with detailed logging
            $savedProduct = Product::find($product->id);
            $this->verifyTranslatableFieldsSaved($savedProduct);            // 3. Generate Real SKU (EZ-XXX-0000)
            Log::info("Generating SKU...");
            $this->generateAndSaveSku($product, $data);

            // 4. Handle Gallery Images (Only if request has files)
            if ($request instanceof \Illuminate\Http\Request) {
                $this->handleGalleryImages($product, $request);
            }

            // 5. Handle Variants
            Log::info("Processing variants, count: " . count($data['variants'] ?? []));
            $variants = $data['variants'] ?? ($request instanceof \Illuminate\Http\Request ? $request->input('variants', []) : []);
            $this->handleVariants($product, $variants);

            Log::info("===== ProductRepository.createProduct SUCCESS =====");
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
            if (! empty($variant['id']) && in_array($variant['id'], $existingVariantIds)) {
                $submittedIds[] = $variant['id'];
                ProductVariant::where('id', $variant['id'])->update([
                    'variant_name' => $variant['variant_name'] ?? $variant['name'],
                    'price' => $variant['price'],
                    'attributes' => $variant['attributes'] ?? null,
                    'status' => $variant['status'] ?? 'active',
                ]);
            } else {
                $suffix = strtoupper(Str::random(4));
                $newSku = $product->sku.'-V-'.$suffix;

                $newVar = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variant['name'],
                    'sku' => $newSku,
                    'price' => $variant['price'],
                    'status' => 'active',
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
            Log::debug("No variants to process");
            return;
        }

        Log::info("handleVariants for product ID {$product->id}, variant count: " . count($variantsData));

        foreach ($variantsData as $index => $variantData) {

            if (! empty($variantData['id'])) {
                // Update logic handled in syncVariants generally, but safe to keep basic update here
                $variant = ProductVariant::find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'variant_name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'status' => $variantData['status'] ?? $variant->status,
                    ]);
                }
            } else {
                $suffix = strtoupper(Str::random(4));
                $variantSku = $product->sku.'-V-'.$suffix;


                $createdVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variantData['variant_name'] ?? $variantData['name'],
                    'sku' => $variantData['sku'] ?? $variantSku,
                    'price' => $variantData['price'],
                    'attributes' => $variantData['attributes'] ?? null,
                    'status' => $variantData['status'] ?? 'active',
                ]);

            }
        }

        Log::info("handleVariants completed");
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
        return Product::select('id', 'name', 'status', 'is_bestseller', 'cover_image')->latest()->get();
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
