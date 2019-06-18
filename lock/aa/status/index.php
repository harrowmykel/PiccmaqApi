<?php 

error_reporting(E_ALL); ini_set('display_errors', 1);
	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
	$req=getReq();

	switch($req){
		case "create":
			$resultt= createStatus();
			break;
		case "fetch":
			$resultt= fetchMessage();
			break;
		case "fetchfrom":
			$resultt= fetchSMessages();
			break;
		case "fetchdeleted":
			$resultt= fetchDeletedMessages();
			break;
		case "deletestatus":
		    $resultt=deleteMsg();
		    break;
		case "viewstat":
		    $resultt=viewStatus();
		    break;
	    case "getallviewers":
	        $resultt=getAllViewers();
	        break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);

	function sendNewMessage(){
		$post_key=getGetString('user_key');
		$user=getThisUser();
		$auth_usr=getUsername($user);
		$rec_usr=getUsername($post_key);
		$time=time();
		$msg=getGetorPostString('message');
		$pic=getPostString('pic');
		$query="INsert INTO msgs (msg_id, auth_key, recip_key, auth_username, recip_username, time, text_msg, sent, confirm, pic) VALUES (NULL, $user, $post_key, $auth_usr, $rec_usr, $time, $msg, 0, 0, $pic)";
		queryMysql($query);
		return success(64564);
	}
	
	function fetchDeletedMessages(){
		$user=getThisUser();
		$time=CURRENT_TIME;
		$timee=$time-ONE_DAY__;
		$timeee=$timee-7;//to optimize code. phone will automatically delete posts oler than a day so just check from 2 days ago.
		$qu="UPDATE afree_status SET deleted_=1  WHERE time<$timee";
		queryMysql($qu);
		$query="Select * from afree_status WHERE ((username IN (SELECT friends.user FROM friends WHERE friends.recip='$user') OR username IN (SELECT friends.recip FROM friends WHERE friends.user='$user')) AND time<$time) AND deleted_=1 AND time>$timeee ORDER BY time ASC";
		$array =msgContent($query);
		return $array;
	}

	function deleteMsg(){ 
		$user=getThisUser();
		$stat_code=getGetorPostString("status_code");
		$dfg=" WHERE status_code='$stat_code' AND username='$user'";
		$nm=checkNum("SELECT * FROM afree_status  ".$dfg);
		if($nm>0){
		    $query="UPDATE afree_status SET deleted_=1 ".$dfg;
    		queryMysql($query);
    		addDeletedStatusNotifyAll($user, $stat_code);
		}
		return apiLeave(success(345));
	}

	function viewStatus(){	
		$id=getGetorPostString('status_code');
		$like=CURRENT_TIME;
		if(empty($id)){
			return apiLeave(noFile());			
		}
		$subj=$user=getThisUser();
        $id=str_replace("\'", "\"", $id);
		$json=json_decode($id);
		if ($json==null){
			return apiLeave(noFile());			
		}
		$jo=count($json);
		for ($io=0; $io<$jo; $io++){
    		$joo=$json[$io]->status_code;
    		
    		if(checknum("SELECT user FROM status_view WHERE status_code='$joo' AND user='$user'")<1){
    			$q="INSERT INTO status_view (id, status_code, user, time) VALUES (NULL, '$joo', '$user', $like)";
        		queryMysql($q);
    		}
		}
		return apiLeave(success(345));;
	}
	
	function getAllViewers(){
		$user=getThisUser();
		$status_code=getGetorPostString("status_code");
		$q_search=getGetorPostString("q");
		$end=" ";
		if(!empty($q_search)){
			$end= " AND user LIKE '%$q_search%'";
		}
		$q="SELECT * FROM status_view WHERE status_code='$status_code' ". $end;
		$nul=checknum($q);
		$qu=$q." ORDER BY id DESC ".calcPages($nul, COMMENTS_NO);
		$result=queryMysql($qu);
		$num=$result->num_rows;
		$array_=array();
		$user=getThisUser();

		$nul=$result->num_rows;
		
		$curr_pages=getCurrentPage();
		$pages=intval($nul/COMMENTS_NO);
		$pages_left=$pages-$curr_pages;
		if($num<1){
			return apiLeave(emptyArray());
		}
		for($i=0; $i<$num; $i++){
			$row=$result->fetch_array(MYSQLI_ASSOC);
			
			$userii=$row['user'];
			$frnd_r=areFrnds($userii, $user);
			$r_rcvd=$frnd_r['r_rcvd'];
			$r_sent=$frnd_r['r_sent'];
			$r_frnds=$frnd_r['r_frnds'];

			array_push($array_, array("id"=>$row['id'],
									"auth_username"=>$row['user'],
									"frnds_data"=>array(
												"r_sent"=>$r_sent,
												"r_rcvd"=>$r_rcvd,
												"r_frnds"=>$r_frnds),
									"auth_data"=>array(
									 			"auth"=>getFullname($row['user']),
									 			"fullname"=>getFullname($row['user']),
									 			"auth_img"=>getUserDp($row['user'])
									 			)
								));
		}


		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}
	
	
	function createStatus(){
		$user=getThisUser();
		$time=getGetorPostString('time');

		$message=$text=replaceSmiles_(getGetorPostString('text'));
		$ret=getGetorPostString("imgurl");		
		$time=CURRENT_TIME;
		$fnjfn= getPrevDay();

		if(empty($ret)){
    		return apiLeave(noFile());
		}

		$stat_code=gnrtNewString(10,12);
		$q="SELECT * FROM afree_status WHERE status_code='$stat_code'";

		while(checknum($q)>0){
			$stat_code=gnrtNewString(10,12);
			$q="SELECT * FROM afree_status WHERE status_code='$stat_code'";
		}
		
		$query="INSERT INTO afree_status (id, username, message, time, status_code, fav, image, deleted_) VALUES (NULL, '$user', '$text', $time, '$stat_code', 0, '$ret', 0)";
	
    	if(in_array($user, ADMIN_LIST)){
    		$query="INSERT INTO afree_status (id, username, message, time, status_code, fav, image, deleted_) VALUES (NULL, '$user', '$text', $time, '$stat_code', 1, '$ret', 0)";
		}
		queryMysql($query);
		addNewStatusNotifyAll($user, getLastid());
		return apiLeave(success(345));
	}

	function fetchSMessages(){
		$user=getThisUser();
		$time=getGetorPostString('time');
		$time=(empty($time) || !is_numeric($time))?0:$time;
		$time_=CURRENT_TIME;
		$timea_=getPrevDay()-345;
		$query="Update afree_status SET deleted_=1 WHERE time<$timea_";
		queryMysql($query);
		$query="Select * from afree_status WHERE ((username IN (SELECT friends.user FROM friends WHERE friends.recip='$user') OR username IN (SELECT friends.recip FROM friends WHERE friends.user='$user')) OR fav>0 OR username='$user' ) AND time>$time AND deleted_=0 ORDER BY time ASC";
		$array =msgContent($query);
		return $array;
	}

	function msgContent($query){
		$q=$query;
		$result=queryMysql($query);
		$num=$result->num_rows;
		$array_=$array=array();

		$curr_pages=getCurrentPage();
		$pages=intval($num/NO_OF_SEARCHES);
		$pages_left=$pages-$curr_pages;

		$result = queryMysql($q .calcpages($num, NO_OF_SEARCHES));
		$num=$result->num_rows;

		if($num<1){
			$array_= emptyArray();
		}

		for($i=0; $i<$num; $i++){
			$row=$result->fetch_array(MYSQLI_ASSOC);
			if(empty($row["username"])){
				continue;
			}
    		$array=array("id"=>$row["id"], 
						 "auth_username"=> $row["username"], 
						 "timestamp"=>$row["time"],
						 "time"=>$row["time"],
						 "subtitle"=> $row["message"], 
						 "status_code"=> $row["status_code"], 
						 "fav"=> $row["fav"], 
						 "image"=> $row["image"],
						 "auth_data"=>array(	
						 			"auth_img"=> getUserDp($row["username"]), 
						 			"auth"=>  getFullname($row["username"])
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