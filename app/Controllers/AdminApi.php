<?php

namespace App\Controllers;

use App\Models\BrandModel;
use App\Models\CategoryModel;
use App\Models\ModelLogin;
use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminApi extends BaseController
{
    protected $session;
    protected $db;
    protected ModelLogin $loginModel;
    protected BrandModel $brandModel;
    protected CategoryModel $categoryModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
        $this->loginModel = new ModelLogin();
        $this->brandModel = new BrandModel();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    private function isAdminLoggedIn(): bool
    {
        return $this->session->has('admin_loggedin') && (string) $this->session->get('admin_loggedin') === '1';
    }

    private function requireAdmin(): ?ResponseInterface
    {
        if ($this->isAdminLoggedIn()) {
            return null;
        }

        return $this->response
            ->setStatusCode(401)
            ->setJSON(['error' => 'Unauthorized']);
    }

    private function inputArray(): array
    {
        $json = $this->request->getJSON(true);
        if (is_array($json)) {
            return $json;
        }
        return (array) $this->request->getPost();
    }

    public function login(): ResponseInterface
    {
        $data = $this->inputArray();

        $userName = (string) ($data['user_name'] ?? $data['username'] ?? '');
        $password = (string) ($data['password'] ?? '');

        if ($userName === '' || $password === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'txt_code' => 102,
                'message' => 'Username and password are required.',
            ]);
        }

        $row = $this->loginModel->checkLogin([
            'user_name' => $userName,
            'password' => $password,
        ]);

        if (!$row) {
            return $this->response->setStatusCode(401)->setJSON([
                'txt_code' => 102,
                'message' => 'Invalid Username And Password.',
            ]);
        }

        $this->session->set([
            'mst_id' => $row->user_id,
            'username' => $row->username,
            'admin_loggedin' => '1',
        ]);

        return $this->response->setJSON([
            'txt_code' => 101,
            'message' => 'Login Successfully.',
            'user' => [
                'mst_id' => $row->user_id,
                'username' => $row->username,
            ],
        ]);
    }

    public function logout(): ResponseInterface
    {
        $this->session->remove(['mst_id', 'username', 'admin_loggedin']);

        return $this->response->setJSON([
            'ok' => true,
        ]);
    }

    public function me(): ResponseInterface
    {
        if (!$this->isAdminLoggedIn()) {
            return $this->response->setStatusCode(401)->setJSON([
                'authenticated' => false,
            ]);
        }

        return $this->response->setJSON([
            'authenticated' => true,
            'user' => [
                'mst_id' => $this->session->get('mst_id'),
                'username' => $this->session->get('username'),
            ],
        ]);
    }

    public function stats(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        return $this->response->setJSON([
            'data' => [
                'totalProduct' => $this->loginModel->totalProductDetails(),
                'totalbrand' => $this->loginModel->totalBrandDetails(),
                'totalcategory' => $this->loginModel->totalCategoryDetails(),
            ],
        ]);
    }

    public function brands(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        return $this->response->setJSON([
            'data' => $this->brandModel->BrandDetails(),
        ]);
    }

    public function createBrand(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $data = $this->inputArray();
        $brandName = trim((string) ($data['brand_name'] ?? ''));
        if ($brandName === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'brand_name is required',
            ]);
        }

        if ($this->brandModel->chkexitsBrandDetails($brandName) > 0) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'Brand already exists',
            ]);
        }

        $ok = $this->brandModel->addBranddetails([
            'brand_name' => $brandName,
            'is_delete' => 0,
        ]);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function updateBrand(int $brandId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $data = $this->inputArray();
        $brandName = trim((string) ($data['brand_name'] ?? ''));
        if ($brandName === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'brand_name is required',
            ]);
        }

        $ok = $this->brandModel->updateBranddetails([
            'brand_name' => $brandName,
        ], $brandId);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function deleteBrand(int $brandId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        // Soft delete to match existing "is_delete" usage
        $ok = $this->brandModel->updateBranddetails([
            'is_delete' => 1,
        ], $brandId);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function categories(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        return $this->response->setJSON([
            'data' => $this->categoryModel->CategoryDetails(),
        ]);
    }

    public function createCategory(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $data = $this->inputArray();
        $brandId = (int) ($data['brand_id'] ?? $data['brandId'] ?? $data['brand_name'] ?? 0);
        $categoryName = trim((string) ($data['category_name'] ?? $data['caterogyName'] ?? ''));

        if ($brandId <= 0 || $categoryName === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'brand_id and category_name are required',
            ]);
        }

        $exists = $this->categoryModel->chkexitsCategoryDetails([
            'brand_name' => (string) $brandId,
            'category_name' => $categoryName,
        ]);
        if ($exists > 0) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'Category already exists',
            ]);
        }

        $ok = $this->categoryModel->addCategorydetails([
            'caterogyName' => $categoryName,
            'brandId' => $brandId,
        ]);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function updateCategory(int $catId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $data = $this->inputArray();
        $brandId = (int) ($data['brand_id'] ?? $data['brandId'] ?? $data['brand_name'] ?? 0);
        $categoryName = trim((string) ($data['category_name'] ?? $data['caterogyName'] ?? ''));

        if ($brandId <= 0 || $categoryName === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'brand_id and category_name are required',
            ]);
        }

        $ok = $this->categoryModel->updateCategorydetails([
            'caterogyName' => $categoryName,
            'brandId' => $brandId,
        ], $catId);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function deleteCategory(int $catId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $ok = $this->categoryModel->deletecategory($catId);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function products(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        return $this->response->setJSON([
            'data' => $this->productModel->ProductDetails(),
        ]);
    }

    public function product(int $proId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $row = $this->db->table('product_details')->where('pro_id', $proId)->get()->getRowArray();
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Product not found',
            ]);
        }

        return $this->response->setJSON([
            'data' => $row,
        ]);
    }

    private function saveUploadedProductImage(?string $existing = null): array
    {
        $file = $this->request->getFile('product_img');
        if (!$file || $file->getName() === '') {
            return ['ok' => true, 'filename' => $existing];
        }

        if (!$file->isValid()) {
            return ['ok' => false, 'error' => 'Invalid product image upload'];
        }

        $dir = ROOTPATH . 'uploads' . DIRECTORY_SEPARATOR . 'Product';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $name = $file->getRandomName();
        if (!$file->move($dir, $name)) {
            return ['ok' => false, 'error' => 'Failed to save product image'];
        }

        return ['ok' => true, 'filename' => $name];
    }

    public function createProduct(): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        // Supports multipart/form-data (for image) and JSON (no image)
        $data = $this->inputArray();

        $brandId = (int) ($data['brand_id'] ?? $data['brand_name'] ?? 0);
        $catId = (int) ($data['cat_id'] ?? $data['catId'] ?? $data['category_name'] ?? 0);
        $name = trim((string) ($data['pro_name'] ?? $data['product_name'] ?? ''));
        $desc = (string) ($data['pro_desc'] ?? $data['product_desc'] ?? '');
        $tech = (string) ($data['pro_tech'] ?? $data['product_tech'] ?? '');

        if ($brandId <= 0 || $catId <= 0 || $name === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'brand_id, cat_id and pro_name are required',
            ]);
        }

        $exists = $this->productModel->chkexitsProductDetails([
            'brand_name' => (string) $brandId,
            'category_name' => (string) $catId,
            'product_name' => $name,
        ]);
        if ($exists > 0) {
            return $this->response->setStatusCode(409)->setJSON([
                'error' => 'Product already exists',
            ]);
        }

        $img = $this->saveUploadedProductImage(null);
        if (!$img['ok']) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => $img['error'],
            ]);
        }

        $ok = $this->productModel->addProductdetails([
            'catId' => $catId,
            'brand_id' => $brandId,
            'pro_name' => $name,
            'pro_desc' => $desc,
            'pro_tech' => $tech,
            'pro_img' => (string) ($img['filename'] ?? ''),
        ]);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function updateProduct(int $proId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $existing = $this->db->table('product_details')->where('pro_id', $proId)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Product not found',
            ]);
        }

        $data = $this->inputArray();

        $brandId = (int) ($data['brand_id'] ?? $data['brand_name'] ?? $existing['brand_id'] ?? 0);
        $catId = (int) ($data['cat_id'] ?? $data['catId'] ?? $data['category_name'] ?? $existing['catId'] ?? 0);
        $name = trim((string) ($data['pro_name'] ?? $data['product_name'] ?? $existing['pro_name'] ?? ''));
        $desc = (string) ($data['pro_desc'] ?? $data['product_desc'] ?? ($existing['pro_desc'] ?? ''));
        $tech = (string) ($data['pro_tech'] ?? $data['product_tech'] ?? ($existing['pro_tech'] ?? ''));

        if ($brandId <= 0 || $catId <= 0 || $name === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => 'brand_id, cat_id and pro_name are required',
            ]);
        }

        $img = $this->saveUploadedProductImage((string) ($existing['pro_img'] ?? ''));
        if (!$img['ok']) {
            return $this->response->setStatusCode(400)->setJSON([
                'error' => $img['error'],
            ]);
        }

        $ok = $this->productModel->updateProductdetails([
            'catId' => $catId,
            'brand_id' => $brandId,
            'pro_name' => $name,
            'pro_desc' => $desc,
            'pro_tech' => $tech,
            'pro_img' => (string) ($img['filename'] ?? ''),
        ], $proId);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }

    public function deleteProduct(int $proId): ResponseInterface
    {
        if ($resp = $this->requireAdmin()) {
            return $resp;
        }

        $ok = $this->productModel->deleteproduct($proId);

        return $this->response->setJSON([
            'ok' => (bool) $ok,
        ]);
    }
}

