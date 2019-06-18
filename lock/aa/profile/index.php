<?php 
    error_reporting(E_ALL); ini_set('display_errors', 1);
	$root="../";
	include $root. "incl/index.php";
	
    $NoLogIn=array("create", "checkuser");
    runApiReqCheck($NoLogIn);
    
	$req=getReq();

	switch($req){
		case "about":
			$resultt= getProfileAbout();
			break;
		case "yokes":
			$resultt= getProfileYokes();
			break;
		case "edit":
		case "editgroup":
			$resultt= editprofile();
			break;
		case "editpass":
			$resultt= editpass();
			break;
		case "searchgroupuser":
		    $resultt=getSearchedGroupUser();
		    break;
		case "search":
			$resultt= getSearchedUser();
			break;
		case "searchonlinefriends":
		case "searchonline":
			$resultt= getSearchedOnlineUser();
			break;
		case "searchbirthdayfriends":
		case "searchbirthday":
			$resultt= getSearchedBirthdayUser();
			break;
		case "creategroup":
			$resultt= createGroup();
			break;
		case "create":
			$resultt= createUser();
			break;
		case "checkuser":
			$resultt= checkUser();
			break;
		case "reqfrnd":
		    $resultt=editRequest();
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
	       case "accept_frnd":
	           accept();
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
			$df="SELECT * FROM friends WHERE ". $drf;
			if(checknum($df)>0){
				queryMysql("DELETE FROM friends WHERE  ". $drf);
			}
		}
	}

	function addfriend(){	
		$user=getThisUser();
		$id=getOtherUser();
		$freq="SELECT * FROM friends WHERE (user='$id' AND recip='$user') OR (user='$user' AND recip='$id')";
		if(checknum($freq)<1){
			queryMysql("INSERT INTO friends (user_id, user, recip, confirm) VALUES (NULL, '$user', '$id', 1)");	
			addFrndReqNotify($user, $id);
		}

	}

	function accept(){
		$id=getOtherUser();
		$user=getThisUser();
		queryMysql("UPDATE friends SET confirm=2 WHERE (user='$id' AND recip='$user') OR (user='$user' AND recip='$id')");
		addAccptReqNotify($id, $user);
	}
	
	function editprofile(){
	    $who=$user=getThisUser();
	    $bio=getGetorPostString('bio');
	    $fullname=getGetorPostString('fullname');
	    $img=getGetorPostString('imgurl');
	    if(getReq()=="editgroup"){
    	    $who=getGetorPostString('who');
	        $nuh=checkNum("Select * from groups WHERE user='$user' AND recip='$who' AND confirm=2");
	        if($nuh<1){
        	    return apiLeave(noAuth());
	        }
	    }
	    return editProf($who, $user, $fullname, $bio, $img);
	}
	
	function editpass(){
	    $who=$user=getThisUser();
	    $new_pass=getGetorPostString('new_pass');
	    $pass=getGetorPostString('pass');
	    if(empty($new_pass)){
    	   return apiLeave(noFile(345));
	    }
	    $cnChng=checkNum("SELECT * FROM members WHERE user='$user' AND pass='$pass'")>0;
	    if($cnChng){
	        $query="UPDATE members set pass='$new_pass' WHERE user='$user' AND pass='$pass'";
	        queryMysql($query);
	        return apiLeave(success(345));
	    }
	   return apiLeave(noAuth(345));
	}
	
	function editProf($who, $user, $fullname, $bio, $img){
	    $user=$who;
	    if(!empty($img)){
    	    queryMysql("UPDATE profiles SET bio='$bio', fullname='$fullname', prof_pic='$img', ext_link=1  where  user='$user'");
    	    saveDPToDB($user, $img, true);
	    }else{
    	    queryMysql("UPDATE profiles SET bio='$bio', fullname='$fullname'  where user='$user'");
	    }
	    return apiLeave(success(345));
	}

    function createUser(){
        $lastname=$firstname=$fullname=$email=$gender=$dob=$mob=$yob=$pass=$user="";
        $fullname=$user=getThisUser();
        $pass=getGetorPostString('pass');
        $check=getGetorPostString('check');
        $who=getGetorPostString('who');
        $ans=invUser();
        $error=validateSignUp_($lastname,$firstname,$fullname,$email,$gender,$dob,$mob,$yob,$pass,$user);
        if(empty($error)){
            $ans=success(3485);
        }else{
            if($error=="inv_user"){
                $ans=invUser();
            }else if($error=="inv_fields"){
                $ans=invUser();
            }
        }
        return apiLeave($ans);
    }

	function createGroup(){
	    $lastname=$firstname=$fullname=$email=$gender=$dob=$mob=$yob=$pass=$user="";
        $fullname=$user=getThisUser();
        $pass=getGetorPostString('pass');
        $check=getGetorPostString('check');
        $who=getGetorPostString('who');
        $ans=invUser();
        $user=$who;
        $fullname=getGetorPostString('fullname');
        $imgurl=getGetorPostString('imgurl');
        $bio=getGetorPostString('bio');
        $dfgh=gnrtNewString(1, 3).$who;
       $error=validateSignUpGroup_($lastname,$firstname,$fullname,$email,$gender,$dob,$mob,$yob,$dfgh,$user);
         if(empty($error)){
            $ans=success(3485);
        }else{
            if($error=="inv_user"){
                $ans=invUser();
            }else if($error=="inv_fields"){
                $ans=invUser();
            }
        }
        editProf($who, $user, $fullname, $bio, $imgurl);
        $userl=getThisUser();
		queryMysql("INSERT INTO groups (user_id, user, recip, confirm) VALUES (NULL, '$userl', '$who', 2)");	
		addGroupNotify($userl, $who);
		return apiLeave($ans);
	}
	
	function checkUser(){
        $lastname=$firstname=$fullname=$email=$gender=$dob=$mob=$yob=$pass=$user="";
        $fullname=$user=getThisUser();
        $pass=getGetorPostString('pass');
        $check=getGetorPostString('check');
        $who=getGetorPostString('who');
        $ans=invUser();
        if((checkNum("SELECT * FROM profiles WHERE user='$user'"))>0){
            if((checkNum("SELECT * FROM profiles WHERE user='$user' AND is_group=0"))>0){
                if(username_and_passValid($user, $pass)>0){
                    $ans=success(345);
                }
            }else{
                $ans=invUser();
            }
        }else{
            $ans=invUser();
        }
        return apiLeave($ans);
	}
	
	function getSearchedUser(){		
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		if(empty($search)){
			if($location==API_CONTACTS){//frnds				
    			$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND confirm = 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND confirm = 2)";
    		}else if($location==API_REQUEST){//requests all
    			$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND confirm <> 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND confirm <> 2)";
    		}else{//all users
    			$q="SELECT * FROM profiles join members WHERE (members.user LIKE '%$search%' OR fullname LIKE '%$search%' OR email='$search') AND members.user=profiles.user";
    		}		
		}else{
			if($location==API_CONTACTS){//frnds
				$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND friends.user LIKE '%$search%' AND confirm = 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND friends.recip LIKE '%$search%' AND confirm = 2)";
			}else if($location==API_REQUEST){//requests all
				$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND friends.user LIKE '%$search%' AND confirm <> 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND friends.recip LIKE '%$search%' AND confirm <> 2)";
			}else{//all users
				$q="SELECT * FROM profiles join members WHERE (members.user LIKE '%$search%' OR fullname LIKE '%$search%' OR email='$search') AND members.user=profiles.user";
			}				
		}
		
	    return getThisProfileFromQuery($q. " AND is_group=0");
	}

	function getSearchedGroupUser(){		
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		if(empty($search)){
    		$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT groups.user FROM groups WHERE groups.recip='$location')";	
		}else{
    		$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT groups.user FROM groups WHERE groups.recip='$location' AND groups.user LIKE '%$search%')";		
		}
	    return getThisProfileFromQuery_($q, true);
	}

	function getSearchedOnlineUser(){		
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		$tomorow=getNextDay();
		$yester=getPrevDay();
		$tim=CURRENT_TIME-(ONLINE_TIMEOUT+DIFF_TIME);
		
		if(empty($search)){
			if($location==API_BIRTHDAY){//frnds				
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT bday_tbl.user FROM bday_tbl  WHERE bday<$tomorow AND bday>$yester)";
    		}else{//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (last_seen>$tim) )";
    		}	
    		/*else if(getReq()=="searchonlinefriends"){//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (members.user IN ((SELECT friends.user FROM friends WHERE friends.recip='$useri') OR members.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri')) AND (last_seen>$tim)))";
    		}*/
		}else{
			if($location==API_BIRTHDAY){//frnds			
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT bday_tbl.user FROM bday_tbl  WHERE (bday_tbl.user LIKE '%$search%') AND (bday<$tomorow AND bday>$yester))";
		    }else{//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (members.user LIKE '%$search%') AND (last_seen>$tim) )";
			}		
			/*else if(getReq()=="searchonlinefriends"){//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (members.user IN ((SELECT friends.user FROM friends WHERE friends.recip='$useri' AND friends.user LIKE '%$search%') OR members.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND friends.recip LIKE '%$search%')) AND (last_seen>$tim)))";
			}*/
		}
		
	    return getThisProfileFromQuery($q);
	}

	function getSearchedBirthdayUser(){		
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		$tomorow=getNextDay();
		$yester=getPrevDay();
		$tim=CURRENT_TIME-(ONLINE_TIMEOUT+DIFF_TIME);
		
		if(empty($search)){
			if($location==API_BIRTHDAY){//frnds				
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT bday_tbl.user FROM bday_tbl  WHERE bday<$tomorow AND bday>$yester)";
    		}else{//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (last_seen>$tim) )";
    		}	
		}else{
			if($location==API_BIRTHDAY){//frnds			
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT bday_tbl.user FROM bday_tbl  WHERE (bday_tbl.user LIKE '%$search%') AND (bday<$tomorow AND bday>$yester))";
		    }else{//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (members.user LIKE '%$search%') AND (last_seen>$tim) )";
			}	
		}
		
	    return getThisProfileFromQuery($q);
	}
		
	function getThisProfileFromQuery($q){
	    return getThisProfileFromQuery_($q, false);
	}
	
		
	function getThisProfileFromQuery_($q, $isGroupUser=false){
		    
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		   
		$num  =checknum($q);

		$curr_pages=getCurrentPage();
		$pages=intval($num/NO_OF_SEARCHES);
		$pages_left=$pages-$curr_pages;

		$result = queryMysql($q. " ORDER BY profiles.user ASC " .calcpages($num, NO_OF_SEARCHES));
		$num  = $result->num_rows;
		if($num<1){
			$array_=emptyArray();
		}


		for($i=0; $i<$num; $i++){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$subtitle=(!empty($row['place']))?$row['place']:$row['country'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['work'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['school'];
			$image=getUserDP($row['user']);
			$fullname=(!empty($row['fullname']))?$row['fullname']:$row['user'];

			$userii=$row['user'];

			$frnd_r=areFrnds($user, $userii);
			$r_rcvd=$frnd_r['r_rcvd'];//;?"1":"0";
			$r_sent=$frnd_r['r_sent'];//?"1":"0";
			$r_frnds=$frnd_r['r_frnds'];//?"1":"0";
			
			$isAdmin="0";
			
			if($isGroupUser){
			    $isAdmin=checkNum("SELECT user FROM groups WHERE groups.recip='location' AND groups.user='$userii' AND confirm=2");
			}

			array_push($array_, array("id"=>$row['user_id'],
									"auth_username"=>$row['user'],
									"subtitle"=>$subtitle,
									"is_admin"=>'$isAdmin',
									"frnds_data"=>array(
												"r_sent"=>$r_sent,
												"r_rcvd"=>$r_rcvd,
												"r_frnds"=>$r_frnds),
									"auth_data"=>array(
									 			"auth"=>$fullname,
									 			"fullname"=>$fullname,
									 			"auth_img"=>$image
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

// 		error_reporting(E_ALL); ini_set('display_errors', 1);
	function getProfileAbout(){	
		$who=getGetorPostString('who');
		$useri=$user=getThisUser();
		$array_=$array=array();
	    $q="SELECT * FROM  profiles WHERE profiles.user ='$who'";

		$result = queryMysql($q);
		$num  = $result->num_rows;
		if($num<1){
			$array_=emptyArray();
		}

        $numk=1;//$num; since we used = in query;
		for($i=0; $i<$numk; $i++){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$gUser=$row['user'];
			$subtitle=(!empty($row['place']))?$row['place']:$row['country'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['work'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['school'];
			$image=getUserDP($row['user']);
			$fullname=(!empty($row['fullname']))?$row['fullname']:$row['user'];

			$userii=$row['user'];

			$frnd_r=areFrnds($user, $userii);
			$r_rcvd=$frnd_r['r_rcvd'];//?"1":"0";
			$r_sent=$frnd_r['r_sent'];//?"1":"0";
			$r_frnds=$frnd_r['r_frnds'];//?"1":"0";
			
			$vv=$row['verified'];
			$veri=($vv==1 || $vv=="1" || in_array(strtolower($gUser), ADMIN_LIST))?"1":"0";
			
			$qo=getSql("SELECT last_seen FROM members WHERE user='$userii'", "last_seen");
		    $tim=CURRENT_TIME-(ONLINE_TIMEOUT+DIFF_TIME);
		    if($qo>$tim){
		        $online="1";
		    }else{
		        $online=$qo;
		    }
			array_push($array_, array("id"=>$row['user_id'],
									"auth_username"=>$row['user'],
									"subtitle"=>$subtitle,
									"bio"=>$row['bio'],
									"verified"=>$veri,
									"online"=>$online,
									"frnds_data"=>array(
												"r_sent"=>$r_sent,
												"r_rcvd"=>$r_rcvd,
												"r_frnds"=>$r_frnds),
									"auth_data"=>array(
									 			"auth"=>$fullname,"fullname"=>$fullname,
									 			"auth_img"=>$image
									 			)
								));
		}


		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>0,
    				"curr_pages"=>0,
    				"pages_left"=>0));
	}

	function getProfileYokes(){
		$user=getThisUser();
		$yks=checknum("SELECT * from posts where auth_key='$user' AND deleted=0");
		$likes=checknum("SELECT * from likes where user='$user' AND deleted=0");
		$yks=checknum("SELECT * from posts where auth_key='$user' AND deleted=0 AND is_reply <> 0");
		$array=array("yokes"=> $yks, "likes"=> $likes, "replies"=> $replies);

	}

?>