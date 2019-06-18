<?php 



    function saveData($name, $value){
		$_SESSION[$name]=encodePass($value);
		if(!isAPi()){
			setCookieUp($name, $value, false);
		}
	}

	function deleteDir($dir){
	    $time=time();
	    $arr=scandir($dir);
	    foreach ($arr as $key => $value) {
	        if(($value!='..' && $value!='.')){
	            //if less than 1hr  ago.
	            $this__=$dir."/".$value;
	            // 
	            if (is_dir($this__)) {
	            	array_map('unlink', glob("$this__/*.*"));
	            	deleteDir($this__);
	            	rmdir($this__);
	            }else{
	            	@unlink($this__);
	            }
	        }
	    }
	}

	function mkdire($dir){
		@mkdir($dir, 0777, true);
	}

	function deleteDir__($dir){
	    $time=time();
	    mkdire($dir);
	    $arr=scandir($dir);
	    foreach ($arr as $key => $value) {
	        if(intval($value)<($time-DUMP_CLEAR_SECONDS) &&($value!='..' && $value!='.')){
	            //if less than 1hr  ago.
	            $this__=$dir."/".$value;
	            // 
	            if (is_dir($this__)) {
	            	array_map('unlink', glob("$this__/*.*"));
	            	deleteDir($this__);
	            	@rmdir($this__);
	            }else{
	            	@unlink($this__);
	            }
	        }
	    }
	}

	function deleteData($name){
		$_SESSION[$name]="";
		deleteCookie($name);
	}

	function getData($name){
		$ref=(isset($_SESSION[$name]))?decodePass($_SESSION[$name]):"";
		$ref=(!empty($ref))?$ref:decodePass(getCookie($name));
		return $ref;
	}


	function getNextDay(){		
	    $yesterday=time();
	    $dt=getdate();
	    $month=$dt['mon'];
	    $day=$dt['mday'];
	    $year=$dt['year'];
	    $yst=mktime(0,0,0,$month,$day,$year);
	    return $yst+(3600*24);
	}

	function checkLoggedIn(){
		global $rel____;
		if(!isLoggedIn()){
			header("Location: ".REL_DIR."index.php?nxt_url=".url_rewrite_query(''));
		}
	}

	function isLoggedIn(){
		$a=getData(USERNAME);
		$b=getData(PASSWORD);
		if(IS_API){
		    $a=getThisUser();
		    $b=getGetOrPostString("pass");
		}
		return username_and_passValid($a, $b);
	}
	
	function url_rewrite_query($req){
		$queries=array();
		$query_=explode("&", $req);
		foreach ($query_ as $key => $query) {
			$gh=explode("=", $query);
			array_push($queries, $gh[0]);
		}
		$gets="?";
		foreach ($_GET as $key => $value) {
		  # code...
			if($key=="alert")
		    	continue;
		    if(in_array($key, $queries))
		    	continue;
		    $gets.=$key.'='.$value.'&';
		}
		return $_SERVER['PHP_SELF'].$gets.$req;
	}

	function url_rewrite_query_($req){
		//adds a value to the already initiated get req;
		$url_get=$_SERVER['REQUEST_URI'];

	    $var_=explode("?", ($url_get));
	    $num=count($var_);
	    $var__=explode("&", ($url_get));
	    $num_=count($var__);
	    if((strlen($var_[$num-1]))<1){
	        $res=$url_get.$req;
	    }else
	    if((strlen($var__[$num_-1]))<1){
	        $res=$url_get.$req;
	    }else
		if(strpos($url_get, "?")>-1){
			$res=$url_get."&".$req;
		}else{
			$res=$url_get."?".$req;
		}
		return $res;
	}

	function goBack__(){
		$ref=(!empty(getGetString('redir_uri')))?getGetString('redir_uri'):"";
		$ref=(empty($ref) && !empty($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:$ref;
		$ref=(empty($ref))?REL_DIR."home.php":$ref;
		return $ref;
	}

	function getBackLink($rel____){
		$ref=(!empty(getGetString('redir_uri')))?getGetString('redir_uri'):"";
		$ref=(empty($ref) && !empty($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:$ref;
		$ref=(empty($ref))?$rel____."home.php":$ref;
		return $ref;		
	}

	function getBackLink_($rel____){
		$ref=(!empty(getGetString('redir_uri')))?getGetString('redir_uri'):"";
		$ref=(empty($ref))?$rel____."home.php":$ref;
		return $ref;		
	}

	function setActiveLink($page){
		//generates class if this link= current page
		if(getActivePage()==$page){
			return " active";
		}
	}
	
	function isActiveLink($page){
		//generates true if this link= current page
		return (getActivePage()==$page)?true:false;
	}

	function setActiveClass($page){
		//generates class if this link= current page
		if(getActivePage()==$page){
			return "class='active'";
		}
	}

	function getActivePage(){
		// explode
		$arr=explode("/", $_SERVER['PHP_SELF']);
		$arr=explode(".", $arr[count($arr)-1]);
		return $arr[0];
	}


	function invPermission($rel____){
		header("Location: ".$rel____."notfound.php?a=504");
	}

	function getConnection(){
		global $connection;
		return $connection;
	}

	function getLastId(){
		return getConnection()->insert_id;
	}

	function isSelected($a, $b){
		if($a==$b){
			return "selected";
		}
	}
	


	function areFrnds($user, $useri){
		$r_rcvd=(checknum("SELECT * FROM friends WHERE (user='$useri' AND recip='$user') AND confirm=1"));
		$r_sent=(checknum("SELECT * FROM friends WHERE user='$user' AND recip='$useri' AND confirm=1"));
		$r_frnds=(checknum("SELECT * FROM friends WHERE ((user='$user' AND recip='$useri') OR (user='$useri' AND recip='$user')) AND confirm=2"));
	    if(isApi()){
    		$r_rcvd=($r_rcvd);
    		$r_sent=($r_sent);
    		$r_frnds=($r_frnds);
	    }else{
    		$r_rcvd=($r_rcvd>0);
    		$r_sent=($r_sent>0);
    		$r_frnds=($r_frnds>0);
	    }

		return array("r_rcvd"=>$r_rcvd, "r_sent"=>$r_sent, "r_frnds"=>$r_frnds);
	}

 ?>