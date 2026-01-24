<?= $this->include('header'); ?>
<div class="page">
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
          <div class="header">
            <h2>Add New Category</h2>
          </div>
         <div class="col-lg-6 col-md-6 col-sm-6">
            <?= $this->include('message') ?>
          </div>
          <div class="col-md-12 ">
            <a href="<?php echo base_url(); ?>Category" class="btn save_btn active pull-right" >Back</a>
          </div>
          <div class="body">
            <form action="<?php echo base_url(); ?>updateCategorydetails" method="POST" enctype="multipart/form-data">
              <div class="row clearfix">
                <div class="col-lg-4 col-md-6">
                  <div class="mb-4">
                    <p>Brand Name: &nbsp;<b class="astrick">*</b></p>
                    <select name="brand_name" id="brand_name"  class="form-control">
                      <option value="">Select Brand</option>
                     <?php
                    if(!empty($brandDetails)){
                    foreach($brandDetails as $val){
                      if($val["brand_id"] == $categoryDetails->brandId){
                    ?> 
                      <option value="<?php echo $val["brand_id"]; ?>" selected><?php echo $val["brand_name"]; ?></option> 
                    <?php }else{ ?>
                       <option value="<?php echo $val["brand_id"]; ?>"><?php echo $val["brand_name"]; ?></option> 

                  <?php } } } ?>
                    </select>
                    <span id="validation_brand_name"></span>
                  </div>
                </div>
                  <div class="col-lg-4 col-md-6">
                  <div class="mb-4">
                    <p>Category Name: &nbsp;<b class="astrick">*</b></p>
                    <input type="text"  name="category_name" id="category_name"  class="form-control" placeholder="Enter Category   Name" value="<?php if(!empty($categoryDetails->caterogyName)) echo $categoryDetails->caterogyName; ?>">
                    <span id="validation_category_name"></span>
                  </div>
                </div>
              </div>
          </div>
          <input type="hidden" name="catId" value="<?php if(!empty($categoryDetails->cat_id)) echo $categoryDetails->cat_id; ?>">
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
  function validate()
  {
     $("#validation_brand_name").fadeOut(1000);   
      $("#validation_category_name").fadeOut(1000);   
      var brand_name=$("#brand_name").val();  
      var category_name=$("#category_name").val();  
     var error=0;
     if(brand_name==""){
       $("#validation_brand_name").fadeIn(2000);
       $("#validation_brand_name").html("Select The Brand  Name");
       $("#validation_brand_name").css("color","red");
       error=1;
     }
     if(category_name==""){
       $("#validation_category_name").fadeIn(2000);
       $("#validation_category_name").html("Enter The Category  Name");
       $("#validation_category_name").css("color","red");
       error=1;
     }
     
    if(error==0)
     return true;
   else return false;
  }
</script>