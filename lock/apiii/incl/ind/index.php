<?php	
	
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config'. DIRECTORY_SEPARATOR ;
	require $rel.'index.php';
	require $rel.'errors.php';
	
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR . 'translate'. DIRECTORY_SEPARATOR ;
	include $rel.'translate.php';

	

	$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($connection->connect_error) die($connection->connect_error);

	function getReq(){
		return getGetString("req");
	}


    function getFullname($name){
    	$user=$name;
    	$array= @(queryMysql("SELECT * FROM profiles WHERE user='$user'"))->fetch_array(MYSQLI_ASSOC);
 		$fullname=ucWords_((!empty(@$array['fullname']))?@$array['fullname']:$user);
		return $fullname;
    }

    function getUserDP($user){
    	global $rel____;
    	$image=$rel____.IMGSTORE___.getDPNAME($user);
		$image=(file_exists($image))?$image:FEMALE_DP;
		return $image;
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

    function getDPNAME($user){
    	$dp_name=getSql("SELECT prof_pic from profiles where user='$user'", "prof_pic");
    	$dp_name=(empty($dp_name))?DEF_PIC:$dp_name;
    	return $dp_name;
    }

	function ucWords_($vr){
		return ucwords($vr);
	}
	
	function getThisUser(){
		return getGetorPostString("username");
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

	function checknum($query){
		$resy=queryMysql("$query");
		$num= $resy-> num_rows;
		return $num;
	}


	function queryMysql($query)
	{
		global $connection;
		// fwrite(fopen("jddj", "a+"), $query);
		$result = $connection->query($query);
		if (!$result) die($connection->error);
		return $result;
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
		$haystack=array("link","user");
		if (isset($_POST[$vr])){		
			if(in_array($vr, $haystack)){
				$fggg=str_replace(" ", "", $_POST[$vr]);
			}
			$fggg=$_POST[$vr];
		}
		return sanitizeString($fggg);
	}
	
	function getGetorPostString($vr){
		$ay=getGetString($vr);
		if(empty($ay)){
			$ay=getPostString($vr);
		}else{
			$ay=getGetString($vr);
		}
		return $ay;
	}

	function sanitizeString($var)
	{
		global $connection;
		if(is_numeric($var) || is_null($var) || empty($var)){
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

	function getConnection(){
		global $connection;
		return $connection;
	}

	function getArrayValue($array, $key, $def){
		if(in_array($key, $array)){
			return $array[$key];
		}
		return $def;
	}

	function getLastId(){
		return getConnection()->insert_id;
	}

	function getSql($sql, $point){
		return @((queryMysql($sql))->fetch_array(MYSQLI_ASSOC))[$point];
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

?>