<?php 
// error_reporting(0); 
require('check.php');
$pageurl_re      = "order$extn";
$pageurl      = "addorder$extn";
$tblname      = "orders";
$pagename     = "Add Order";
$sessionid    = date("ymdHis"); 

// Get POST Values
$city_id    = get_safe_value($con,@$_POST['city_id']);
$user_id    = get_safe_value($con,@$_POST['user_id']);
$address    = get_safe_value($con,@$_POST['address']);
$category_id    = get_safe_value($con,@$_POST['category_id']);
$subcategory_id    = get_safe_value($con,@$_POST['subcategoryid']);
$subsubcategory_id    = get_safe_value($con,@$_POST['subsubcategoryid']);
$serviceid    = get_safe_value($con,@$_POST['serviceid']);
$qty    = get_safe_value($con,@$_POST['qty']);
$order_date    = get_safe_value($con,@$_POST['order_date']);
$order_time1    = get_safe_value($con,@$_POST['order_time']);

// INSERT INTO `orders_detail`(`id`, `type`, `order_id`, `category_id`, `subcategory_id`, `subsubcategory_id`, `service_id`, `qty`, `ori_price`, `dis_price`) 
// Row Update Query 
if(@$_GET['mode']=="addnew" ){

  
    $query_order = "SELECT * FROM `orders` WHERE orders.user_id='".$user_id."' ";

    $sql_order = mysqli_query($con,$query_order)or die(mysqli_error($con));
    $num_rows_order = mysqli_num_rows($sql_order);

    if($num_rows_order > 0)
    {
        $repeat_user = 1;
    }else{
        $repeat_user = 0;
    }

    $sql_city = "SELECT * FROM city  WHERE city.status = 1 and city.ID='".$city_id."'  ORDER BY city.ID DESC";
    $result_city = mysqli_query($con,$sql_city);  
    $row_city = mysqli_fetch_array($result_city);
    $convenience_fee = $row_city['convenience_fee'];
    $safety_charge = $row_city['safety_charge'];


    $order_otp=rand(1000,9999);

    $qry1="INSERT INTO `orders`(`repeat_user`,`user_id`,`city_id`,`address`, `payment_type`,`conveyance_charges`,`safety_hygiene_charges`,  `payment_status`, `order_status`,`added_on` ,`order_date`, `order_time`,`order_otp`)
		    VALUES ('".$repeat_user."','".$user_id."',$city_id,'".$address."','1','".$convenience_fee."','".$safety_charge."','1','1','$now','".$order_date."','".$order_time1."','".$order_otp."')"; 
			                        
    $result1 = mysqli_query($con,$qry1);

    $last_id = mysqli_insert_id($con);  

    if(!empty($last_id))
	{   
        $sql_service = "SELECT * FROM services  WHERE services.hide = 0 and services.ID='".$serviceid."'  ORDER BY services.ID DESC";
        $result_service = mysqli_query($con,$sql_service);  
        $row_services = mysqli_fetch_array($result_service);
        $services_ori_price = $row_services['original_amount'];
        $services_dis_price = $row_services['discount_amount'];
    
        $total_o_dis_price = $services_dis_price * $qty;
        $total_o_ori_price = $services_ori_price * $qty;

        $final_amount = $total_o_dis_price + $convenience_fee + $safety_charge;
        // exit;
        $sql = "INSERT INTO `orders_detail`(`type`, `order_id`, `category_id`, `subcategory_id`, `subsubcategory_id`, `service_id`, `qty`, `ori_price`, `dis_price`) 
        VALUES ('1', '$last_id', '$category_id', '$subcategory_id', '$subsubcategory_id', '$serviceid', '$qty', '$services_ori_price','$services_dis_price')";
        if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }
    
        $sql_order="UPDATE `orders` SET `ori_price`='$total_o_ori_price', `dis_price`='$total_o_dis_price' , `final_price`='$final_amount'  WHERE id = $last_id";
        if (!mysqli_query($con,$sql_order)){die('Error: ' . mysqli_error($con)); }
   
        
    }

  
    echo "<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl_re?msg=$pagename%20Upload%20Successfully...'>";  exit(0);

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
              
                <form role="form" method="POST" action="<?= $pageurl; ?>?mode=addnew" enctype="multipart/form-data">
              
                <div class="box-body">
                    <div class="form-group col-md-6">
                        <label> City Name <span class="text-red">*</span></label>
                        <select class="form-control" name="city_id" required id="city_id" required>
                            <option class="text-blue bold">Select City</option>
                            <?php  
                            $res = mysqli_query($con,"SELECT ID,name FROM `city` where city.status=1 ORDER BY ID ASC");
                            while ($row = mysqli_fetch_array($res)) {
                               
                                echo "<option value=".$row['ID'].">".$row['name']."</option>";
                                
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label> User Name <span class="text-red">*</span></label>
                        <select class="form-control" name="user_id" required id="user_id">
                            <option class="text-blue bold">Select User</option>
                            <?php  
                            $res = mysqli_query($con,"SELECT ID,name,mobile FROM `user_registration` where user_registration.status=1 ORDER BY ID ASC");
                            while ($row = mysqli_fetch_array($res)) {
                               
                                echo "<option value=".$row['ID'].">".$row['name'].' / '.$row['mobile']."</option>";
                                
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label> Address Details <span class="text-red">*</span></label>
                        <select class="form-control" name="address" required id="address">
                            <option class="text-blue bold">Select Address</option>
                            <?php  
                            $res = mysqli_query($con,"SELECT houser_no,lendmark,adderss,ID FROM `address` where address.status=1 ORDER BY ID ASC");
                            while ($row = mysqli_fetch_array($res)) {
                   
                                echo "<option value=".$row['ID'].">".$row['houser_no'].', '.$row['lendmark'].', '.$row['adderss']."</option>";
                                
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Category <span class="text-red">*</span></label>
                        <select class="form-control" name="category_id" required id="category_id">
                            <option class="text-blue bold">Select Category</option>
                            <?php  
                            $res = mysqli_query($con,"SELECT ID,category FROM `category` where category.hide=0 ORDER BY ID ASC");
                            while ($row = mysqli_fetch_array($res)) {
               
                                echo "<option value=".$row['ID'].">".$row['category']."</option>";
                              
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
                                    
                                        echo "<option value=".$row['ID'].">".$row['subcategory']."</option>";
                                    
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
                                    
                                        echo "<option value=".$row['ID'].">".$row['subsubcategory_name']."</option>";
                                    
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
                                   
                                        echo "<option value=".$row['ID'].">".$row['title']."</option>";
                                    
                                    }
                                }
                            ?>
                        </select>
                    </div>
                        
                    <div class="form-group col-md-6">
                        <label>Service Qty <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="qty" name="qty" value="<?= @$get_mobile; ?>" placeholder="Enter Service quantity" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Order Date <span class="text-red">*</span></label>
                        <input type="date" class="form-control" id="order_date" name="order_date"  placeholder="Enter Order Date" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Order Slot <span class="text-red">*</span></label>
                        <select class="form-control" id="order_time" name="order_time" required>
                            <option class="text-blue bold">Select Slot </option>
            
                                <?php
                                            
                                // $qry1="SELECT * FROM `timeslot` ORDER BY `timeslot`.`ID` ASC";
                                // $results1=mysqli_query($con,$qry1);
                                //     while ($row1=mysqli_fetch_array($results1)) 
                                //     {

                                    ?>
                                    
                                    <!-- <option value="<?php echo $row1['timeslot'];?>" >  <?php echo $row1['timeslot'];?> </option> -->
                                
                                <?php //}
                                            
                                ?>

                        </select>
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
    // console.log("SubCategory ID: "+ get_scid);

      var id=jQuery(this).val();
    //   console.log("Category ID: "+ id);
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
    // console.log("SubSubCategory ID: "+ get_sscid);

      var id=jQuery(this).val();
    //   console.log("Category ID: "+ id);
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
    // console.log("subsubcategoryid ID: "+ get_ssscid);

      var id=jQuery(this).val();
    //   console.log("Category ID: "+ id);
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

<script>
  $(document).ready(function(){
    jQuery('#user_id').change(function(){

      var id=jQuery(this).val();
    //   alert(id);
    //   console.log("user ID: "+ id);
      if(id=='-1'){
        jQuery('#address').html('<option value="-1">Select Address </option>');
      }else{
        $("#divLoading").addClass('show');
        jQuery('#address').html('<option value="-1">Select Address </option>');
        jQuery.ajax({
          type:'post',
          url:'get_data6.php?mode=addressdetails',
          data:'id='+id,
          // data:'id='+id,
          success:function(result){
            $("#divLoading").removeClass('show');
            jQuery('#address').append(result);
          }
        });
      }
    });
  });
</script>

<script>
  $(document).ready(function(){
    jQuery('#order_date').change(function(){

      var date = jQuery(this).val();
      var city = $("#city_id option:selected").val();  

      alert(date);
      alert(city);
    //   console.log("user ID: "+ id);
      if(date=='-1'){
        jQuery('#order_time').html('<option value="1">Select Timeslot </option>');
      }else{
        $("#divLoading").addClass('show');
        jQuery('#order_time').html('<option value="1">Select Timeslot </option>');
        jQuery.ajax({
          type:'post',
          url:'get_data7.php?mode=timeslotdetails',
        //   data:'id='+id,
          data: 'date='+date+'&city='+city,
          success:function(result){
            alert(result);
            $("#divLoading").removeClass('show');
            jQuery('#order_time').append(result);
          }
        });
      }
    });
  });
</script>

</body>
</html>