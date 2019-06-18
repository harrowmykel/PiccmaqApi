<?php

	function getStoryArray(){
		$postid=getGetString('postid');
		$array=array();

		if(!empty($postid)){
			return getStoryArray_($postid);
		}
		return $array;
	}

	function getAllLikes(){
		$post_id=getGetString('postid');
		$array = array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=getlikes"), 
					"datatype"=>"json", 
					"data"=>getUserCred("postid=".$post_id), 
					"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$postparser = new LikesParser();
			$postparser->load($row);
			if(!$postparser->isEmpty()){				
				array_push($array, array("username"=>$postparser->getAuth_username(),
										"fullname"=>$postparser->getAuth_data()->getFullname(),
		    							 "liked"=>untranslate($postparser->getLiked())
										));
			}
		}
		return $array;
	}

	function getStoryArray_($postid){
		$user=getThisUser();
		$array= array();
		/*$q="SELECT * FROM messages INNER JOIN friends ON messages.auth=friends.recip AND friends.user='$user' ORDER BY messages.time DESC";*/

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=fetchthis"), 
					"datatype"=>"json", 
					"data"=>getUserCred("postid=".$postid), 
					"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		if($num>0){
			$row=$result[0];
			$postparser = new PostParser();
			$postparser->load($row);
			if(!$postparser->isEmpty()){
		
		 		$sd= $postparser->getSubtitle();
				$commh=( getActivePage___ ( "edit" ))?$sd:replaceSmiles($sd);
				switch ($postparser->getPrivacy()) {
					case privacy_friends:
						$privacy=translate("frnds");
						break;
					case privacy_private:
						$privacy=translate("only_me");
						break;			
					default:
						$privacy=translate("public");
						break;
				}
	
		    	$array = array("auth"=>$postparser->getAuth_data()->getFullname(), 
		    							 "recip"=> $postparser->getReciv_data()->getFullname(), 
		    							 "auth_username"=>$postparser->getAuth_username(), 
		    							 "recip_username"=>$postparser->getReciv_username(), 
		    							 "time"=> getDWM($postparser->getTime()), 
		    							 "timestamp"=>$postparser->getTimestamp(),
		    							 "message"=> $commh,  
		    							 "img_auth"=>$postparser->getAuth_data()->getAuth_img(), 
		    							 "postid"=> $postparser->getId(),
		    							 "phrase"=>$postparser->getPhrase(),
		    							 "privacy"=>$privacy,
		    							 "priv"=>$postparser->getPrivacy(),
		    							 "is_editable"=>1,
		    							 "ext_link"=>1,
			    						"reply_to"=>$postparser->getReply_to(),
		    							 'image'=>$postparser->getImage(),
		    							 "likes"=>$postparser->getLikes(),
		    							 "liked"=>$postparser->getLiked(),
		    							 "comments"=>$postparser->getComments());
			}
		}
		return $array;
	}


	function getComments(){
		$postid=getGetString('postid');
		$array=array();

		if(!empty($postid)){
			return getComments_($postid);
		}
		return $array;
	}

	function getComments_($post_id){
		$postid=$post_id;
		$user=getThisUser();
		$array= array();
		/*$q="SELECT * FROM messages INNER JOIN friends ON messages.auth=friends.recip AND friends.user='$user' ORDER BY messages.time DESC";*/

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=getpost"), 
					"datatype"=>"json", 
					"data"=>getUserCred("postid=".$postid), 
					"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$postparser = new PostParser();
			$postparser->load($row);
			if(!$postparser->isEmpty()){
		
		 		$sd= $postparser->getSubtitle();
				$commh=( getActivePage___ ( "edit" ))?$sd:replaceSmiles($sd);
				switch ($postparser->getPrivacy()) {
					case privacy_friends:
						$privacy=translate("frnds");
						break;
					case privacy_private:
						$privacy=translate("only_me");
						break;			
					default:
						$privacy=translate("public");
						break;
				}
	
		    	array_push($array, array("auth"=>$postparser->getAuth_data()->getFullname(), 
		    							 "recip"=> $postparser->getReciv_data()->getFullname(), 
		    							 "auth_username"=>$postparser->getAuth_username(), 
		    							 "recip_username"=>$postparser->getReciv_username(), 
		    							 "time"=> getDWM($postparser->getTime()), 
		    							 "timestamp"=>$postparser->getTimestamp(),
		    							 "message"=> $commh,  
		    							 "img_auth"=>$postparser->getAuth_data()->getAuth_img(), 
		    							 "postid"=> $postparser->getId(),
		    							 "phrase"=>$postparser->getPhrase(),
		    							 "privacy"=>$privacy,
		    							 "priv"=>$postparser->getPrivacy(),
		    							 "is_editable"=>1,
		    							 "ext_link"=>1,
			    						"reply_to"=>$postparser->getReply_to(),
		    							 'image'=>$postparser->getImage(),
		    							 "likes"=>$postparser->getLikes(),
		    							 "liked"=>$postparser->getLiked(),
		    							 "comments"=>$postparser->getComments()));
			}
		}
		return $array;
	}

	function savePost(){
		$user=strtolower(trim(getThisUser()));
		$message=$text=getPostString('xc_message');
		$privacy=getPostString('dropdown');
		$view=(isset($_POST['recip']))?getPostString('recip'):getThisUser();
		$view=strtolower(trim($view));
		$time=CURRENT_TIME;


		$fnjfn= getPrevDay();

		 // $fn=60*24*60;
		 // echo $fnjfn;
		 $ret="";


		if(strtolower(trim($view))!=strtolower(trim($user))){
    		$text="@".$view." ".$text;
		}
		$message=$text;
		
		if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name']))
		{	
			$e=$_FILES['image']['name'];
			$ret=genFileName(gnrtNewString(6, 16).$e);
			$ty=ROOT_DIR.IMG_STORE.$fnjfn."/";	
			@mkdir($ty, 0777, true);
			$f_location=$ty.$ret;
	        while(is_file($f_location) && file_exists($f_location)){          
				$ret=genFileName(gnrtNewString(6, 16).$e);
				$ty=ROOT_DIR.IMG_STORE.$fnjfn."/";
				@mkdir($ty, 0777, true);	
				$f_location=$ty.$ret;
	        }
			$saveto = $f_location;
			move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
			$typeok = TRUE;
			//$k = $_FILES['image']['type'];

			//$l = $_FILES['image']['size'];

			switch($_FILES['image']['type'])
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

		$ret=(!empty($ret))?$fnjfn."/".$ret:$ret;

		$maq = new Maq();
		$adf="who=".$view."&privacy=".$privacy."&text=".$text."&imgurl=".$ret;
		if($view==$user){
			$post_param=array("url"=>getUrlEnd("posts/index.php?req=create"), 
						"datatype"=>"json", 
						"data"=>getUserCred($adf), 
						"baseurl"=>API_BASE_URL);
		}else{
			$post_param=array("url"=>getUrlEnd("posts/index.php?req=createtouser"), 
						"datatype"=>"json", 
						"data"=>getUserCred($adf), 
						"baseurl"=>API_BASE_URL);			
		}

		$res=$maq->post($post_param)->body;
		$result=$res->data;

		$succError = new SuccessError();
		$succError->load($result[0]);
		if($succError->isSuccessful())
			return true;
		else
			return false;
	}

	function resavePost(){
		$user=getThisUser();
		$message=$text=getPostString('xc_message');
		$privacy=getPostString('dropdown');
		$time=CURRENT_TIME;
		$id=getGetString("postid");

	    if(empty($id) || empty($text)){
			return false;
		}

		$maq = new Maq();
		$adf="postid=".$id."&privacy=".$privacy."&text=".$text;
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=edit"), 
						"datatype"=>"json", 
						"data"=>getUserCred($adf), 
						"baseurl"=>API_BASE_URL);

		$res=$maq->post($post_param)->body;
		$result=$res->data;

		$succError = new SuccessError();
		$succError->load($result[0]);
		if($succError->isSuccessful())
			return true;
		else
			return false;
	}

?>