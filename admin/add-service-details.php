<?php 
require('check.php');
$pageurl_re      = "add-service-details$extn";
$pageurl      = "order-details$extn";
$tblname      = "orders_detail";
$pagename     = "Add Services";
$id           = @$_GET['order_id']; 
$img_required = 'required';
$sessionid    = date("ymdHis"); 

// Get POST Values
$category_id    = get_safe_value($con,@$_POST['category_id']);
$subcategory_id    = get_safe_value($con,@$_POST['subcategoryid']);
$subsubcategory_id    = get_safe_value($con,@$_POST['subsubcategoryid']);
$serviceid    = get_safe_value($con,@$_POST['serviceid']);
$qty    = get_safe_value($con,@$_POST['qty']);

  //Select query when we click on pencil for edit existing entry
  $sql = "SELECT *,orders_detail.ori_price as od_ori_price, orders_detail.dis_price as od_dis_price, orders.dis_price as o_dis_price, orders.ori_price as o_ori_price FROM `$tblname`
  left join orders on orders.id = orders_detail.order_id
  left join user_registration on user_registration.ID = orders.user_id
  WHERE orders.id ='$id'";
  $result = mysqli_query($con,$sql);
  $count = mysqli_num_rows($result);
  if($count>0){
    while($row = mysqli_fetch_array($result)) {
  
      $get_user = $row['name'];
      $get_qty = $row['qty'];
      $get_o_ori = $row['o_ori_price'];
      $conveyance_charges = $row['conveyance_charges'];
      $safety_hygiene_charges = $row['safety_hygiene_charges'];
      $final_price = $row['final_price'];
      $coupon_value = $row['coupon_value'];
      $get_o_dis = $row['o_dis_price'];
      
    }
  
}



// INSERT INTO `orders_detail`(`id`, `type`, `order_id`, `category_id`, `subcategory_id`, `subsubcategory_id`, `service_id`, `qty`, `ori_price`, `dis_price`) 
// Row Update Query 
if(@$_GET['mode']=="addnew" ){

    $sql_service = "SELECT * FROM services  WHERE services.hide = 0 and services.ID='".$serviceid."'  ORDER BY services.ID DESC";
    $result_service = mysqli_query($con,$sql_service);  
    $row_services = mysqli_fetch_array($result_service);
    $services_ori_price = $row_services['original_amount'];
    $services_dis_price = $row_services['discount_amount'];

    $total_o_dis_price = $services_dis_price * $qty;
    $total_o_ori_price = $services_ori_price * $qty;
    // echo $total_o_dis_price;
    $total_dis = $get_o_dis + $total_o_dis_price;
    $total_ori = $get_o_ori + $total_o_ori_price;


    $total_final_price = ($final_price + $total_o_dis_price);
    // exit;

    $sql = "INSERT INTO `orders_detail`(`type`, `order_id`, `category_id`, `subcategory_id`, `subsubcategory_id`, `service_id`, `qty`, `ori_price`, `dis_price`) 
    VALUES ('1', '$id', '$category_id', '$subcategory_id', '$subsubcategory_id', '$serviceid', '$qty', '$services_ori_price','$services_dis_price')";
    if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }

    $sql_order="UPDATE `orders` SET `ori_price`='$total_ori', `dis_price`='$total_dis' , `final_price`='$total_final_price'  WHERE id = $id";
    if (!mysqli_query($con,$sql_order)){die('Error: ' . mysqli_error($con)); }
   
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?order_id=$id&msg=$pagename%20Upload%20Successfully...'>";  exit(0);

}
// exit(0);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $pagename . " | $admintitle"; ?></title>
  <?php require('bootstrap.inc.php'); ?> 
  <?php require('css.inc.php'); ?> 
</head>
<?php require('skincolor.inc.php'); ?>


