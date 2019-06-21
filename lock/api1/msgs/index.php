<?php 

	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array("createreport");
    runApiReqCheck($NoLogIn);
    
	$req=getReq();

	switch($req){
	    case "about":
			page_is_first();
	        $resultt=getChatAbout();
	        break;
		case "create":
		case "custommsg":
			$resultt= sendNewMessage();
			break;	
		case "createreport":
			$resultt= sendNewReport();
			break;
		case "fetch":
			$resultt= fetchMessage();
			break;
		case "fetchmessage":
			$resultt= fetchMessageThumbnail();
			break;
		case "fetchmsgfromuser":
			$resultt= fetchMsgsFromUser();
			break;
		case "delete":
			$resultt= deleteMsg();
			break;
		case "deleteconvo":
			$resultt= deleteConvo();
			break;
		case "fetchfrom":
			$resultt= fetchSMessages();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);

	function getChatAbout(){	
		$who=getGetorPostString('who');
		$useri=$user=getThisUser();
		$array_=$array=array();
	    $q="SELECT * FROM  profiles WHERE profiles.user ='$who'";

		$time=getGetorPostString('time');
		$time=(empty($time) || !is_numeric($time))?0:$time;
	    queryMysql("UPDATE pmesages SET confirm='".message_read."' WHERE aut='$who' AND reciv='$user' AND (time<$time OR time=$time)");
		$result = queryMysql($q);
		$num  = $result->num_rows;
		if($num<1){
			$array_=emptyArray();
		}

        $numk=1;//$num; since we used = in query;
		for($i=0; $i<$numk; $i++){
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$gUser=$row['user'];
			$image=getUserDP($row['user']);
			$fullname=(!empty($row['fullname']))?$row['fullname']:$row['user'];

			$userii=$row['user'];
			$vv=$row['verified'];
			$veri=($vv==1 || $vv=="1" || in_array(strtolower($gUser), ADMIN_LIST))?"1":"0";
			
			$qo=getSql("SELECT last_seen FROM members WHERE user='$userii'", "last_seen");
		    $tim=CURRENT_TIME-(ONLINE_TIMEOUT+DIFF_TIME);
		    if($qo>$tim){
		        $online="1";
		    }else{
		        $online=$qo;
		    }
		    $verio=getSql("SELECT time from pmesages WHERE (aut='$user' AND reciv='$userii') AND confirm='".message_read."' ORDER BY TIME DESC LIMIT 1", "time");
		    $verio1=getSql("SELECT time from pmesages WHERE (aut='$user' AND reciv='$userii') AND confirm='".message_rcvd."' ORDER BY TIME DESC LIMIT 1", "time");
		    $verio1=empty($verio1)?0:$verio1;
		    $verio=empty($verio)?0:$verio;
		    
		    
		    $recip = $user;
		    $auth = $gUser;
		    
		    $query5="SELECT * FROM pmessages_users WHERE (auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip')";
		    
			$row5 = queryMySql($query5)->fetch_array(MYSQLI_ASSOC);
			$score = $row5['score'];
			$image_convo =$row5["img"];
		    
			array_push($array_, array("id"=>$row['user_id'],
									"auth_username"=>$row['user'],
									"verified"=>$veri,
						            "confirm"=> message_read, 
									"online"=>$online,
									"last_read"=>$verio,
									"last_rcvd"=>$verio1,
									"convo_score"=>$score,
									"convo_image"=>$image_convo,
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
					"pages"=>0,
    				"curr_pages"=>0,
    				"pages_left"=>0));
	}
	
	function deleteMsg(){
		$who=getGetorPostString('msg_id');
		$user=getThisUser();
		if(empty($who)){
		    return apiLeave(noFile());
		}
		$query="UPDATE pmesages set del_auth=1 WHERE aut='$user' AND user_id=$who";
		queryMysql($query);
		$query="UPDATE pmesages set del_recip=1 WHERE reciv='$user' AND user_id=$who";
		queryMysql($query);
		return apiLeave(success(345));
	}
	
	function deleteconvo(){
		$who=getGetorPostString('who');
		$user=getThisUser();
		if(empty($who)){
		    return apiLeave(noFile());
		}
		$query="UPDATE pmesages set del_auth=1 WHERE aut='$user' AND reciv='$who'";
		queryMysql($query);
		$query="UPDATE pmesages set del_recip=1 WHERE reciv='$user' AND aut='$who'";
		queryMysql($query);
		return apiLeave(success(345));
	}
	
	function sendNewMessage(){
		$user=getThisUser();
		$time=CURRENT_TIME;
		$array_list=getGetorPostString('text');
		$useri=getGetorPostString("who");
		$id = 0;
		
		if(empty($useri)){
            $array_list=str_replace("\\'", '"', $array_list);
    		$arr=json_decode($array_list);
    		$num=count($arr);
    		for($i=0; $i<$num; $i++){
    		    $arg=$arr[$i];
    		    $persy=replaceKeys($arg->message);
    		    $useri=$recip=$arg->recip;
    		    $img_url=$arg->image;
    		    $id = addMsgToDb($user, $useri, $persy, $img_url);
        		if(in_array($useri, getAdmin_accts_dnfnfnnfnfjn())){
        			$persy_=translate_var("get_back_to_user", array($user));
        			$q="DELETE FROM pmesages WHERE text='$persy_'";
        			queryMysql($q);
        			addMsgToDb($useri, $user, $persy_);
        		}	
    		}
		}else{

    		$persy="";
    		$img_url="";

			if(getReq()=="custommsg"){
				$type = getGetorPostString('type');
				$fname1 = "@".$useri;
				$fname2 = "@".$user;

				if($type=="welcome"){
		    		$persy=translate_var('wel_msg', array($fname1, translate('app_name'), $fname2));
		    		$qq1 = "INSERT INTO welcome_list (id, recip, auth) VALUES (NULL, '$useri', '$user')";
		    		queryMysql($qq1);
				}else if($type=="bday"){
		    		$persy=translate_var('wel_msg', array($fname1, translate('app_name'), $fname2));
				}
	    		$img_url="";	
			}else{
	    		$persy=replaceKeys(getGetorPostString('message'));
	    		$img_url=getGetorPostString("imgurl");				
			}
    		$id = addMsgToDb($user, $useri, $persy, $img_url);
		
    		if(in_array($useri, getAdmin_accts_dnfnfnnfnfjn())){
    			$persy_=translate_var("get_back_to_user", array($user));
    			$q="DELETE FROM pmesages WHERE text='$persy_'";
    			queryMysql($q);
    			addMsgToDb($useri, $user, $persy_);
    		}	
		}
		$rty=success(64564);
		$rty[0]["id"]=$id;
		return apiLeave($rty);
	}
	
	function sendNewReport(){
		$user=getThisUser();
		$time=CURRENT_TIME;
		$array_list=getGetorPostString('text');
		$useri=getGetorPostString("who");
		$id = 0;
		
		if(empty($useri)){
            $array_list=str_replace("\\'", '"', $array_list);
    		$arr=json_decode($array_list);
    		$num=count($arr);
    		for($i=0; $i<$num; $i++){
    		    $arg=$arr[$i];
    		    $persy=replaceKeys($arg->message);
    		    $useri=$recip=$arg->recip;
    		    $img_url=$arg->image;
    		    $id = addMsgToDb($user, $useri, $persy, $img_url);
        		if(in_array($useri, getAdmin_accts_dnfnfnnfnfjn())){
        			$persy_=translate_var("get_back_to_user", array($user));
        			$q="DELETE FROM pmesages WHERE text='$persy_'";
        			queryMysql($q);
        			addMsgToDb($useri, $user, $persy_);
        		}	
    		}
		}else{
    		$persy=replaceKeys(getGetorPostString('message'));
    		$img_url=getGetorPostString("imgurl");
    		$id = addMsgToDb($user, $useri, $persy, $img_url);
		
    		if(in_array($useri, getAdmin_accts_dnfnfnnfnfjn())){
    			$persy_=translate_var("get_back_to_user", array($user));
    			$q="DELETE FROM pmesages WHERE text='$persy_'";
    			queryMysql($q);
    			addMsgToDb($useri, $user, $persy_);
    		}	
		}
		$rty=success(64564);
		$rty[0]["id"]=$id;
		return apiLeave($rty);
	}

	function fetchMessage(){
		return fetchSMessages();
	}

	function fetchSMessages(){
		$user=getThisUser();
		$time=getGetorPostString('time');
		$time=(empty($time) || !is_numeric($time))?0:$time;
		$query="Select * from pmesages WHERE ((reciv='$user' AND del_recip<>1)  OR (aut='$user' AND del_auth<>1)) AND time>$time  ORDER BY time ASC";
		$array =msgContent($query);
		return $array;
	}

	function fetchMsgsFromUser(){
		$user=getThisUser();
		$time=getGetorPostString('time');
		$who=getGetorPostString("who");
		$time=(empty($time) || !is_numeric($time))?0:$time;
		$query="Select * from pmesages WHERE ((reciv='$user' AND aut='$who' AND del_recip<>1)  OR (aut='$user' AND reciv='$who' AND del_auth<>1)) AND time>$time  ORDER BY time DESC";
		$array =msgContent($query);
		return $array;
	}

	function fetchMessageThumbnail(){
		$user=getThisUser();
		$array=array();
		$qq="SELECT DISTINCT pmesages.aut,pmesages.reciv FROM pmesages WHERE (reciv='$user' AND del_recip<>1)  OR (aut='$user' AND del_auth<>1)  ORDER BY time  DESC";
		if(!empty(getGetString("unread"))){
			$qq="SELECT DISTINCT pmesages.aut,pmesages.reciv FROM pmesages WHERE (reciv='$user' AND del_recip<>1 AND confirm='".message_sent."')  ORDER BY time  DESC";
		}

		/*CUSTOM CODE*/
		// $qq = "SELECT * FROM pmessages_users WHERE (recip='$user' OR auth = '$user')  ORDER BY end_time DESC ".calcpages($num, NO_OF_MSGS));
		$qq .= calcpages(2000, NO_OF_MSGS);
		$result=queryMysql($qq);

		$num= $result->num_rows;
		$arr=[];

		for($i=0; $i<$num; $i++){
			$rowd = $result->fetch_array(MYSQLI_ASSOC);
			$useri=(strtolower($user)==strtolower($rowd['aut']))?$rowd['reciv']:$rowd['aut'];
			if(in_array(ucfirst($useri), $arr))continue;
			$arr[]=ucfirst($useri);
			$qq="SELECT * FROM pmesages WHERE (reciv='$user' AND aut='$useri' AND del_recip<>1) OR (reciv='$useri' AND aut='$user' AND del_auth <> 1) ORDER BY pmesages.time DESC LIMIT 1";
			$resultf=queryMysql($qq);
			$row = $resultf->fetch_array(MYSQLI_ASSOC);
			$subtitle=replaceSmiles($row['text']);
			//$useri=($user==$row['aut'])?$row['reciv']:$row['aut'];
			$image=getUserDP($useri);
			$fullname=getFullname($useri);
			if(!empty($subtitle)){
				array_push($array, array("id"=>$useri, 
										 "auth_username"=> $useri, 
										"time"=>$row['time'],
										 "timestamp"=>$row['time'],
										 "subtitle"=> $subtitle, 
										 "confirm"=> $row["confirm"], 
										 "image"=> $image,
										 "auth_data"=>fetchAuthData($useri)
										));
			}
		}
		return apiLeave($array);
	}

	function msgContent($query){//only for fetch smessages
		$result=queryMysql($query);
		$num=$result->num_rows;
		$array_=$array=array();

		$curr_pages=getCurrentPage();
		$pages=intval($num/MAX_MESSAGES);
		$pages_left=$pages-$curr_pages;
		$result=queryMysql($query.calcpages($num,MAX_MESSAGES));
		$num=$result->num_rows;

		if($num<1){
			$array_=emptyArray();
		}

        $word_query="UPDATE pmesages SET confirm='".message_rcvd."' WHERE ( user_id=0";
        $thisUser=getThisUser();
        $thisUser=strtolower(trim($thisUser));
		for($i=0; $i<$num; $i++){
			$row=$result->fetch_array(MYSQLI_ASSOC);			
			$image_pic="";
			if(strlen($row['pic'])>4){
				if($row['ext_link']==1){		
					$image_pic=$row['pic'];
				}else{				
					$image_pic=IMG_CLOUD_LINK.MESS_IMG_STORE.$row['pic'];
				}
			}
			$idop=$row["user_id"];
			$reciv=strtolower(trim($row["reciv"]));
			if($reciv==$thisUser){
			    $word_query.=" OR user_id=$idop";
			}
    		$array=array("id"=>$idop,
    					"reciv_username"=>$reciv, 
						 "auth_username"=> $row["aut"], 
						 "timestamp"=>$row['time'],
						 "subtitle"=> $row["text"], 
						 "confirm"=> $row["confirm"], 
						 "image"=> $image_pic,
						 "auth_data"=>fetchAuthData($row["aut"]),
						 "reciv_data"=>fetchRecivData($row["reciv"])
						);
			array_push($array_, $array);
		}

		queryMysql($word_query.")");
		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}

?>