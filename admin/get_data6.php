<?php  
require('check.php');

// topic dropdown code
if(@$_GET['mode']=="addressdetails"){
	$cid      = $_POST['id'];

	$sql1="SELECT houser_no,lendmark,adderss,ID FROM `address` WHERE user_id ='$cid' and address.status=1";
	$result1 = mysqli_query($con,$sql1);   
	while($row11 =mysqli_fetch_array($result1)){ 
		$arr1[] = $row11;
	}

	$html0='';
	foreach($arr1 as $topic){
		
	  	$html0.='<option value='.$topic['ID'].'>'.$topic['houser_no'].', '.$topic['lendmark'].', '.$topic['adderss'].'</option>';	

	}
	echo $html0;
}
?>

<!-- check for topic id when we edit  - still not working 6 pm -->