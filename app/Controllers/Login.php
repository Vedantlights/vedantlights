<?php
namespace App\Controllers;
use App\Models\ModelLogin;
class Login extends BaseController{
	public function __construct(){
	$this->session = \Config\Services::session();
	$this->ModelLogin = new ModelLogin();
	}
	
	public function index(){
    	if(!$this->session->has('admin_loggedin')){
		echo view('login');
		} else {
			$data["totalProduct"] = $this->ModelLogin->totalProductDetails();
			$data["totalbrand"] = $this->ModelLogin->totalBrandDetails();
			$data["totalcategory"] = $this->ModelLogin->totalCategoryDetails();
		    echo view('dashboard',$data);
		}
    }

	public function dashboard(){
		if(!$this->session->has('admin_loggedin')){
		echo view('login');
		} else {
			$data["totalProduct"] = $this->ModelLogin->totalProductDetails();
			$data["totalbrand"] = $this->ModelLogin->totalBrandDetails();
			$data["totalcategory"] = $this->ModelLogin->totalCategoryDetails();
			echo view('dashboard',$data);
		}
	}
	public function check_login(){
	$request 	= service('request');
	$data 		= $request->getPost();
	$newdata = $this->ModelLogin->checkLogin($data);
	if(!empty($newdata)){
			$SESSIONdata = [
						'mst_id'     	 => $newdata->user_id,
						'username'     => $newdata->username,
						'admin_loggedin' => '1',
						];
			$this->session->set($SESSIONdata); // setting session data
	
			$response["txt_code"]=101;
			$response["message"]="Login Successfully.";
			
		}else{
			$response["txt_code"]=102;
			$response["message"]="Invalid Username And Password.";
		}
		echo json_encode($response);die;
	}
	
}
