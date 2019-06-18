<?php 

error_reporting(E_ALL); ini_set('display_errors', 1);
	$root="../";
	include $root. "incl/index.php";

    $NoLogIn=array();
    runApiReqCheck($NoLogIn);
	$req=getReq();

	switch($req){
		case "fetch":
			page_is_first();
			$resultt= getCoins();
			break;
		case "rmvoffer":
			$resultt= rmvPiccoinByOffer();
			break;
		case "sendoffer":
			$resultt= sendPiccoinByOffer();
			break;
		case "send":
			$resultt= sendPiccoin();
			break;
		case "sendpiccoinmsg":
			$resultt= sendPiccoinMsg();
			break;
		default:
			$resultt=apiLeave(invReq());
			break;
	}

	release($resultt);
// getCoins($user){rmvPiccoinByOffer($amt, $from){sendPiccoinByOffer($amt, $from){
// 		$fname__=getFullname($subj);
// 		$piccoin = $coins_to=getPostString('piccoin');sendPiccoin()sendPiccoinMsg($subj, $obj, $amt)

	function sendPiccoin(){
		$piccoin = $coins_to=getGetorPostString('piccoin');
		$user=trim(getThisUser());
	    $coin = getCoins($user);
	    $ink = ($coin - $piccoin);
	    $to = trim(getGetorPostString('who'));
	    
	    if (strtolower($to)== strtolower($user)){
	    	return apiLeave(invOP());
	    }
	    if ($ink < 1){
	    	return apiLeave(invUser());
	    }else if (!is_numeric($coins_to)){
	    	return apiLeave(invOP());
	    }else{
	    	$result3 = queryMysql("SELECT * FROM profiles WHERE user='$to' AND is_group=0");
	    	$num = $result3->num_rows;
	    	if ($num == 0){
	    		return apiLeave(invUser());
	    	}else{
			    $t3 = $result3->fetch_array(MYSQLI_ASSOC);
			    $coi = $t3['piccoins'];
			    $coink = ($coi + $piccoin);
			    queryMysql("UPDATE profiles SET piccoins='$coink' WHERE user='$to'");
			    queryMysql("UPDATE profiles SET piccoins='$ink' WHERE user='$user'");
			    // addPiccoinShareNotify($user, $to, $piccoin);
			    //requires message_incl
			    sendPiccoinMsg($user, $to, $piccoin);
				return apiLeave(success(345));
			}
		}
	}


	function sendPiccoinMsg($subj, $obj, $amt){
		$fname__=getFullname($subj);
		$_POST['body']= translate_var('send_piccoin_msg', array($fname__, $amt));
		$useri=$obj;
		return true; //sendMsg_($useri);
	}


	function sendPiccoinByOffer($amt, $from){
		$var=$amt;
		$other=$from;
		$piccoin = $var;
	    $to = $other;
    	$result3 = queryMysql("SELECT * FROM profiles WHERE user='$to'");
    	$num = $result3->num_rows;
	    $t3 = $result3->fetch_array(MYSQLI_ASSOC);
	    $coi = $t3['piccoins'];
	    $coink = ($coi + $piccoin);
	    queryMysql("UPDATE profiles SET piccoins='$coink' WHERE user='$to'");
		return apiLeave(success(345));	
	}

	function rmvPiccoinByOffer($amt, $from){
		$var=$amt;
		$other=$from;
		$piccoin = $var;
	    $to = $other;
    	$result3 = queryMysql("SELECT * FROM profiles WHERE user='$to'");
    	$num = $result3->num_rows;
	    $t3 = $result3->fetch_array(MYSQLI_ASSOC);
	    $coi = $t3['piccoins'];
	    $coink = ($coi - $piccoin);
	    queryMysql("UPDATE profiles SET piccoins='$coink' WHERE user='$to'");
	    sendPiccoinByOffer($amt, ADMIN_USERNAME);
		return apiLeave(success(345));
	}

	function getCoins($user){
		$result1 = queryMysql("SELECT * FROM profiles WHERE user='$user'");
	    $t2 = $result1->fetch_array(MYSQLI_ASSOC);
	    $adf=(!empty($t2['piccoins']))?$t2['piccoins']:0;	
	    return (($adf));	
	    // return apiLeave(array("res"=>$adf));	
	}

?>