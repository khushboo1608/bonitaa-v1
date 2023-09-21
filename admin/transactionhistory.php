<?php 
require('check.php');
$pageurl = "transactionhistory$extn";
$tblname = "wallet_history";
$pagename = "Transaction History";
$id = @$_GET['id']; 
$img_required = 'required';

// Delete OR hide Query
 if(@$_GET['mode']=="delete" && $id !=""){
   $sql = "DELETE FROM `$tblname` WHERE wh_id='$id'";
   if (!mysqli_query($con,$sql)){die('Error: ' . mysqli_error($con)); }
   echo"<META HTTP-EQUIV='REFRESH' CONTENT='0; URL=$pageurl?msgdanger=$pagename%20Deleted%20successfully...'>";  exit(0);
}

//Select Query to display the records (join)
// $sql = "SELECT $tblname.*, courses.title FROM $tblname, courses WHERE $tblname.course_id=courses.ID ORDER BY $tblname.ID desc";
$sql = "SELECT *,u.name as username,s.name as staffname  FROM `$tblname` 
left join user_registration as u on u.ID = wallet_history.wh_user_id
left join staff as s on s.ID = wallet_history.wh_user_id
ORDER BY wallet_history.wh_id DESC";
$result = mysqli_query($con,$sql);   
$rowcount = mysqli_num_rows($result);

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
      <div class="<?php echo $boxcolor; ?>">
        <div class="box-header with-border">
          <h3 class="box-title text-blue"><?php echo $pagename. ' ('. $rowcount. ') '; ?></h3>
          <a href="<?=$pageurl;?>"> <i class="fa fa-refresh text-green"></i></a>
         

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
                <th width="20">Role</th>
                <th>User/Staff Name</th>
                <th>Amount</th>
                <th width="140">Description</th>
                <th width="140">Transaction Type</th>
                <th width="140">Type</th>
                <th width="140">Date</th>
                <th width="20">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php $num=0; 
              while($row=mysqli_fetch_array($result)){ $num=$num+1; 
                // $cpic = ($row['pic']!="") ? $row['pic'] : "noimg.webp";
                ?>
              <tr>
                <!-- <td><input type="checkbox" class="td" name="ids[]" value="<?php echo $row['ID'] ?>" style="cursor:pointer;"></td> -->
                <th align="center"><?php echo $num; ?></td>
                <td style="text-align: left;"><?php if($row['wh_role'] == 1){echo 'User';}else if($row['wh_role'] == 2){echo 'Staff';} ?></td>
                <td style="text-align: left;"><?php if($row['wh_role'] == 1){echo $row['username'];}else if($row['wh_role'] == 2){ echo $row['staffname'];} ?></td>
                <td style="text-align: left;"><?php echo $row['wh_amount']; ?></td>
                <td style="text-align: left;"><?php echo $row['description']; ?></td>
                <td style="text-align: left;"><?php if($row['wh_transaction_type'] == 1){echo 'Credit';}else if($row['wh_transaction_type'] == 2){echo 'Debit';} ?></td>
                <td style="text-align: left;"><?php if($row['wh_type'] == 1){echo 'Parent';}else if($row['wh_type'] == 2){echo 'Child';}else if($row['wh_type'] == 3){echo 'Wallet payment';} ?></td>
                <td style="text-align: left;"><?php echo $row['wallet_date']; ?></td>
                <td>
                  <!-- <a href="<?= $pageurl; ?>?mode=edit&id=<?php echo $row['ID'] ?>" title="Edit"><i class="fa fa-edit"></i>&nbsp;</a> -->
                  <a onClick="return verifCompare();" href="<?= $pageurl; ?>?id=<?php echo $row['wh_id'] ?>&mode=delete" class="text-red" title="Delete"><i class="fa fa-trash-o"></i>&nbsp;</a>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </form>
        </div>
        <!-- /.box-body -->
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