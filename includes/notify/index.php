<?php

//close form tag
//comment iv_vals



$notif_array=array("sent_req"=>"sent_req_n",
		"mention_req"=>"mention_req_n",
		"comment_like"=>"comment_like_n",
		"add_reply"=>"add_reply_n",
		"comment_req"=>"comment_req_n",
		"acc_req"=>"acc_req_n",
		"add_like"=>"add_like_n",
		"new_msg"=>"new_msg_r",
		"new_post_by_admin"=>"new_post_by_admin_n",
		"new_post_r"=>"new_post_r_n",
		"new_mess_r"=>"new_mess_r_n",
		"check_prof"=>"check_prof_n",
		"send_piccoin"=>"send_piccoin_n");

    function getNotifText($idf){
    	global $notif_array;
    	return translate(@$notif_array["$idf"]);
    }


	function addNotification($arr){
		array("link"=>"",
			"subj"=>"",
			"obj"=>"",
			"notif_id"=>"");
			
		$tme=time();
		$link=@$arr['link'];
		$subj=@$arr['subj'];
		$obj=@$arr['obj'];
		$notif=@$arr['notif_id'];
		$notif_c=@$arr['notif_c'];
		$obj_id=$notif_c;
		
		queryMysql("INSERT INTO notific_list (id, link, subject, object, seen, notify_id, time, obj_id) VALUES (NULL, '$link', '$subj', '$obj', 0, '$notif', $tme, '$obj_id')");
		
        makeNotGh("notify");
		
		$data=array("notif_id"=>"$notif",
                    "notif_c"=>"$notif_c",
                    "link"=>"$link",
                    "subj"=>"$subj",
                    "obj"=>"$obj");

		$apiquery=new apiQuery();
		$res=$apiquery->send($obj, $data);
	}

	function addFrndReqNotify($subj, $obj){
		if($subj==$obj)return "";
		addNotification(array("link"=>"profile/index.php?username=".$subj,
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"sent_req",
			"notif_c"=>$subj));
	}

	function addPiccoinShareNotify($subj, $obj, $amt){
		if($subj==$obj)return "";
		addNotification(array("link"=>"messages/read/index.php?username=".$subj,
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"send_piccoin",
			"notif_c"=>$subj));		
	}

	function addCheckProfNotify($subj, $obj){
		if($subj==$obj)return "";
		$sd=PREF_.$obj;
		if(isset($_SESSION[$sd])){
			return "";
		}
		$_SESSION[$sd]=$obj;
		addNotification(array("link"=>"profile/index.php?username=".$subj,
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"check_prof",
			"notif_c"=>$subj));
	}

	function addMentionNotify($subj, $obj, $id){
		if($subj==$obj)return "";
		addNotification(array("link"=>"story.php?postid=".$id,
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"mention_req",
			"notif_c"=>"$id"));
	}

	function addNewStatusNotifyAll($user, $id){
		$q="SELECT * FROM afree_status WHERE id=".$id;
		$array=msgContent($q)["data"][0];
		
		$data=array("data"=>array("status"=>$array));
		makeNotGh("status");
        
        if(FIREBASE_IS_TOPIC){
        	$apiquery=new apiQuery();
            $rese=$apiquery->sendTopic($user.FIREBASE_STATUS_TOPIC_ENDING, $data);
        }else{
    		$fog=strtolower(getThisUser());
    	    $q="SELECT * FROM friends WHERE (user='$user' OR recip='$user') AND confirm=2";
    		$res=queryMysql($q);
    		$num=$res->num_rows;
        	$apiquery=new apiQuery();
    		for($i=0; $i<$num; $i++){
    		    $row=$res->fetch_array(MYSQLI_ASSOC);
    		    $fg=strtolower($row['user']);
    		    $obj=$usero=($fg==$fog)?$row['recip']:$fg;
        
        		$rese=$apiquery->send($obj, $data);
    		}
    		$apiquery->send($user, $data);
        }
	}
	
	function makeNotGh($var){
        if(!defined("notgh")){
	        define("notgh", "$var");
        }
	}
	
	function getNotGh(){
	    return (defined("notgh"))?notgh:"notify";
	}
	
	function addDeletedStatusNotifyAll($user, $stat_code){
		$fog=strtolower(getThisUser());
    	$data=array("status_code"=>$stat_code);
        makeNotGh("del_status");
        
        if(FIREBASE_IS_TOPIC){
        	$apiquery=new apiQuery();
            $rese=$apiquery->sendTopic($user.FIREBASE_STATUS_TOPIC_ENDING, $data);
        }else{
    	    $q="SELECT * FROM friends WHERE (user='$user' OR recip='$user') AND confirm=2";
    		$res=queryMysql($q);
    		$num=$res->num_rows;
        
        	$apiquery=new apiQuery();
    		
    		for($i=0; $i<$num; $i++){
    		    $row=$res->fetch_array(MYSQLI_ASSOC);
    		    $fg=strtolower($row['user']);
    		    $obj=$usero=($fg==$fog)?$row['recip']:$fg;
        		$rese=$apiquery->send($obj, $data);
    		}
    		$apiquery->send($user, $data);
        }
	}

	function addNewPostNotify($subj, $obj, $id){
		$user=$subj;
		if($subj==$obj)return "";
		addNotification(array("link"=>"story.php?postid=".$id,
			"subj"=>$user,
			"obj"=>$obj,
			"notif_id"=>"new_post_r",
			"notif_c"=>"$id"));
	}

	function sendAllUsers(){

		$flm=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron'. DIRECTORY_SEPARATOR;
		@mkdir($flm,0777, true);
		$dones_file=$flm."cron.txt";
		if(!file_exists($dones_file)){
			// save it
			fwrite(fopen($dones_file, "a+"), json_encode(array()));
		}
		//long query here; ignore if user quits.
		ignore_user_abort(true);

		$arr=json_decode(file_get_contents($dones_file));
		foreach ($arr as $key => $value) {
			$result=queryMysql("SELECT user FROM members LIMIT 1");
			$num=$result->num_rows;
			foreach ($value as $user => $id) {
				for ($i=0; $i < $num; $i++) { 
					array_shift($arr);
					$row=$result->fetch_array(MYSQLI_ASSOC);
					$obj=$row['user'];
					//delete it
					unlink($dones_file);
					// save it
					fwrite(fopen($dones_file, "a+"), json_encode($arr));
					addNotification(array("link"=>"story.php?postid=".$id,
						"subj"=>$user,
						"obj"=>$obj,
						"notif_id"=>"new_post_by_admin"));
				}
			}
		}
     	$num=checkNum("SELECT * FROM notific_list where seen=1")-MAX_MESSAGES;
        if($num>0){
            queryMysql("Delete from notific_list where seen=1 order by time ASC Limit $num");
        }
		return "done";
	}

	function addNewPostNotifyAll($user, $id){
		global $admin_accts_dnfnfnnfnfjn;
		if(!in_array($user, $admin_accts_dnfnfnnfnfjn))return "";
		saveForCronJob($user, $id);
	}

	function addNewMsgNotify($subj, $obj, $id){
		$user=$subj;
		if($subj==$obj)return "";
		$data=array("notif_id"=>"new_mess_r",
		                            "notif_c"=>"$subj",
		                            "link"=>"$id",
		                            "subj"=>"$subj",
		                            "obj"=>"$obj");
        makeNotGh("message");
		$apiquery=new apiQuery();
		$res=$apiquery->sendMsg($obj, $data);
	}

	function addGroupNotify($subj, $obj){
		$user=$subj;
		if($subj==$obj)return "";
		$data=array("message"=>array("notif_id"=>"$subj",
		                            "notif_c"=>"$subj",
		                            "link"=>"$subj",
		                            "subj"=>"$subj",
		                            "obj"=>"$obj"));
	}

	function saveForCronJob($user, $id){

		$flm=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cron'. DIRECTORY_SEPARATOR;
		@mkdir($flm,0777, true);
		$dones_file=$flm."cron.txt";
		if(!file_exists($dones_file)){
			// save it
			fwrite(fopen($dones_file, "a+"), json_encode(array()));
		}
		$arr=json_decode(file_get_contents($dones_file));
		array_push($arr, array($user=>$id));
		//delete it
		unlink($dones_file);
		// save it
		fwrite(fopen($dones_file, "a+"), json_encode($arr));	
	}		

	function addLikeNotify($subj, $obj, $post_id){
	    $id=$post_id;
		if($subj==$obj)return "";
		addNotification(array("link"=>"story.php?postid=$post_id",
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"add_like",
			"notif_c"=>"$post_id"));		
	}

	function addCommentNotify($subj, $obj, $post_id){
		if($subj==$obj)return "";
		addNotification(array("link"=>"story.php?postid=$post_id",
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"comment_req",
			"notif_c"=>"$post_id"));		
	}

	function addCommentLikeNotify($subj, $obj, $post_id){
		if($subj==$obj)return "";
		addNotification(array("link"=>"story.php?postid=$post_id",
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"comment_like",
			"notif_c"=>"$post_id"));		
	}

	function addReplyNotify($subj, $obj, $post_id){
		if($subj==$obj)return "";
		addNotification(array("link"=>"story.php?postid=$post_id",
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"add_reply",
			"notif_c"=>"$post_id"));		
	}

	function addAccptReqNotify($obj, $subj){
		if($subj==$obj)return "";
		addNotification(array("link"=>"profile/index.php?username=".$subj,
			"subj"=>$subj,
			"obj"=>$obj,
			"notif_id"=>"acc_req",
			"notif_c"=>"$subj"));		
	}

?>