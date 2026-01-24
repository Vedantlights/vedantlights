<?= $this->include('header'); ?>

<div class="page">

  <div class="container-fluid">

    <div class="row clearfix">

      <div class="col-lg-12 col-md-12 col-sm-12">

        <div class="card">

          <div class="header">

            <h2>Add New Product</h2>

          </div>

          <div class="col-lg-6 col-md-6 col-sm-6">

          </div>

          <div class="col-lg-6 col-md-6 col-sm-6 align-items-center">

          </div>

          <div class="col-md-12 ">

            <a href="<?php echo base_url(); ?>Product" class="btn save_btn active pull-right" >Add New Product</a>

          </div>

         <div class="body">

            <div class="table-responsive">

              <table class="table table-bordered table-hover dataTable js-exportable">

                <thead>

                  <tr>

                    <th>Sr No.</th>

                    <th>Brand Name</th>

                     <th>Category Name</th> 

                     <th>Product Name</th> 

                     <!--<th>Product Technical Specification</th> 

                     <th>Product Description </th>--> 

                     <th>Product Image</th> 

                    <th>Action</th>

                  </tr>

                </thead>

                <tbody>

                  <?php

                    $sr=1;

                    if(!empty($productDetails)){

                    foreach($productDetails as $val){

                    ?>

                  <tr>

                    <td><?php echo $sr++; ?></td>

                    <td><?php echo $val["brand_name"]; ?></td>

                    <td><?php echo $val["caterogyName"]; ?></td>

                    <td><?php echo $val["pro_name"]; ?></td>

                   <!-- <td><?php echo $val["pro_tech"]; ?></td>

                    <td><?php echo $val["pro_desc"]; ?></td>-->

                    <td>

                      <?php if ($val["pro_img"] != '') { ?>
                      <img src="<?php base_url(); ?>uploads/Product/<?php echo $val["pro_img"]; ?>" width="50px" height="50px">
                      <?php } ?>
                    </td>

                     <td>

                      <div class="dt-buttons btn-group"><a href="<?php echo base_url(); ?>editProductDetails/<?php echo base64_encode($val["pro_id"]); ?>"><button type="button" class="btn btn-success btn-icon-only"><span class="btn-inner--icon"><i class="fa fa-pencil-square-o"></i></span></button></a>&nbsp;&nbsp; | &nbsp;&nbsp; <a href="<?php echo base_url(); ?>Product/deleteproduct/<?php echo base64_encode($val["pro_id"]); ?>"><button type="button" class="btn btn-danger btn-icon-only"><span class="btn-inner--icon"><i class="fa fa-trash-o"></i></span></button></a></div>

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

