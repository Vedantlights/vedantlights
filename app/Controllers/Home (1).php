<?php

namespace App\Controllers;

use App\Models\HomeModel;

class Home extends BaseController{

	public function __construct(){

		$this->session = \Config\Services::session();

		$this->controller = 'Home';

	$this->HomeModel = new HomeModel();	

	}

 public function index(){

 	$data["brandDetails"] = $this->HomeModel->BrandDetails();

    	echo view($this->controller.'/index',$data);

	 }

 	public function brandDetails($brandId,$brandName){

 	$data["brandName"] = $brandName;

 	$data["brandId"] = $brandId;

 	$data["brandDetails"] = $this->HomeModel->BrandDetails();

 	$data["catDetails"] = $this->HomeModel->getCategoryDetails($brandId);

 	$data["proDetails"] = $this->HomeModel->getCategoryproductDetails($brandId,$data["catDetails"][0]["cat_id"]);

 	echo view($this->controller.'/brandDetails',$data);

	 }

	 public function categoryDetails($brandId,$brandName,$cat_id){

 	$data["brandName"] = $brandName;

 	$data["brandId"] = $brandId;

 	$data["brandDetails"] = $this->HomeModel->BrandDetails();

 	$data["catDetails"] = $this->HomeModel->getCategoryDetails($brandId);

 	$data["proDetails"] = $this->HomeModel->getCategoryproductDetails($brandId,$cat_id);

    	echo view($this->controller.'/brandDetails',$data);

	 }

	  public function productDetails($proid){

	  	$data["brandDetails"] = $this->HomeModel->BrandDetails();

    	 $data["proDetails"] = $this->HomeModel->getproductDetails($proid);

    	echo view($this->controller.'/productDetails',$data);

	 }

	 

	 public function contactus(){

 	$data["brandDetails"] = $this->HomeModel->BrandDetails();

    	echo view($this->controller.'/contactus',$data);

	 }

public function sendmail()
{
    $email = \Config\Services::email();
    $request = service('request');
    $data = $request->getPost();

    // GoDaddy SMTP FIX
    $email->initialize([
        'protocol'   => 'smtp',
        'SMTPHost'   => 'smtpout.secureserver.net',
        'SMTPPort'   => 465,
        'SMTPUser'   => 'support@vedantlights.com',
        'SMTPPass'   => 'vedantlights2026',
        'SMTPCrypto' => 'ssl',
        'mailType'   => 'html',
        'charset'    => 'utf-8',
        'newline'    => "\r\n",
        'CRLF'       => "\r\n"
    ]);

    // EMAIL BODY
    $body = "
        <h3>New Contact Form Inquiry</h3>
        <p><b>Name:</b> {$data['name']}</p>
        <p><b>Email:</b> {$data['email']}</p>
        <p><b>Subject:</b> {$data['subject']}</p>
        <p><b>Message:</b><br>{$data['message']}</p>
    ";

    $email->setTo('sudhakarpoul@vedantlights.com');
    $email->setFrom('support@vedantlights.com', 'Vedant Lights');
    $email->setSubject('New Website Inquiry');
    $email->setMessage($body);

    if ($email->send()) {
        $this->session->setFlashdata('success', 'Mail sent successfully.');
        return redirect()->to(base_url('contactus'));
    } else {
        echo "<pre>";
        print_r($email->printDebugger());
        echo "</pre>";
    }
}
