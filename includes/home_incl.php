<?php 
function getRecentPosts(){
	$user=getThisUser();
	$array= array();
	/*$q="SELECT * FROM messages INNER JOIN friends ON messages.auth=friends.recip AND friends.user='$user' ORDER BY messages.time DESC";*/

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("posts/index.php?req=fetchlatest"), 
				"datatype"=>"json", 
				"data"=>getUserCred("location=".API_FRIENDS), 
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

function getFriendsRecentPosts(){
	$user=getThisUser();
	$array= array();
	/*$q="SELECT * FROM messages INNER JOIN friends ON messages.auth=friends.recip AND friends.user='$user' ORDER BY messages.time DESC";*/

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("posts/index.php?req=fetchfrom"), 
				"datatype"=>"json", 
				"data"=>getUserCred("location=".API_FRIENDS), 
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

function getPosts($q){
	$array=array();
	$user=getThisUser();	
	$result= querymysql($q);
	$nul=$result->num_rows;
	
	$result = queryMysql($q.calcpages($nul, NO_OF_HOME_RESULTS));
	$num  = $result->num_rows;

	for($la=0; $la<$num; ++$la){
   		$row=$result->fetch_array(MYSQLI_ASSOC); 

   		$time= $row['time'];
   		$postid=$row['id'];
   		$likes=checknum("SELECT * FROM likes WHERE msg_id=$postid");
   		$comments=checknum("SELECT * FROM messages WHERE reply_to=$postid");
   		$liked=checknum("SELECT * FROM likes WHERE msg_id=$postid AND user='$user'")>0;

	   if(ucfirst($row['auth']) != ucfirst($row['recip']))
		   	$d=2;
		   else
		   	$d=1;
		   	$image="";
		   	if(!empty($row['picture'])){
		   	    if(($row['ext_link'])!=0){
		   	      $image=$row['picture'];
		   	    }else {
		   	      $image=IMG_CLOUD_LINK.IMG_STORE.$row['picture'];
		   	    }
		   	}

		
		switch ($row['privacy_']) {
			case privacy_friends:
				$privacy=translate("frnds");
				break;
			case privacy_private:
				$privacy=translate("only_me");
				break;			
			default:
				$privacy=translate("public");
				break;
		}

    	array_push($array, array("auth"=>getFullname($row['auth']), 
    							 "recip"=> getFullname($row['recip']), 
    							 "auth_username"=>$row['auth'], 
    							 "recip_username"=> $row['recip'], 
    							 "time"=> getDWM($time), 
    							 "timestamp"=>$row['time'],
    							 "message"=> replaceSmiles($row['message']),  
    							 "img_auth"=>getUserDp($row['auth']), 
    							 "postid"=> $postid,
    							 "phrase"=>$d,
    							 "privacy"=>$privacy,
    							 'image'=>$image,
    							 "likes"=>$likes,
    							 "liked"=>$liked,
    							 "comments"=>$comments));
	}
	return $array;
}
?>