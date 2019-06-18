<?php 

function search()
{ 
	$array=array();
	$search=getSearchQuery();
	if(searchIsFromPosts()){
		$array=getPostsSearch($search);
	}else{
		$array=getMembersSearch($search);
	}

	return $array;
}

function searchIsFromPosts(){
	return (getGetString("from")=="all_posts");
}

function getPostsSearch($search){
		$array= array();
		/*$q="SELECT * FROM messages INNER JOIN friends ON messages.auth=friends.recip AND friends.user='$user' ORDER BY messages.time DESC";*/

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("posts/index.php?req=fetchlatest"), 
					"datatype"=>"json", 
					"data"=>getUserCred("q=".$search), 
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

				$subtitle=substr($commh, 0, MAX_SRCH_MSG_PRVW_LEN);
				array_push($array, array("username"=>$postparser->getAuth_username(),
										"userid"=>$postparser->getId(),
										"fullname"=>$postparser->getAuth_data()->getFullname(),
										"subtitle"=>ucwords($subtitle),
										"image"=>$postparser->getAuth_data()->getAuth_img(),
										"image_post"=>$postparser->getImage()));
			}
		}
		return $array;
}

function getMembersSearch($search){
		$user=getThisUser();
		$array=array();

		$maq = new Maq();
		$post_param=array("url"=>getUrlEnd("profile/index.php?req=search"), 
					"datatype"=>"json", 
					"data"=>getUserCred("q=".$search), 
					"baseurl"=>API_BASE_URL);
		$res=$maq->post($post_param)->body;

		$result=$res->data;
		$num=count($result);
		for($i=0;$i<$num;$i++){
			$row=$result[$i];
			$profileParser = new ProfileParser();
			$profileParser->load($row);
			if(!$profileParser->isEmpty()){
				array_push($array, array("username"=>$profileParser->getAuth_username(),
										"userid"=>$profileParser->getId(),
										"fullname"=>$profileParser->getAuth_data()->getFullname(),
										"subtitle"=>$profileParser->getSubtitle(),
										"image"=>$profileParser->getAuth_data()->getAuth_img()));
			}
		}
	return $array;
}

function getSrchMethod($mthd_name){
	$srchMethod=getGetString("from");
	if(empty($srchMethod) && $mthd_name!="all_posts"){
		return " not_active ";
	}
	return ($srchMethod=="$mthd_name")?" not_active ":"";
}



?>