<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="ThemeMakker">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/themify-icons/themify-icons.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>admin_asset/css/main.css">
</head>

<body class="theme-indigo">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30"><img src="<?php echo base_url(); ?>admin_asset/images/brand/icon_black.svg" width="48" height="48" alt="ArrOw"></div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="vertical-align-wrap">
            <div class="vertical-align-middle auth-main">
                <div class="auth-box">
                    <span id="error_div"></span>
                    <div class="card">
                        <div class="top">
                        <strong>-</strong> <span>-</span>
                    </div>
                        <div class="header">
                            <p class="lead">Login to your account</p>
                        </div>
                        <div class="body">
                            <form id="sign_in" class="form-auth-small">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="username" id="username" placeholder="Enter The Username">
                                    <span id="validation_username"></span>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" id="password"  name="password" placeholder="Enter The Password">
                                      <span id="validation_password"></span>
                                </div>
                             
                                <center><button type="submit" id="btn_generate" class="btn btn-primary btn-lg btn-block">LOGIN</button></center>
                           
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
<script src="<?php echo base_url(); ?>admin_asset/bundles/libscripts.bundle.js"></script>
<script src="<?php echo base_url(); ?>admin_asset/bundles/vendorscripts.bundle.js"></script>

<script src="<?php echo base_url(); ?>admin_asset/js/theme.js"></script>
<script>
$(document).ready(function(){
  $("#sign_in")[0].reset();
  $("#btn_generate").click(function(e)
  {
    e.preventDefault();
    $("#validation_username").fadeOut(1000);
    $("#validation_password").fadeOut(1000);
     var username = $("#username").val();
     var password = $("#password").val();
      var error=0;
    if(username =="")
   {
    $("#validation_username").fadeIn(2000);
    $("#validation_username").html("Enter The Username");
    $("#validation_username").css("color","red");
   error=1;
   } if(password == "")
   {
    $("#validation_password").fadeIn(1000);
    $("#validation_password").html("Enter The Password");
    $("#validation_password").css("color","red");
   error=1;
   } 
   if(error==0){
       $.ajax({
        url: '<?php echo base_url(); ?>Login/check_login', 
        data: {"user_name":username,"password":password}, 
        type: "post",
        beforeSend: function( xhr ) 
        {
         $(".loader").show();
         $("body").css("opacity","0.5");
         $(".loader").css("z-index","999999");
        },
        success: function (response) 
        {
         $("#error_div").show().delay(1000).fadeOut();
          var response =JSON.parse(response);
          if(response.txt_code==101)
          {
           $("#error_div").html('<div class="alert alert-success catalrt"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>'+ response.message +'</div>');
           window.setTimeout(function() {window.location.href = 'dashboard';
                                    }, 1500);
           }else{
           $("#error_div").html('<div class="alert alert-danger catalrt"><button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>'+ response.message +'</div>');
          
          }
        }
        });
}
   });
});
</script>
</body>

</html>
