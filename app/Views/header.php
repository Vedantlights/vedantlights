<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Vedantlights">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
     <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/jquery-datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/css/main.css" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/css/style.css" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/css/summernote.css" rel="stylesheet">

</head>
  <body class="theme-indigo">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
      <div class="loader">
        <div class="m-t-30"><img src="<?php echo base_url(); ?>admin_asset/images/brand/icon_black.svg" width="48" height="48" alt="ArrOw"></div>
        <p>Please wait...</p>
      </div>
    </div>
    <nav class="navbar custom-navbar navbar-expand-lg py-2">
      <div class="container-fluid px-0">
        <a href="javascript:void(0);" class="menu_toggle"><i class="fa fa-align-left"></i></a>
        <a href="#" class="navbar-brand"><strong></strong> &nbsp;</a>
        <div id="navbar_main">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
              <a class="nav-link nav-link-icon" href="#" id="navbar_1_dropdown_3" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                class="fa fa-user"></i></a>
              <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href=""><i class="fa fa-sign-out text-primary"></i>Sign
                out</a>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="main_content" id="main-content">
    <div class="left_sidebar">
      <nav class="sidebar">
        <div class="user-info">
          <div class="detail mt-3">
            <h5 class="mb-0">Admin</h5>
            <small>Admin</small>
          </div>
        </div>
        <ul id="main-menu" class="metismenu">
          <li><a href="<?php echo base_url(); ?>dashboard"><i class="ti-home"></i><span>Dashboard</span></a></li>
            <li><a href="<?php echo base_url(); ?>Brand"><i class="ti-home"></i><span>Brand Deatils</span></a></li>
            <li><a href="<?php echo base_url(); ?>Category"><i class="ti-home"></i><span>Category Deatils</span></a></li>
           <li><a href="<?php echo base_url(); ?>Product"><i class="ti-home"></i><span> Add Product</span></a></li>
           <li><a href="<?php echo base_url(); ?>ProductDetails"><i class="ti-home"></i><span>Product Details</span></a></li>
            
       </ul>
      </nav>
    </div>
    