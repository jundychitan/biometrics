<?php

$dir="/projects/biometrics/unsent";
$defaults_file="/projects/biometrics/defaults.cfg";
$conn = mysqli_connect("localhost","phpmyadmin","linux","fp_db")
or die("Could not connect to the server.");

mysqli_query($conn,"SET NAMES 'utf8'");
mysqli_query($conn,"SET CHARACTER SET 'utf8'");

if(!is_readable($dir))return NULL;
$files = scandir($dir); // scan directory for files in ascending order
foreach ($files as $key => $value){
	if(!in_array($value,array(".",".."))){
		if (!is_dir($dir . DIRECTORY_SEPARATOR . $value)){ //skip if directory
		
			$array_config=parse_ini_file($defaults_file);
			$_location=$array_config["location"];
			$location=str_replace(' ','%20',$_location);
			$raw_fp_data=file_get_contents($dir . DIRECTORY_SEPARATOR . $value);
			$array_fp_data=explode("&", $raw_fp_data);
			$emp_no=$array_fp_data[0];
			$_name=$array_fp_data[1];
			$name=str_replace(' ','%20',$_name);
			$id_name=$array_fp_data[2];
			$date=$array_fp_data[4];
			$time=$array_fp_data[5];

			$result_server = mysqli_query($conn,"SELECT * FROM `server_config`")
			or die(mysql_error());

			$row_server = mysqli_fetch_array($result_server);
			$server_ip = $row_server['server_ip'];

			//echo $date;
			//echo $time;
			
			$url = "http://$server_ip?location=$location&emp_no=$emp_no&name=$name&id_name=$id_name&date=$date&time=$time";
			$response = file_get_contents($url,false);
			echo $response;
			if($response==TRUE){
				echo "SENT";
				unlink($dir . DIRECTORY_SEPARATOR . $value);
			}
		}		
	}
}
?>
