<?php  
require('check.php');

// topic dropdown code
if(@$_GET['mode']=="servicecategory"){
	$cid      = $_POST['id'];
	$get_ssscid = $_POST['get_ssscid'];

	$sql1="SELECT ID,title FROM services WHERE subsubcategory='$cid'";
	$result1 = mysqli_query($con,$sql1);   
	while($row11 =mysqli_fetch_array($result1)){ 
		$arr1[] = $row11;
	}

	$html0='';
	foreach($arr1 as $topic){
		if($topic['ID']==$get_ssscid){
	    $html0.="<option selected value=".$topic['ID']." style='background: grey; color:white;'>".$topic['title']."</option>";
	  }else{
	  	$html0.='<option value='.$topic['ID'].'>'.$topic['title'].'</option>';	
	  }
	}
	echo $html0;
}
?>

<!-- check for topic id when we edit  - still not working 6 pm -->