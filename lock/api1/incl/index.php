<?php
	ob_start();
	define('API_IN_DEBUG', 1);
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR ;
// 	require $rel.'acct_creator.php';

    $reciv_arrays = array();
    $auth_arrays = array();

	if(API_IN_DEBUG){
		$rel____=$root."../../";
	}else{
		$rel____=$root."../../";
	}
	define('IS_API', true);	
	$rel="/lock/apiii/incl/config";

	
    $a_root_dir_parse=str_replace("lock/apiii/incl", "", dirname(__FILE__));
    $a_root_dir_parse=str_replace("lock\apiii\incl", "", $a_root_dir_parse);
    $relsdf=$a_root_dir_parse;

    
	include $relsdf . 'includes/incl.php';
	include $relsdf . 'includes/validate_incl.php';
	include $relsdf . 'includes/profile_incl.php';
	include $relsdf . 'includes/avatar_incl.php';
	include $relsdf . 'includes/notification_incl.php';
//pass username
    
    function checkApiLogin(){
        $signedIn=isLoggedIn();
        if(WORK_WITHOUT_LOGIN!=true){
            if(!$signedIn){
                release(apiLeave(invUserPass()));
            }else{
            	saveLastSeenForUser();
            }
        }
    }

	function saveLastSeenForUser(){
		$user=getThisUser();
		$time=CURRENT_TIME;
		if(!empty($user)){
			queryMysql("UPDATE members SET last_seen=$time WHERE user='$user'");
		}
	}
    
    function runApiReqCheck($NoLogIn){
    	define("API_KEY_VALUE", getGetOrPostString("api_key"));

    	$query_api="SELECT * FROM apis_table WHERE api_key='".API_KEY_VALUE."'";
        $num_api=checknum($query_api);

        if($num_api<1){
            release(apiLeave(invAppId()));
        }
        
        $base=(in_array(getReq(), $NoLogIn));
        define("WORK_WITHOUT_LOGIN", $base);
        checkApiLogin();
    }
    
	function replaceSmiles_($var){		
		// global icons_array;
		if(!SHOW_ICONS){
			return $var;
		}
		/*$df=strtolower(trim("icon")).":";
		if((strpos($var, $df))!=(-1)){
			foreach (icons_array as $key => $value) {
				$str=$df.$key;
				$rep="<img src='".DEF_URL.SMILIE_DIR.$value."' alt='".$key."' />";
				if(SHOW_ICON_DESC)
					$rep.="($key)";
				$var=str_replace($str, $rep, $var);
			}			
		}	*/	
		return $var;
	}
	
	function getReq(){
		return getGetorPostString("req");
	}

	function make_positive($a, $b, $InterSwitch=false){
		if($InterSwitch){
			if($b>$a){
				$c=$b-$a;
			}
		}
		$c=$a-$b;
		if($c<0){
			$c= 0;
		}
		return $c;
	}

	function apiLeave($arr){
		/*fwrite(fopen("api.txt", "a+"), json_encode($arr));
		fwrite(fopen("api_get.txt", "a+"), json_encode($_GET));
		fwrite(fopen("api_post.txt", "a+"), json_encode($_POST));*/
		return array(
						"data"=>$arr,
						"pagination"=>array(
							"pages"=>0,
							"curr_pages"=>0,
							"pages_left"=>0));
	}


	if(UNDER_EDIT){
		if(IS_API){
			release(array(
				"data"=>error(6485),
				"pagination"=>array(
					"pages"=>0,
					"curr_pages"=>0,
					"pages_left"=>0)
			));
		}
	}

	function fetchRecivData($user){
		global $reciv_array;

		if(empty($reciv_arrays["$user"])){
			$fullname = getFullname($user);
			$reciv_arrays["$user"]=array("reciv_img"=>getUserDp($user),
										"reciv"=> $fullname,
										"fullname"=> $fullname);
		}
		return $reciv_arrays["$user"];
	}

	function fetchAuthData($user){
		global $auth_arrays;

		if(empty($auth_arrays["$user"])){
			$fullname = getFullname($user);
			$auth_arrays["$user"]=array("auth_img"=>getUserDp($user),
										"auth"=> $fullname,
										"fullname"=> $fullname);
		}
		return $auth_arrays["$user"];
	}

	function page_is_first(){
		$_GET['page']=1;
	}

