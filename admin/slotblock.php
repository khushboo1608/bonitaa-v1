<?php 
require('check.php');
$pageurl      = "slotblock$extn";
$tblname      = "timeslot_block";
$pagename     = "Slot block";
$id           = @$_GET['id']; 
$img_required = 'required';
$sessionid    = date("ymdHis"); 

// Get POST Values
$city_id    = get_safe_value($con,@$_POST['city_id']);
$date    = get_safe_value($con,@$_POST['tb_date']);
$timeslot = implode(',', (array)@$_POST['tb_timezone']);

// Insert new entry Query
if(@$_GET['mode']=="addnew"){
    // INSERT INTO `timeslot_block`(`tb_id`, `city_id`, `tb_timezone`, `tb_date`, `tb_cdate`, `tb_status`)
    $sql = "INSERT INTO `$tblname`(`city_id`, `tb_timezone`, `tb_date`,`tb_cdate`,`tb_status`) 
    VALUES ('$city_id', '$timeslot','$date','$now','1')";
    if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }
    echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msg=$pagename%20Upload%20Successfully...'>";  exit(0);
}

// Row Update Query 
else if(@$_GET['mode']=="update" && $id !=""){
$img_required ="";
  $sql="UPDATE `$tblname` SET `city_id`='$city_id', tb_timezone='$timeslot',tb_date='$date'  WHERE tb_id =$id";
  if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }

  echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msg=$pagename%20Updated%20Successfully...'>";  exit(0);
}

// Delete OR hide Query
else if(@$_GET['mode']=="delete" && $id !=""){

  $sql = "DELETE FROM `$tblname` WHERE tb_id='$id'";
  // $sql="UPDATE `$tblname` SET status ='1' WHERE ID='$id'";
  if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }
   echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msgdanger=$pagename%20Deleted%20successfully...'>";  exit(0);
}

//Select query when we click on pencil for edit existing entry
else if(@$_GET['mode']=="edit" && $id!="") { 
  $img_required ="";
  $sql = "SELECT * FROM `$tblname` WHERE tb_id='$id'";
  $result = mysqli_query($con,$sql);
  $count = mysqli_num_rows($result);
  if($count>0){
    while($row = mysqli_fetch_array($result)) {
      
      $get_city_id = $row['city_id'];
      $get_timeslot = $row['tb_timezone'];
      $get_tb_date = $row['tb_date'];
     
    }
  }else{
    echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msgdanger=Dont change in URL...'>";  exit(0);
  }
}

//Select Query to display the records (join)
// $sql = "SELECT $tblname.*, courses.title FROM $tblname, courses WHERE $tblname.course_id=courses.ID ORDER BY $tblname.ID desc";
$sql = "SELECT *,city.ID as city_id ,city.name as city_name FROM $tblname s left join city on city.ID = s.city_id ORDER BY s.tb_id DESC";
$result = mysqli_query($con,$sql);   


