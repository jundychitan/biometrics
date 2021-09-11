<?php
		$defaults_file="/projects/biometrics/defaults.cfg";
		$fp_id_file="/var/txtalert/fp_id";
		$unsent_file_path="/projects/biometrics/unsent";
		
		$conn = mysqli_connect("localhost","phpmyadmin","linux","fp_db")
		or die("Could not connect to the server.");
		
		mysqli_query($conn,"SET NAMES 'utf8'");
		mysqli_query($conn,"SET CHARACTER SET 'utf8'");
	
	/*
		$result_current = mysql_query("SELECT * FROM `current` where event='valid'",$conn)
		or die(mysql_error());

		$row_current = mysql_fetch_array($result_current);
		$emp_no = $row_current['emp_no'];
		$_name = $row_current['name'];
		$id_name = $row_current['id_name'];
		$date = $row_current['date'];
		$time = $row_current['time'];
		$name=str_replace(' ','%20',$_name);
	*/
		//get location
		$array_config=parse_ini_file($defaults_file);
		$_location=$array_config["location"];
		$location=str_replace(' ','%20',$_location);
		$raw_fp_data=file_get_contents($fp_id_file);
		$array_fp_data=explode("&", $raw_fp_data);
		$emp_no=$array_fp_data[0];
		$_name=$array_fp_data[1];
		$name=str_replace(' ','%20',$_name);
		$id_name=$array_fp_data[2];
		$date=$array_fp_data[4];
		$time=$array_fp_data[5];
		
		$result_server = mysqli_query($conn,"SELECT * FROM `server_config`")
		or die(mysqli_error());

		$row_server = mysqli_fetch_array($result_server);
		$server_ip = $row_server['server_ip'];

		// $date;
		//echo $time;
		
		$url = "http://$server_ip?location=$location&emp_no=$emp_no&name=$name&id_name=$id_name&date=$date&time=$time";
		$response = file_get_contents($url,false);
		echo $response;
		if($response==FALSE){
			if (!file_exists($unsent_file_path)){
				mkdir($unsent_file_path);
			}
			$unsent_file=$unsent_file_path."/".$date.$time;
			copy($fp_id_file,$unsent_file);
		}
		else{
			echo "SENT";
		}
		
?>