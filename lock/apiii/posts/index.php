<?php 

	error_reporting(E_ALL); ini_set('display_errors', 1);
	$root="../";
	include $root. "incl/index.php";
	$req=getReq();
	
    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
	switch($req){
		case "fetchfrom":
			$resultt= getNewestPost();
			break;
		case "fetchdiscover":
			$resultt= getDiscoverPost();
			break;
		case "fetchlatest":
    		$resultt= getLatestPost();
    		break;
		case "fetchthis":
			$resultt= getVeryPost();
			break;
		case "deletepost":
			$resultt= deletePost();
			break;
		case "userposts":
			$resultt= getPostsBy();
			break;
		case "groupposts":
			$resultt= getGPostsBy();
			break;
		case "likepost":
			$resultt= likePost();
			break;
		case "unlikepost":
			$resultt= unlikePost();
			break;
		case "create":
			$resultt= createPost(false);
			break;
		case "edit":
			$resultt= editPost();
			break;
		case "createtouser":
			$resultt= createPost(true);
			break;
		case "getpost":
			$resultt= getPostsInfo();
			break;
		case "getlikes":
			$resultt= getLikes();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);

	function getNewestPost(){
		$user=getThisUser();
		$fav=getGetorPostString("q");
		$who=getGetorPostString('who');
		$q="SELECT * FROM messages WHERE reply_to=0 AND is_group=0 AND privacy_=".privacy_public;
		$m1month=OLD_POST_AGO;
		if(RECENT_POST_IS_RAND){
			$q=$q." AND messages.time >$m1month  ORDER BY RAND() DESC";
		}else{
			$q=$q." ORDER BY messages.time DESC";
		}
		if(!empty($fav)){
			//only searches friends post OR public
			$q="SELECT * FROM messages WHERE ((messages.auth IN (SELECT friends.recip FROM friends WHERE friends.user='$user' AND confirm=2)) OR (messages.auth IN (SELECT friends.recip FROM friends WHERE friends.user='$user' AND confirm=2)) OR privacy_=".privacy_public.")  AND message LIKE '%$fav%' ORDER BY messages.time DESC ";
			//searches all public posts
			// $q="SELECT * FROM messages WHERE privacy_=".privacy_public."  ORDER BY messages.time DESC ";
		}
		if($who!=""){
		    $q="SELECT * FROM messages WHERE messages.auth ='$who' AND is_group=0 ORDER BY messages.time DESC ";
		}
		return getPosts($q);
	}

	function getDiscoverPost(){
		$user=getThisUser();
		$fav=getGetorPostString("q");
		$who=getGetorPostString('who');
		$q="SELECT * FROM messages WHERE reply_to=0 AND is_group=0 AND privacy_=".privacy_public;
		$m1month=OLD_POST_AGO;
		if(RECENT_POST_IS_RAND){
			$q=$q." AND messages.time >$m1month  ORDER BY RAND() DESC";
		}else{
			$q=$q." ORDER BY messages.time DESC";
		}
		if(!empty($fav)){
			//only searches friends post OR public
			$q="SELECT * FROM messages WHERE ((messages.auth IN (SELECT friends.recip FROM friends WHERE friends.user='$user' AND confirm=2)) OR (messages.auth IN (SELECT friends.recip FROM friends WHERE friends.user='$user' AND confirm=2)) OR privacy_=".privacy_public.")  AND  AND is_group=0 message LIKE '%$fav%' ORDER BY messages.time DESC ";
			//searches all public posts
			// $q="SELECT * FROM messages WHERE privacy_=".privacy_public."  ORDER BY messages.time DESC ";
		}
		if($who!=""){
		    $q="SELECT * FROM messages WHERE messages.auth ='$who' AND is_group=0 ORDER BY messages.time DESC ";
		}
		return getPosts($q);
	}

	function getLatestPost(){
		$user=getThisUser();
		$fav=getGetorPostString("q");
		$q="SELECT * FROM messages WHERE reply_to=0 AND is_group=0 AND privacy_=".privacy_public;
		$q=$q." ORDER BY messages.time DESC";
		if(!empty($fav)){
			//only searches public
			$q="SELECT * FROM messages WHERE privacy_=".privacy_public."  AND is_group=0 AND message LIKE '%$fav%' ORDER BY messages.time DESC ";
		}
		return getPosts($q);
	}

	function getVeryPost(){
		$user=getThisUser();
		$fav=getGetorPostString("q");
		$who=getGetorPostString('postid');
		$q="SELECT * FROM messages WHERE messages.id ='$who'";
		return getPosts($q, true);
	}

	function getPostsBy(){
		$user=getThisUser();
		$who=getGetorPostString('who');
		if(empty(trim($who))){
		    return apiLeave(emptyArray());
		}
	    $q="SELECT * FROM messages WHERE (messages.auth ='$who' OR recip='$who') AND reply_to=0 AND is_group=0 ORDER BY messages.time DESC ";
		return getPosts($q);
	}

	function getGPostsBy(){
		$user=getThisUser();
		$who=getGetorPostString('who');
		if(empty(trim($who))){
		    return apiLeave(emptyArray());
		}
	    $q="SELECT * FROM messages WHERE (messages.auth ='$who' OR recip='$who') AND reply_to=0 AND is_group=1 ORDER BY messages.time DESC ";
		return getPosts($q);
	}

	function deletePost(){
		$postid=getGetorPostString('postid');
		return apiLeave(delPost($postid));
	}

	function delPost($id, $chck=false){
        $postid=$id;
		$user=getThisUser();
		$q="SELECT * FROM messages WHERE id=$postid order by time DESC";
		$result= querymysql($q);
		$num  = $result->num_rows;
   		$row=$result->fetch_array(MYSQLI_ASSOC); 
   		$ntAllowed=(ucfirst($user)==ucfirst($row['auth'])) || ucfirst($user)==ucfirst($row['recip']) || $chck;
   		if(!$ntAllowed){
   			return noAuth();
   		}
   		if(!empty($row['picture'])){
   			$pic=$row['picture'];
   			if(!empty($pic)){
   				$pic_url="..".IMG_STORE.$pic;
   				if(file_exists($pic_url) && is_file($pic_url)){
   					unlink($pic_url);
   				}
   			}
   		}

		$q="DELETE FROM messages WHERE id=$postid";
		querymysql($q);
   		$likes=queryMysql("DELETE FROM likes WHERE msg_id=$postid");   		

		$q="SELECT * FROM messages WHERE reply_to=$postid";
		$result= querymysql($q);
		$num  = $result->num_rows;
   		for ($i=0; $i <$num ; $i++) { 
   			$row=$result->fetch_array(MYSQLI_ASSOC);
   			$idd=$row['id'];
   			delPost($idd, true);
   		}
		return success(64569);
	}

	function unlikePost(){	
		$id=getGetorPostString('postid');
		$subj=$user=getThisUser();
		queryMysql("DELETE FROM likes WHERE msg_id=$id AND user='$user'");
		return apiLeave(success(345));
	}


	function likePost(){	
		$id=getGetorPostString('postid');
		if(empty($id)){
			return apiLeave(success(345));			
		}
		$subj=$user=getThisUser();
		$like=(!empty(getGetorPostString("type")))?getGetorPostString("type"):LIKE;		
		$obj=getSql("SELECT auth FROM messages WHERE id=$id", 'auth');
		
			
		if(checknum("SELECT typen FROM likes WHERE msg_id=$id AND user='$user'")>0){
			$q="UPDATE likes SET typen='$like' WHERE msg_id=$id AND user='$user'";
		}else{
			$q="INSERT INTO likes (id, msg_id, user, typen) VALUES (NULL, $id, '$user', '$like')";
		}
		queryMysql($q);
		$post_id=$id;
		addLikeNotify($subj, $obj, $post_id);
		return apiLeave(success(345));;
	}

	function getLikes(){
		$post_id=getGetorPostString('postid');
		$q_search=getGetorPostString("q");
		$end=" ";
		if(!empty($q_search)){
			$end= " AND user LIKE '%$q_search%'";
		}
		$q="SELECT * FROM likes WHERE msg_id=$post_id ". $end;
		/*$nul=checknum($q);*/

		$nul=DEFAULT_NUM_VAR;
		$qu=$q." ORDER BY id DESC ".calcPages($nul, COMMENTS_NO);
		$result=queryMysql($qu);
		$num=$result->num_rows;
		$array_=array();
		$user=getThisUser();

		$nul=$result->num_rows;
		
		$curr_pages=getCurrentPage();
		$pages=intval($nul/COMMENTS_NO);
		$pages_left=$pages-$curr_pages;
		if($num<1){
			return apiLeave(emptyArray());
		}
		for($i=0; $i<$num; $i++){
			$row=$result->fetch_array(MYSQLI_ASSOC);
			$liked=$row['typen'];

			$userii=$row['user'];
			$frnd_r=areFrnds($userii, $user);
			$r_rcvd=$frnd_r['r_rcvd'];
			$r_sent=$frnd_r['r_sent'];
			$r_frnds=$frnd_r['r_frnds'];

			array_push($array_, array("id"=>$row['id'],
									"auth_username"=>$row['user'],
									"liked"=>$row['typen'],
									"frnds_data"=>array(
												"r_sent"=>$r_sent,
												"r_rcvd"=>$r_rcvd,
												"r_frnds"=>$r_frnds),
									 "auth_data"=>fetchAuthData($row["user"])
									 			));
		}


		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}

	function createPost($toUser){
		$user=getThisUser();
		$posgid=getGetorPostString("postid");
		$message=$text=replaceSmiles_(getGetorPostString('text'));
		$privacy=getGetorPostString('privacy');
		if($toUser){
    		$view=(!empty(getGetorPostString('who')))?getGetorPostString('who'):$user;
		    $privacy=privacy_public;
		}else{
		    $view=$user;
		}

		if($view!=$user){
    		$text="@".$view." ".$text;
		}
		$message=$text;
		
		$time=CURRENT_TIME;
		$fnjfn= getPrevDay();
		$ret=getGetorPostString("imgurl");

		if(empty($text) && empty($ret)){
			return apiLeave(noFile());
		}
		$posgid=(empty($posgid))?0:$posgid;
		if($posgid!=0){
		    $view=getSql("SELECT auth from messages where id=".$posgid, "auth");
		}
		$isgrp=(checkNum("SELECT * FROM profiles WHERE user='$view' AND is_group=1")>0)?1:0;
		$e="INSERT INTO messages (id, auth, recip, privacy_, time, message, picture, is_uneditable, reply_to, ext_link, is_group) VALUES(NULL, '$user', '$view', '$privacy', $time, '$text', '$ret', 0, $posgid, 1, $isgrp)";
		
    	queryMysql($e);
        $id=getLastId();
   		checkMentions($id, $user, $view, $text, true);
		$rty=success(345);
		$rty[0]["id"]=$id;
		return apiLeave($rty);
	}
	
	function editPost(){
		$user=getThisUser();
		$view=$id=$posgid=getGetorPostString("postid");
		$message=$text=replaceSmiles_(getGetorPostString('text'));
		$privacy=getGetorPostString('privacy');
		$time=CURRENT_TIME;

		if(empty($text) || empty($posgid)){
			return apiLeave(noFile());
		}
		$nu=checkNum("SELECT * FROM messages WHERE auth='$user' AND id=$posgid");
		
		if($nu>0){
			$e="UPDATE messages SET privacy_='$privacy', message='$text' WHERE auth='$user' AND id=$posgid";
		}else{
			return apiLeave(invUser());
		}
    	queryMysql($e);

   		checkMentions($id, $user, $view, $text);
		
		return apiLeave(success(345));
	}

	function checkMentions($id, $user, $recip, $message, $is_comment=false){
		if(in_array($user, ADMIN_LIST)){	
			if($user==$recip && !$is_comment){
				addNewPostNotifyAll($user, $id);
			}
		}
		if($user!=$recip && !$is_comment){
			addNewPostNotify($user, $recip, $id);
		}
		if($is_comment){
		    addReplyNotify($user, $recip, $id);
		}

	    $replacement=' ';
	    $patter = '/^[^@#]*[ ]/';
	    $pattern = '/[ ][^@#]*/';
	   	$new_phone = preg_replace($patter, $replacement, $message);
	   	$final = preg_replace($pattern, $replacement, $new_phone); 
	    $new = preg_replace('/[@]/', ' @', $final);
	    $newf = preg_replace('/[#]/', ' #', $new);
	           
	    // Extract the username keywords into an array
	    $clean_search = str_replace(',', ' ', $newf);
	    $search_words = explode(' ', $clean_search);
	   	$final_search_words = array();
	    if (count($search_words) > 0) {
	      foreach ($search_words as $word) {
	        if (!empty($word)) {
	          $final_search_words[] = $word;
	        }
	      }
	    }
	   if (count($final_search_words) > 0) {
			foreach($final_search_words as $word) {
			  	if(strpos($word, "@")!=(-1)){
			    	$newfo = str_replace('@', '', $word);
			  		//check if frnds
			  		$knw=checkNum("SELECT * from friends where ((user='$newfo' AND recip='$user') OR (user='$user' AND recip='$newfo')) AND confirm=2");
			  		if($knw>0){
					 	queryMysql("INSERT INTO mentions VALUES(NULL, '$id', '$user', '$newfo')");
					 	addMentionNotify($user, $newfo, $id);
			  		}
			  	}
		 	}
		}
	}


	function getPostsInfo(){
		$user=getThisUser();
		$fav=getGetorPostString("postid");
		if(empty($fav)){
			apiLeave(invReq());
		}
		$array=$array_replies=array();
		$q="SELECT * FROM messages WHERE id=$fav ORDER BY messages.time DESC ";
		$array_=getPosts($q, true)['data'];//08002295555
	
		$q="SELECT * FROM messages WHERE reply_to=$fav ORDER BY messages.time DESC ";
		$left=getPosts($q);
		$array__=$left['data'];//08002295555
		$asd=count($array__);
		for($i=0; $i<$asd; $i++){
			array_push($array_replies, $array__[$i]);
		}
		$pages_left=$left['pagination']["pages_left"];
		$pages=$left['pagination']["pages"];
		$curr_pages=$left['pagination']["curr_pages"];
		$arrayb= array(
				"main_post"=>$array_[0],
				"data"=>$array_replies,
				"pagination"=>array(
					"pages"=>$pages,
					"curr_pages"=>$curr_pages,
					"pages_left"=>$pages_left));
		return $arrayb;
	}


