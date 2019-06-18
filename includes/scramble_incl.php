<?php 
function getScrambleBasic(){
	global $rel____;
	$rkl=$rel____;
	$id=getScramblekey();
	$info=getCurrentScramble($rkl);
	$data=getData(SCRAMBLE_NAME);
	$score=$info["score"];
	$inf=$info["word"];
	$array=array("username"=>$id,
				"userid"=>$id,
				"fullname"=>translate('scramble'),
				"subtitle"=>translate_var('scramble_sub',array($score, $inf)),
				"image"=>SCRAMBLE_DP);
	return $array;

}

function getScramblekey(){
	return SCRAMBLE_KEY;
}

function getAllTopWinnersScramble(){
	$user=getThisUser();
	$search = getScramblekey();
	$array=array();

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("scramble/index.php?req=board"), 
				"datatype"=>"json", 
				"data"=>getUserCred("who=".$search), 
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
									"fullname"=>$profileParser->getAuth_data()->getFullname(),
									"scramble"=>$profileParser->getSubtitle(),
									"image"=>$profileParser->getAuth_data()->getAuth_img()));
		}
	}
	return $array;
}

function getCurrentScramble($relk){
		$user=getThisUser();
		$array=array();
		$useri=getScramblekey();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("scramble/index.php?req=about"), 
					"datatype"=>"json", 
					"data"=>getUserCred("who=".$useri), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		if($num>0){
			$row=$result[0];
			$profileParser = new ScrambleParser;
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				$array = array("username"=>$profileParser->getAuth_username(),
						"userid"=>$profileParser->getId(),
						"fullname"=>$profileParser->getAuth_data()->getAuth(),
						"score"=>$profileParser->getScore(),
						"word"=>$profileParser->getCurrent_word(),
						"image"=>$profileParser->getAuth_data()->getAuth_img()
						);
			}
		}
		return $array;
		// return $profileParser->getBio();
}

function getScrambleMessages(){
	$array=array();
	$user=getThisUser();
	$ef=$useri=getScramblekey();
	$array=array();

	$maq = new Maq();
	$post_param=array("url"=>getUrlEnd("scramble/index.php?req=fetchmsgfromuser"), 
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
									"auth"=>$postparser->getAuth_data()->getAuth(),//fullname
									"reciv"=>$postparser->getReciv_data()->getReciv(),
									"fullname"=>$postparser->getAuth_data()->getAuth(),
									"message"=>replaceSmiles($postparser->getSubtitle()),
									"time"=>getDWM2($postparser->getTime()),
									"timestamp"=>$postparser->getTime(),
									"image"=>$postparser->getImage()));
		}
	}
	return $array;
}

function saveScrambleMessage(){

}
 ?>