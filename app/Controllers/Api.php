<?php

namespace App\Controllers;

use App\Models\HomeModel;
use CodeIgniter\HTTP\ResponseInterface;

class Api extends BaseController
{
    protected HomeModel $homeModel;

    public function __construct()
    {
        $this->homeModel = new HomeModel();
    }

    public function brands(): ResponseInterface
    {
        return $this->response->setJSON([
            'data' => $this->homeModel->BrandDetails(),
        ]);
    }

    public function categories(int $brandId): ResponseInterface
    {
        return $this->response->setJSON([
            'data' => $this->homeModel->getCategoryDetails($brandId),
        ]);
    }

    public function products(int $brandId): ResponseInterface
    {
        $catId = $this->request->getGet('catId');

        $products = ($catId !== null && $catId !== '')
            ? $this->homeModel->getCategoryproductDetails($brandId, (int) $catId)
            : $this->homeModel->getBrandProducts($brandId);

        return $this->response->setJSON([
            'data' => $products,
        ]);
    }

    public function product(int $proId): ResponseInterface
    {
        $row = $this->homeModel->getproductDetails($proId);
        if (!$row) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Product not found',
            ]);
        }

        // Get multiple PDFs for this product
        $db = \Config\Database::connect();
        $pdfs = $db->table('product_pdf_details')
            ->where('pro_id', $proId)
            ->orderBy('pdf_id', 'ASC')
            ->get()
            ->getResultArray();

        // Add PDFs to product data
        if (is_array($row)) {
            $row['pdfs'] = $pdfs;
        } elseif (is_object($row)) {
            $row->pdfs = $pdfs;
        }

        return $this->response->setJSON([
            'data' => $row,
        ]);
    }
}

