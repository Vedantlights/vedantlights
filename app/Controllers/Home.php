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

	 public function aboutus(){

 	$data["brandDetails"] = $this->HomeModel->BrandDetails();

    	echo view($this->controller.'/aboutus',$data);

	 }

	 public function categorywiseProductdetails($brandId,$catId){

 	$data["brandId"] = $brandId;

 	$data["catId"] = $catId;

 	$data["brandDetails"] = $this->HomeModel->BrandDetails();

 	$data["catDetails"] = $this->HomeModel->getCategoryDetails($brandId);

 	$data["proDetails"] = $this->HomeModel->getCategoryproductDetails($brandId,$catId);

    	echo view($this->controller.'/brandDetails',$data);

	 }

public function sendmail()
{
    $email = \Config\Services::email();
    $request = service('request');
    $data = $request->getPost();

    // Add validation and safety checks
    $name = $data['name'] ?? '';
    $userEmail = $data['email'] ?? '';
    $subject = $data['subject'] ?? 'No Subject';
    $message = $data['message'] ?? '';

    // Log the received data for debugging
    log_message('debug', 'Contact form data received: ' . json_encode($data));
    log_message('debug', "Processed data - Name: $name, Email: $userEmail, Subject: $subject, Message: $message");

    // Basic validation
    if (empty($name) || empty($userEmail) || empty($message)) {
        log_message('error', 'Contact form: Missing required fields');
        
        // Check if it's an AJAX request
        if ($request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Please fill all required fields.'
            ]);
        }
        
        $this->session->setFlashdata('error', 'Please fill all required fields.');
        return redirect()->to(base_url('contactus'));
    }

    $body = '<html><body style="font-family: Arial, sans-serif;">' . PHP_EOL;
    $body .= '<h3 style="color: #333;">New Contact Form Inquiry</h3>' . PHP_EOL;
    $body .= '<table style="border-collapse: collapse; width: 100%;">' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Name:</td><td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</td></tr>' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Email:</td><td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') . '</td></tr>' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Subject:</td><td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</td></tr>' . PHP_EOL;
    $body .= '<tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Message:</td><td style="padding: 8px; border: 1px solid #ddd;">' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '</td></tr>' . PHP_EOL;
    $body .= '</table>' . PHP_EOL;
    $body .= '</body></html>' . PHP_EOL;

    try {
        $email->setTo('sudhakarpoul@vedantlights.com');
        $email->setFrom('sudhakarpoul@vedantlights.com', 'Vedant Lights');
        $email->setSubject('New Website Inquiry: ' . $subject);
        $email->setMessage($body);

        if ($email->send()) {
            log_message('info', 'Contact form email sent successfully to: sudhakarpoul@vedantlights.com');
            
            // Check if it's an AJAX request
            if ($request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Thank you! Your message has been sent successfully.'
                ]);
            }
            
            $this->session->setFlashdata('success', 'Thank you! Your message has been sent successfully.');
            return redirect()->to(base_url('contactus'));
        } else {
            log_message('error', 'Contact form email failed: ' . $email->printDebugger());
            
            // Check if it's an AJAX request
            if ($request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Sorry, there was an error sending your message. Please try again later.',
                    'debug' => $email->printDebugger() // Only for debugging
                ]);
            }
            
            $this->session->setFlashdata('error', 'Sorry, there was an error sending your message. Please try again later.');
            return redirect()->to(base_url('contactus'));
        }
    } catch (Exception $e) {
        log_message('error', 'Contact form email exception: ' . $e->getMessage());
        
        // Check if it's an AJAX request
        if ($request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sorry, there was an error sending your message. Please try again later.'
            ]);
        }
        
        echo "<h3>Email Error:</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        exit;
    }
}
}