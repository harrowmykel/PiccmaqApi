<?php 

function getMessageLists(){
	$user=getThisUser();
	$array= array();
	$useri=getOtherUser();

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("msgs/index.php?req=fetchmessage"), 
				"datatype"=>"json", 
				"data"=>getUserCred("who=".$useri), 
				"baseurl"=>API_BASE_URL);

	$res=$maq->post($post_param)->body;

	$result=$res->data;
	$num=count($result);
	for($i=0;$i<$num;$i++){
		$row=$result[$i];
		$postparser = new MsgParser();
		$postparser->load($row);
		if(!$postparser->isEmpty()){//fetch msg		
	    	array_push($array, array("username"=>$postparser->getAuth_username(),
									"userid"=>$postparser->getAuth_username(),
									"fullname"=>$postparser->getAuth_data()->getAuth(),
									"subtitle"=>$postparser->getSubtitle(),
									"time"=>getDWM2($postparser->getTime()),
									"timestamp"=>$postparser->getTime(),
									"auth"=>$postparser->getAuth_username(),
									"confirm"=>$postparser->getConfirm(),
									"image"=>$postparser->getImage(),
									"img"=>$postparser->getImage()));
		}
	}
	return $array;
}


function getAllMessages(){
	$user=getThisUser();
	$time_lauft=getGetorPostString('time');
	if(empty($time_lauft)){
		$time_lauft=CURRENT_TIME;
	}// time >$time_lauft
	$array=array();
		if(!empty($subtitle)){
			array_push($array, array("username"=>$useri,
									"userid"=>$useri,
									"fullname"=>getFullname($useri),
									"subtitle"=>$subtitle,
									"time"=>getDWM2($row['time']),
									"timestamp"=>$row['time'],
									"auth"=>$row['aut'],
									"confirm"=>$row['confirm'],
									"image"=>$image,
									"img"=>IMG_CLOUD_LINK.$image));
		}

}

	function sendMsg_($useri, $imgUrl=""){
		$user=getThisUser();
		$time=CURRENT_TIME;
		$persy=replaceKeys(getPostString('body'));

		if(!empty(getGetorPostString("is_wel"))){

		}else if(!empty(getGetorPostString("is_wel"))){

		}else{
			$adf="who=".$useri."&message=".$persy."&imgurl=".$imgUrl;		
		}

		/*$maq = new Maq();
		$adf="who=".$useri."&message=".$persy."&imgurl=".$imgUrl;
		$post_param=array("url"=>getUrlEnd("msgs/index.php?req=create"), 
					"datatype"=>"json", 
					"data"=>getUserCred($adf), 
					"baseurl"=>API_BASE_URL);	

		$res=$maq->post($post_param)->body;
		$result=$res->data;

		$succError = new SuccessError();
		$succError->load($result[0]);
		if($succError->isSuccessful())
			return $succError->getId();*/
		return 0;
	}


function getMessages(){
	$array=array();
	
	$user=getThisUser();
	$array= array();
	$useri=getOtherUser();

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("msgs/index.php?req=fetchmsgfromuser"), 
				"datatype"=>"json", 
				"data"=>getUserCred("who=".$useri), 
				"baseurl"=>API_BASE_URL);

	$res=$maq->post($post_param)->body;

	$result=$res->data;
	$num=count($result);
	for($i=0;$i<$num;$i++){
		$row=$result[$i];
		$postparser = new MsgParser();
		$postparser->load($row);
		if(!$postparser->isEmpty()){//fetch msg		
	    	array_push($array, array("auth_username"=>$postparser->getAuth_username(),
									"userid"=>$postparser->getAuth_username(),
									"auth"=>$postparser->getAuth_data()->getAuth(),//fullname
									"reciv"=>$postparser->getReciv_data()->getReciv(),
									"fullname"=>$postparser->getAuth_data()->getAuth(),
									"message"=>$postparser->getSubtitle(),
									"time"=>getDWM2($postparser->getTime()),
									"timestamp"=>$postparser->getTime(),
									"confirm"=>$postparser->getConfirm(),
									"image"=>$postparser->getImage(),
									"user_img"=>$postparser->getAuth_data()->getAuth_img(),
									"img"=>$postparser->getImage()));
		}
	}
	return $array;
}
 ?>