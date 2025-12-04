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

            // --- A. Handle Dropdown IDs ---
            if (isset($data['brand']) && ! isset($data['brand_id'])) {
                $data['brand_id'] = $data['brand'];
            }
            if (isset($data['category']) && ! isset($data['category_id'])) {
                $data['category_id'] = $data['category'];
            }
            if (isset($data['sub_category']) && ! isset($data['sub_category_id'])) {
                $data['sub_category_id'] = $data['sub_category'];
            }

            // --- B. Temporary SKU (Placeholder) ---
            // We set a temporary SKU to pass database unique checks if necessary
            $data['sku'] = 'TEMP-'.Str::random(10);

            // --- C. Handle Cover Image ---
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('products', 'public');
            }

            // --- D. Create Product (To get the ID) ---
            $product = Product::create($data);

            // --- E. GENERATE REAL SKU & UPDATE ---
            // Format: EZ - [3 Chars of Name] - [4 Digit ID]
            // Example: Product ID 5, Name "iPhone" -> EZ-IPH-0005

            $namePart = is_array($data['name']) ? ($data['name']['en'] ?? reset($data['name'])) : $data['name'];

            // Clean name: remove spaces/symbols, take first 3 chars, uppercase
            $cleanName = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namePart), 0, 3));

            // Pad ID with zeros (e.g., 1 -> 0001, 25 -> 0025)
            $paddedId = str_pad($product->id, 4, '0', STR_PAD_LEFT);

            $finalSku = "EZ-{$cleanName}-{$paddedId}";

            // Update the product with the real SKU
            $product->update(['sku' => $finalSku]);

            // --- F. Handle Gallery Images ---
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $image) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $image->store('product_images', 'public'),
                        'sort_order' => 0,
                    ]);
                }
            }

            // --- G. Handle Variants & Variant SKUs ---
            if ($request->has('variants')) {
                foreach ($request->input('variants') as $index => $variantData) {

                    // Variant SKU: Parent SKU + -V + Index
                    // Example: EZ-IPH-0005-V1
                    if (empty($variantData['sku'])) {
                        $variantData['sku'] = $finalSku.'-V'.($index + 1);
                    }

                    ProductVariant::create([
                        'product_id' => $product->id,
                        'variant_name' => $variantData['name'],
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'attributes' => $variantData['attributes'] ?? null,
                        'status' => $variantData['status'] ?? 'active',
                    ]);
                }
            }

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.createProduct: '.$e->getMessage());
            throw $e;
        }
    }

    public function updateProduct($id, $request, $data = null)
    {
        try {
            // 1. Find the Record
            $product = Product::findOrFail($id);

            // 2. Prepare Data
            $data = $data ?? $request->validated();

            // Ensure we don't accidentally overwrite the SKU with null unless intended
            // Usually, we REMOVE 'sku' from the update data to prevent changing it
            unset($data['sku']);

            // --- A. Handle Dropdown IDs ---
            if (isset($data['brand']) && ! isset($data['brand_id'])) {
                $data['brand_id'] = $data['brand'];
            }
            if (isset($data['category']) && ! isset($data['category_id'])) {
                $data['category_id'] = $data['category'];
            }
            if (isset($data['sub_category']) && ! isset($data['sub_category_id'])) {
                $data['sub_category_id'] = $data['sub_category'];
            }

            // --- B. Handle Cover Image ---
            if ($request->hasFile('cover_image')) {
                // Optional: Delete old image from storage here if needed
                $data['cover_image'] = $request->file('cover_image')->store('products', 'public');
            }

            // --- C. Update Main Product ---
            $product->update($data);

            // --- D. Handle Gallery Images (Append New) ---
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $image) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $image->store('product_images', 'public'),
                        'sort_order' => 0,
                    ]);
                }
            }

            // --- E. Handle Variants (Update Existing vs Create New) ---
            if ($request->has('variants')) {
                foreach ($request->input('variants') as $index => $variantData) {

                    // CASE 1: Update Existing Variant
                    if (! empty($variantData['id'])) {
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant) {
                            $variant->update([
                                'name' => $variantData['name'],
                                'price' => $variantData['price'],
                                'stock_quantity' => $variantData['stock_quantity'] ?? $variant->stock_quantity,
                                'attributes' => $variantData['attributes'] ?? $variant->attributes,
                                'status' => $variantData['status'] ?? $variant->status,
                            ]);
                        }
                    }
                    // CASE 2: Create New Variant
                    else {
                        // Generate a unique SKU suffix for the new variant
                        // We use Random string instead of simple index to prevent collisions with deleted variants
                        $suffix = strtoupper(Str::random(4));
                        $newSku = $product->sku.'-V-'.$suffix;

                        ProductVariant::create([
                            'product_id' => $product->id,
                            'variant_name' => $variantData['name'],
                            'sku' => $variantData['sku'] ?? $newSku,
                            'price' => $variantData['price'],
                            'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                            'attributes' => $variantData['attributes'] ?? null,
                            'status' => $variantData['status'] ?? 'active',
                        ]);
                    }
                }
            }

            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.updateProduct: '.$e->getMessage());
            throw $e;
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id)->delete();

            // Delete all related variants
            ProductVariant::where('product_id', $id)->delete();

            // Delete all related images
            // first fetch all images related to this product
            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            // Delete all related Cover Image
            if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                Storage::disk('public')->delete($product->cover_image);
            }

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

    public function getDataforDataTable()
    {
        return Product::select('id', 'name', 'status', 'cover_image')->get();
    }
}