<div class="wrapper"> 
  <?php require('header.inc.php'); ?>
  <?php require('leftmenu.inc.php'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active"><?php echo $pagename; ?></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- SELECT2 EXAMPLE -->

        <div class="<?php echo $boxcolor; ?>">
            <div class="box-header with-border">
                <h3 class="box-title text-blue"><?php echo $pagename; ?></h3>
                <!-- <a href="<?=$pageurl.'?order_id='.$_GET['order_id'];?>"> <i class="fa fa-refresh text-green"></i></a> -->
            </div>
        <!-- /.box-header -->
            <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                <!-- form start -->
              
                <form role="form" method="POST" action="<?= $pageurl_re; ?>?mode=addnew&order_id=<?php echo $id; ?>" enctype="multipart/form-data">
              
                <div class="box-body">
                    <div class="form-group col-md-6">
                        <label> User Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="user_fullname" name="user_fullname" value="<?= @$get_user; ?>" placeholder="Enter User Name" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Category <span class="text-red">*</span></label>
                        <select class="form-control" name="category_id" required id="category_id">
                            <option class="text-blue bold">Select Category</option>
                            <?php  
                            $res = mysqli_query($con,"SELECT ID,category FROM `category` where category.hide=0 ORDER BY ID ASC");
                            while ($row = mysqli_fetch_array($res)) {
                                // echo 'c_id:'.$get_cid;
                                if($row['ID']==$get_cid){
                                echo "<option selected value=".$row['ID'].">".$row['category']."</option>";  
                                }
                                else{
                                echo "<option value=".$row['ID'].">".$row['category']."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Sub Category <span class="text-red">*</span></label>
                        <select class="form-control" name="subcategoryid" id="subcategoryid">
                            <option>Select SubCategory</option>
                            <?php
                            // echo $get_cid;
                                if( @$get_cid ) {
                                    $res = mysqli_query($con,"SELECT ID,subcategory FROM `subcategory` where subcategory.hide=0 and subcategory.ID='".$get_scid."' ORDER BY ID ASC");
                                    while ($row = mysqli_fetch_array($res)) {
                                    if($row['ID']==$get_scid){
                                        echo "<option selected value=".$row['ID'].">".$row['subcategory']."</option>";  
                                    }
                                    else{
                                        echo "<option value=".$row['ID'].">".$row['subcategory']."</option>";
                                    }
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Sub Sub Category <span class="text-red">*</span></label>
                        <select class="form-control" name="subsubcategoryid" id="subsubcategoryid">
                            <option>Select SubSubCategory</option>
                            <?php
                            // echo $get_cid;
                                if( @$get_scid ) {
                                    $res = mysqli_query($con,"SELECT ID,subsubcategory_name FROM `subsubcategory` where subsubcategory.hide=0 and subsubcategory.ID='".$get_sscid."' ORDER BY ID ASC");
                                    while ($row = mysqli_fetch_array($res)) {
                                    if($row['ID']==$get_sscid){
                                        echo "<option selected value=".$row['ID'].">".$row['subsubcategory_name']."</option>";  
                                    }
                                    else{
                                        echo "<option value=".$row['ID'].">".$row['subsubcategory_name']."</option>";
                                    }
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Service <span class="text-red">*</span></label>
                        <select class="form-control" name="serviceid" id="serviceid">
                            <option>Select Service</option>
                            <?php
                            // echo $get_cid;
                                if( @$get_sscid ) {
                                    $res = mysqli_query($con,"SELECT ID,title FROM `services` where services.hide=0 and services.ID='".$get_ssscid."' ORDER BY ID ASC");
                                    while ($row = mysqli_fetch_array($res)) {
                                    if($row['ID']==$get_ssscid){
                                        echo "<option selected value=".$row['ID'].">".$row['title']."</option>";  
                                    }
                                    else{
                                        echo "<option value=".$row['ID'].">".$row['title']."</option>";
                                    }
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Service Qty <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="qty" name="qty" value="<?= @$get_mobile; ?>" placeholder="Enter Service quantity" required>
                    </div>

                </div>

                
                <!-- /.box-body -->
                <div align="right" class="box-footer">

                    <button type="submit" class="<?= $btncolor; ?>"><i class="fa fa-save"></i>&nbsp; Save</button>
                   
                </div>
                </form>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
      
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<!-- footer include  -->
<?php require('footer.inc.php'); ?>
</div>
<!-- ./wrapper -->
<?php require('plugin.inc.php'); ?>
<?php require('script.inc.php'); ?>
<!-- Page script -->

<script>
  $(document).ready(function(){
    jQuery('#category_id').change(function(){

    var get_scid = "";
    console.log("SubCategory ID: "+ get_scid);

      var id=jQuery(this).val();
      console.log("Category ID: "+ id);
      if(id=='-1'){
        jQuery('#subcategoryid').html('<option value="-1">Select SubCategory </option>');
      }else{
        $("#divLoading").addClass('show');
        jQuery('#subcategoryid').html('<option value="-1">Select SubCategory </option>');
        jQuery.ajax({
          type:'post',
          url:'get_data.php?mode=subcategory',
          data:'id='+id+'&get_scid='+get_scid,
          // data:'id='+id,
          success:function(result){
            $("#divLoading").removeClass('show');
            jQuery('#subcategoryid').append(result);
          }
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function(){
    jQuery('#subcategoryid').change(function(){

    var get_sscid = "";
    console.log("SubSubCategory ID: "+ get_sscid);

      var id=jQuery(this).val();
      console.log("Category ID: "+ id);
      if(id=='-1'){
        jQuery('#subsubcategoryid').html('<option value="-1">Select SubSubCategory </option>');
      }else{
        $("#divLoading").addClass('show');
        jQuery('#subsubcategoryid').html('<option value="-1">Select SubSubCategory </option>');
        jQuery.ajax({
          type:'post',
          url:'get_data1.php?mode=subsubcategory',
          data:'id='+id+'&get_sscid='+get_sscid,
          // data:'id='+id,
          success:function(result){
            $("#divLoading").removeClass('show');
            jQuery('#subsubcategoryid').append(result);
          }
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function(){
    jQuery('#subsubcategoryid').change(function(){

    var get_ssscid = "";
    console.log("subsubcategoryid ID: "+ get_ssscid);

      var id=jQuery(this).val();
      console.log("Category ID: "+ id);
      if(id=='-1'){
        jQuery('#serviceid').html('<option value="-1">Select Service </option>');
      }else{
        $("#divLoading").addClass('show');
        jQuery('#serviceid').html('<option value="-1">Select Service </option>');
        jQuery.ajax({
          type:'post',
          url:'get_data5.php?mode=servicecategory',
          data:'id='+id+'&get_ssscid='+get_ssscid,
          // data:'id='+id,
          success:function(result){
            $("#divLoading").removeClass('show');
            jQuery('#serviceid').append(result);
          }
        });
      }
    });
  });
</script>

</body>
</html>