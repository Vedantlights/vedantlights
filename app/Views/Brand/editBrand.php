<?= $this->include('header'); ?>
<div class="page">
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
          <div class="header">
            <h2>Update Brand</h2>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6">
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6 align-items-center">
          </div>
          <div class="col-md-12 ">
            <a href="<?php echo base_url(); ?>Brand" class="btn save_btn active pull-right" >Back</a>
          </div>
          <div class="body">
            <form action="<?php echo base_url(); ?>updateBranddetails" method="POST" enctype="multipart/form-data">
              <div class="row clearfix">
                <div class="col-lg-4 col-md-6">
                  <div class="mb-4">
                    <p>Brand Name: &nbsp;<b class="astrick">*</b></p>
                    <input type="text"  name="brand_name" id="brand_name"  class="form-control" placeholder="Enter Brand  Name" value="<?php if(!empty($brandDetails->brand_name)) echo $brandDetails->brand_name; ?>">
                    <span id="validation_brand_name"></span>
                  </div>
                </div>
              </div>
          </div>
          <input type="hidden" name="brandId" value="<?php if(!empty($brandDetails->brand_id)) echo $brandDetails->brand_id; ?>">
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
  
  $('#brand_name').keydown(function (e) {
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
      var brand_name=$("#brand_name").val();  
     var error=0;
     if(brand_name=="")
     {
       $("#validation_brand_name").fadeIn(2000);
       $("#validation_brand_name").html("Enter The Brand  Name");
       $("#validation_brand_name").css("color","red");
       error=1;
     }
    if(error==0)
     return true;
   else return false;
  }
</script>