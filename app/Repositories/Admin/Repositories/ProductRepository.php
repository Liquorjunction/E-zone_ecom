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

            // 2. Create Product
            $product = Product::create($data);

            // 3. Generate Real SKU (EZ-XXX-0000)
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
            Log::error('ProductRepository.createProduct: '.$e->getMessage());
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
            return;
        }

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

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variantData['variant_name'] ?? $variantData['name'],
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

    public function getProductById($id)
    {
        return Product::findOrFail($id);
    }

    public function getDataforDataTable($request = null)
    {
        return Product::select('id', 'name', 'status', 'cover_image')->latest()->get();
    }
}
