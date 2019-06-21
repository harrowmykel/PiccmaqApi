<?php 

	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
    
	$req=getReq();

	switch($req){
	    case "about":
			page_is_first();
	        $resultt=getChatAbout();
	        break;
		case "create":
			$resultt= sendNewMessage();
			break;
		case "board":
			$resultt= getLeaderBoard();
			break;
		case "fetchmsgfromuser":
			$resultt= fetchMsgsFromUser();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);

	function getChatAbout(){	
		$who=getGetorPostString('who');
		$user=getThisUser();
		$array = array("id"=>0,
						"auth_username"=>translate('scramble'),
						"current_word"=>getCurrentScramble(),
						"score"=>getScore($user),
						"auth_data"=>array(
						 			"auth"=>translate('scramble'),
						 			"fullname"=>translate('scramble'),
						 			"auth_img"=>SCRAMBLE_DP));
		return apiLeave(array($array));
	}
	
	function sendNewMessage(){
		global $rkl;
		$user=getThisUser();
		$time=CURRENT_TIME;
		$array_list=getGetorPostString('message');
		$useri=getGetorPostString("who");	
		$user_scramble=USER_SCRAMBLE;
		$id = 0;

		$cur_word=strtolower(getCurrentScramble($rkl));
		$word=$persy=strtolower(trim(sanitizeString($array_list)));
		 //this can be used for bckup
		if(!empty($persy)){
			$num_amt=checknum("SELECT * FROM scramble_dict WHERE word='$word'");
			$won=($num_amt>0);
			if($won){
				$score=(saveScrambleScore($persy))[1];
				$persy_=translate_var("correct_scramble", array($user, $score, $persy, $cur_word));					
			}else{
				$score=(deductIncorrectScramble($persy))[1];
				$persy_=translate_var("incorrect_scramble", array($user, $score, $persy, $cur_word));
			}		
			$q="INSERT INTO scramble_messages (id, reciv, aut, time, text, pic) VALUES(NULL, '$useri', '$user_scramble', $time, '$persy_', 1)";
			queryMysql($q);	
			$q="INSERT INTO scramble_messages (id, reciv, aut, time, text, pic) VALUES(NULL, '$useri', '$user', $time, '$persy', 1)";
			queryMysql($q);
		}		
		$rty=success(64564);
		$rty[0]["id"]=$id;
		return apiLeave($rty);
	}

	function fetchMsgsFromUser(){
		$user=getThisUser();
		$time=getGetorPostString('time');
		$who=getGetorPostString("who");
		$time=(empty($time) || !is_numeric($time))?0:$time;
		$query="SELECT * FROM scramble_messages WHERE reciv='$who' ORDER BY time DESC";
		$array =msgContent($query);
		return $array;
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
			$idop=$row["id"];
			$reciv=strtolower(trim($row["reciv"]));
    		$array=array("id"=>$idop,
    					"reciv_username"=>$reciv, 
						 "auth_username"=> $row["aut"], 
						 "timestamp"=>$row['time'],
						 "time"=>$row['time'],
						 "subtitle"=> $row["text"], 
						 "image"=> $image_pic,
						 "auth_data"=>fetchAuthData($row["aut"])
						);
			array_push($array_, $array);
		}

		return array(
				"data"=>$array_,
				"pagination"=>array(
					"pages"=>$pages,
    				"curr_pages"=>$curr_pages,
    				"pages_left"=>$pages_left));
	}

	function getLeaderBoard(){
		$array=array();
		$user=getThisUser();
		$id=getGetorPostString("who");
		$VARR=VARR;

		$q="SELECT * FROM games_tbl ";

		$num=checknum($q);
		$q=$q." ORDER BY scramble DESC ". calcpages($num, $VARR);

		$resultf=queryMysql($q);
		$nm=checknum($q);
		$efkg=0;
		for ($i=0; $i <$nm ; $i++) { 
			$row = $resultf->fetch_array(MYSQLI_ASSOC);
			$useri=$row['user'];
			array_push($array,array("id"=>$row['id'],
										"auth_username"=>$row['user'],
										 "auth_data"=>fetchAuthData($row["user"]),
										"subtitle"=>$row['scramble']));
			$efkg++;
		}
		return apiLeave($array);
	}


function getCurrentScramble(){
	$relk = ROOT_DIR;

	$l_time=$relk."scramble/l_time.txt";
    $word__=$word_= CURRENT_TIME;
	if(!file_exists($l_time)){
		fwrite(fopen($l_time, "a+"), $word_);
	}
	$arr_time=file_get_contents($l_time);
	$min_time=$word_-TIME_TILL_SCRAMBLE_CHANGE;

	$word=$relk."scramble/current_words.txt";

	 //&& ucfirst($user)!=ucfirst($useri)
	 //this can be used for bckup
	if(!file_exists($word) || !empty(getGetString('del')  || $arr_time<$min_time)){
		@unlink($word);
		@unlink($l_time);
        $word_= getRandomWord($relk);
		fwrite(fopen($l_time, "a+"), $word__);
		fwrite(fopen($word, "a+"), $word_);
	}
	$arr=file_get_contents($word);	
	return $arr;
}

function getRandomWord($relk){
	$words_array=$relk."scramble/words_arr.txt";
	$arr=json_decode(file_get_contents($words_array));
	$rand=$arr[rand(10, count($arr)-1)];
	$rem=7-(strlen($rand));
	$fgh=str_shuffle($rand).gnrtNewString_(0, $rem);
	return $fgh;
}

function saveScrambleScore($word){
	$user = getThisUser();	
	$data=getScore($user);
	$score=strlen($word)*SCRAMBLE_POINT;
	$prev_score=(empty($data))?0:$data;
	$new_score=$score+$prev_score;
	saveGameScore($user, $new_score, "scramble");
	return array($score, $new_score);
}

function getScore($user){
	$user = getThisUser();	
	$data=getSql("SELECT * FROM games_tbl WHERE user='$user'", "scramble");
	return empty($data)?0:$data;
}

function saveGameScore($user, $new_score, $col_nm){
	$q="SELECT * FROM games_tbl WHERE user='$user'";
	$num=checknum($q);
	if($num>0){
		$q="UPDATE games_tbl SET $col_nm=$new_score WHERE user='$user'";
	}else{
		$q="INSERT INTO games_tbl(id, user, $col_nm) VALUES (NULL, '$user', $new_score)";
	}
	queryMysql($q);
}

function deductIncorrectScramble($word){
	$user = getThisUser();	
	$data=getScore($user);
	$score=SCRAMBLE_LOSS;
	$prev_score=(empty($data))?0:$data;
	$new_score=$prev_score-$score;
	return array($score, $new_score);
}

function saveScrambleMessage(){

}
?>