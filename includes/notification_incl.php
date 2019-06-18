<?php
if(!isAPi()){
	checkLoggedIn();
}

if(PAGEVIEW_STAT){
	// saveLastSeen();
	saveTotalOnlineCounter();
	saveTotalPageCounter();
}

function saveTotalOnlineCounter(){
	$user=getThisUser();
	// $_SESSION[LAST_ONLINE_TIME]=0;
	$_SESSION[LAST_ONLINE_TIME]=(empty($_SESSION[LAST_ONLINE_TIME]))?CURRENT_TIME:$_SESSION[LAST_ONLINE_TIME];
	$old_time=$_SESSION[LAST_ONLINE_TIME];
	$new_time=CURRENT_TIME-$old_time;
	// add to total time here
	$total_time_online=getSql("SELECT total_seen_time from members WHERE user='$user'", "total_seen_time");
	$total_seen_time=$total_time_online+$new_time;
	queryMysql("UPDATE members SET total_seen_time=$total_seen_time WHERE user='$user'");
	$_SESSION[LAST_ONLINE_TIME]=CURRENT_TIME;

	$curr_page=PREF_ONLINE.getPrevDay();
	$time=0;
	$q="select * from afree_pageview where page='$curr_page'";
	$result=queryMysql($q);
	$num=$result->num_rows;
	if($num>0){
		$row=$result->fetch_array(MYSQLI_ASSOC);
		$value=$row['pageviews'];
		$value__=$row['time'];
		$_SESSION[ONLINE__TODAY]=(empty($_SESSION[ONLINE__TODAY]))?"":$_SESSION[ONLINE__TODAY];
		if(empty($_SESSION[ONLINE__TODAY])){
			$value++;
		}
		$_SESSION[ONLINE__TODAY]=CURRENT_TIME;
		$value__=$value__+$new_time;
		$q="UPDATE afree_pageview set pageviews=$value, time=$value__ where page='$curr_page'";
	}else{
		$q="INSERT into afree_pageview (id, page, pageviews, time, is_page) VALUES (NULL, '$curr_page', 1, 0, 0)";
	}
	queryMysql($q);
    
}


function saveTotalPageCounter(){
	$curr_page=$_SERVER["PHP_SELF"];
	$prvDay=getPrevDay()+1;//so it is different from the others above
	$q="select * from afree_pageview where page='$curr_page' and time=$prvDay";
	$result=queryMysql($q);
	$num=$result->num_rows;
	if($num>0){
		$row=$result->fetch_array(MYSQLI_ASSOC);
		$value=$row['pageviews'];
		$valueN=$value+1;
		$q="UPDATE afree_pageview set pageviews=$valueN where page='$curr_page' and time=$prvDay";
	}else{
		$q="INSERT into afree_pageview (id, page, pageviews, time, is_page) VALUES (NULL, '$curr_page', 1, $prvDay, 1)";
	}
	queryMysql($q);
    
}

function saveLastSeen(){
	$user=getThisUser();
	$time=CURRENT_TIME;
	if(!empty($user)){
		queryMysql("UPDATE members SET last_seen=$time WHERE user='$user'");
	}
}

function getNotifArray(){

	$user=getThisUser();
	$array= array();
	$useri=getOtherUser();

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("notify/index.php?req=notifcount"), 
				"datatype"=>"json", 
				"data"=>getUserCred("who=".$useri), 
				"baseurl"=>API_BASE_URL);

	$res=$maq->post($post_param)->body;

	$result=$res->data;
	$num=count($result);
	if($num>0){
		$row=$result[0];
		$postparser = new NotifVarParser();
		$postparser->load($row);
		if(!$postparser->isEmpty()){//fetch msg	
			return array("msgs"=>$postparser->getMsgs(),
				"chat_msgs"=>$postparser->getChat_msgs(),
				"frnds"=>$postparser->getFrnds(),
				"notif_numb"=>$postparser->getNotif_numb());
		}
	}
	return array("msgs"=>0,
				"chat_msgs"=>0,
				"frnds"=>0,
				"notif_numb"=>0);
}

function getNewMessages($bool){
	$numb=NOTIF_ARRAY["chat_msgs"];
	return ($bool)?"($numb)":$numb;
}

function getNewChats($bool){
	$numb=NOTIF_ARRAY["msgs"];
	return ($bool)?"($numb)":$numb;
}

function getNewFriends($bool){
	$numb=NOTIF_ARRAY["frnds"];
	return ($bool)?"($numb)":$numb;
}

function getNewNotif($bool){
	$numb=NOTIF_ARRAY["notif_numb"];
	return ($bool)?"($numb)":$numb;
}

function getAllNotifications(){	
		$array=array();
		$user=getThisUser();
		$id=getOtherUser();
		$VARR=VARR;

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("notify/index.php?req=fetchweb"), 
					"datatype"=>"json", 
					"data"=>getUserCred("ss=a"), 
					"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$postparser = new NotifParser();
			$postparser->load($row);
			if(!$postparser->isEmpty()){//fetch msg	
				array_push($array,array("subject"=>$postparser->getSubject(),
							"subj_fullname"=>ucwords($postparser->getAuth_data()->getAuth()),
							"object"=>$postparser->getObject(),
							"obj_fullname"=>$postparser->getObject(),
							"link"=>$postparser->getLink(),
							"notif_id"=>$postparser->getNotify_id(),
							"seen"=>$postparser->getSeen(),
							"notif_text"=>getNotifText($postparser->getNotify_id())));
			}
		}
		return $array;
}

?>