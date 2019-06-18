<?php 

	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
	$req=getReq();

	queryMysql("DELETE FROM afree_status WHERE deleted_=1");
	
	switch($req){
		case "create":
			$resultt= createStatus();
			break;
		case "fetchforuser":
			$resultt= fetchForUser();
			break;
		case "fetchfromuser":
			$resultt= fetchFromUser();
			break;
		case "fetchfrom":
			$resultt= fetchFromField();
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
		case "viewstatweb":
		    $resultt=viewStatusWeb();
		    break;
	    case "getallviewers":
	        $resultt=getAllViewers();
	        break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);
	
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

	function viewStatusWeb(){	
		$id=getGetorPostString('status_code');
		$subj=$user=getThisUser();
		$like=CURRENT_TIME;
		if(empty($id)){
			return apiLeave(noFile());			
		}
		if(checknum("SELECT user FROM status_view WHERE status_code='$id' AND user='$user'")<1){
			$q="INSERT INTO status_view (id, status_code, user, time) VALUES (NULL, '$id', '$user', $like)";
    		queryMysql($q);
		}
		return apiLeave(success(345));;
	}
	
	function getAllViewers(){
		$array_=array();
		$user=getThisUser();
		$status_code=getGetorPostString("status_code");
		$q_search=getGetorPostString("q");
		$end=" ";

		$timeee=CURRENT_TIME - (ONE_DAY__ + 17);

		queryMysql("DELETE FROM status_view WHERE time<$timeee");

		$qu="SELECT * FROM afree_status WHERE status_code='$status_code' AND username='$user' AND deleted_=0";
		$num=checknum($qu);
		if($num<1){
			return apiLeave(noAuth());
		}

		if(!empty($q_search)){
			$end= " AND user LIKE '%$q_search%'";
		}
		$q="SELECT * FROM status_view WHERE status_code='$status_code' ". $end;
		$nul=checknum($q);
		$qu=$q." ORDER BY id DESC ".calcPages($nul, COMMENTS_NO);
		$result=queryMysql($qu);
		$num=$result->num_rows;
		
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
		$idi=getLastid();
		addNewStatusNotifyAll($user, $idi);


		$qq="SELECT * FROM status_users WHERE username='$user'";

		$qq1="INSERT INTO status_users (id, username, time) VALUES (NULL, '$user', $time)";
		if(checknum($qq)>0){
			$qq1="UPDATE status_users SET time=$time WHERE username='$user'";
		}

		queryMysql($qq1);

		return apiLeave(success(345));
	}

	function fetchFromUser(){
		$other_user=getGetorPostString("who");
		$user=getThisUser();
		$array_=$array=array();

		$query="Select * from afree_status WHERE username='$other_user' AND deleted_=0 ORDER BY time ASC";
		$result3=queryMysql($query);
		$num3=$result3->num_rows;
		if($num3<1){
			return apiLeave(emptyArray());
		}

		$array_response=array();
		for($i3=0; $i3<$num3; $i3++){
			$row3=$result3->fetch_array(MYSQLI_ASSOC);
			$stat_code=$row3["status_code"];
			$seen=checknum("Select * from status_view WHERE status_code='$stat_code' AND user='$user'");
			array_push($array_response, array("id"=>$row3["id"],  
								 "timestamp"=>$row3["time"],
								 "time"=>$row3["time"],
								 "subtitle"=> $row3["message"], 
								 "status_code"=> $stat_code, 
								 "fav"=> $row3["fav"],  
								 "seen"=> $seen, 
								 "image"=> $row3["image"]));
		}

		array_push($array, array("auth_username"=> $other_user,
						 "auth_data"=>fetchAuthData($other_user),
						 "response"=>$array_response));	

		return apiLeave($array);
	}

	function fetchForUser(){
		$user=getThisUser();
		$array_=$array=array();
		$timea_=CURRENT_TIME - (ONE_DAY__ + 17);
		$q_search=getGetorPostString("q");

		$query="DELETE FROM status_users WHERE time<$timea_";
		queryMysql($query);
		$query="Update afree_status SET deleted_=1 WHERE time<$timea_";
		queryMysql($query);

		$query="Select distinct username from status_users WHERE ((username IN (SELECT friends.user FROM friends WHERE friends.recip='$user') OR username IN (SELECT friends.recip FROM friends WHERE friends.user='$user')) OR username='$user') ORDER BY time DESC";
		if(!empty($q_search)){
			$query="Select distinct username from status_users WHERE ((username IN (SELECT friends.user FROM friends WHERE friends.recip LIKE '%$user%') OR username IN (SELECT friends.recip FROM friends WHERE friends.user LIKE '%$user%')) OR username='$user') ORDER BY time DESC";
		}
		$result=queryMysql($query);
		$num=$result->num_rows;
		if($num<1){
			return apiLeave(emptyArray());
		}

		$result2 = queryMysql($query .calcpages($num, NO_OF_SEARCHES));
		$num2 = $result2->num_rows;

		$curr_pages=getCurrentPage();
		$pages=intval($num2/NO_OF_SEARCHES);
		$pages_left=$pages-$curr_pages;

		if($num2<1){
			return apiLeave(emptyArray());
		}

		$this_user=getThisUser();

		if(getCurrentPage()==1){
			$num_val=checknum("Select * from afree_status WHERE username='$this_user'");
			if($num_val>0){
				array_push($array, array("auth_username"=> $this_user,
								 "auth_data"=>fetchAuthData($this_user)));
			}
		}

		for($i=0; $i<$num2; $i++){
			$row=$result2->fetch_array(MYSQLI_ASSOC);
			$other_user=$row["username"];
			if(empty($other_user) || $other_user==$this_user){
				continue;
			}

			array_push($array, array("auth_username"=> $row["username"],
							 "auth_data"=>fetchAuthData($row["username"])));
		}

		return array(
				"data"=>$array,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}

	function fetchFromField(){
		$user=getThisUser();
		$time=getGetorPostString('time');
		$time=(empty($time) || !is_numeric($time))?0:$time;
		$time_=CURRENT_TIME;
		$timea_=getPrevDay()-345;

		$query="DELETE FROM status_users WHERE time<$timea_";
		queryMysql($query);

		$query="Update afree_status SET deleted_=1 WHERE time<$timea_";
		queryMysql($query);
		$type_stat=trim(getGetOrPostString('type'));
		if(empty($type_stat)){
    		$query="Select * from afree_status WHERE ((username IN (SELECT friends.user FROM friends WHERE friends.recip='$user') OR username IN (SELECT friends.recip FROM friends WHERE friends.user='$user')) OR fav>0 OR username='$user' ) AND time>$time AND deleted_=0 ORDER BY time ASC";
		}else{
		    if(($type_stat)=="FAV"){
        		$query="Select * from afree_status WHERE fav>0 AND time>$time AND deleted_=0 ORDER BY time ASC";
		    }else {
        		$query="Select * from afree_status WHERE ((username IN (SELECT friends.user FROM friends WHERE friends.recip='$user') OR username IN (SELECT friends.recip FROM friends WHERE friends.user='$user')) OR username='$user' ) AND time>$time AND deleted_=0 ORDER BY time ASC";
		    }
		}
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
						 "auth_data"=>fetchAuthData($row["username"])
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