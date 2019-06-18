<?php
		$website="adpay.tk";
		define('website', "$website");
		$name="changer.php";
		define('name', "$name");

// include 'unirest.php';

class apiQuery{

	var $last_retrofit;

	public function __construct(){

	}

	public function getRetrofit(){
		//return last retrofit
		return $last_retrofit;
	}

	public function send($recip, $array){
    	$data=array("data"=>$array);
	    if(SEND_TO_USER_TOPIC){
		    $firekey=$recip.USER_FIREBASE_TOPIC;
			$apiquery=new apiQuery();
			$res=$apiquery->sendTopic($firekey, $data);
	    }else{
    		$q="SELECT * FROM firebase_keys WHERE username='$recip'";
    		$df=checknum($q);
    		if($df>0){
    			$resu=queryMysql($q);
    			for($o=0; $o<$df; $o++){
    			    $row=$resu->fetch_array(MYSQLI_ASSOC);
        			$firekey=$row["firekey"];
        			$apiquery=new apiQuery();
        			$res=$apiquery->sendToUser($firekey, $data);
    			}
    		}	
	    }
	}

	public function sendMsg($recip, $array){
        $this->send($recip, $array);
	}

	public function sendTopic($to, $data){  
		$rtt=$this->call("/topics/".$to, $data);
		return $rtt;		
	}	

	public function sendToUser($user, $data){	
		$rtt=$this->call($user, $data);
		return $rtt;		
	}	

	public function sendToGlobal($user, $data){
		$rtt=$this->call($user, $data);
		return $rtt;		
	}	
	
	function refurbish($data){
	 	$data=json_encode($data);
	 	$data=str_replace("\"", "&quot;", $data);
	 	return $data;
	}

	 function call($to, $data){
	 	 // return $this->callText($to, $data);
// 	 	return $this->callJson($to, $data);
	 	return null;
	 }
		
    function callText($to, $data){   	
		$data = array("to"=>$to,
					"notification"=>array(
							"body"=>$data,
							"title"=>"firebase"
									));
    	$headers =$this->header_();
    	$rt=Httpful\Request::post($this->getApiHome())
			->method(Httpful\Http::POST)
			->withoutStrictSsl()
			->sendsJson() 
			->addHeaders($headers)
			->body($data)
			->sendIt();
		$this->last_retrofit=$rt;
		return $rt;
    }
		
    function callJson($to, $data){ 
        $sda=getNotGh();
        $data1=array($sda=>$data);
        $data_array=array("data"=>$this->refurbish($data1),
							"title"=>translate("app_name"));
		if($sda=="message" || $sda=="notify"){
            $duty=$data["data"];
            $noti=$duty["notif_id"];
            $subj=$duty["subj"];
            $arr=array("subject"=>$subj);
            $txt=swap_word_array_and_ntbolden($arr, getNotifText($noti));
            $risk="OPEN_NOTIFY_1";
            if($sda=="message"){
                $risk="OPEN_MSG_1";
            }
            $notif_array=array("body"=>$txt,
    							"title"=>translate("app_name"),
                                 "click_action"=> "$risk");
    							
    		$data = array("to"=>$to, "notification"=>$notif_array, "data"=> $data_array);
		}else{
    		$data = array("to"=>$to,  "data"=> $data_array);
		}
		
    	$headers =$this->header_();
    	$rt=Httpful\Request::post($this->getApiHome())
			->method(Httpful\Http::POST)
			->withoutStrictSsl()
			->sendsJson() 
			->expectsJson()
			->addHeaders($headers)
			->body($data)
			->sendIt();
		$this->last_retrofit=$rt;
		return $rt;
    }

	function header_(){
		return array('Content-Type' => 'application/json',"Authorization"=>FIREBASE_API_KEY);
	}

    function getApiHome(){
    	return "https://fcm.googleapis.com/fcm/send";
    }

}

/*

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
?>