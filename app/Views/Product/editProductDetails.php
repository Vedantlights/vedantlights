<?= $this->include('header'); ?>

<div class="page">

  <div class="container-fluid">

    <div class="row clearfix">

      <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="card">

          <div class="header">

            <h2>Update Product</h2>

          </div>

         <div class="col-lg-6 col-md-6 col-sm-6">

          <?= $this->include('message') ?>

        </div>

          <div class="col-md-12 ">

            <a href="<?php echo base_url(); ?>ProductDetails" class="btn save_btn active pull-right" >Back</a>

          </div>

          <div class="body">

            <form action="<?php echo base_url(); ?>Product/updateProductdetails" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="prodId" id="prodId" value="<?php echo $productDetails->pro_id;?>">
              <input type="hidden" name="old_image" id="old_image" value="<?php echo $productDetails->pro_img;?>">
              <div class="row clearfix">

                <div class="col-lg-4 col-md-6">

                  <div class="mb-4">

                    <p>Brand Name: &nbsp;<b class="astrick">*</b></p>

                    <select name="brand_name" id="brand_name"  class="form-control" onchange="getCategoryDetails(this.value)">

                      <option value="">Select Brand Name</option>

                     <?php

                    if(!empty($brandDetails)){

                    foreach($brandDetails as $val){
                      
                      if ($val['brand_id'] == $productDetails->brand_id)
                        $selected = "selected";
                      else
                        $selected = "";
                    ?> 

                      <option <?php echo $selected;?>  value="<?php echo $val["brand_id"]; ?>"><?php echo $val["brand_name"]; ?></option> 

                  <?php } } ?>

                    </select>

                    <span id="validation_brand_name"></span>

                  </div>

                </div>

              <div class="col-lg-4 col-md-6">

                  <div class="mb-4">

                    <p>Category Name: &nbsp;<b class="astrick">*</b></p>

                    <select  name="category_name" id="category_name"  class="form-control">

                       <option value="">Select Category Name</option>

                    </select>

                    <span id="validation_category_name"></span>

                  </div>

                </div>

                 <div class="col-lg-4 col-md-6">

                  <div class="mb-4">

                    <p>Product Name: &nbsp;<b class="astrick">*</b></p>

                     <input type="text"  name="product_name" id="product_name"  class="form-control" placeholder="Enter Product Name" value="<?php echo $productDetails->pro_name;?>">

                   <span id="validation_product_name"></span>

                  </div>

                </div>

              </div>

                <div class="row clearfix">

               <div class="col-lg-4 col-md-6">

                  <div class="mb-4">

                    <p>Product Technical Specification: &nbsp;<b class="astrick">*</b></p>

                     
                    <textarea type="textarea"   name="product_tech" id="product_tech"  class="form-control summernote"   style="width:300px; height:550px;"><?php echo $productDetails->pro_tech;?></textarea>
                   <span id="validation_product_tech"></span>

                  </div>

                </div>

                 <div class="col-lg-4 col-md-6">

                  <div class="mb-4">

                    <p>Product Description: &nbsp;<b class="astrick">*</b></p>


                     <textarea type="textarea"   name="product_desc" id="product_desc"  class="form-control summernote" style="width:300px; height:450px;"><?php echo $productDetails->pro_desc;?></textarea>
                   <span id="validation_product_desc"></span>

                  </div>

                </div>

                 <div class="col-lg-4 col-md-6">

                    <div class="mb-4">

                      <p>Product  Image: &nbsp;<b class="astrick">*</b></p>

                      <input type="file"  name="product_img" id="product_img" >
                      <?php if ($productDetails->pro_img != '') { ?>
                      <img src="https://www.vedantlights.com/uploads/Product/<?php echo $productDetails->pro_img; ?>" width="50px" height="50px">
                      <?php } ?>
                    </div>

                    <span id="validation_product_img"></span>

                 </div>

              </div>

          </div>



          <div class="row">

          <div class="col-lg-12 col-md-6">

          <div class="mb-12">

          <center>

          <button type="submit" class="btn save_btn active" onclick="return validate();">Save</button>

          </center>

          </div>

          </div>

          <br><br>

          <br><br>

          </div>

          </form>

        </div>

      </div>

    </div>

   

  </div>

</div>

<?= $this->include('footer'); ?>

<script>

  $(function() {

  $('#category_name').keydown(function (e) {

      if (e.shiftKey || e.ctrlKey || e.altKey) {

      e.preventDefault();

      } else {

      var key = e.keyCode;

      if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {

      e.preventDefault();

      }

    }

    });

  });


 $(document).ready(function() {

          $('.summernote').summernote();
          getCategoryDetails('<?php echo $productDetails->brand_id;?>');
    });
  
  function getCategoryDetails(brandId){

     $.ajax({

         url:'<?php echo base_url();?>Product/CategoryDetails',

         type:'POST',

        data:{'brandId':brandId},

       success: function(data)

       {

        var option ='<option value="">Select Category Name</option>';

        var obj=JSON.parse(data);

        var catid = '<?php echo $productDetails->catId;?>';
       if(obj.StatusCode==101) {

         for (i = 0; i < obj.categoryDetails.length; i++){

            if (catid == obj.categoryDetails[i].cat_id)
                option +='<option selected="selected" value="'+ obj.categoryDetails[i].cat_id +'">'+ obj.categoryDetails[i].caterogyName +'</option> ';
            else
              option +='<option value="'+ obj.categoryDetails[i].cat_id +'">'+ obj.categoryDetails[i].caterogyName +'</option> ';
          }

         $("#category_name").html(option);

         }else{

        $("#category_name").html(option);

        }

  

       }

  

   });

  }

  function validate()

  {

     $("#validation_brand_name").fadeOut(1000);   

      $("#validation_category_name").fadeOut(1000);   

      $("#validation_product_name").fadeOut(1000);   

      $("#validation_product_img").fadeOut(1000);   

      $("#validation_product_tech").fadeOut(1000);   

       $("#validation_product_desc").fadeOut(1000);   

      var brand_name=$("#brand_name").val();  

      var category_name=$("#category_name").val();  

      var product_name=$("#product_name").val();  

       var product_img=$("#product_img").val(); 

       var product_desc=$("#product_desc").val();  

        var product_tech=$("#product_tech").val();   

     var error=0;

     if(brand_name==""){

       $("#validation_brand_name").fadeIn(2000);

       $("#validation_brand_name").html("Select The Brand  Name");

       $("#validation_brand_name").css("color","red");

       error=1;

     }

     if(category_name==""){

       $("#validation_category_name").fadeIn(2000);

       $("#validation_category_name").html("Select The Category  Name");

       $("#validation_category_name").css("color","red");

       error=1;

     }

     if(product_name==""){

       $("#validation_product_name").fadeIn(2000);

       $("#validation_product_name").html("Enter The Product Name");

       $("#validation_product_name").css("color","red");

       error=1;

     }

     if(product_tech==""){

       $("#validation_product_tech").fadeIn(2000);

       $("#validation_product_tech").html("Enter The Product Technical Specification");

       $("#validation_product_tech").css("color","red");

       error=1;

     }

      if(product_desc==""){

       $("#validation_product_desc").fadeIn(2000);

       $("#validation_product_desc").html("Enter The Product Description");

       $("#validation_product_desc").css("color","red");

       error=1;

     }

     

    if(error==0)

     return true;

   else return false;

  }

</script>