<?php 
require('check.php');
$pageurl = "users$extn";
if(@$_GET['mode']=="export")  
{  
    // echo 'hii';
    header('Content-Type: text/csv; charset=utf-8');  
    header('Content-Disposition: attachment; filename=user_details.csv');  
    $file = fopen('php://output', 'w');
      
    $header = array('ID', 'Status', 'Name', 'Email', 'Phone', 'Alternate phone', 'Password', 'City Name', 'OTP', 'Code', 'Referral code', 'Wallet', 'Date');  
    fputcsv($file, $header);  

    $query="SELECT *,u.ID, u.status, u.name, u.email, u.mobile, u.alternate_mobile, u.password, city.name as city_name, u.confirm_code, u.code, u.referral, u.wallet, u.createdon  FROM `user_registration` as u
      left join city on city.ID = u.city_id
      ORDER BY u.ID ASC";

    $result = mysqli_query($con, $query);  
    while($row = mysqli_fetch_assoc($result))  
    {  
          
          $orderData = array();
      
          $orderData[] = $row["ID"];
          $orderData[] = $row["status"];
          $orderData[] = $row["name"];
          $orderData[] = $row["email"];
          $orderData[] = $row["mobile"];
          $orderData[] = $row["alternate_mobile"];
          $orderData[] = $row["password"];
          $orderData[] = $row["city_name"];
          $orderData[] = $row["confirm_code"];
          $orderData[] = $row["code"];
          $orderData[] = $row["referral"];
          $orderData[] = $row["wallet"];
          $orderData[] = $row["createdon"];
         
          fputcsv($file, $orderData);
    }  
    fclose($file);
    exit;	
      
}

?>