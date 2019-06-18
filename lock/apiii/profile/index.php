<?php 
    error_reporting(E_ALL); ini_set('display_errors', 1);
	$root="../";
	include $root. "incl/index.php";
	
    $NoLogIn=array("create", "checkuser");
    runApiReqCheck($NoLogIn);
    
	$req=getReq();

	switch($req){
		case "about":
			page_is_first();
			$resultt= getProfileAbout();
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
		case "creategroup":
			$resultt= createGroup();
			break;
		case "create":
			$resultt= createUser();
			break;
		case "checkuser":
			page_is_first();
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
	    $str_str="";

	    $inp_array=array("dob", "mob", "yob");
	    $inp_array1=array("email", "phone");
	    foreach ($inp_array as $key => $value) {
	    	$key_value=getGetorPostString($value);
	    	if(!empty($key_value)){
	    		$str_str=$str_str." ".$value."=".$key_value.",";
	    	}
	    }
	    foreach ($inp_array1 as $key => $value) {
	    	$key_value=getGetorPostString($value);
	    	if(!empty($key_value)){
	    		if($value=="email"){
		    	    queryMysql("UPDATE members SET ".$value."='$key_value' where  user='$user'");
	    		}else{
	    			$str_str=$str_str." ".$value."='".$key_value."',";
	    		}
	    	}
	    }

	    if(!empty($img)){
    	    queryMysql("UPDATE profiles SET bio='$bio', ".$str_str." fullname='$fullname', prof_pic='$img', ext_link=1  where  user='$user'");
    	    saveDPToDB($user, $img, true);
	    }else{
    	    queryMysql("UPDATE profiles SET bio='$bio', ".$str_str." fullname='$fullname'  where user='$user'");
	    }
	    return apiLeave(success(345));
	}

    function createUser(){
        $fullname=$email=$gender=$dob=$mob=$yob=$pass=$user="";
        $user=getThisUser();
        $fullname=getGetorPostString('fullname');
        $fullname=empty($fullname)?$user:$fullname;
        
        $pass=getGetorPostString('pass');
        $check=getGetorPostString('check');
        $who=getGetorPostString('who');
        $ans=invUser();
        $error=validateSignUpUser($fullname, $pass, $user, "", false);
        if(empty($error)){
            $ans=success(3485);
        }else{
            if($error=="inv_user"){
                $ans=invUser();
            }else if($error=="inv_fields"){
                $ans=invUser();
            }else{
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
        $fullname=empty($fullname)?$who:$fullname;
        $imgurl=getGetorPostString('imgurl');
        $bio=getGetorPostString('bio');
        $dfgh=gnrtNewString(1, 3).$who;
       $error=validateSignUpUser($fullname, $pass, $user, $bio, true);
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
		$who=getGetorPostString('who');//show frnds of who?
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		if(empty($search)){
			if($location==API_CONTACTS){//frnds	
				$useri=empty($who)?$user:$who;			
    			$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND confirm = 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND confirm = 2)";
    		}else if($location==API_REQUEST){//requests all
    			$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND confirm <> 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND confirm <> 2)";
    		}else if($location==API_SUGGESTIONS){//suggestions all
    			$q="SELECT * FROM profiles WHERE (profiles.user NOT IN (SELECT user FROM friends WHERE friends.recip='$user')  OR profiles.user NOT IN (SELECT recip FROM friends WHERE friends.user='$user')) AND profiles.user <> '$user'";
    		}else{//all users
    			$q="SELECT * FROM profiles join members WHERE (members.user LIKE '%$search%' OR fullname LIKE '%$search%' OR email='$search') AND members.user=profiles.user";
    		}		
		}else{
			if($location==API_CONTACTS){//frnds
				$useri=empty($who)?$user:$who;	
				$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND friends.user LIKE '%$search%' AND confirm = 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND friends.recip LIKE '%$search%' AND confirm = 2)";
			}else if($location==API_REQUEST){//requests all
				$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT friends.user FROM friends WHERE friends.recip='$useri' AND friends.user LIKE '%$search%' AND confirm <> 2) OR profiles.user IN (SELECT friends.recip FROM friends WHERE friends.user='$useri' AND friends.recip LIKE '%$search%' AND confirm <> 2)";
			}else if($location==API_SUGGESTIONS){//suggestions all
				$q="SELECT * FROM profiles WHERE (profiles.user NOT IN (SELECT user FROM friends WHERE friends.recip='$user' AND friends.user LIKE '%$search%')  OR profiles.user NOT IN (SELECT recip FROM friends WHERE friends.user='$user' AND friends.recip LIKE '%$search%')) AND profiles.user <> '$user'";
			}else{//all users
				$q="SELECT * FROM profiles join members WHERE (members.user LIKE '%$search%' OR fullname LIKE '%$search%' OR email='$search') AND members.user=profiles.user";
			}				
		}
		
	    return getThisProfileFromQuery($q. " AND is_group=0");
	}

	function getSearchedGroupUser(){		
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=empty(getGetorPostString("location"))?$who:getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		if(empty($search)){
    		$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT groups.user FROM groups WHERE groups.recip='$location')";	
		}else{
    		$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT groups.user FROM groups WHERE groups.recip='$location' AND groups.user LIKE '%$search%')";		
		}
	    return getThisProfileFromQuery_($q, true);
	}

	function getNewMembers(){
		$search=$inp=getGetorPostString('q');
		$who=getGetorPostString('who');
		$location=getGetorPostString("location");
		$useri=$user=getThisUser();
		$array_=$array=array();
		$tomorow=getNextDay();
		$yester=getPrevDay();
		$tim=CURRENT_TIME-(ONLINE_TIMEOUT+DIFF_TIME);
		
		$array=array();
		$user=getThisUser();
		$id=getOtherUser();
		$VARR=VARR;

		$q="SELECT user FROM members WHERE (NOT members.user IN (SELECT welcome_list.recip FROM welcome_list WHERE welcome_list.recip=members.user AND welcome_list.auth='$user')) ORDER BY members.join_date DESC";
		$num=checknum($q);
		$result = queryMysql($q .calcpages($num, NO_OF_SEARCHES));
		$num  = $result->num_rows;
		if($num<1){
			$array_=emptyArray();
		}

		for($i=0; $i<$num; $i++){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			if(empty($row['user'])){
				continue;
			}
			$qui= "SELECT * FROM profiles WHERE profiles.user='".$row['user']."'";
			$result3 = queryMysql($qui);
			$row3 = $result3->fetch_array(MYSQLI_ASSOC);
			$image=getUserDP($row3['user']);
			$fullname=(!empty($row3['fullname']))?$row3['fullname']:$row3['user'];
			array_push($array_, array("id"=>$row3['user_id'],
									"auth_username"=>$row['user'],
									"auth_data"=>array(
									 			"auth"=>$fullname,
									 			"fullname"=>$fullname,
									 			"auth_img"=>$image
									 			)
								));
		}


		return apiLeave($array_);
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
		
		if($location==API_NEW_MEMBERS){
			return getNewMembers();
		}
		if(empty($search)){
			if($location==API_BIRTHDAY){//frnds				
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT bday_tbl.user FROM bday_tbl  WHERE bday<$tomorow AND bday>$yester)";
    		}/*else if($location==API_FRIENDS){//all users
    			$q="SELECT * FROM members WHERE members.user IN (SELECT DISTINCT friends.user FROM friends WHERE (friends.user='$user' OR friends.recip='$user') OR  members.user IN (SELECT DISTINCT friends.recip FROM friends WHERE (friends.user='$user' OR friends.recip='$user') AND friends.confirm=2) AND friends.confirm=2) AND last_seen>$tim";
    		}*/else{//all users
    			$q="SELECT * FROM profiles WHERE profiles.user IN (SELECT members.user FROM members WHERE (last_seen>$tim) )";
    		}	
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

// var_dump($q. " ORDER BY profiles.user ASC " .calcpages($num, NO_OF_SEARCHES));

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
			
			$isAdmin=0;

			if($isGroupUser){
			    $isAdmin=checkNum("SELECT user FROM groups WHERE groups.recip='location' AND groups.user='$userii' AND confirm=2");
			}

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
									"is_admin"=>$isAdmin,
									"online"=>$online,
									"last_seen"=>$qo,
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

		//Deprecated 
 /*place
  school
 	hobby
 	 dream
 	 	work */
 	 	
		$who=getGetorPostString('who');
		$useri=$user=getThisUser();
		$array_=$array=array();
	    $q="SELECT * FROM  profiles WHERE is_group=0 and user ='$who'";

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
			
			$result2=queryMysql("SELECT * FROM members WHERE user='$userii'");
			$row2=$result2->fetch_array(MYSQLI_ASSOC);
			$qo=$row2["last_seen"];
		    $tim=CURRENT_TIME-(ONLINE_TIMEOUT+DIFF_TIME);
		    if($qo>$tim){
		        $online="1";
		    }else{
		        $online=$qo;
		    }
		    $frnds = checkNum("Select * from friends where user='$userii'");
		    //$frnds
			array_push($array_, array("id"=>$row['user_id'],
									"auth_username"=>$row['user'],
									"piccoins"=>$row['piccoins'],
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
									 			),//new array obj begins here
									"join_date"=>$row2['join_date'],
									"last_seen"=>$row2['last_seen'],
									"total_seen_time"=>$row2['total_seen_time'],
									"email"=>$row2['email'],
									"dob"=>$row['dob'],
									"mob"=>$row['mob'],
									"yob"=>$row['yob'],
									"gender"=>!empty($row['gender'])?$row['gender']:MALE,
									"phone"=>$row['phone'],
									"place"=>$row['place'],
									"approved"=>$veri,
									"school"=>$row['school'],
									"country"=>$row['country'],
									"work"=>$row['work'],
									"dream"=>$row['dream'],
									"hobby"=>$row['hobby'],
									"piccoins"=>$row['piccoins'],
									"total_friends"=>$frnds
								));
		}


		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>0,
    				"curr_pages"=>0,
    				"pages_left"=>0));
	}

?>