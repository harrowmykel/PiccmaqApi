<?php 

	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
    
	$req=getReq();

	switch($req){
		case "send":
			$resultt= sendNewMessage();
			break;
		case "about":
			page_is_first();
			$resultt= getGroupAbout();
			break;
		case "reqfrnd":
		    $resultt=editRequest();
		    break;
		case "search":
			$resultt= fetchAllGroups();
			break;
		case "fetchfrom":
			$resultt= fetchUserGroups();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);
	
	function editRequest(){
	    $type=getGetorPostString('type');
	    $who=getGetorPostString('who');
	    $user=getThisUser();
	    switch($type){
	        case "delete_frnd":
	            deletereq();
	            break;
	        case "send_frnd":
	        default:
	           addfriend();
	            break;
	    }
	    return apiLeave(success(345));
	}
	
	
	function deletereq(){
		$user=getThisUser();
		$id=getOtherUser();
		$fg=true;

		if(!CAN_UNFRND_ADMIN){
			foreach (ADMIN_LIST as $key => $value) {
				if(ucfirst($id)==ucfirst($value)){				
					$fg=false;
					break;
				}
			}
		}
		if($fg){
		    $drf=" (user='$id' AND recip='$user') OR (user='$user' AND recip='$id') ";
			$df="SELECT * FROM groups WHERE ". $drf;
			if(checknum($df)>0){
				queryMysql("DELETE FROM groups WHERE  ". $drf);
			}
		}
	    return apiLeave(success(345));
	}

	function addfriend(){	
		$user=getThisUser();
		$id=getOtherUser();
		$freq="SELECT * FROM groups WHERE (user='$id' AND recip='$user') OR (user='$user' AND recip='$id')";
		if(checknum($freq)<1){
			queryMysql("INSERT INTO groups (user_id, user, recip, confirm) VALUES (NULL, '$user', '$id', 1)");	
			addGroupNotify($user, $id);
		}

	}

	function fetchAllGroups(){
	    $inp=getGetorPostString('q');
	    $q="SELECT * FROM profiles WHERE (user LIKE '%$inp%' OR fullname LIKE '%$inp%') AND is_group=1";
	    if(empty(trim($inp)) || $inp=="1234"){
	        //show discover
    	    $q="SELECT * FROM profiles WHERE is_group=1";
	    }
	    return groupContent($q);
	}
	
	function getGroupAbout(){	
		$who=getGetorPostString('who');
	    $q="SELECT * FROM  profiles WHERE user ='$who' AND is_group=1";
	    return groupContent($q);
	}
	
	function fetchUserGroups(){	
		$user=getThisUser();
	    $q="SELECT * FROM  profiles WHERE user IN (SELECT groups.recip from groups where groups.user='$user')";
	    return groupContent($q);
	}
	
	function groupContent($q){
		$useri=$user=getThisUser();
		$array_=$array=array();

		$result = queryMysql($q);
		$num  = $result->num_rows;
			
		if($num<1){
			return apiLeave(emptyArray());
		}

		$curr_pages=getCurrentPage();
		$pages=intval($num/NO_OF_SEARCHES);
		$pages_left=$pages-$curr_pages;

		$result = queryMysql($q .calcpages($num, NO_OF_SEARCHES));
		$num  = $result->num_rows;

		if($num<1){
			return apiLeave(emptyArray());
		}


		for($i=0; $i<$num; $i++){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$gUser=$row['user'];
			$subtitle=(!empty($row['place']))?$row['place']:$row['country'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['work'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['school'];
			$image=getUserDP($row['user']);
			$fullname=(!empty($row['fullname']))?$row['fullname']:$row['user'];

			$id=$userii=$row['user'];
			
			$vv=$row['verified'];
			$veri=($vv==1 || $vv=="1" || in_array(strtolower($gUser), ADMIN_LIST))?"1":"0";
			
		    $confirm=checknum("SELECT * FROM groups WHERE (user='$id' AND recip='$user') OR (user='$user' AND recip='$id')");
		    $approved=checknum("SELECT * FROM groups WHERE recip='$id' AND confirm=2 AND user='$user'");
		    $frnds = checkNum("SELECT * FROM groups WHERE recip='$id'");
		    
			array_push($array_, array("id"=>$row['user_id'],
									"auth_username"=>$row['user'],
									"username"=>$row['user'],
									"subtitle"=>$subtitle,
									"bio"=>$row['bio'],
									"confirm"=>"$confirm",
									"approved"=>"$approved",
									"verified"=>$veri,
									"frnds_data"=>array(
												"r_sent"=>$confirm,
												"r_rcvd"=>$approved,
												"r_frnds"=>$confirm),
									"auth_data"=>array(
									 			"auth"=>$fullname,"fullname"=>$fullname,
									 			"auth_img"=>$image
									 			),
									"total_friends"=>$frnds
								));
		}


		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}

?>