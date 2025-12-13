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
            // 2. Create Product
            $product = Product::create($data);
            $this->generateAndSaveSku($product, $data);
            // 3. Handle Variants
            if (!empty($data['variants']) && is_array($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }
            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.createProduct FAILED: ' . $e->getMessage() . ' | Data: ' . json_encode($data));
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    public function updateProduct($id, $request, $data = null)
    {
        try {
            $product = Product::findOrFail($id);
            $data = $data ?? $request->validated();
            unset($data['sku'], $data['id']);
            $this->normalizeForeignKeys($data);
            // Update Cover Image
            if ($request->hasFile('cover_image')) {
                if ($product->cover_image && Storage::disk('public')->exists($product->cover_image)) {
                    Storage::disk('public')->delete($product->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('products/covers', 'public');
            }
            $product->update($data);
            // Sync Variants
            if (isset($data['variants']) && is_array($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }
            return $product;
        } catch (\Exception $e) {
            Log::error('ProductRepository.updateProduct FAILED: ' . $e->getMessage() . ' | Data: ' . json_encode($data));
            throw new \Exception('Failed to update product: ' . $e->getMessage());
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete all associated images
            $images = ProductImage::where('product_id', $id)->get();
            foreach ($images as $image) {
                if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
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

        foreach ($variantsData as $key => $data) {
            if (empty($data) || (!isset($data['variant_name']) && !isset($data['name']))) {
                continue;
            }
            $data = $this->normalizeVariantData($data, is_numeric($key) ? (int)$key : 0);

            // Update
            if (isset($data['id']) && in_array($data['id'], $existingIds)) {
                $variant = ProductVariant::find($data['id']);
                if ($variant) {
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
                    $data['sku'] = $product->sku . '-V-' . $suffix . '-' . ($key + 1);
                }
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

    private function handleImportedFile($sourcePath, $targetDir)
    {
        if (Storage::disk('public')->exists($sourcePath)) {
            return $sourcePath;
        }

        if (file_exists($sourcePath)) {
            $filename = basename($sourcePath);
            $targetPath = $targetDir . '/' . Str::random(5) . '_' . $filename;
            Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));
            return $targetPath;
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

        if (isset($data['attributes'])) {
            $cleanAttributes = [];
            foreach ($data['attributes'] as $lang => $attrs) {
                if (is_string($attrs)) {
                    // Decode JSON string from form
                    $attrs = json_decode($attrs, true) ?? [];
                }
                if (is_array($attrs)) {
                    $cleanAttributes[$lang] = array_values($attrs);
                }
            }
            $data['attributes'] = $cleanAttributes;
        }

        return $data;
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
            if (!isset($data[$field])) $data[$field] = (object)[];
        }
    }
}
