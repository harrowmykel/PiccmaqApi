<?php 

	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
	$req=getReq();
	
	switch($req){
		case "create":
			$resultt= createApiKey();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);
	
	
	function createApiKey(){
		$user=getThisUser();
		$email=getGetorPostString("email");
		$website=getGetorPostString("website");
		$time=CURRENT_TIME;

		$sql="SELECT * FROM apis_table WHERE username='$user'";
		$api_reg=checknum($sql);
		if($api_reg>0){
			$row=((queryMysql($sql))->fetch_array(MYSQLI_ASSOC));
			$api_key=$row["api_key"];
			$email=$row["email"];
			$website=$row["website"];
			$persy_=translate_var("api_reg_now",array($user, $api_key, $email, $website));
			$useri=API_ADMIN_USERNAME;
			addMsgToDb($useri, $user, $persy_);

			return apiLeave(success(345));
		}

		if(empty($email)){
    		return apiLeave(noFile());
		}

		$stat_code=gnrtNewString(10,12);
		$q="SELECT * FROM apis_table WHERE api_key='$stat_code'";

		while(checknum($q)>0){
			$stat_code=gnrtNewString(10,12);
			$q="SELECT * FROM apis_table WHERE api_key='$stat_code'";
		}
		$api_key=$stat_code;		

		$query="INSERT INTO apis_table (id, username, website, email, api_key, time) VALUES (NULL, '$user', '$website', '$email', '$api_key', $time)";
		queryMysql($query);

		$persy_=translate_var("api_reg_now",array($user, $api_key, $email, $website));
		$useri=API_ADMIN_USERNAME;
		addMsgToDb($useri, $user, $persy_);

		return apiLeave(success(345));
	}

?>