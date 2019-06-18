<?php
  
	function sendPiccoin(){
		$piccoin = $coins_to=getPostString('piccoin');
	    $to = getOtherUser();

	    $maq = new Maq();
		$post_param=array("url"=>getUrlEnd("piccoins/index.php?req=send"), 
					"datatype"=>"json", 
					"data"=>getUserCred("who=".$to."&piccoin=".$piccoin), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;
		$result=$res->data;
		$succError = new SuccessError();
		$succError->load($result[0]);
		if($succError->isSuccessful())
			return SUCCESS;
		else{
			$err=$succError->getError()->getError();
			if($err==6480){
				return NO_USER;
			}else{
				return INV_OP;
			}
		}
	}


	function sendPiccoinMsg($subj, $obj, $amt){
		$fname__=getFullname($subj);
		$_POST['body']= translate_var('send_piccoin_msg', array($fname__, $amt));
		$useri=$obj;
		return sendMsg_($useri);
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
	    return SUCCESS;		
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
	    return SUCCESS;		
	}

	function getCoins($user){
		$result1 = queryMysql("SELECT * FROM profiles WHERE user='$user'");
	    $t2 = $result1->fetch_array(MYSQLI_ASSOC);
	    return (!empty($t2['piccoins']))?$t2['piccoins']:0;		
	}
?>