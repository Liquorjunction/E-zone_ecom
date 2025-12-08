<?php

namespace App\Repositories\Admin\Interfaces;

interface ProductRepositoryInterface
{
    public function getAllProducts();
    public function createProduct($request, $data = null);
    public function updateProduct($id, $request, $data = null);
    public function deleteProduct($id);
    public function updateProductStatus($id);
    public function updateProductBestseller($id);
    public function getProductById($id);
    public function getDataforDataTable();
}
