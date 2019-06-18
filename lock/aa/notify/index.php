<?php 

    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
	$req=getReq();

	switch($req){
		case "fetchfrom":
			$resultt= fetchfrom();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);
	
	function fetchfrom(){
		$user=getThisUser();
		$time=getGetorPostString('time');
		$time=(empty($time) || !is_numeric($time))?0:$time;// 
		$query="Select * from notific_list WHERE object='$user' AND seen=0  AND time>$time ORDER BY time ASC";
		$array =msgContent($query);
		return $array;
	}

	function msgContent($query){
		$result=queryMysql($query);
		$num=$result->num_rows;
		$array_=$array=array();
		$user=getThisUser();

		$curr_pages=getCurrentPage();
		$pages=intval($num/MAX_MESSAGES);
		$pages_left=$pages-$curr_pages;

		$result=queryMysql($query.calcpages($num,MAX_MESSAGES));
		$num=$result->num_rows;
		
		if($num<1){
			$array_=emptyArray();
		}
		
		for($i=0; $i<$num; $i++){
			$row=$result->fetch_array(MYSQLI_ASSOC);
			$dk=$row["id"];
			$qefg="UPDATE notific_list set seen=1 WHERE object='$user' AND id=$dk";
			queryMysql($qefg);
    		$array=array("id"=>$dk,
                		"link"=>$row["link"],
                		"subject"=>$row["subject"],
                		"object"=>$row["object"],
                		"notify_id"=>$row["notify_id"],
                		"obj_id"=>$row["obj_id"],
                		"seen"=>$row["seen"],
                		"time"=>$row["time"],
                		"timestamp"=>$row["time"],
						 "auth_username"=> $row["subject"],
						 "auth_data"=>array(	
						 			"auth_img"=> getUserDp($row["subject"]), 
						 			"auth"=>  getFullname($row['subject'])
						 			)
						);
			array_push($array_, $array);
		}


		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}
	
?>