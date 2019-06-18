<?php

class Maq{
	// url, datatype==json:text, baseurl, data, 
	//DEPRECATED type,
	private $mUrl;
	private $mDatatype="json";
	private $mData;
	private $mType;
	private $mBaseurl;
	private $mError;
	private $mErrorCode;
	private $mJsonResult;
	private $mSuccCallback;
	private $mErrCallback;
	private $mlastRetrofit=array();

	public function post($jsonData){
		$this->parseParam($jsonData);
		$data=$this->getMData();

		if($this->isJson()){
	    	$rt=Httpful\Request::post($this->getApiHome())
				->method(Httpful\Http::POST)
				->withoutStrictSsl()
				->sendsType(Httpful\Mime::FORM)
				->expectsJson()
				->body($data)
				->sendIt();
		}else{
	    	$rt=Httpful\Request::post($this->getApiHome())
				->method(Httpful\Http::POST)
				->withoutStrictSsl()
				->sendsType(Httpful\Mime::FORM)
				->body($data)
				->sendIt();
		}
		$this->setMlastRetrofit($rt);
		return $rt;
    }

	public function get($jsonData){
		$this->parseParam($jsonData);
		$data=$this->getMData();

		if($this->isJson()){
	    	$rt=Httpful\Request::get($this->getApiHome())
				->withoutStrictSsl()
				->expectsJson()
				->body($data)
				->sendIt();
		}else{
	    	$rt=Httpful\Request::get($this->getApiHome())
				->withoutStrictSsl()
				->body($data)
				->sendIt();
		}

		$this->setMlastRetrofit($rt);
		return $rt;
    }

    public function isJson(){
    	if($this->getMDatatype()=="json"){
    		return true;
    	}
    	return false;
    }

    public function getApiHome(){
    	return $this->getMBaseurl().$this->getMUrl();
    }

	public function parseParam($jsonData){
		if(!is_array($jsonData)){
			$array = json_decode($jsonData);
		}else{
			$array=$jsonData;
		}
		if(!is_null($array)){
			foreach ($array as $key => $value) {
				$key=strtolower(trim($key));
				$value=strtolower(trim($value));
				/*if($key=="type"){
					$this->setMType($value);
				}else */
				if($key=="url"){
					$this->setMUrl($value);
				}else if($key=="datatype"){
					$this->setMDatatype($value);
				}else if($key=="baseurl"){
					$this->setMBaseurl($value);
				}else if($key=="data"){
					$this->setMData($value);
				}
			}
		}
	}

	public function setMlastRetrofit($mlastRetrofit) { $this->mlastRetrofit = $mlastRetrofit; }
	public function getMlastRetrofit() { return $this->mlastRetrofit;}
	public function getHttpJunk() { return $this->getMlastRetrofit();}
	public function getHttpResponse() { return $this->getMlastRetrofit()->body;}
	public function setMUrl($mUrl) { $this->mUrl = $mUrl; }
	public function getMUrl() { return $this->mUrl; }
	public function setMDatatype($mDatatype) { $this->mDatatype = $mDatatype; }
	public function getMDatatype() { return $this->mDatatype; }
	public function setMData($mData) { $this->mData = $mData; }
	public function getMData() { return $this->mData; }
	public function setMType($mType) { $this->mType = $mType; }
	public function getMType() { return $this->mType; }
	public function setMBaseurl($mBaseurl) { $this->mBaseurl = $mBaseurl; }
	public function getMBaseurl() { return $this->mBaseurl; }
	public function setMError($mError) { $this->mError = $mError; }
	public function getMError() { return $this->mError; }
	public function setMErrorCode($mErrorCode) { $this->mErrorCode = $mErrorCode; }
	public function getMErrorCode() { return $this->mErrorCode; }
	public function setMJsonResult($mJsonResult) { $this->mJsonResult = $mJsonResult; }
	public function getMJsonResult() { return $this->mJsonResult; }
	public function setMSuccCallback($mSuccCallback) { $this->mSuccCallback = $mSuccCallback; }
	public function getMSuccCallback() { return $this->mSuccCallback; }
	public function setMErrCallback($mErrCallback) { $this->mErrCallback = $mErrCallback; }
	public function getMErrCallback() { return $this->mErrCallback; }

}
?>
