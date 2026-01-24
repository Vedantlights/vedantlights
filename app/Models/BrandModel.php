<?php 
namespace App\Models;
use CodeIgniter\Model;

class BrandModel extends Model{
   function addBranddetails($data) {
		return $this->db->table('brand_details')->insert($data);
	}
	function chkexitsBrandDetails($brandname){
		return $this->db->table('brand_details')->where('brand_name',$brandname)->get()->getNumRows();
	}
	function BrandDetails(){
		return $this->db->table('brand_details')->where('is_delete',0)->orderBy("brand_id","DESC")->get()->getResultArray();
	}
	function getBrandDetails($brandId){
		return $this->db->table('brand_details')->where('brand_id',base64_decode($brandId))->get()->getRow();
	}
	public function updateBranddetails($data,$id) {
		return $this->db
                        ->table('brand_details')
                        ->where(["brand_id" => $id])
                        ->set($data)
                        ->update();
	}
	
}