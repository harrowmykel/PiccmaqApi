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
		case "fetchweb":
			$resultt= fetchforweb();
			break;
		case "notifcount":
			$resultt= getNotificationsCount();
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
	
	function fetchforweb(){
		$user=getThisUser();
		$query="Select * from notific_list WHERE object='$user'";
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
						 "auth_data"=>fetchAuthData($row["subject"])
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
	


function getNotificationsCount(){
	$user=getThisUser();
	$msgs_numb=checknum("SELECT aut FROM pmesages WHERE reciv='$user' AND confirm='w'");

	$tim=CURRENT_TIME-ONLINE_TIMEOUT-DIFF_TIME;	
	$q="SELECT * FROM members WHERE last_seen>$tim";
	IF(CHAT_IS_FRIENDS_ONLY){
		$q="SELECT * FROM members WHERE members.user IN (SELECT DISTINCT friends.user FROM friends WHERE (friends.user='$user' OR friends.recip='$user') OR  members.user IN (SELECT DISTINCT friends.recip FROM friends WHERE (friends.user='$user' OR friends.recip='$user') AND friends.confirm=2) AND friends.confirm=2) AND last_seen>$tim";
	}

	$chat_numb=checknum($q);
	$frnd_numb=checknum("SELECT user FROM friends WHERE friends.recip='$user' AND NOT confirm=2");
	$notif_numb=checknum("SELECT subject FROM notific_list WHERE object='$user' AND NOT seen=1");

	$asdg=array(array("msgs"=>$msgs_numb, 
				"chat_msgs"=>$chat_numb,
				"frnds"=>$frnd_numb,
				"notif_numb"=>$notif_numb
		));
	return apiLeave($asdg);
}
?>