<?php

	function saveBDay($user, $bday){
		$q="SELECT * FROM bday_tbl WHERE user='$user'";
		$qw="INSERT INTO bday_tbl (id, bday, user) VALUES (NULL, $bday, '$user')";
		if(checknum($q)>0){
			$qw="UPDATE bday_tbl SET bday=$bday WHERE user='$user'";
		}
		queryMysql($qw);
	}

	function DPIsHuman($user){
		if(!CHECK_HUMAN_DP){
			return true;
		}		
		$q="SELECT dp_human, has_dp FROM members WHERE user='$user' AND dp_human>0";
		return (checknum($q)>0);
	}

	function getAllNewMem(){
		$user=getThisUser();
		$array=array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=searchonlinefriends"), 
					"datatype"=>"json", 
					"data"=>getUserCred("location=".API_NEW_MEMBERS), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				array_push($array, array("fullname"=>$profileParser->getAuth_data()->getFullname(),
										"username"=>$profileParser->getAuth_username()));
			}
		}
		return $array;
	}

	function getAllNewBday(){
		$array=array();
		$user=getThisUser();
		$id=getOtherUser();
		$VARR=VARR;
		$tomorow=getNextDay();
		$yester=getPrevDay();

		$q="SELECT user FROM bday_tbl ";

		$num=checknum($q);
		$q=$q." WHERE (NOT bday_tbl.user IN (SELECT bday_list.recip FROM bday_list WHERE bday_list.recip=bday_tbl.user AND bday_list.auth='$user')) AND bday<$tomorow AND bday>$yester ". calcpages($num, $VARR);

		$resultf=queryMysql($q);
		$nm=checknum($q);
		$efkg=0;
		for ($i=0; $i <$nm ; $i++) { 
			$row = $resultf->fetch_array(MYSQLI_ASSOC);
			$useri=$row['user'];
			$fullname=getFullname($useri);
			array_push($array,array("username"=>$useri,
						"fullname"=>ucwords($fullname)));
			$efkg++;
		}
		return $array;
	}
	
	function getUserBasic($id){
		$user=getThisUser();
		$array=array();
		$useri=getOtherUser();

		$maq = new Maq();

		$post_param=array("url"=>getUrlEnd("msgs/index.php?req=about"), 
					"datatype"=>"json", 
					"data"=>getUserCred("who=".$useri), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		if($num>0){
			$row=$result[0];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				$array = array("username"=>$profileParser->getAuth_username(),
						"userid"=>$profileParser->getId(),
						"fullname"=>$profileParser->getAuth_data()->getFullname(),
						"approved"=>$profileParser->getVerified(),
						"verified"=>$profileParser->getVerified(),
						"bio"=>replaceSmiles($profileParser->getBio()),
						"image"=>$profileParser->getAuth_data()->getAuth_img(),
						"r_sent"=>$profileParser->getFrnds_data()->getR_sent(),
						"r_rcvd"=>$profileParser->getFrnds_data()->getR_rcvd(),
						"r_frnds"=>$profileParser->getFrnds_data()->getR_frnds(),
						"t_friends"=>$profileParser->getTotal_friends()

						);
			}
		}
		return $array;
	}

	function getUserByUsername(){
		$useri=getOtherUser();
		return getUserProperty($useri);
	}

	function getGroupByUsername(){
		$useri=getOtherUser();
		return getUserProperty($useri, true);
	}

	function getUserProperty($useri, $isGroup=false){
		$user=getThisUser();
		$array=array();

		$maq = new Maq();
		if($isGroup){
			$post_param=array("url"=>getUrlEnd("groups/index.php?req=about", false), 
						"datatype"=>"json", 
						"data"=>getUserCred("who=".$useri), 
						"baseurl"=>API_BASE_URL);
		}else{			
			$post_param=array("url"=>getUrlEnd("profile/index.php?req=about", false), 
						"datatype"=>"json", 
						"data"=>getUserCred("who=".$useri), 
						"baseurl"=>API_BASE_URL);
		}
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		if($num>0){
			$row=$result[0];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				$array = array("username"=>$profileParser->getAuth_username(),
						"userid"=>$profileParser->getId(),
						"join_date"=>$profileParser->getJoin_date(),
						"last_seen"=>$profileParser->getLast_seen(),
						"total_seen_time"=>$profileParser->getTotal_seen_time(),
						"fullname"=>$profileParser->getAuth_data()->getFullname(),
						"gender"=>$profileParser->getGender(),
						"approved"=>$profileParser->getVerified(),
						"verified"=>$profileParser->getVerified(),
						"piccoins"=>$profileParser->getPiccoins(),
						"bio"=>replaceSmiles($profileParser->getBio()),
						"email"=>$profileParser->getEmail(),
						"dob"=>$profileParser->getDob(),
						"mob"=>$profileParser->getMob(),
						"yob"=>$profileParser->getYob(),
						"phone"=>$profileParser->getPhone(),
						"image"=>$profileParser->getAuth_data()->getAuth_img(),
						"r_sent"=>$profileParser->getFrnds_data()->getR_sent(),
						"r_rcvd"=>$profileParser->getFrnds_data()->getR_rcvd(),
						"r_frnds"=>$profileParser->getFrnds_data()->getR_frnds(),
						"t_friends"=>$profileParser->getTotal_friends()
						);
			}
		}
		return $array;
	}


	function getAllUsersPosts(){
		$user=getThisUser();
		$array= array();
		$useri=getOtherUser();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=userposts"), 
					"datatype"=>"json", 
					"data"=>getUserCred("who=".$useri), 
					"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$postparser = new PostParser();
			$postparser->load($row);
			if(!$postparser->isEmpty()){		
		    	array_push($array, array("auth"=>$postparser->getAuth_data()->getFullname(), 
		    							 "recip"=> $postparser->getReciv_data()->getFullname(), 
		    							 "auth_username"=>$postparser->getAuth_username(), 
		    							 "recip_username"=>$postparser->getReciv_username(), 
		    							 "time"=> getDWM($postparser->getTime()), 
		    							 "timestamp"=>$postparser->getTimestamp(),
		    							 "message"=> replaceSmiles($postparser->getSubtitle()),  
		    							 "img_auth"=>$postparser->getAuth_data()->getAuth_img(), 
		    							 "postid"=> $postparser->getId(),
		    							 "phrase"=>$postparser->getPhrase(),
		    							 "privacy"=>$postparser->getPrivacy(),
		    							 'image'=>$postparser->getImage(),
		    							 "likes"=>$postparser->getLikes(),
		    							 "liked"=>$postparser->getLiked(),
		    							 "comments"=>$postparser->getComments()));
			}
		}
		return $array;
	}

	function getAllGroupsPosts(){
		$user=getThisUser();
		$array= array();
		/*$q="SELECT * FROM messages INNER JOIN friends ON messages.auth=friends.recip AND friends.user='$user' ORDER BY messages.time DESC";*/
		$useri=getOtherUser();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=groupposts"), 
					"datatype"=>"json", 
					"data"=>getUserCred("who=".$useri), 
					"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$postparser = new PostParser();
			$postparser->load($row);
			if(!$postparser->isEmpty()){		
		    	array_push($array, array("auth"=>$postparser->getAuth_data()->getFullname(), 
		    							 "recip"=> $postparser->getReciv_data()->getFullname(), 
		    							 "auth_username"=>$postparser->getAuth_username(), 
		    							 "recip_username"=>$postparser->getReciv_username(), 
		    							 "time"=> getDWM($postparser->getTime()), 
		    							 "timestamp"=>$postparser->getTimestamp(),
		    							 "message"=> replaceSmiles($postparser->getSubtitle()),  
		    							 "img_auth"=>$postparser->getAuth_data()->getAuth_img(), 
		    							 "postid"=> $postparser->getId(),
		    							 "phrase"=>$postparser->getPhrase(),
		    							 "privacy"=>$postparser->getPrivacy(),
		    							 'image'=>$postparser->getImage(),
		    							 "likes"=>$postparser->getLikes(),
		    							 "liked"=>$postparser->getLiked(),
		    							 "comments"=>$postparser->getComments()));
			}
		}
		return $array;
	}

  function getUsernameAndRedirect_(){
    $username=$_GET['username'];
    if(!isUsernameValid($username)){
      header("Location: ".PC_ERROR_PAGE);
      return true;
    }
    return false;
  }

?>