//change APP/index.php too
	function addMsgToDb($aut, $recip, $persy, $img_url=""){
		$time=CURRENT_TIME;
		$q="INSERT INTO pmesages (user_id, aut, reciv, time, text, del_recip,del_auth, confirm, pic, ext_link) VALUES(NULL, '$aut', '$recip', $time, '$persy',0,0, '".message_sent."', '$img_url', 1)";
		queryMysql($q);
		$id=getLastId();
		addNewMsgNotify($aut, $recip, $id);
		
		$auth=$aut;
    	$query1="SELECT * FROM pmessages_users WHERE ((auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip'))";
    	if(checknum($query1)<1){
        	$query2="INSERT INTO pmessages_users (id, auth, recip, crt_time, end_time, msg_count, score, img) VALUES (NULL, '$auth', '$recip', $time, $time, 1, 1, 'a')";
        	queryMysql($query2);
    	}else{
    	    $coun=getSql($query1, "msg_count");
    	    $coun+=1;
        	$score= getConvoScore($coun);
        	$im=getConvoScoreImage($score);
        	$q2="UPDATE pmessages_users SET end_time=$time, msg_count=$coun, score=$score, img='$im' WHERE ((auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip'))";
        	queryMysql($q2);
    	}
    	
		return $id;
	}
	
	function getConvoScoreImage($user_score){
	    $msg_score_img_sheet=array(array('score'=>0, 'img'=>'a'),
                                 array('score'=>10000, 'img'=>'b'),
                                 array('score'=>24000, 'img'=>'c'),
                                 array('score'=>38300, 'img'=>'d'),
                                 array('score'=>52800, 'img'=>'e'),
                                 array('score'=>67600, 'img'=>'f'),
                                 array('score'=>82600, 'img'=>'g'),
                                 array('score'=>97600, 'img'=>'h'),
                                 array('score'=>112750, 'img'=>'i'),
                                 array('score'=>128000, 'img'=>'j'),
                                 array('score'=>143250, 'img'=>'k'),
                                 array('score'=>158500, 'img'=>'l'),
                                 array('score'=>174350, 'img'=>'m'),
                                 array('score'=>190600, 'img'=>'n'),
                                 array('score'=>206850, 'img'=>'o'),
                                 array('score'=>223100, 'img'=>'p'),
                                 array('score'=>239350, 'img'=>'q'),
                                 array('score'=>256100, 'img'=>'r'),
                                 array('score'=>272850, 'img'=>'s'),
                                 array('score'=>289600, 'img'=>'t'),
                                 array('score'=>306350, 'img'=>'u'),
                                 array('score'=>323100, 'img'=>'v'),
                                 array('score'=>340600, 'img'=>'w'),
                                 array('score'=>358100, 'img'=>'x'),
                                 array('score'=>375600, 'img'=>'y'),
                                 array('score'=>393100, 'img'=>'z'),
                                 array('score'=>410600, 'img'=>'aa'),
                                 array('score'=>428100, 'img'=>'ab'),
                                 array('score'=>450600, 'img'=>'ac'),
                                 array('score'=>473100, 'img'=>'ad'));
        
	   
        $t='a';
        foreach ($msg_score_img_sheet as $key => $value) {
            $this_score = $value["score"];
            if($user_score>410600){
                $t="ab";
            }else if($user_score < $this_score && $key > 0){
                $t = $msg_score_img_sheet[$key]["img"];
                break;
            }
        }
        return $t;
	}
	
    function getConvoScore($user_count){
    	$msg_score_sheet=array(array("count"=>5, "score"=>100),
    							array("count"=>7, "score"=>260),
    							array("count"=>10, "score"=>280),
    							array("count"=>15, "score"=>300),
    							array("count"=>20, "score"=>310),
    							array("count"=>23, "score"=>350),
    							array("count"=>25, "score"=>370),
    							array("count"=>30, "score"=>400),
    							array("count"=>35, "score"=>600),
    							array("count"=>40, "score"=>800),
    							array("count"=>45, "score"=>1000),
    							array("count"=>50, "score"=>1200));
    	$total_score = 0;
    	$total_count = $user_count;
    	foreach ($msg_score_sheet as $key => $value) {
    		$this_score = $value['score'] + 300;
    		$this_count = $value['count'];
    		if($total_count > $this_count){
    			$total_score += ($this_count * $this_score);
    			$total_count = $total_count - $this_count;
    		}else{
    			$total_score += ($total_count * $this_score);
    			break;
    		}
    	}
    	// score/4000 == image
    	return $total_score * 5;
    }

?>