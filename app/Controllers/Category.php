<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class Category extends BaseController{

	public function __construct(){

		$this->session = \Config\Services::session();

		$this->controller = 'Category';

	$this->CategoryModel = new CategoryModel();	

	}

 public function index(){

	if(!$this->session->has('admin_loggedin')){

	echo view('login');

	} else {

	    $data['session']	= $this->session;	

		$data["brandDetails"] = $this->CategoryModel->BrandDetails();

		$data["categoryDetails"] = $this->CategoryModel->CategoryDetails();



	echo view($this->controller.'/index',$data);

	}

 }	

  public function editcategory($catId){

	if(!$this->session->has('admin_loggedin')){

	echo view('login');

	} else {

	   $data['session']	= $this->session;	

	  $data["brandDetails"] = $this->CategoryModel->BrandDetails();

	  $data["categoryDetails"] = $this->CategoryModel->getCategoryDetails($catId);

	echo view($this->controller.'/editcategory',$data);

	}

 }

 public function deletecategory($catId) {
	if(!$this->session->has('admin_loggedin')){

		echo view('login');

	} else {

		$id = base64_decode($catId);
		$data["categoryDetails"] = $this->CategoryModel->deletecategory($id);

		$this->session->setFlashdata('fail', 'Category has been deleted Successfully.');

			return redirect()->to(base_url().'Category');

	} 	
 }

 public function addCategorydetails(){

    	if(!$this->session->has('admin_loggedin')){

		echo view('login');

		} else {

		 $request 	= service('request');

		$data 		= $request->getPost();

		$chk_result = $this->CategoryModel->chkexitsCategoryDetails($data);

		if(!empty($chk_result)){

			$this->session->setFlashdata('fail', 'Category Name Already Exits.');

			return redirect()->to(base_url().'Category');

		}else{

				$categorydata = array(

								"caterogyName" => $data['category_name'],

								"brandId" => $data['brand_name']

								);

			$chk_result = $this->CategoryModel->addCategorydetails($categorydata);

		  if(!empty($chk_result)){

			$this->session->setFlashdata('success', 'Category Name Added Successfully.');

			return redirect()->to(base_url().'Category');

			}else{

			$this->session->setFlashdata('fail', 'Error Category Name Addtion.');		

			return redirect()->to(base_url().'Category');

			}	

		}

		

		}

    }

    public function updateCategorydetails(){

    	if(!$this->session->has('admin_loggedin')){

		echo view('login');

		} else {

		 $request 	= service('request');

		$data 		= $request->getPost();

		$chk_result = $this->CategoryModel->chkexitsCategoryDetails($data);

		if(!empty($chk_result)){

			$this->session->setFlashdata('fail', 'Category Name Already Exits.');

			return redirect()->to(base_url().'Category');

		}else{

				$categorydata = array(

								"caterogyName" => $data['category_name'],

								"brandId" => $data['brand_name']

								);

			$chk_result = $this->CategoryModel->updateCategorydetails($categorydata,$data['catId']);

		  if(!empty($chk_result)){

			$this->session->setFlashdata('success', 'Category Name Updated Successfully.');

			return redirect()->to(base_url().'Category');

			}else{

			$this->session->setFlashdata('fail', 'Error Category Name Update.');		

			return redirect()->to(base_url().'Category');

			}	

		}

		

		}

    }

    

 

}