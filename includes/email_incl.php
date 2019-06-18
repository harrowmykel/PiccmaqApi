<?php

	function sendRecoveryMail($a){
		if(count(explode("@", $a))>1){
			$email=$a;
			$uusername=(queryMysql("SELECT * FROM members WHERE email='$a'")->fetch_array(MYSQLI_ASSOC))['user'];
		}else{
			$uusername=$a;
			$email=(queryMysql("SELECT * FROM members WHERE user='$a'")->fetch_array(MYSQLI_ASSOC))['email'];
		}

		$hashCode=gnrtNewString(7,10);
		$time=time();
		$likn=DEF_URL."/passrecovery.php?hash=$hashCode&user=$uusername";
		$app=translate("app_name");

		queryMysql("INSERT INTO password_gen (id, hash_code, username, time, deactivated_) VALUES (NULL, '$hashCode', '$uusername', $time, 0)");
		$message="Hello $uusername, \n \t \t You are receiving this message because you clicked on forgot password, please use this link $likn to login again.\n \t \t if you did not make this request, please discard this message.\n \t \t The $app Team.";

		return @mail("$email","$app:New Password Request","$message");
	}

	?>