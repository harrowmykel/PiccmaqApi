<?php

//id, short_code, language_, translation_
	function getNewTranslationQuery($lang){
		global $translate_en;
		foreach ($translate_en as $key => $value) {				
			$w="SELECT * FROM translation_tbl WHERE short_code='$key' AND language_='$lang'";
			$ert=checknum($w);
			if($ert>0 || $key=="app_name"){
				continue;
			}else{
				return array('short_code'=>$key,
							'english_trans'=>$value);
			}
		}
		return array('short_code'=>translate('no_mr_trans'),
					'english_trans'=>translate('no_mr_trans'));
	}


	function uploadTranstoDb($shortcode, $value, $lang){
		global $translate_en;	
		$w="SELECT * FROM translation_tbl WHERE short_code='$shortcode' AND language_='$lang'";
		$ert=checknum($w);
		$eng_trn=$translate_en[$shortcode];
		//count num of vars, do they match?
		$e=0;
		$orderis=SUCCESS;
		$cnt=count(explode("{{", $eng_trn));
		$cnt_2=count(explode("{{", $value));

		$cnto=count(explode("}}", $eng_trn));
		$cnto_2=count(explode("}}", $value));
		if($cnt>$cnt_2 || $cnto>$cnto_2){
			$orderis=INV_VAR_COUNT;
		}
		if($orderis==SUCCESS){
			$q="INSERT INTO translation_tbl (id, short_code, language_, translation_) VALUES (NULL, '$shortcode', '$lang', '$value')";
			if ($ert>0) {
				$q="UPDATE translation_tbl SET translation_='$value' WHERE short_code='$shortcode' AND language_='$lang'";
			}
			queryMysql($q);			
		}
		return $orderis;
	}

	function insertCorrected($shortcode, $value, $lang){
		$w="SELECT * FROM translation_cor WHERE short_code='$shortcode' AND language_='$lang'";
		$ert=checknum($w);		
		$q="INSERT INTO translation_cor (id, short_code, language_, translation_) VALUES (NULL, '$shortcode', '$lang', '$value')";
		if ($ert>0) {
			$q="UPDATE translation_cor SET language_='$lang' WHERE short_code='$shortcode' AND language_='$lang'";
		}
		queryMysql($q);
	}