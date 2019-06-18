<?php

    function getAllSiteList(){
        $array=array();
        $search=getGetString("query");
        if(empty($search)){
            $q=$query="SELECT distinct page FROM afree_pageview where is_page=1";
        }else{
            $q=$query="SELECT distinct page FROM afree_pageview where is_page=1 and page LIKE '%$search%'";
        }
        
        switch(getGetString("order")){
            case "page":
            case "time":
                $ordr=getGetString("order");
                break;
            default:
                $ordr="page";
                break;
        }
        
        if(getGetString("sort")=="desc"){
            $sort="DESC";
        }else{
            $sort="ASC";
        }
        
        $q=$query=$q." ORDER BY $ordr $sort";
		$result=queryMysql($query);
		$num=$result->num_rows;
		
		$curr_pages=getCurrentPage();

		$result = queryMysql($q .calcpages($num, NO_OF_PAGES));
		$num=$result->num_rows;
		
    	if($num>0){
    	    for($a=0; $a<$num; $a++){
        		$row=$result->fetch_array(MYSQLI_ASSOC);
        		$page=$row["page"];
        		$id=$row["id"];
        		array_push($array, array("page"=>$page, "id"=>$id));
    	    }
    	}
    	return $array;
    }
    
    function getSiteDetails(){
        $array=array();
        $search=getGetString("link");
        $q=$query="SELECT * FROM afree_pageview where page='$search'";
        
        switch(getGetString("order")){
            case "page":
            case "time":
            case "pageviews":
                $ordr=getGetString("order");
                break;
            default:
                $ordr="page";
                break;
        }
        
        if(getGetString("sort")=="desc"){
            $sort="DESC";
        }else{
            $sort="ASC";
        }
        
        $q=$query=$q." ORDER BY $ordr $sort";
		$result=queryMysql($query);
		$num=$result->num_rows;
		
		$curr_pages=getCurrentPage();

		$result = queryMysql($q .calcpages($num, NO_OF_PAGES));
		$num=$result->num_rows;
		
    	if($num>0){
    	    for($a=0; $a<$num; $a++){
        		$row=$result->fetch_array(MYSQLI_ASSOC);
        		$page=$row["page"];
        		$id=$row["id"];
        		$time=$row["time"];
        		$pgvw=$row["pageviews"];
        		array_push($array, array("page"=>$page, "id"=>$id, "time"=>$time, "pageviews"=>$pgvw));
    	    }
    	}
    	return $array;
    }

?>