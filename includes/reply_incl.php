<?php 
	function getCommentArray(){
		$postid=getGetString('like_comment_id');
		$array=array();

		if(!empty($postid)){
			return getCommentArray_($postid);
		}
		return $array;
	}

	function resaveComment(){
		$comment=getPostString('xc_message');
		$user=getThisUser();
		$msg=getGetString('like_comment_id');
		$time=CURRENT_TIME;
		if(ucfirst($user)==ucfirst(@getCommentArray()['auth_username'])){			
			$q="UPDATE comments SET comment='$comment'";
			queryMysql($q);	
			return true;
		}
		return false;
	}

	function getAllReplyLikes(){
		$post_id=getGetString('like_comment_id');
		$q="SELECT * FROM comment_likes WHERE comment_id=$post_id";
		$num=checknum($q);
		$qu=$q." ORDER BY id DESC ".calcPages($num, COMMENTS_NO);
		$result=queryMysql($qu);
		$num=$result->num_rows;
		$array=array();
		$user=getThisUser();
		
		for($i=0; $i<$num; $i++){
			$row=$result->fetch_array(MYSQLI_ASSOC);
			$liked=$row['typen'];
			array_push($array, array("username"=>$row['user'],
									"fullname"=>getFullname($row['user']),
	    							 "liked"=>untranslate($liked)
									));
		}
		return $array;
	}

	function getCommentArray_($postid){
		$user=getThisUser();
		$array= array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=fetchthis".API_CONST), 
					"datatype"=>"json", 
					"data"=>getUserCred("postid=".$postid), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$postparser = new PostParser();
		$postparser->load($res->data);

		if(!$postparser->isEmpty()){
			$array=array("auth"=>$postparser->getAuth_data()->getFullname(), 
					 "auth_username"=>$postparser->getAuth_username(), 
					 "time"=> $postparser->getTime(), 
					 "timestamp"=>$postparser->getTimestamp(),
					 "message"=> replaceSmiles($postparser->getSubtitle()), 
					 "postid"=> $postparser->getId(),
					 "likes"=>$postparser->getLikes(),
					 "liked"=>$postparser->getLiked(),
					 'image'=>$postparser->getImage(),
					 'msg_id'=>$postparser->getId(),
					 "replies"=>$postparser->getComments());
		}
		
		return $array;
	}


	function getReplies(){
		$postid=getGetString('like_comment_id');
		$array=array();

		if(!empty($postid)){
			return getReplies_($postid);
		}
		return $array;
	}

	function getReplies_($post_id){
		$user=getThisUser();
		$q="SELECT * FROM comment_replies WHERE comment_id=$post_id order by time DESC";
		$num=checknum($q);
		$result=queryMysql($q.calcPages($num, COMMENTS_NO));
		$num=$result->num_rows;
		$array=array();
		
		for($i=0; $i<$num; $i++){				
	   		$row=$result->fetch_array(MYSQLI_ASSOC); 
	   		$time= $row['time'];
	    	array_push($array, array("fullname"=>getFullname($row['username']),  
						 "username"=>$row['username'], 
						 "time"=> getDWM($time), 
						 "timestamp"=>$row['time'],
						 "message"=> replaceSmiles($row['reply']), 
						 "commentid"=> $row['id']));
		}
		return $array;

	}

?>