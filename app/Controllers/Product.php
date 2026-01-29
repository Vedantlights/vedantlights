<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Product extends BaseController{

	public function __construct(){

		$this->session = \Config\Services::session();

		$this->controller = 'Product';

	$this->ProductModel = new ProductModel();	

	}

 public function index(){

	if(!$this->session->has('admin_loggedin')){

	echo view('login');

	} else {

	    $data['session']	= $this->session;	

		$data["brandDetails"] = $this->ProductModel->BrandDetails();

		

	echo view($this->controller.'/index',$data);

	}

 }

 public function ProductDetails(){

	if(!$this->session->has('admin_loggedin')){

	echo view('login');

	} else {

	    $data['session']	= $this->session;	

		$data["productDetails"] = $this->ProductModel->ProductDetails();

		

	echo view($this->controller.'/ProductDetails',$data);

	}

 }

  public function editProductDetails($id){

	if(!$this->session->has('admin_loggedin')){

	echo view('login');

	} else {

	    $data['session']	= $this->session;	
	    
		$data["productDetails"] = $this->ProductModel->getProductDetails($id);
		$data["brandDetails"] = $this->ProductModel->BrandDetails();
		echo view($this->controller.'/editProductDetails',$data);

	}

 }

 

 

 public function CategoryDetails(){

    	if(!$this->session->has('admin_loggedin')){

		echo view('login');

		} else {

		$request 	= service('request');

		$data 		= $request->getPost();

		$data["categoryDetails"]=$this->ProductModel->getCategoryDetails($data["brandId"]);

		if(!empty($data["categoryDetails"])){

			$data["StatusCode"] = 101;



		}else{

			$data["StatusCode"] = 102;

		}

		echo json_encode($data);exit; 

		}

    }

  public function addproductdetails(){

    	if(!$this->session->has('admin_loggedin')){

		echo view('login');

		} else {

		 $request 	= service('request');

		$data 		= $request->getPost();

		$chk_result = $this->ProductModel->chkexitsProductDetails($data);

		if(!empty($chk_result)){

			$this->session->setFlashdata('fail', 'Product Name Already Exits.');

			return redirect()->to(base_url().'Product');

		}else{

			$img = $this->request->getFile('product_img');

			$pro_img = $img->getName();

			if(!empty($pro_img)){

			$img->move(ROOTPATH. 'uploads/Product');

				$pro_pdf = '';
				$pdfFile = $this->request->getFile('product_pdf');
				if ($pdfFile && $pdfFile->getName() !== '' && $pdfFile->isValid()) {
					$mime = $pdfFile->getClientMimeType();
					$ext = strtolower($pdfFile->getClientExtension());
					if (in_array($mime, ['application/pdf', 'application/x-pdf'], true) || $ext === 'pdf') {
						$pdfFile->move(ROOTPATH . 'uploads/Product');
						$pro_pdf = $pdfFile->getName();
					}
				}

				$productdata = array(

								"catId" => $data['category_name'],

								"brand_id" => $data['brand_name'],

								"pro_name" => $data['product_name'],

								"pro_desc" => $data['product_desc'],

								"pro_tech" => $data['product_tech'],

								"pro_img" => $pro_img,

								"pro_pdf" => $pro_pdf

								);

			$chk_result = $this->ProductModel->addProductdetails($productdata);

		  if(!empty($chk_result)){

			$this->session->setFlashdata('success', 'Product Name Added Successfully.');

			return redirect()->to(base_url().'Product');

			}else{

			$this->session->setFlashdata('fail', 'Error Product Name Addtion.');		

			return redirect()->to(base_url().'Product');

			}

		}else{

			$this->session->setFlashdata('fail', 'Error Product Image Upload.');		

			return redirect()->to(base_url().'Product');

		}



		}

		

		}

    }

    public function deleteproduct($catId) {
		if(!$this->session->has('admin_loggedin')){

			echo view('login');

		} else {

			$id = base64_decode($catId);
			$data["categoryDetails"] = $this->ProductModel->deleteproduct($id);

			$this->session->setFlashdata('fail', 'Product has been deleted Successfully.');

				return redirect()->to(base_url().'ProductDetails');

		} 	
	}


	public function updateProductdetails(){

    	if(!$this->session->has('admin_loggedin')){

		echo view('login');

		} else {

		 $request 	= service('request');

		$data 		= $request->getPost();

		$chk_result = $this->ProductModel->chkexitsProductDetails($data);

		if(!empty($chk_result)){

			$this->session->setFlashdata('fail', 'Product Name Already Exits.');

			return redirect()->to(base_url().'Product');

		}else{

				$img = $this->request->getFile('product_img');
				$pro_img = $img->getName();

				if(!empty($pro_img)){

					$img->move(ROOTPATH. 'uploads/Product');
				} else {
					$pro_img = $data['old_image'];
				}

				$pro_pdf = isset($data['old_pdf']) ? $data['old_pdf'] : '';
				$pdfFile = $this->request->getFile('product_pdf');
				if ($pdfFile && $pdfFile->getName() !== '' && $pdfFile->isValid()) {
					$mime = $pdfFile->getClientMimeType();
					$ext = strtolower($pdfFile->getClientExtension());
					if (in_array($mime, ['application/pdf', 'application/x-pdf'], true) || $ext === 'pdf') {
						$pdfFile->move(ROOTPATH . 'uploads/Product');
						$pro_pdf = $pdfFile->getName();
					}
				}
				
					$productdata = array(

								"catId" => $data['category_name'],

								"brand_id" => $data['brand_name'],

								"pro_name" => $data['product_name'],

								"pro_desc" => $data['product_desc'],

								"pro_tech" => $data['product_tech'],

								"pro_img" => $pro_img,

								"pro_pdf" => $pro_pdf

								);

				


			$chk_result = $this->ProductModel->updateProductdetails($productdata,$data['prodId']);

		  if(!empty($chk_result)){

			$this->session->setFlashdata('success', 'Product Updated Successfully.');

			return redirect()->to(base_url().'ProductDetails');

			}else{

			$this->session->setFlashdata('fail', 'Error Product Update.');		

			return redirect()->to(base_url().'ProductDetails');

			}	

		}

		

		}

    }

}