<?php
namespace App\Controllers;
use App\Models\BrandModel;
class Brand extends BaseController{
	public function __construct(){
		$this->session = \Config\Services::session();
		$this->controller = 'Brand';
	$this->BrandModel = new BrandModel();	
	}
	public function index(){
    	if(!$this->session->has('admin_loggedin')){
		echo view('login');
		} else {
		    $data['session']	= $this->session;	
			$data["brandDetails"] = $this->BrandModel->BrandDetails();
		echo view($this->controller.'/index',$data);
		}
    }
    public function editBrand($brandId){
	if(!$this->session->has('admin_loggedin')){
	echo view('login');
	} else {
	   $data['session']	= $this->session;	
	   $data["brandDetails"] = $this->BrandModel->getBrandDetails($brandId);
	echo view($this->controller.'/editBrand',$data);
	}
 }
 
    public function addBranddetails(){
    	if(!$this->session->has('admin_loggedin')){
		echo view('login');
		} else {
		 $request 	= service('request');
		$data 		= $request->getPost();
		$chk_result = $this->BrandModel->chkexitsBrandDetails($data['brand_name']);
		if(!empty($chk_result)){
			$this->session->setFlashdata('fail', 'Brand Name Already Exits.');
			return redirect()->to(base_url().'Brand');
		}else{
		$branddata = array(
								"brand_name" => $data['brand_name']
								);
		$chk_result = $this->BrandModel->addBranddetails($branddata);
		  if(!empty($chk_result)){
			$this->session->setFlashdata('success', 'Brand Name Added Successfully.');
			return redirect()->to(base_url().'Brand');
			}else{
			$this->session->setFlashdata('fail', 'Error Brand Name Addtion.');		
			return redirect()->to(base_url().'Brand');
			}	
		}
		
		}
    }
    public function updateBranddetails(){
    	if(!$this->session->has('admin_loggedin')){
		echo view('login');
		} else {
		 $request 	= service('request');
		$data 		= $request->getPost();
		$chk_result = $this->BrandModel->chkexitsBrandDetails($data['brand_name']);
		if(!empty($chk_result)){
			$this->session->setFlashdata('fail', 'Brand Name Already Exits.');
			return redirect()->to(base_url().'Brand');
		}else{
		$branddata = array(
								"brand_name" => $data['brand_name']
								);
		$chk_result = $this->BrandModel->updateBranddetails($branddata,$data['brandId']);
		  if(!empty($chk_result)){
			$this->session->setFlashdata('success', 'Brand Name Updated Successfully.');
			return redirect()->to(base_url().'Brand');
			}else{
			$this->session->setFlashdata('fail', 'Error Brand Name Update.');		
			return redirect()->to(base_url().'Brand');
			}	
		}
		
		}
    }
    
}