//Select all code
$selectvariable = '';
if (@$_POST['action'] == 'Delete') {
  for ($i=0; $i < count($_POST['ids']);$i++) {
    $selectvariable =$_POST['ids'][$i].", ".$selectvariable;
  }
  $ids = substr($selectvariable, 0, -2);
  $companyasend = str_replace(", ","' or tb_id = '", $ids);
  // $sql="DELETE FROM `$tblname` WHERE ID='$companyasend'";
  $sql="UPDATE `$tblname` SET status ='1' WHERE tb_id='$companyasend'";
  if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }
  echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msgdanger=Selected $pagename are deleted..'>";  exit(0);
}
else if (@$_POST['action'] == 'ON') {
  for ($i=0; $i < count($_POST['ids']);$i++) {
    $selectvariable =$_POST['ids'][$i].", ".$selectvariable;
  }
  $ids = substr($selectvariable, 0, -2);
  $companyasend = str_replace(", ","' or tb_id = '", $ids);
  $sql="UPDATE `$tblname` SET status ='1' WHERE tb_id='$companyasend'";
  if (!mysqli_query($con,$sql)) {die('Error: ' . mysqli_error($con)); }
  echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msg=Status%20updated...'>"; exit(0);
} else if (@$_POST['action'] == 'OFF') {
  for ($i=0; $i < count($_POST['ids']);$i++) {
    $selectvariable =$_POST['ids'][$i].", ".$selectvariable;
  }
  $ids = substr($selectvariable, 0, -2);
  $companyasend = str_replace(", ","' or tb_id = '", $ids);
  $sql="UPDATE `$tblname` SET status ='0' WHERE tb_id='$companyasend'";
  if (!mysqli_query($con,$sql)) {die('Error: ' . mysqli_error($con)); }
  echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msg=Status%20updated...'>";   exit(0);}
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
      <?php if(@$_GET['mode']=="edit" && @$_GET['id']!="") { $add_edit = "Edit"; ?>
      <div class="<?= $boxcolor; ?>">
      <?php }else{ $add_edit = "Add"; ?>
      <div class="<?= $boxcolor; ?> collapsed-box">
      <?php } ?>
        <div class="box-header with-border" data-widget="collapse" style="cursor: pointer;">
          <h3 class="box-title text-blue"><?= $add_edit; ?> <?php echo $pagename; ?></h3>

          <div class="box-tools pull-left">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus text-blue"></i></button>
          </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
            <!-- form start -->
            <?php if(@$_GET['mode']=="edit" && @$_GET['id']!="") { ?>
            <form role="form" method="POST" action="<?= $pageurl; ?>?mode=update&id=<?= @$_GET['id'] ?>" enctype="multipart/form-data">
            <?php } else {?>
            <form role="form" method="POST" action="<?= $pageurl; ?>?mode=addnew" enctype="multipart/form-data">
            <?php } ?>

            <div class="box-body">
                <div class="form-group col-md-4">
                  <label for="name">Date <span class="text-red">*</span></label>
                  <input type="date" class="form-control" name="tb_date" value="<?= @$get_tb_date; ?>" autocomplete="off" placeholder="date" required>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group col-md-4">
                  <label for="name">City <span class="text-red">*</span></label>
                  <select class="form-control" name="city_id" required id="city_id">
                    <option class="text-blue bold">Select City</option>
                    <?php  
                      $res = mysqli_query($con,"SELECT ID,name FROM `state` WHERE state.status = '1' ORDER BY ID ASC");
                      while ($row = mysqli_fetch_array($res)) {
                        // echo 'c_id:'.$get_cid;
                        if($row['ID']==$get_city_id){
                          echo "<option selected value=".$row['ID'].">".$row['name']."</option>";  
                        }
                        else{
                          echo "<option value=".$row['ID'].">".$row['name']."</option>";
                        }
                      }
                    ?>
                  </select>
                </div>
                </div>

              <div class="box-body">
                <div class="form-group col-md-4">
                  <label for="name">Timeslot <span class="text-red">*</span></label>
                  <select class="form-control" id="tb_timezone[]" name="tb_timezone[]" multiple="multiple">
                  <option class="text-blue bold">Select Time </option>
    
                    <?php
                                
                      $qry1="SELECT * FROM `timeslot` ORDER BY `timeslot`.`ID` ASC";
                      $results1=mysqli_query($con,$qry1);
                        while ($row1=mysqli_fetch_array($results1)) 
                        {
                        
                          $places1 = explode (',',$get_timeslot);
                        //   print_r($places1);
                          ?>
                            <?php if (in_array($row1['timeslot'], $places1))  {
                                    
                            $str_flag = "selected";
                    
                                }else
                                {
                                $str_flag = "";
                                }?>
                        
                                <option value="<?php echo $row1['timeslot'];?>" <?php echo $str_flag;?>  >  <?php echo $row1['timeslot'];?> </option>
                      
                          <?php } ?>

                </select>
                </div>
              </div>

             
              
              <!-- /.box-body -->
              <div align="right" class="box-footer">
                <?php if(@$_GET['mode']=="edit" && @$_GET['id']!="") { ?>
                <button type="submit" class="<?= $btncolor; ?>"><i class="fa fa-save"></i>&nbsp; Update</button>
                <?php }else{ ?>
                <button type="submit" class="<?= $btncolor; ?>"><i class="fa fa-save"></i>&nbsp; Save</button>
                <?php } ?>
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

      <div class="box">
        <div class="box-body table-responsive no-padding" style="padding: 10px!important;">
        <?php  
          echo displayMsg(@$_GET['msg']);
          echo dangerMsg(@$_GET['msgdanger']);
          echo dupl_msg(@$_GET['dupl_msg']);
        ?>
        <form name="delete" id="frmCompare" class="frmCompare" action="" method="post"> 
          <table id="example1_samy" class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <td width="45"> 
                  <div id="btnCompare" style="display: none; margin: 0px -9px;">
                    <button name="action" value="ON" id="on_off_btn" title="Status ON" onClick="return verifCompare();"><img src="dist/img/green.png" title="Click to Activate" data-toggle='tooltip' /></button>&nbsp;
                    <button name="action" value="OFF" id="on_off_btn" title="Status OFF" onClick="return verifCompare();"><img src="dist/img/red.png" name="action"  style="cursor:pointer" value="OFF" title="Click to Deactivate" data-toggle='tooltip' /></button>&nbsp;
                    <button name="action" value="Delete" id="on_off_btn" title="Delete" onClick="return verifCompare();"><img src="dist/img/delete.png" title="Click to Delete" /></button> &nbsp;
                  </div>
                </td>
              </tr>
              <tr>
                <!-- <td><input type='checkbox' id='selectall' title='Select All' style='cursor:pointer;'/></td></td> -->
                <th align="center" width="20">ID</th>
                <th width="20">City name</th>
                <th width="20">Timezone</th>
                <th width="20">Date</th>
                <th width="20">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $num=0; 
              while($row=mysqli_fetch_array($result)){ $num=$num+1; 
               
                ?>
              <tr>
                <!-- <td><input type="checkbox" class="td" name="ids[]" value="<?php echo $row['tb_id'] ?>" style="cursor:pointer;"></td> -->
                <td align="center"><?php echo $num; ?></td>
                
                <td valign="middle"> <?php echo $row['city_name']; ?> </td>
                <td valign="middle"> <?php echo $row['tb_timezone']; ?> </td>
                <td valign="middle"> <?php echo $row['tb_date']; ?> </td>
            
                <td>
                  <a href="<?= $pageurl; ?>?mode=edit&id=<?php echo $row['tb_id'] ?>" title="Edit"><i class="fa fa-edit"></i>&nbsp;</a>
                  <a onClick="return verifCompare();" href="<?= $pageurl; ?>?id=<?php echo $row['tb_id'] ?>&mode=delete" class="text-red" title="Delete"><i class="fa fa-trash-o"></i>&nbsp;</a>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </form>
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
</body>
</html>