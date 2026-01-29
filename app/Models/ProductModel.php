<?php 

namespace App\Models;

use CodeIgniter\Model;



class ProductModel extends Model{

 	function BrandDetails(){

		return $this->db->table('brand_details')->where('is_delete',0)->orderBy("brand_id","DESC")->get()->getResultArray();

	}

function getCategoryDetails($brandId){

		return $this->db->table('category_details')->where('brandId',$brandId)->get()->getResultArray();

	}

	function chkexitsProductDetails($data){

		return $this->db->table('product_details')->where('brand_id',$data["brand_name"])->where('catId',$data["category_name"])->where('pro_name',$data["product_name"])->get()->getNumRows();

	}

	function getProductDetails($catId){

		return $this->db->table('product_details')->where('pro_id',base64_decode($catId))->get()->getRow();

	}

	function addProductdetails($data) {

		return $this->db->table('product_details')->insert($data);

	}

	function ProductDetails(){

		  return $this

                    ->db

                    ->table('product_details prod')

                    ->select('prod.*, cat.caterogyName, brand.brand_name')

                    ->join('category_details AS cat','cat.cat_id = prod.catId')

                     ->join('brand_details AS brand','brand.brand_id = prod.brand_id')

                    ->orderBy('prod.pro_id',"DESC")

                    ->get()

                    ->getResultArray();

	}

	public function updateProductdetails($data,$id) {

		return $this->db

                        ->table('product_details')

                        ->where(["pro_id" => $id])

                        ->set($data)

                        ->update();

	}
	public function deleteproduct($id) {
		return $this->db->table('product_details')->where('pro_id',$id)->delete();
		
	}
	

}