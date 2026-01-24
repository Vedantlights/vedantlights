<?php 
namespace App\Models;
use CodeIgniter\Model;

class HomeModel extends Model{
function BrandDetails(){
		return $this->db->table('brand_details')->where('is_delete',0)->orderBy("brand_id","DESC")->get()->getResultArray();
	}
	function getCategoryDetails($brandId){
		return $this->db->table('category_details')->where('brandId',$brandId)->orderBy("cat_id","DESC")->get()->getResultArray();
	}
	function getCategoryproductDetails($brandId,$catId){
		return $this->db->table('product_details')->where('brand_id',$brandId)->where('catId',$catId)->orderBy("pro_id ","DESC")->get()->getResultArray();
	}
	function getBrandProducts($brandId){
		return $this->db->table('product_details')->where('brand_id',$brandId)->orderBy("pro_id ","DESC")->get()->getResultArray();
	}
	function getproductDetails($proId){
		return $this->db->table('product_details')->where('pro_id',$proId)->get()->getRow();
	}
	
	
}