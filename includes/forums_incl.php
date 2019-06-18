<?php 

    function getAllGroupMembers(){
		$user=getThisUser();
		$useri=empty(getOtherUser())?getThisUser():getOtherUser();
		$array=array();		
		$q="SELECT * FROM  profiles WHERE profiles.user IN (SELECT groups.user FROM groups WHERE groups.recip='$useri')";
		$q=" (SELECT groups.user FROM groups WHERE groups.recip='$useri')";
		$t_num=checknum($q);
		$result=queryMysql($q.calcpages($t_num, VARR));
		$num=$result->num_rows;
		for ($i=0; $i <$num ; $i++) { 
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$subtitle=(!empty($row['place']))?$row['place']:$row['country'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['work'];
			$subtitle=(!empty($subtitle))?$subtitle:$row['school'];
			$image=getUserDP($row['user']);
			$fullname=(!empty($row['fullname']))?$row['fullname']:$row['user'];

			$frnd_r=areFrnds($user, $row['user']);
			$r_rcvd=$frnd_r['r_rcvd'];
			$r_sent=$frnd_r['r_sent'];
			$r_frnds=$frnd_r['r_frnds'];
			$admin=0;

			array_push($array, array("username"=>$row['user'],
									"userid"=>$row['user_id'],
									"fullname"=>ucwords($fullname),
									"subtitle"=>ucwords($subtitle),
									"r_sent"=>$r_sent,
									"r_rcvd"=>$r_rcvd,
									"r_frnds"=>$r_frnds,
									"admin"=>$admin,
									"image"=>$image));
		}
		return $array;
	}

    function getForumLists($search=""){    		
		$user=getThisUser();
		$array=array();

		$maq = new Maq();
		$asd=(empty($search))?"":"q=".$search;
		$post_param=array("url"=>getUrlEnd("groups/index.php?req=search"), 
					"datatype"=>"json", 
					"data"=>getUserCred($asd), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0; $i<$num; $i++){
			$row=$result[$i];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				array_push($array, array("fullname"=>$profileParser->getAuth_data()->getFullname(),
            							 "subtitle"=>replaceSmiles($profileParser->getBio()),
            							 "username"=>$profileParser->getAuth_username(),
            							 "image"=>$profileParser->getAuth_data()->getAuth_img()));
			}
		}
		return $array;
    }


	function getForumBasic($id){
		$array=array();
		$image=getUserDP("djnd");
		$array=array("username"=>$id,
					"userid"=>$id,
					"fullname"=>@forum_array()[$id],
					"subtitle"=>@forum_array()[$id],
					"image"=>$image);
		return $array;

	}

	function forum_array(){
		$arr=array("157752"=>translate("general_discussions"),
					"367972"=>translate("girls_only"),
					"417549"=>translate("boys_only"),
					"425179"=>translate("sports"),
					"220962"=>translate("just_for_fun"),
					"243509"=>translate("video_games"),
					"94409"=>translate("religion_and_beliefs"),
					"4077"=>translate("news_and_gossip"),
					"279941"=>translate("automotives"),
					"91117"=>translate("programmers"),
					"193589"=>translate("movies"),
					"117009"=>translate("music"),
					"50321"=>translate("technology_and_computers"),
					"113915"=>translate("education"),
					"322390"=>translate("business"),
					"425443"=>translate("languages"));
		return $arr;
	}

	function getForumMessages(){
		$array=array();
		$user=getThisUser();
		$ef=$useri=getForumkey();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=userposts"), 
					"datatype"=>"json", 
					"data"=>getUserCred("who=".$useri), 
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
	

				array_push($array, array("auth_username"=>$postparser->getAuth_username(),
										"reciv"=>$row['reciv'],
										"message"=>replaceSmiles($row['text']),
										"auth"=>getFullname($row['aut']),
										"time"=>getDWM2($row['time']),
										"timestamp"=>$row['time'],
										"image"=>$image));
				
		    	/*array_push($array, array("auth"=>$postparser->getAuth_data()->getFullname(), 
		    							 "recip"=> $postparser->getReciv_data()->getFullname(), 
		    							 "auth_username"=>, 
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
		    							 "comments"=>$postparser->getComments()));*/
			}
		}
		return $array;
	}
 ?>