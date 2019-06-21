<?php 

	$root="../";
	include $root. "incl/index.php";

	$req=getReq();

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
    
	switch($req){
		case "create":
			$resultt= createStatus();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);
	
	function createStatus(){
		$user=getThisUser();

		$ret=$message=$text=getGetorPostString('token');
		$time=CURRENT_TIME;

		if(empty($ret) || empty($user)){
			return array(
				"data"=>noFile(),
				"pagination"=>array(
					"pages"=>0,
					"curr_pages"=>0,
					"pages_left"=>0));
		}

		$q="SELECT * FROM firebase_keys WHERE firekey='$text'";
		if(checknum($q)<1){
			$query="INSERT INTO firebase_keys (id, username, firekey, time) VALUES (NULL, '$user', '$text', $time)";
				queryMysql($query);
		}else{
			$query="DELETE FROM firebase_keys WHERE firekey='$text'";
				queryMysql($query);
			$query="INSERT INTO firebase_keys (id, username, firekey, time) VALUES (NULL, '$user', '$text', $time)";
				queryMysql($query);
		}
		
		return array(
				"data"=>success(345),
				"pagination"=>array(
					"pages"=>0,
					"curr_pages"=>0,
					"pages_left"=>0));
	}

?>