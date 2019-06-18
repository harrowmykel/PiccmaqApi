<?php 
	/*echo "<div style='color:white; background-color:purple;'>Sorry, Server is being updated. please come back later in an hour. :) </div>";
	exit();*/
	ob_start();
	$rel____=(isset($rel____))?$rel____:"";
	/*aro micheal oluwaseun*/
	/*go to config/index.php to edit changes*/
	//checklogin is registerd in notif_incl

  	$rel="";;
	include $rel . 'incl2.php';
	include $rel . 'incl3.php';
	include $rel . 'api_help.php';
	
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'http'. DIRECTORY_SEPARATOR ;
	require $rel.'apiquery.php';
	include $rel.'httpful.phar';
	require $rel.'maq.php';

  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR ;
	require $rel.'icons_incl.php';
	require $rel.'badwords.php';
	require $rel.'index.php';
	require $rel.'error.php';
	
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'translate'. DIRECTORY_SEPARATOR ;
	include $rel.'translate.php';
	
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'notify'. DIRECTORY_SEPARATOR ;
	include $rel.'index.php';
	

	$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($connection->connect_error) die($connection->connect_error);
	session_start();

    deleteDir__($rel____.DUMPYARD_);
	recordPageView();

    function teil($r){
    	return ceil($r);
    }

    function isAPi(){
    	return IS_API;
    }

    function recordPageView(){
    	$curr_page=$_SERVER['PHP_SELF'];
    	recordPageView_($curr_page);
    	recordPageView_(getPrevDay());
    	recordPageView_(TOTAL_VIEWS);
    }

    function recordPageView_($curr_page){
    	$time=CURRENT_TIME;
		$q="select * from afree_pageview where page='$curr_page'";
		$result=queryMysql($q);
		$num=$result->num_rows;
		if($num>0){
			$row=$result->fetch_array(MYSQLI_ASSOC);
			$value=$row['pageviews'];
			$value++;
			$q="UPDATE afree_pageview set pageviews=$value, time=$time where page='$curr_page'";
		}else{
			$q="INSERT into afree_pageview (id, page, pageviews, time) VALUES (NULL, '$curr_page', 1, $time)";
		}
		queryMysql($q);
    }

	function getSql($sql, $point){
		return @((queryMysql($sql))->fetch_array(MYSQLI_ASSOC))[$point];
	}

	function getFileName($f_name){
		//get the last or ending 
		//returns f.txt in hme/f/d/f.txt
		$arr=explode("\\", $f_name);
		$f_name=$arr[count($arr)-1];
		$arr=explode("/", $f_name);
		$f_name=$arr[count($arr)-1];
		return $f_name;
	}

	function getFileExtension($f_name){
		//get the last or ending 
		//returns f.txt in hme/f/d/f.txt
		$arr=explode(".", getFileName($f_name));
		$f_name=$arr[count($arr)-1];
		return $f_name;
	}

    function getFullname($name){
    	if(ucfirst(getThisUser())!=ucfirst($name)){
    	$user=$name;
    $array= @(queryMysql("SELECT * FROM profiles WHERE user='$user'"))->fetch_array(MYSQLI_ASSOC);
 	$fullname=ucWords_((!empty(@$array['fullname']))?@$array['fullname']:$user);
	}else{
	$fullname=getThisUserArray()['fullname'];
	}
	return $fullname;
    }

    function getThisUserArray_($user){
    	return (queryMysql("SELECT * FROM profiles WHERE user='$user'"))->fetch_array(MYSQLI_ASSOC);
    }

    function areTheseFriends($user, $useri){
		$frnds=checknum("SELECT recip FROM friends WHERE (user='$user' AND recip='$useri') OR (user='$useri' AND recip='$user') AND confirm=2");
		return ($frnds>0);
    }

    function getThisUserArray(){
    	global $rel____;
    	$gender=MALE;
    /*	if(IS_API){
    		$arr=refreshProfileApiCache();
    	}else{    		
		    $usert=$user=getData(USERNAME);
		    $jou= getData_(TIME_OF_REFRESH)<(CURRENT_TIME- REFRESH_TIME);
	    	if(getData_(USER_PROFILE_CACHE_SAVED)==USER_PROFILE_CACHE_SAVED || !$jou){
	    		$arr=json_decode(getData_(USER_PROFILE_CACHE))[0];
	    		$thr=array();
			    foreach ($arr as $key => $value) {
			      	$thr[$key]=$value;
			    	if($key=="gender"){ 
			    		$value=(empty($value))?MALE:FEMALE;
				    	$thr[$key]=$value;
			    	}
			    	if($key=="image"){    	
						$image=getUserDp($user);
			    		$thr[$key]=$image;
			    	}
			    }
			    $arr=$thr;
	    	}else{
	    		$arr=refreshProfileCache();
	    	}
    	}*/
    	
    	$arr=refreshProfileApiCache();
    	return $arr;
    }

    function refreshProfileCache(){
    	global $rel____;
    	$usert=getThisUser();
    	$row=$array=getThisUserArray_($usert);
    	$user=$usert;
    	$fullname=ucWords_((!empty(@$array['fullname']))?@$array['fullname']:$user);
		$subtitle=(!empty($row['place']))?$row['place']:$row['country'];
		$subtitle=(!empty($subtitle))?$subtitle:$row['work'];
		$subtitle=(!empty($subtitle))?$subtitle:$row['school'];
		$gender=$row['gender'];			
		$image=getUserDp($usert);
		$arr=array("username"=>$usert,
					"userid"=>$array['user_id'],
					"fullname"=>$fullname,
					"subtitle"=>$subtitle,
					"country"=>$row['country'],
					"gender"=>$gender,
					"image"=>$image);
		saveData_(USER_PROFILE_CACHE, json_encode(array($arr)));
		saveData_(USER_PROFILE_CACHE_SAVED, USER_PROFILE_CACHE_SAVED); 
		saveData_(TIME_OF_REFRESH, CURRENT_TIME); 
		return $arr;
    }

    function refreshProfileApiCache(){
    	global $rel____;
    	global $COOKIE;
    	if(!isset($COOKIE[USER_PROFILE_CACHE_SAVED])){
	    	$usert=getThisUser();
	    	$row=$array=getThisUserArray_($usert);
	    	$user=$usert;
	    	$fullname=ucWords_((!empty(@$array['fullname']))?@$array['fullname']:$user);
			$subtitle=(!empty($row['place']))?$row['place']:$row['country'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['work'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['school'];
			$gender=$row['gender'];			
			$image=getUserDp($user);
			$arr=array("username"=>$usert,
						"userid"=>$array['user_id'],
						"fullname"=>$fullname,
						"subtitle"=>$subtitle,
						"country"=>$row['country'],
						"gender"=>$gender,
						"image"=>$image);
		}else{
			$arr=$COOKIE[USER_PROFILE_CACHE_SAVED];
		}
		return $arr;
    }

    function getThisUserEmail(){
    	$user=getData(USERNAME);
    	$arr=(queryMysql("SELECT * FROM members WHERE user='$user'"))->fetch_array(MYSQLI_ASSOC);
    	return ($arr['email']);
    }


    function getUserDP($user){
    	global $rel____;
    	$a=MALE_DP;//(getThisUserArray()['gender']==MALE)?MALE_DP:FEMALE_DP;
    	$dp=getDPNAME($user, true);
    	if($dp["ext_link"]!=0){
    		$image=$dp["prof_pic"];
    	}else{
	    	$image=$rel____.IMGSTORE__.$dp["prof_pic"];
	    	$df=IMGSTORE__.$dp["prof_pic"];
			$image=(!empty($dp["prof_pic"]) && file_exists($image))?DEF_URL.$df:getDpFromUsername($user);
    	}
		return $image;
    }

    function getDpFromUsername($user){
        return DEF_URL.getDpFromUsername_($user);
    }

    function getDpFromUsername_($user){
        $str=trim($user);
        if(!empty($str)){
            $f_character=strtolower(substr($str, 0, 1));
        }else{
            $f_character="a";
        }
        $tst_img=@DP_USER_ARRAY_[$f_character];
        if(empty($tst_img)){
            $f_character="c";
        }
        
        $sd=DP_USER_ARRAY_[$f_character];
        return USER_DP_STORE_ . $sd;
    }
    
    function getDPLink($user){
    	return str_replace(DEF_URL, "", getUserDP($user));
    }

    function genFileName($e){
    	$e=str_replace(" ", "", $e);
    	$e=str_replace("\\", "", $e);
    	$e=str_replace("-", "", $e);
    	return $e;
    }

    function getDPNAME($user, $use_new=false){
        $user=strtolower($user);
    	$q="SELECT * from profiles where user='$user'";
    	$res=queryMysql($q);
    	$row=$res->fetch_array(MYSQLI_ASSOC);
    	if($use_new){    		
	    	if($res->num_rows > 0){
	    		$dp_name=array("prof_pic"=>$row["prof_pic"], "ext_link"=>$row["ext_link"]);
	    	}else{
	    		$dp_name=array("prof_pic"=>getDpFromUsername($user), "ext_link"=>1);
	    	}
    	}else{
    		$dp_name=$row["prof_pic"];		
    		$dp_name=(empty($dp_name))?getDpFromUsername($user):$dp_name;
    	}
    	return $dp_name;
    }

    function userHasUploadedDp($user){
    	global $rel____;
    	$image=getDPNAME($user, true)["prof_pic"];
		return (!empty($image));
    }

    function getUserDPFull($user){
    	global $rel____;
		$image=getUserDP($user);
		return $image;
    }


    function isUsernameValid($username){
    	return (checknum("SELECT * FROM members WHERE user='$username'")>0 &&!empty($username));
    }

    function username_and_passValid($user, $pass){
    	return (checknum("SELECT * FROM members WHERE user='$user' AND pass='$pass'")>0 && !empty($user));
    }

    function username_or_emailValid($user){
    	return (checknum("SELECT * FROM members WHERE user='$user' OR email='$user'")>0 && !empty($user));
    }

	function isEmailValid($email){
    	return (checknum("SELECT * FROM members WHERE email='$email'")>0 && !empty($email));		
	}

	function deserialise($dob){
        $dobo=explode("-", "$dob");
        if (count($dobo)==3){
            for ($ri=0; $ri<3; $ri++){
                //check numericity
                if(!is_numeric($dobo[$ri])){
                    return 0;
                } 
            }
            $day=$dobo[0];
            $month=$dobo[1];
            $year=$dobo[2];

            $dob=$day;
            $mob=$month;
            $yob=$year;

            $job = checkdate($mob,$dob,$yob) ? 'trueo' :'falseo'; // checks if date is corrr
            // $job="trueo";
            if ($job == 'trueo'){
                $importantDate = mktime(9,40,0,$month,$day,$year);
                return $importantDate;
            } 
        }
         return 0;
        // print_r($dobo);
    }

	function getNewChar(){
	  $a=rand(1,3);
	  switch ($a){
	      case 1:              
	    $wrd=rand(48, 57);//numbers
	          break;
	      case 2:              
	    $wrd=rand(65, 90);//capital
	          break;
	      case 3:              
	    $wrd=rand(97, 122);//small
	          break;
	    }
	  return chr($wrd);
	}

	function getNewChar_(){
	  $a=rand(1,2);
	  switch ($a){
	      case 1:              
	    $wrd=rand(65, 90);//capital
	          break;
	      case 2:              
	    $wrd=rand(97, 122);//small
	          break;
	    }
	  return chr($wrd);
	}
	  
	function gnrtNewString($a,$b){
	    $wrd="";
	    $d=rand($a,$b);
	    for ($i=0; $i<$d; $i++){
	        $wrd.=getNewChar();
	    }
	    return $wrd;
	    // return str_replace("=", "", base64_encode($wrd));
	}

	function gnrtNewString_($a, $b){
		$wrd="";
	    $d=rand($a,$b);
	    for ($i=0; $i<$d; $i++){
	        $wrd.=getNewChar_();
	    }
	    return $wrd;
	}

	function getUsername($id){
		return @((queryMysql("SELECT user FROM profiles WHERE user_id=$id"))->fetch_array(MYSQLI_ASSOC)['user']);
	}

	function getOtherUser(){
		if(isAPi()){
			$id=getGetorPostString("who");
			$id=empty($id)?getThisUser():$id;
		}else{
			$id=(is_numeric(getGetString('uid')))?getUsername(getGetString('uid')):getGetString('uid');
			$id=(empty($id))?getGetString(API_RECIPKEY):$id;
			$id=(empty($id))?getGetString('username'):$id;
			$id=(empty($id))?getThisUser():$id;
			$id=(is_numeric($id))?getUsername($id):$id;			
		}
		return strtolower($id);
	}

	function getForumkey(){		
		$id=getGetString('uid');
		$id=(empty($id))?getGetString('username'):$id;
		$id=(empty($id))?getThisUser():$id;
		return $id;
	}

	function ucWords_($vr){
		return ucwords($vr);
	}

	function getOnline($user){
		// OFFLINE;
		return ONLINE;
	}

	function getOnlineByClass($user, $class_array){
		//creates an img with a css-class
		//array(a $img_class, b $words_class, c height="12" d width="8", e=show words)
		$class=@$class_array['a'];
		$word="online";
		$show=(isset($class_array['e']) && $class_array['e'])?$word:"";
		$src=DEF_URL."/web_files/phone.png";
		return "<img class='$class' src='$src'/> $show";
	}

	function getOnlineByClass_($user, $class_array){
		//creates an img with a css-class
		//array(a $img_class, b $words_class, c height="12" d width="8", e=show words)
		$class=@$class_array['img'];
		$word="online";
		$show=(isset($class_array['show_word']) && $class_array['show_word'])?$word:"";
		$src=DEF_URL."/web_files/phone.png";
		return "<img class='$class' src='$src'/> $show";
	}

	function editImage($src, $saveto){
	    if(DO_NOT_RESHRINK_IMAGE){
	        rename($src, $saveto);
	        return "";
	    }
	    if(SHRINK_IF_SIZE_GREATER){
            $filesize = filesize($src); // bytes
	        if(!($filesize>MAX_IMAGE_B4_SHRINK_SIZE)){
    	        rename($src, $saveto);
	            return "";
	        }
	    }
		list($w, $h) = getimagesize($saveto);
		  $max = 600;
		  $tw  = $w;
		  $th  = $h;


		  if ($w > $h && $max < $w)
		  {
		    $th = $max / $w * $h;
		    $tw = $max;
		    
		  }
		  elseif ($h > $w && $max < $h)
		  {
		    $tw = $max / $h * $w;
		    $th = $max;
		  }
		  elseif ($max < $w)
		  {
		    $tw = $th = $max;
		  }

		  $tmp = imagecreatetruecolor($tw, $th);
		  imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h); //first two  after 0's may be changed 
		  imageconvolution($tmp, array(array(-1, -1, -1),
		    array(-1, 16, -1), array(-1, -1, -1)), 8, 0);
		  imagejpeg($tmp, $saveto);
		  imagedestroy($tmp);
		  imagedestroy($src);
	}



    function createSelect($var, $name, $firstIsSelected=false){
    	$wrd='<select class="form-control input-sm" name="'.$name.'">';
    	$i=0;
    	foreach ($var as $key => $value) {
    		if($key==getGetString($name) || $key==getPostString($name)){
    			$wrd.='<option value="'.$key.'" selected="true">'.$value.'</option>';     
    		}else if($i==0 && $firstIsSelected){
    			$wrd.='<option value="'.$key.'" selected="true">'.$value.'</option>';     
    		}else{
    			$wrd.='<option value="'.$key.'">'.$value.'</option>';     
    		}
    		$i++;        
    	}
    	$wrd.='</select>';
    	return $wrd;
    }


	function saveDPToDB($user, $ppic, $isNtHere=false){	
	    $dfk=$ppic;
		$view=$user;
	    $privacy=DEF_DP_PRIVACY;
	    $time=CURRENT_TIME;
	    $text=translate("just_edit_dp");
	    $ret="";
		$dp_name=getSql("SELECT prof_pic from profiles where user='$user'", "prof_pic");
		$dp_name_=getSql("SELECT ext_link from profiles where user='$user'", "ext_link");
		if(!empty($dp_name) && $dp_name_==0){
			@unlink(getRel____().IMGSTORE__.$dp_name);
		}
		if(!$isNtHere){
			$ret = "";

		    $saveto =$toy = generateNewDpImgLink(IMG_STORE, $ppic);
			$ppic=ROOT_DIR.IMGSTORE__.$ppic;
			copy($ppic, ROOT_DIR.$saveto);			
    		$ret=(!empty($toy))?DEF_URL."/".$toy:"";
    		if(!IS_API)
    		    savePP($user, $view, $privacy, $time, $text, $ret);
		}else{
    		savePP($user, $view, $privacy, $time, $text, $ppic, true);
		}
				
		$q="UPDATE members SET has_dp=1 WHERE user='$user'";
		queryMysql($q);
		if($isNtHere){
			$END=", ext_link=1 ";
		}else{
			$END=", ext_link=0 ";			
		}
		$q="UPDATE profiles SET prof_pic='$dfk'".$END." WHERE user='$user'";
		queryMysql($q);
		saveData_(USER_PROFILE_CACHE_SAVED, 0); 
	}

	  function generateNewDpImgLink($store, $dppp){
	    $user=getThisUser();
	    $ret="";
	    $fnjfn= getPrevDay();

	    $e=gnrtNewString(1, 3).".".getFileExtension($dppp); 
	    $rnd_str=gnrtNewString(6, 16);         
	    $ret2=$ret=genFileName($rnd_str.$e);    
	    $ty=ROOT_DIR.$store.$fnjfn."/";
	    $toy=$store.$fnjfn."/".$ret;

	    @mkdir($ty, 0777, true);
	    $f_location=$ty.$ret;
	    while(is_file($f_location) && file_exists($f_location)){ 
	      $rnd_str=gnrtNewString(6, 16);         
	      $ret2=$ret=genFileName($rnd_str.$e);  
	      $ty=ROOT_DIR.$store.$fnjfn."/";
	      @mkdir($ty, 0777, true);  
	      $f_location=$ty.$ret;
	      $toy=$store.$fnjfn."/".$ret;
	    }
	    $saveto = $f_location;
	    return $toy;
	  }
	
	function savePP($user, $view, $privacy, $time, $text, $ret, $ntHere=false){
		$cv=0;
		if($ntHere){
		    $cv=1;
		}
    
		$isgrp=(checkNum("SELECT * FROM profiles WHERE user='$view' AND is_group=1")>0)?1:0;
		$e="INSERT INTO messages (id, auth, recip, privacy_, time, message, picture, is_uneditable, reply_to, ext_link, is_group) VALUES (NULL, '$user', '$view', '$privacy', $time, '$text', '$ret', 0, 0, $cv, $isgrp)";
    	return queryMysql($e);
	}
	
	function getRel____(){
		global $rel____;
		return $rel____;
	}

	function cleanLink($path){
		$path=str_replace('//', '/', $path);
		$path=str_replace('\\\\', '\\', $path);
		return $path;
	}

	function getUserCred($var, $addUser=true){
		if($addUser)
			return $var."&username=".getThisUser()."&pass=".getData(PASSWORD);
		else
			return $var;
	}

	function getUrlEnd($vf){
		return $vf.API_CONST."&page=".getCurrentPage();
	}
	?>