<?php

namespace App\Repositories\Admin\Interfaces;

interface ProductRepositoryInterface
{
    public function getAllProducts();
    public function getAllActiveProducts();
    public function getProductVariants($productId);
    public function getProductVarientCount($productId);
    public function getProductById($id);
    public function getVariantsByProductId(int $productId);
    public function getVarientList($term = null);
    public function getDataforDataTable();

    public function createProduct($request, $data = null);
    public function updateProduct($id, $request, $data = null);
    public function deleteProduct($id);

    public function updateProductStatus($id);
    public function updateProductBestseller($id);

    public function updateProductVariantStatus($variantId);
    public function updateProductVariantBestseller($variantId);
    public function deleteVariantImage($imageId);
    public function reorderVariantImages($variantId, array $order);
}
