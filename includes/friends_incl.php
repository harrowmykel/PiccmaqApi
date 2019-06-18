<?php 

	function getAllUsersFriends(){
		$user=getThisUser();
		$useri=empty(getOtherUser())?getThisUser():getOtherUser();
		$array=array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=search"), 
					"datatype"=>"json", 
					"data"=>getUserCred("location=".API_CONTACTS."&who=".$useri), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				array_push($array, array("username"=>$profileParser->getAuth_username(),
										"userid"=>$profileParser->getId(),
										"fullname"=>$profileParser->getAuth_data()->getFullname(),
										"subtitle"=>$profileParser->getSubtitle(),
										"r_sent"=>$profileParser->getFrnds_data()->getR_sent(),
										"r_rcvd"=>$profileParser->getFrnds_data()->getR_rcvd(),
										"r_frnds"=>$profileParser->getFrnds_data()->getR_frnds(),
										"image"=>$profileParser->getAuth_data()->getAuth_img()));
			}
		}
		return $array;
	}

	function getTotalFriends(){
		$user=getOtherUser();
		$frnds=checknum("SELECT recip FROM friends WHERE (user='$user' OR recip='$user') AND confirm=2");
		return $frnds;
	}

	/*"API_FRIENDS", "f
"API_REQUESTS", "
""*/

	function getFriendsSuggestions($numbw){
		$user=getThisUser();
		$useri=empty(getOtherUser())?getThisUser():getOtherUser();
		$array=array();
		
		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=search"), 
					"datatype"=>"json", 
					"data"=>getUserCred("location=".API_SUGGESTIONS."&who=".$useri), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);

		if($numbw!=0){
			$num=($num>$numbw)?$numbw:$num;
		}
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				array_push($array, array("username"=>$profileParser->getAuth_username(),
										"userid"=>$profileParser->getId(),
										"fullname"=>$profileParser->getAuth_data()->getFullname(),
										"subtitle"=>$profileParser->getSubtitle(),
										"image"=>$profileParser->getAuth_data()->getAuth_img()));
			}
		}
		return $array;
	}

	function getUnconfirmedRequests($numbw){
		$user=getThisUser();
		$useri=empty(getOtherUser())?getThisUser():getOtherUser();
		$array=array();
		
		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=search"), 
					"datatype"=>"json", 
					"data"=>getUserCred("location=".API_REQUEST), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);

		if($numbw!=0){
			$num=($num>$numbw)?$numbw:$num;
		}
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				array_push($array, array("username"=>$profileParser->getAuth_username(),
										"userid"=>$profileParser->getId(),
										"fullname"=>$profileParser->getAuth_data()->getFullname(),
										"subtitle"=>$profileParser->getSubtitle(),
										"image"=>$profileParser->getAuth_data()->getAuth_img()));
			}
		}
		return $array;
	}

	function getUnconfirmedRequestsByPage(){
		return getUnconfirmedRequests(0);	
	}

	function getAllOnlineFriends(){
		$user=getThisUser();
		$array=array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=searchonlinefriends"), 
					"datatype"=>"json", 
					"data"=>getUserCred("location=".API_FRIENDS), 
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
										"username"=>$profileParser->getAuth_username(),
										//"userid"=>$row['user_id'], user_idis only valid at profileuse username
										"lastseen"=>$profileParser->getLast_seen(),
										"online"=>($profileParser->getOnline()==1)));
			}
		}
		return $array;
	}

	function getAllOnlines(){
		$user=getThisUser();
		$array=array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=searchonlinefriends"), 
					"datatype"=>"json", 
					"data"=>getUserCred("location=".API_FRIENDS), 
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
										"username"=>$profileParser->getAuth_username(),
										//"userid"=>$row['user_id'], user_idis only valid at profileuse username
										"lastseen"=>$profileParser->getLast_seen(),
										"online"=>($profileParser->getOnline()==1)));
			}
		}
		return $array;
	}
?>