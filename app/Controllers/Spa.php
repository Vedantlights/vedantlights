<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Spa extends BaseController
{
    public function index(): ResponseInterface
    {
        $indexPath = ROOTPATH . 'react-app' . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR . 'index.html';

        if (!is_file($indexPath)) {
            return $this->response
                ->setStatusCode(503)
                ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->setBody("React frontend is not built yet.\nBuild it from public_html/react-app with: npm run build");
        }

        return $this->response
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody(file_get_contents($indexPath));
    }
}

