<?php 

namespace App\Models;

use CodeIgniter\Model;



class CategoryModel extends Model{

 	function addCategorydetails($data) {

		return $this->db->table('category_details')->insert($data);

	}

	function CategoryDetails(){

		  return $this

                    ->db

                    ->table('category_details cat')

                    ->select()

                    ->join('brand_details AS brand','cat.brandId = brand.brand_id')

                    ->orderBy('cat_id',"DESC")

                    ->get()

                    ->getResultArray();

	}

	function BrandDetails(){

		return $this->db->table('brand_details')->where('is_delete',0)->orderBy("brand_id","DESC")->get()->getResultArray();

	}

	function chkexitsCategoryDetails($data){

		return $this->db->table('category_details')->where('brandId',$data["brand_name"])->where('caterogyName',$data["category_name"])->get()->getNumRows();

	}

	function getCategoryDetails($catId){

		return $this->db->table('category_details')->where('cat_id',base64_decode($catId))->get()->getRow();

	}

	public function updateCategorydetails($data,$id) {

		return $this->db

                        ->table('category_details')

                        ->where(["cat_id" => $id])

                        ->set($data)

                        ->update();

	}

	public function deletecategory($id) {
		return $this->db->table('category_details')->where('cat_id',$id)->delete();
		
	}

	

}