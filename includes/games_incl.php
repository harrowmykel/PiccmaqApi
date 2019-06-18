<?php

	function getResemblance($user){
		return getThisUserAlienName($user);
	}


	function getThisUserAlienName($user){
		if(getCoins($user)<ALIEN_COST){
			return NO_PICCOIN;
		}
		rmvPiccoinByOffer(ALIEN_COST, $user);

		$q="SELECT * FROM alien_names WHERE ndsrjj <>'' ORDER BY RAND() LIMIT 1";
		$q_="SELECT * FROM alien_names ORDER BY RAND() LIMIT 1";
		$first_name=getSql(str_replace("ndsrjj", "adjectives", $q), "adjectives");
		$second_name=getSql(str_replace("ndsrjj", "nouns", $q), "nouns");
		$sur=getSql(str_replace("ndsrjj", "video_game_names", $q), "video_game_names");
		$planet=getSql($q_, "planet");
		$subtitle=getSql($q_, "subtitle");
		if(!empty($user)){
			$name=$first_name." ".$sur." ".$second_name;
			$array=array("username"=>str_replace(" ", "", $name),
				"fullname"=>ucwords($name),
				"hobby"=>"",
				"country"=>$planet,
				"image"=>DEF_URL.ALIEN_IMG);
			return $array;
		}
		return false;
	}

	function writeOnImageForAlien($rel____, $image, $text){
	  $im=@imagecreatefromjpeg($rel____.$image) or die("cannot init new GD img stream");
	  // $im=@imagecreate(110, 20) or die("cannot init new GD img stream");
	  $background_color=imagecolorallocate($im, 0, 0, 0);
	  $text_color=imagecolorallocate($im, 1000, 1000, 1000);
	  $imghj=DUMPYARD.CURRENT_TIME;
	  $dir=$rel____.$imghj;
	  @mkdir($dir, 0777, true);
	  $imgh=gnrtNewString(6, 16).".png";
	  $link=$dir.$imgh;
	  imagestring($im, 10, 40, 110, $text, $text_color);
	  imagepng($im, $link);
	  imagedestroy($im);
	  return $imghj.$imgh;
	}

	function shareGameResult($user, $text, $image, $rel____, $copy=true, $use_rel=true){
		$user=getThisUser();
		$message=$text;
		$privacy=privacy_public;
		$view=$user;
		$time=CURRENT_TIME;
		if(!$use_rel){
			$rel____='';
		}
		$fnjfn= getPrevDay();

		 // $fn=60*24*60;
		 // echo $fnjfn;
		 $ret="";
		 if($image!=""){
		 	$e=getFileName($image);
			$ret=gnrtNewString(6, 16).$e;
			$ty=getRel____().IMG_STORE.$fnjfn."/";	
			@mkdir($ty, 0777, true);
			$f_location=$ty.$ret;
	        while(is_file($f_location) && file_exists($f_location)){          
				$ret=gnrtNewString(6, 16).$e;
				$ty=getRel____().IMG_STORE.$fnjfn."/";
				@mkdir($ty, 0777, true);	
				$f_location=$ty.$ret;
	        }
			$saveto = $f_location;
			@copy($rel____.$image, $f_location);
			if(!$copy){
				@unlink($rel____.$image);
			}
			$typeok = TRUE;

			switch(getFileExtension($image))
			{
			  case "image/gif":   $src = imagecreatefromgif($saveto); break;
			  case "image/jpeg":  // Both regular and progressive jpegs
			  case "image/jpeg": $src = imagecreatefromjpeg($saveto); break;
			  case "image/png":   $src = imagecreatefrompng($saveto); break;
			  default:            $typeok = FALSE; break;
			}

			if ($typeok)
			{
			  editImage($src,$saveto);
			 }
		}

		if(empty($text) && empty($ret)){
			return false;
		}

		echo $image;

		echo $saveto;

		$ret=(!empty($ret))?$fnjfn."/".$ret:$ret;
		$e="INSERT INTO messages (id, auth, recip, privacy_, time, message, picture, is_uneditable) VALUES(NULL, '$user', '$view', '$privacy', $time, '$text', '$ret', 1)";
    	queryMysql($e);
    	$id=getLastId();
    	$recip=$user=getThisUser();
    	$message=$text;
    	checkMentions($id, $user, $recip, $message);
	}

	function getTwin($user){
		if(getCoins($user)<TWIN_COST){
			return NO_PICCOIN;
		}
		rmvPiccoinByOffer(TWIN_COST, $user);
		$q="SELECT * FROM bday_tbl WHERE user='$user'";
		$user_bday=getSql($q, "bday");
		$q="SELECT * FROM bday_tbl WHERE bday='$user_bday' ORDER BY RAND()";
		$num=checknum($q);
		$q=$q.calcpages($num, 1);
		$user_bday=getSql($q, "user");
		$useri=$user_bday;
		if(TWIN_MUST_HAVE_UPLOADED_PIC){
			while (!userHasUploadedDp($useri)) {
				$q="SELECT * FROM bday_tbl WHERE bday='$user_bday' ORDER BY RAND()";
				$num=checknum($q);
				$q=$q.calcpages($num, 1);
				$user_bday=getSql($q, "user");
				$useri=$user_bday;
			}			
		}
		if(!empty($useri))
			return getUserProperty($useri);
		return false;
	}
?>