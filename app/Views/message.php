 <?php
    $session = \Config\Services::session();
  if($session->getFlashdata('success'))
 { ?>
 <div class="alert alert-success catalrt text-center">
<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×
</button>
<?php echo $session->getFlashdata('success');
  
 ?>
 </div>
 <?php }else if($session->getFlashdata('fail')){ ?>
<div class="alert alert-danger catalrt text-center">
<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×
</button>
<?php echo $session->getFlashdata('fail');
 ?>
</div>
<?php } ?>