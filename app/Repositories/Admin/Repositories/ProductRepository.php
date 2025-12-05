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

            // Handle dropdown IDs
            $this->normalizeForeignKeys($data);

            // Temporary SKU to satisfy unique constraint during initial insert
            $data['sku'] = 'TEMP-'.uniqid();

            // Handle Cover Image
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('products', 'public');
            }

            // Create Product
            $product = Product::create($data);

            // 2. Generate Real SKU (EZ-XXX-0000)
            $this->generateAndSaveSku($product, $data);

            // 3. Handle Gallery Images
            $this->handleGalleryImages($product, $request);

            // 4. Handle Variants
            $this->handleVariants($product, $request->input('variants', []));

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.createProduct: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateProduct($id, $request, $data = null)
    {
        try {
            // 1. Find Product
            $product = Product::findOrFail($id);
            $data = $data ?? $request->validated();

            // 2. Security: Don't allow changing SKU or ID via mass assignment
            unset($data['sku'], $data['id']);

            // 3. Normalize Dropdowns (Handle 'brand' vs 'brand_id' mismatch if any)
            if (isset($data['brand']) && ! isset($data['brand_id'])) {
                $data['brand_id'] = $data['brand'];
            }
            if (isset($data['category']) && ! isset($data['category_id'])) {
                $data['category_id'] = $data['category'];
            }
            if (isset($data['sub_category']) && ! isset($data['sub_category_id'])) {
                $data['sub_category_id'] = $data['sub_category'];
            }

            // 4. Handle Cover Image Replacement
            if ($request->hasFile('cover_image')) {
                // Delete old image
                if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                    Storage::disk('public')->delete($product->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            }

            // 5. Update Main Attributes (including JSON translations)
            $product->update($data);

            // 6. Handle Gallery Images (Append New)
            if ($request->hasFile('image_path')) {
                foreach ($request->file('image_path') as $image) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $image->store('product_images', 'public'),
                        'sort_order' => 0,
                    ]);
                }
            }

            // 7. Sync Variants
            if (isset($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            return $product;

        } catch (\Exception $e) {
            Log::error('ProductRepository.updateProduct: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Logic to Add, Edit, or Delete variants based on form data
     */
    private function syncVariants(Product $product, array $variantsData)
    {
        $existingVariantIds = $product->productVariants()->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($variantsData as $variant) {
            // Case A: Update Existing Variant
            if (! empty($variant['id']) && in_array($variant['id'], $existingVariantIds)) {
                $submittedIds[] = $variant['id'];
                ProductVariant::where('id', $variant['id'])->update([
                    'variant_name' => $variant['name'],
                    'price' => $variant['price'],
                    'status' => $variant['status'] ?? 'active',
                ]);
            }
            // Case B: Create New Variant
            else {
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

        // Case C: Delete Removed Variants
        // Any existing ID that was NOT in the submitted form should be deleted
        $idsToDelete = array_diff($existingVariantIds, $submittedIds);
        if (! empty($idsToDelete)) {
            ProductVariant::whereIn('id', $idsToDelete)->delete();
        }
    }

    // --- Helper Methods to keep main logic clean ---

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
        // Extract English name for SKU base
        $namePart = is_array($data['name']) ? ($data['name']['en'] ?? reset($data['name'])) : $data['name'];
        // Remove non-alphanumeric, take first 3 chars, uppercase
        $cleanName = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namePart), 0, 3));
        // Pad ID (e.g., 0005)
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
            // Case: Update
            if (! empty($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                if ($variant) {
                    $variant->update([
                        'variant_name' => $variantData['name'], // Ensure field name matches DB
                        'price' => $variantData['price'],
                        'attributes' => $variantData['attributes'] ?? $variant->attributes,
                        'status' => $variantData['status'] ?? $variant->status,
                    ]);
                }
            }
            // Case: Create
            else {
                // Generate Variant SKU suffix
                $suffix = strtoupper(Str::random(4));
                $variantSku = $product->sku.'-V-'.$suffix;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variantData['name'],
                    'sku' => $variantData['sku'] ?? $variantSku,
                    'price' => $variantData['price'],
                    'attributes' => $variantData['attributes'] ?? null,
                    'status' => $variantData['status'] ?? 'active',
                ]);
            }
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete variants
            ProductVariant::where('product_id', $id)->delete();

            // Delete images
            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            // Delete cover
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

    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    public function getDataforDataTable($request = null)
    {
        return Product::select('id', 'name', 'status', 'cover_image')->latest()->get();
    }
}
