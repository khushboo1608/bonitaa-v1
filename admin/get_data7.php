<?php  
require('check.php');

// topic dropdown code
if(@$_GET['mode']=="timeslotdetails"){
    $my_date = date("Y-m-d");

	$date = $_POST['date'];
    $city = $_POST['city'];

	$sql1="SELECT * FROM `timeslot` ORDER BY `timeslot`.`ID` ASC";
	$result1 = mysqli_query($con,$sql1);   
	while($row11 =mysqli_fetch_array($result1)){ 
		$arr1[] = $row11;
	}




	$html0='';
    $disabled = '';
    foreach($arr1 as $topic){

        
    $qry_check_booling = "SELECT * FROM orders
    WHERE orders.city_id='".$city."' and orders.order_date='".$date."' AND FIND_IN_SET('".$topic['timeslot']."',orders.order_time) AND (orders.payment_type ='1' OR orders.payment_type = '2' OR orders.payment_type ='6' OR orders.payment_type = '7'  ) ORDER BY orders.id DESC";
    $result_check_booking = mysqli_query($con,$qry_check_booling);
    $row_check_booking = mysqli_fetch_assoc($result_check_booking);
    $fetch_booking_check = mysqli_num_rows($result_check_booking);

    $qry_check_block = "SELECT * FROM timeslot_block
    WHERE timeslot_block.city_id='".$city."' and timeslot_block.tb_date='".$date."' AND FIND_IN_SET('".$topic['timeslot']."',timeslot_block.tb_timezone) 
    ORDER BY timeslot_block.tb_id DESC ";
    $result_check_block = mysqli_query($con,$qry_check_block);
    $num_row_check_block = mysqli_num_rows($result_check_block);

    if($num_row_check_block){
        $disabled = "disabled";	
    }else if($fetch_booking_check >= $topic['slot_max_book'] ){

        
        $disabled = "disabled";	
    }else{

        $time = date('h:i A');
        $my_timeschedule_export=$topic['timeslot'];
        $expload = (explode("to",$my_timeschedule_export));
        
        $ftime = $expload[0];
        $ltime = $expload[1];
        // $ltime="06:00 PM";
        //$timestamp = time();
        $hours = 2;
        $timestamp = time() + ($hours * 60 * 60); // hours; 60 mins; 60secs
        // 08:00 < 06:00
        if($my_date == $date){
            if (strtotime($ftime) < $timestamp) {
                
                $disabled = "disabled";	
            }else{
                
                $disabled = "";	
            }
        }else{
            
            $disabled = "";	
            
        }
    }

		
	  	$html0.='<option value='.$topic['ID'].' '.$disabled.'>'.$topic['timeslot'].'</option>';	

	}
	echo $html0;
}
?>

<!-- check for topic id when we edit  - still not working 6 pm -->