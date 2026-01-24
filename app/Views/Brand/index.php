<?= $this->include('header'); ?>
<div class="page">
  <div class="container-fluid">
    <div class="row clearfix">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
          <div class="header">
            <h2>Add New Brand</h2>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-6">
              <?= $this->include('message') ?>
            </div>
          <div class="col-lg-6 col-md-6 col-sm-6 align-items-center">
          </div>
          <div class="col-md-12 ">
            <a href="<?php echo base_url(); ?>Brand" class="btn save_btn active pull-right" >Back</a>
          </div>
          <div class="body">
            <form action="<?php echo base_url(); ?>addBranddetails" method="POST" enctype="multipart/form-data">
              <div class="row clearfix">
                <div class="col-lg-4 col-md-6">
                  <div class="mb-4">
                    <p>Brand Name: &nbsp;<b class="astrick">*</b></p>
                    <input type="text"  name="brand_name" id="brand_name"  class="form-control" placeholder="Enter Brand  Name" >
                    <span id="validation_brand_name"></span>
                  </div>
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
    <div class="row clearfix">
      <div class="col-lg-12">
        <div class="card">
          <div class="header">
            <h2><strong>BRAND</strong>&nbsp;DETAILS</h2>
          </div>
          <div class="body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover dataTable js-exportable">
                <thead>
                  <tr>
                    <th>Sr No.</th>
                    <th>Brand Name</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sr=1;
                    if(!empty($brandDetails)){
                    foreach($brandDetails as $val){
                    ?>
                  <tr>
                    <td><?php echo $sr++; ?></td>
                    <td><?php echo $val["brand_name"]; ?></td>
                     <td>
                      <div class="dt-buttons btn-group"><a href="<?php echo base_url(); ?>editBrand/<?php echo base64_encode($val["brand_id"]); ?>"><button type="button" class="btn btn-success btn-icon-only"><span class="btn-inner--icon"><i class="fa fa-pencil-square-o"></i></span></button></a>&nbsp;&nbsp; | &nbsp;&nbsp; <a href="<?php echo base_url(); ?>editCategory/<?php echo base64_encode($val["brand_id"]); ?>"><button type="button" class="btn btn-danger btn-icon-only"><span class="btn-inner--icon"><i class="fa fa-trash-o"></i></span></button></a></div>
                    </td>
                  </tr>
                  <?php } }?>
                </tbody>
              </table>
            </div>
          </div>
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