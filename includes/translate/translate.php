<?php 
	
  	$rel=dirname(__FILE__) . DIRECTORY_SEPARATOR;
	//
	include $rel.'translate_en.php';
	include $rel.'untrans.php';

	/*THIs is a very large translation  array  that contains all translations;
	should i use numbers? translate[1]? or translate["create"]? or a fn? tarncslate(create)? i am lost yet i like oyin...wtf??am i writing? today is 4/2/2017.. should i break up or makeup? i am sure noone will see this so, fuck that shit. this is Aro wolfgang micheal. i want to make an habit of writing shit before every codes.. i want to change the world, yeah right..like Galvin Fucking Belson??.... :)*/


	function translate($word){
		global $translate_en;		
		$string="$word";
		if(isset($translate_en[$word])){
			$string=$translate_en[$word];
		    $string=str_replace("{{br}}", "<br>", $string);
		    if(strpos($string, "{{app_name}}"))
		    $string=str_replace("{{app_name}}", translate('app_name'), $string);
		}
		$string=str_replace("_", " ", $string);
	    return $string;
	}

	function swap_word($key, $word, $string){		
	    $string=str_replace("{{".$key."}}", $word, $string);
	    return $string;
	}

	function swap_word_bold($key, $word, $string){		
	    $string=str_replace("{{".$key."}}", "<b>".$word."</b>", $string);
	    return $string;
	}

	function swap_word_ntbold($key, $word, $string){		
	    $string=str_replace("{{".$key."}}", "@".$word."", $string);
	    return $string;
	}

	function swap_word_array($word_array, $string){	
		foreach ($word_array as $key => $word) {
	    	$string=swap_word($key, $word, $string);
		}	
	    return $string;
	}

	function swap_word_array_and_bolden($word_array, $string){	
		foreach ($word_array as $key => $word) {
	    	$string=swap_word_bold($key, $word, $string);
		}	
	    return $string;
	}

	function swap_word_array_and_ntbolden($word_array, $string){	
		foreach ($word_array as $key => $word) {
	    	$string=swap_word_ntbold($key, $word, $string);
		}	
	    return $string;
	}

	function untranslate($word){
		global $untranslable;		
		$string="$word";
		if(empty($untranslable[$word])){
			return "";
		}
		return translate($untranslable[$word]);
	}

	function translate_var($word, $array){
		global $translate_en;
		return str_from_array(translate($word), $array);
	}

    function str_from_array($string, $array){
        foreach ($array as $key => $value) {
            $string=str_replace("{{".$key."}}", "$value", $string);
        }
        return $string;
    }




 ?>