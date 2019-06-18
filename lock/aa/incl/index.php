<?php
	ob_start();
	$rel____=$root."../../";
	define('IS_API', true);	
	$rel="/lock/apiii/incl/config";
	$relsdf="/home/piccmaqc/public_html/";
	include $relsdf . 'includes/incl.php';
	include $relsdf . 'includes/validate_incl.php';
	include $relsdf . 'includes/profile_incl.php';
	include $relsdf . 'includes/avatar_incl.php';
	include $relsdf . 'includes/notification_incl.php';

    
    function checkApiLogin(){
        $signedIn=isLoggedIn();
        if(WORK_WITHOUT_LOGIN!=true){
            if(!$signedIn){
                release(apiLeave(invUserPass()));
            }
        }
    }
    
    function runApiReqCheck($NoLogIn){
    	define("API_KEY_VALUE", getGetOrPostString("api_key"));
    	
        if(!in_array(API_KEY_VALUE, AUTH_API_KEY_VALUE)){
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

?>