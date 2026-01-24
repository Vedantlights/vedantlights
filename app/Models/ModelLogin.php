<?php 
namespace App\Models;

use CodeIgniter\Model;
class ModelLogin extends Model {
	public function checkLogin($data) {
	return $this->db->table('user_details')->where('username',$data["user_name"])->where('password',$data["password"])->get()->getRow();

	}
	function totalProductDetails(){
		return $this->db->table('product_details')->get()->getNumRows();
	}
	function totalCategoryDetails(){
		return $this->db->table('category_details')->get()->getNumRows();
	}
	function totalBrandDetails(){
		return $this->db->table('brand_details')->get()->getNumRows();
	}
	
}