function getPosts($q, $noPagination=false){
	$array=array();
	$user=getThisUser();
	$result = null;
	$num = $nul=DEFAULT_NUM_VAR;	
	

	if(!$noPagination){
		$curr_pages=getCurrentPage();
		$pages=intval($nul/NO_OF_HOME_RESULTS);
		$pages_left=$pages-$curr_pages;

		$result = queryMysql($q.calcpages($nul, NO_OF_HOME_RESULTS));
		$num  = $result->num_rows;		
	}else{
		$result= querymysql($q);
		$num=$nul=$result->num_rows;

		$num=$nul;
		$curr_pages=0;
		$pages=0;
		$pages_left=0;
	}
	for($la=0; $la<$num; ++$la){
   		$row=$result->fetch_array(MYSQLI_ASSOC); 

   		$time= $row['time'];
   		$postid=$row['id'];
   		$likes=checknum("SELECT * FROM likes WHERE msg_id=$postid");
   		$comments=checknum("SELECT * FROM comments WHERE msg_id=$postid");
   		$liked=checknum("SELECT * FROM likes WHERE msg_id=$postid AND user='$user'")>0;
   		if($liked){
   			$liked=getSql("SELECT * FROM likes WHERE msg_id=$postid AND user='$user'", "typen");
   		}else{
   			$liked="0";
   		}
	   if(ucfirst($row['auth']) != ucfirst($row['recip']))
		   	$d=2;
		   else
		   	$d=1;

		if($row['ext_link']>0){
			$image=(!empty($row['picture']))?$row['picture']:"";
		}else{
			$image=(!empty($row['picture']))?IMG_CLOUD_LINK.IMG_STORE.$row['picture']:"";			
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
		
		$repl=empty($row['reply_to'])?"0":$row['reply_to'];
		$whok=getFullname($row['auth']);
		if(empty(trim($whok))){
		   continue;
		}
        $wb_link=DEF_URL."/story.php?postid=".$postid;
    	array_push($array, array("id"=> $postid,
    							 "auth_username"=>$row['auth'], 
    							 "reciv_username"=> $row['recip'], 
    							 "time"=> $time, 
    							 "timestamp"=>$row['time'],
    							 "subtitle"=> $row['message'], 
    							 "reply_to"=>$repl,
    							 "phrase"=>$d,
    							 "privacy"=>$privacy,
    							 'image'=>$image,
    							 "likes"=>$likes,
    							 "liked"=>$liked,
    							 "web_link"=>$wb_link,
								 "auth_data"=>fetchAuthData($row["auth"]),
								 "reciv_data"=>fetchRecivData($row["recip"]),
    							 "comments"=>$comments));
	}

	if(count($array)<1){
		$array=emptyArray();
	}

	return array(
			"data"=>$array,
			"pagination"=>array(
				"pages"=>$pages,
				"curr_pages"=>$curr_pages,
				"pages_left"=>$pages_left));
}
?>