<?php    


	function outPrint($word){	
		$string="$word";
	    $string=str_replace("{{br}}", "<br>", $string);
	    $string=str_replace("{{b}}", "<b>", $string);
	    $string=str_replace("{{/b}}", "</b>", $string);
	    $string=str_replace("{{null}}", "", $string);
	    $string=str_replace("{{app_site}}", APP_SITE, $string);
	    if(strpos($string, "{{app_name}}"))
	    	$string=str_replace("{{app_name}}", translate('app_name'), $string);
	    return $string;
	}

	function createTable($name, $query)
	{   global $imgsu; global $messu; global $bgsu;
		queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
		echo "Table '$name' created or already exists.<br>";
	}

	function queryMysql($query)
	{
		global $connection;
	    if(SHOW_QUERY){
	        echo $query;
	    }
	    if(NO_QUERY){
	        return "";
	    }
		// fwrite(fopen("jddj", "a+"), $query);
		$result = $connection->query($query);
		if (!$result) die($connection->error);
		return $result;
	}

	function replaceKeys($var = ""){
		global $replace_string_array_;
		$df=strtolower(trim(translate("app_name"))).":";
		if((strpos($var, $df))!=(-1)){
			foreach ($replace_string_array_ as $key => $value) {
				$str=$df.$key;
				$var=str_replace($str, $value, $var);
			}			
		}	
		return censorString($var);
	}

	function replaceSmiles($var=""){		
		// global icons_array;
		if(!SHOW_ICONS){
			return $var;
		}
		$df=strtolower(trim("icon")).":";
		if((strpos($var, $df))!=(-1)){
			foreach (icons_array as $key => $value) {
				$str=$df.$key;
				$rep="<img src='".DEF_URL.SMILIE_DIR.$value."' alt='".$key."' />";
				if(SHOW_ICON_DESC)
					$rep.="($key)";
				$var=str_replace($str, $rep, $var);
			}			
		}		
		return $var;
	}

	function sanitizeString($var)
	{
		global $connection;
		if(is_numeric($var) || is_null($var)){
			//return straight up nothing to process here.
			return $var;
		}
		$var = strip_tags($var);
		$var = htmlentities($var);
		$var = stripslashes($var);
		if(!empty($var) && isset($var) ){
			if((!is_null($var) && !is_numeric($var))){
				$var=$connection->real_escape_string($var);
			}
		}
		return $var;
	}	

	function censorString($str){
		$variable=badwords();
		$var=$str;
		if(!CENSOR_WORDS){
			return $var;
		}
		if(is_numeric($var) || is_null($var)){
			//return straight up nothing to process here.
			return $var;
		}
		if(CENSOR_WORDS){
			foreach ($variable as $key => $value) {
				if((strpos($str, $value))!=(-1)){					
					$v=strlen($value);
					$df=@substr($value, 0, 2);
					$vg=$v-(strlen($df));
					$chg="";
					for($oi=0; $oi<$vg; $oi++){
						$chg.="#";
					}
					$str=str_replace($value, $df.$chg, $str);	
				}
			}
		}
		return $str;
	}

	function checknum($query){
		$resy=queryMysql("$query");
		$num= $resy-> num_rows;
		return $num;
	}


	function getGetString($vr){
		$str="";
		if (isset($_GET[$vr])){
			$str=sanitizeString($_GET[$vr]);
		}
		return $str;
	}

	function getPostString($vr){
		$fggg="";
		$haystack=array("link","user", "who", "username");
		if (isset($_POST[$vr])){		
			$fggg=$_POST[$vr];
			if(in_array($vr, $haystack)){
				$fggg=str_replace(" ", "", $_POST[$vr]);
				$fggg=strtolower($fggg);
			}
		}
		return sanitizeString($fggg);
	}

	function getGetorPostString($vr){
		return (!empty(getGetString($vr)))?getGetString($vr):getPostString($vr);
	}

	function calcpages($total, $results_per_page){
	 // Calculate pagination information
		$cur_page = getCurrentPage();

		if(is_numeric($cur_page)){
	  		$skip = (($cur_page - 1) * $results_per_page);
	  		$num_pages = ceil($total / $results_per_page);

	    	// if ($num_pages > 1) {
	    		return " LIMIT $skip, $results_per_page";
			// }
			// if($)			
		}
		 return "";	
	}

	function getCurrentPage(){
		$cur_page = (!empty(getGetorPostString('page'))) ? getGetorPostString('page') : 1;
		if ($cur_page<1 || !is_numeric($cur_page)){
			$cur_page=1;
		}
		return $cur_page;
	}

	function getNextPage(){
		return getCurrentPage()+1;
	}
	function getPrevPage(){
		return getCurrentPage()-1;
	}

	function getSearchQuery($show_placeholder=false){
		$search=($show_placeholder)?translate('search'):"";
		if(isset($_GET['query'])){
			$search = getGetString('query');
		}else if(isset($_GET['q'])){
			$search = getGetString('q');
		}
		return $search;
	}

	function getThisUser(){
		if(IS_API){
			$usid=getGetorPostString("username");
		}else{
			$usid=(empty(getGetorPostString(API_USERKEY)))?strtolower(getData(USERNAME)):getGetorPostString(API_USERKEY);
		}
		return $usid;
	}

	function getThisUserFullname(){
		return getThisUserArray()['fullname'];
	}

	function getTotalDWM($TtimeStamp){
		$time_diff=$TtimeStamp;
        //tobe returned=$tbr
        if(empty($time_diff) && $time_diff!=0){
            return "0";
        }

        $one_min=60;
        $one_hr=3600;
        $one_day=$one_hr*24;
        $one_week=$one_day*7;

        $numb=$time_diff/$one_day;
        $tbr=translate_var("min", array(1));
        $to=$time_diff;

        if($numb>=1){
            //check for number of days
            if($numb>7){
            	$numb_=$numb/7;
            	if(is_int($numb_)){            		
	                //show 1w
	                $tbr=translate_var("w", array(teil($numb_)));
            	}else{
            		$num_months=$numb/30;
            		if(is_integer($num_months)){
                		$tbr=translate_var("m", array(teil($numb)));
            		}else{
                		$tbr=translate_var("d", array(teil($numb)));
            		}
            	}
            }else{
                $tbr=translate_var("d", array(teil($numb)));
            }
        }else{
            //show 1d
            $tbr=translate_var("d", array(1));
            $num_hr=$to/$one_hr;
            $num_min=$to/$one_min;

            if($num_hr>=1){
            	$tbr=translate_var("hr", array(teil($num_hr)));
            }else if($num_min>=1){
                //min
            	$tbr=translate_var("min", array(teil($num_min)));
            }else{
                //sec
                if(teil($to)<JUST_NOW)
            	$tbr=translate("j_nw");
                else
            	$tbr=translate_var("s", array(teil($to)));
            }
        }
        return $tbr;
		
	}

	function getFutureDWM($TtimeStamp){
        //tobe returned
        if(empty($TtimeStamp)){
            return "";
        }
		$time_diff=$TtimeStamp-time();
		return calcTimeDiff($time_diff, $TtimeStamp);
	}

	function getDWM2($TtimeStamp){
		return getDWM($TtimeStamp);
	}

	function getDWM3($TtimeStamp){
		return getDWM($TtimeStamp);
	}

	function getDWM($TtimeStamp){
        //tobe returned
        if(empty($TtimeStamp)){
            return "";
        }
		$time_diff=time()-$TtimeStamp;
		return calcTimeDiff($time_diff, $TtimeStamp);
	}

	function calcTimeDiff($time_diff, $TtimeStamp){

        //tobe returned=$tbr
        if(empty($time_diff) && $time_diff!=0){
            return "";
        }

        $one_min=60;
        $one_hr=3600;
        $one_day=$one_hr*24;
        $one_week=$one_day*7;

        $numb=$time_diff/$one_day;
        $tbr=translate_var("min", array(1));
        $to=$time_diff;

        if($numb>=1){
            //check for number of days
            if($numb>30){
                //show dd/mm
                $art=getdate($TtimeStamp);
                $min=$art['minutes'];
                $mon=$art['mon'];
                $dayy=$art['mday'];
                $tbr="".$dayy.'/'.$mon;
            }else if($numb>7){
            	$numb_=$numb/7;
            	if(is_int($numb_)){            		
	                //show 1w
	                $tbr=translate_var("w", array(teil($numb_)));
            	}else{
                	$tbr=translate_var("d", array(teil($numb)));
            	}
            }else{
                $tbr=translate_var("d", array(teil($numb)));
            }
        }else{
            //show 1d
            $tbr=translate_var("d", array(1));
            $num_hr=$to/$one_hr;
            $num_min=$to/$one_min;

            if($num_hr>=1){
            	$tbr=translate_var("hr", array(teil($num_hr)));
            }else if($num_min>=1){
                //min
            	$tbr=translate_var("min", array(teil($num_min)));
            }else{
                //sec
                if(teil($to)<JUST_NOW)
            	$tbr=translate("j_nw");
                else
            	$tbr=translate_var("s", array(teil($to)));
            }
        }
        return $tbr;
    }

  	function setActiveClass__($page, $other_must_classes="", $other_classes=""){
   	 //generates class if this link= current page
    	if(getActivePage___($page)){
      		return "class='active $other_must_classes $other_classes'";
    	}
      		return "class='$other_must_classes'";
  	}

  	/*function setActiveClass__Array($arr){
  		foreach ($arr as $key => $page) {
  			return setActiveClass__($page);
  		}
  		return "";
  	}*/

	function getActivePage___($page){
	  // explode
	  return ((strpos($_SERVER['PHP_SELF'], $page))>0);
	}

  	function setActiveClass__array($page, $other_must_classes="", $other_classes=""){
   	 //generates class if this link= current page
    	if(getActivePage___array($page)){
      		return "class='active $other_must_classes $other_classes'";
    	}
      		return "class='$other_must_classes'";
  	}

	function getActivePage___array($page){
	  // explode
		foreach ($page as $key => $value) {
			# code...
			if(((strpos($_SERVER['PHP_SELF'], $value))>0)){
				return true;
			}
		}
	  	return false;
	}

	

	function destroySession()
	{
		$_SESSION=array();
		session_destroy();
		if (isset($_COOKIE['userpiccmaq']) 
			|| isset($_COOKIE['passpiccmaq']))
			setcookie('userpiccmaq', '', time()-2592000, '/');
	      // Delete the user ID and username cookies by setting their expirations to an hour ago (3600)
		setcookie('userpiccmaq', '', time() - 545450);
		setcookie('passpiccmaq', '', time() - 35676676);
	}

	function encodePass($str){
		for ($i=0; $i < 6; $i++) { 
			$str=base64_encode($str);
		}
		return $str;
	}

	function decodePass($str){
		for ($i=0; $i < 6; $i++) { 
			$str=base64_decode($str);
		}
		return $str;
	}

	
	function setCookieUp_($name, $value, $time){		global $COOKIE;
		deleteCookie_($name);
		$time=is_numeric($time)?time()+$time:time()+COOKIE_TIMEOUT;
		if(IS_API){
			$COOKIE["$name"]=$value;
		}else{
			setcookie( $name, encodePass_($value), $time, "/","", 0);
		}
	}

	function getCookie_($name){
		global $COOKIE;
		if(IS_API){
			$rtrn = (isset($COOKIE[$name]))?decodePass_($COOKIE[$name]):"";
		}else{
			$rtrn = (isset($_COOKIE[$name]))?decodePass_($_COOKIE[$name]):"";
		}
		return $rtrn;
	}

	function deleteCookie_($name){	
		global $COOKIE;
		if(IS_API){
			$COOKIE["$name"]="";
		}else{
			setcookieUp( $name, "", 23, "/","", 0);
		}
	}

	function setCookieUp($name, $value, $time){	
		global $COOKIE;	
		if(IS_API){
			$COOKIE["$name"]=$value;
		}else{
			deleteCookie($name);
			$time=is_numeric($time)?time()+$time:time()+COOKIE_TIMEOUT;
			setcookie($name, encodePass($value), $time, "/","", 0);
		}
	}

	function getCookie($name){
		global $COOKIE;
		if(IS_API){
			$rtrn = (isset($COOKIE[$name]))?decodePass_($COOKIE[$name]):"";
		}else{
			$rtrn = (isset($_COOKIE[$name]))?decodePass_($_COOKIE[$name]):"";
		}
		return $rtrn;
	}

	function deleteCookie($name){	
		global $COOKIE;
		if(IS_API){
			$COOKIE["$name"]="";
		}else{
			setcookie( $name, "", 23, "/","", 0);
		}
	}

	 function saveData_($name, $value){
		$_SESSION[$name]=encodePass_($value);
		if(!isAPi()){
			setCookieUp_($name, $value, false);
		}
	}

	function deleteData_($name){
		$_SESSION[$name]="";
		if(!isAPi()){
			deleteCookie_($name);
		}
	}

	function getData_($name){
		$ref=(isset($_SESSION[$name]))?decodePass_($_SESSION[$name]):"";
		if(!isAPi()){
			$ref=(!empty($ref))?$ref:getCookie_($name);
		}
		return $ref;
	}


	function encodePass_($str){
		// for ($i=0; $i < 1; $i++) { 
		// 	$str=base64_encode($str);
		// }
		return $str;
	}

	function decodePass_($str){
		// for ($i=0; $i < 1; $i++) { 
		// 	$str=base64_decode($str);
		// }
		return $str;
	}

	function getPrevDay(){		
	    $yesterday=time();
	    $dt=getdate();
	    $month=$dt['mon'];
	    $day=$dt['mday'];
	    $year=$dt['year'];
	    $yst=mktime(0,0,0,$month,$day,$year);
	    return $yst;
	}



	?>