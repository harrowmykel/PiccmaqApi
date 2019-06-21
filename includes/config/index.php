 <?php
// ['<']['\/ A-Za-z1-9 \:\-\;\{\} \.\n ']*[>]
	$rel____=(isset($rel____))?$rel____:"";
	define("REL_DIR", "$rel____");
	define('AF_IN_DEBUG', API_IN_DEBUG);
	define('UNDER_EDIT', 0);
	define('CHAT_IS_FRIENDS_ONLY', 0);
	define('EASY_REGISTER', 1);
	define('PAGEVIEW_STAT', true);
	define('SHOW_LEISURE', true);
	define("SHRINK_IF_SIZE_GREATER", true);
	define("MAX_IMAGE_B4_SHRINK_SIZE", 57);//in bytes
	define("DO_NOT_RESHRINK_IMAGE", false);
	define('AUTOGENERATE_DP', false);//cant fetch object form ext url
	define("AUTOGENERATE_DP_FROM_FILE", !AUTOGENERATE_DP);
	define("AUTO_GEN_DP_AT_START", true);
	define("SHOW_QUERY", 0);//all query will be shown
	define("NO_QUERY", 0);//no query will run
	define("SAVE_LOG", 0);//saves which file was ran //1=api only; 2=aprt frm api// 3=all 0=none
	define("SAVE_LOG_FILE", 0);//saves which file was ran //1=api only; 2=aprt frm api// 3=all 0=none
	define("SAVE_API_RESULT", 0);//false
	
	define('SCRAMBLE_KEY', 124);
	
	
	define("DEF_API_KEY_VALUE", "DEF_API_KEY_VALUE");
	define("AUTH_API_KEY_VALUE", array("123456789"));

	if(!defined('IS_API')){
		define('IS_API', false);
	}

	if (IS_API){
	  define('API_KEY', true);
	}else{
      define("API_KEY_VALUE", DEF_API_KEY_VALUE);
    }
	
	define('isApi', IS_API);	

	$COOKIE["jbhjjgfg"]="hjfhybh";

	if(UNDER_EDIT){
		if(!IS_API){
			echo "We are editing piccmaq to give you the best experience. please check back soon";
			exit();		
		}
	}


	$piccmaq_array=array(
			"dbhost"  => 'localhost',
			"dbname"  => 'piccmaqc_db',
			"dbuser"  => 'piccmaqc_user',
			"dbpass"  => 'A@ehjda2333hednde');
	$localhost_array=array(
			"dbhost"  => 'localhost',
			"dbname"  => 'piccmaqreal',
			"dbuser"  => 'phorum5',
			"dbpass"  => 'phorum5');
	
	if(AF_IN_DEBUG){

	/*Edit lock/apiii/incl/index.php too..if you change root_dir*/
		define('DEF_URL', 'http://localhost/GitProjects/PiccmaqApi22');
		define('DB_ARRAY', $localhost_array);	
		define('BASESES', $rel____);
		// define('ROOT_DIR', "/home/piccmaqc/public_html/");
		define('ROOT_DIR', str_replace("includes\config", "", dirname(__FILE__)));//"/storage/ssd3/664/2090664/public_html/");
	}else{
		// str_replace(search, replace, subject)
 ;
	/*Edit lock/apiii/incl/index.php too..if you change root_dir*/
		define('DEF_URL', 'http://piccmaq.com.ng');
		define('DB_ARRAY', $piccmaq_array);//$heliohost_array);
		define('ROOT_DIR', "/home/piccmaqc/public_html/");//"/storage/ssd3/664/2090664/public_html/");
		define('BASESES', ROOT_DIR);
	}
	
	
	$dbhost = DB_ARRAY["dbhost"];
	$dbname = DB_ARRAY["dbname"];
	$dbuser = DB_ARRAY["dbuser"];
	$dbpass = DB_ARRAY["dbpass"];

	define('APP_NAME', 'Piccmaq');
	$sampless=array("stacfjhsuhufrhufhuhusfhhugushyuguk.png", "abott.png");

	define("FIREBASE_STATUS_TOPIC_ENDING", "ending");
	define("FIREBASE_GROUP_TOPIC_ENDING", "endgroup");
	define("FIREBASE_IS_TOPIC", false);//for status sends stat notif as username +ending topic notifi
	define("SEND_TO_USER_TOPIC", true);//notification will be sent to user as a topic instead of as a user
    define("APP_FIREBASE_TOPIC", "nefskdnkgldfndkfsnkvdndknfkdndfndld");
    define("USER_FIREBASE_TOPIC", "asdfghj");
    define("FIREBASE_API_KEY1", "key=AAAAQA51SO0:APA91bGHxl5YjGWetEmbTaXdyMXo_u-6XxZD3KNBC2jAWpKZxhF-FbGy7I00Ik4TZ0D0g0lOfucIEsaqimQ7AylRO4rAL2ZADd6142tbVZK9v9MpbQ_AwsC4NhkiSjyFZClBkjbFttFQ");
    define("FIREBASE_API_KEY", "key=AAAAGZvHGtM:APA91bHxXhlDS5zrp4Ryxp75bNc6EJwsSjOIxco2f-e1uLQp2dCUTG5xY7MdIJp71Wx2fd9QhQOo6NhhsZHCNUQDObdI-XRZCmETt2g_eXxt0vMW37FvNAm2D9MYiWE-_x_8sMJS2fcs");
    
	define('DEF_PIC', $sampless[array_rand($sampless)]);	
	define('SAMPLE_DP', DEF_URL."/web_files/avatar/".DEF_PIC);
	define('SCRAMBLE_DP', DEF_URL."/web_files/avatar/dps/0C60ZAqlPF0Eod67.png");
	define('USER_DP_STORE_', "/web_files/avatar/dps/");
    define("PC_ERROR_PAGE", REL_DIR."notfound.php?asdf=dsd&");
    define("SIGN_UP_PAGE", REL_DIR."index.php?kbj&");
	define('USER_DP_STORE', DEF_URL.USER_DP_STORE_);
	define('USER_DP', DEF_URL."/samples/homea_files/17796513_1293848504037112_5627968627927812219_n.jpg");
	define("MALE_DP", SAMPLE_DP);
	define("FEMALE_DP", SAMPLE_DP);
	
    define('IMG_CLOUD_LINK', DEF_URL);
    
    define('API_CONST', '&api_key=@fgfgxfbf67667@_gjvg');
    

	define('IMGSTORE',DEF_URL."/media/profiles/prof_pic/");
	define("API_BASE_URL", DEF_URL."/lock/apiii/");
	define('IMGSTORE__',"/media/profiles/prof_pic/");
	define('IMGSTORE___',"media/profiles/prof_pic/");
	define('DP_STORE', "/media/profiles/prof_pic/");
	define('COVER_STORE', "/media/profiles/cover_pic/");
	define('IMG_STORE', "media/posts/media/");
	define('MESS_IMG_STORE', '/media/messages/media/');	
	define('COMMENT_STORE', "/media/comment/media/");
	define('MEDSTORE',DEF_URL."/media/pictures/");
	define('SMILIE_DIR', '/web_files/smilies/');
	define('ALIEN_IMG', '/web_files/aliens/alien_name.jpg');
	define('AVATAR_IMG', '/web_files/avatar/abott.png');
	define('VERIFIED_IMG', '/web_files/verified.png');
	define("DUMPYARD_", 'dump_yard/');
	define('DUMPYARD', DUMPYARD_.time()."/");	
	define("DUMP_DIR", ROOT_DIR."dump/");
	
	define('APP_SITE', DEF_URL);

	define('USER_CAN_ADD_USER', true);
		
	define('ONLINE',2213834);
	define('OFFLINE',3933995);
	define('MOBILE',223313834);
	define('DESKTOP',393309390995);


	define('NO_USER',"3933rklrjlrlrn09390995");
	define('INV_OP',"39330939rlkrnnfrn0995");
	define('SUCCESS',"nfrn0995");
	define('INV_VAR_COUNT', 'dkjjkg');
	define('NO_PICCOIN', 'djkhfkjstjtghj');	
	define('APP_ID_', 'dkhdhfrreqhrulaluugr');
	define('error_title', 'error');
	define('API_USERKEY', "apiuser");
	define('API_RECIPKEY', 'user_recip_by_api');
    
	define('API_CONTACTS', "1234");
	define('API_REQUEST', '6789');
	define('API_ALL', "3456");
	
	define('API_BIRTHDAY', 'birthday');
	define('API_NEW_MEMBERS', 'members');
	define('API_ONLINE', "online");
	define("API_FRIENDS", API_CONTACTS);
	define("API_SUGGESTIONS", "5678");

	define("WELCOME_BONUS", 200);
	define("TRANSLATE_BONUS", 300);
	define("INITIAL_PICCOIN", 10000);
	define('BDAY_BONUS', 100);
	define('SCRAMBLE_POINT', 20);
	define('SCRAMBLE_LOSS', 30);
	define('RESEMBLANCE_COST', 1000);
	define('TWIN_COST', 500);	
	define('RESEMBLANCE_SPEED_COST', 800);
	define('ALIEN_COST', 700);
	
  define("REFERRAL_COOKIE_NAME", "piccmaq_referer_name");
  define("REFERRAL_VAR", "snd");
  define("REFERRAL_USED", "piccmaq_referer_used");
  define("REFERRAL_THIEF", "referrer_piccmaq");
  define("REFERRAL_PAGE_USED", 1);
  define("REFERRAL_PAGE_SIGNUP", 0);
  define("REFERRAL_PAGE_VIEW", 3);
  
	define('FEMALE',22334);
	define('MALE',393345);

	define('USERNAME',"393309390sdjkfkfkknk995");
	define('PASSWORD',"393309dknndnkfkfkknk995");
	define('USER_PROFILE_CACHE', 'kjrjflhfjkffrkjfdkjr');
	define('USER_PROFILE_CACHE_SAVED', 'fkjtskhrhknldkdlroihofj');
	define('SCRAMBLE_NAME', 'jkreqkjrujrjewbjkbekj');
	define('USER_SCRAMBLE', 'Scramble_ADMIN');
	define('USERS_SUBMISSIONS', 'users_already_scrambled');
	define('PREF_', 'dkdkjdkjdkj_');
	define('ALIEN_GAME_CACHE', 'ruiauhrfiuhndifobfyuadu');
	define('TWIN_GAME_CACHE', 'edkbjhfkjarjneeuh4uighru');	
	define('LAST_ONLINE_TIME', 'ufgwhroqheuorhuoh38uhuehuhur');
	define('TOTAL_VIEWS', 'total_page_views');
	define('PREF_ONLINE', 'online_on_');
	define('ONLINE__TODAY', 'kfnsjkhjtkkjgthgth');
	define('TIME_OF_REFRESH', 'kfnsjkdkndwklndlkhjtkkjgthgth');
	
	define('LOVE',"2636hb363");
	define('LIKE',"393k99fd5");
	define('SAD',"393k995fdsf");
	define('WOW',"393k995fafk");
	define('ANGRY',"393krkjhj995");
	define('HAHA',"393k9rgrr95");
	define('NONE',"393in95");
	
	define("APP_SIZE", "6mb");
	
	
	define('message_sent',"w");
	define('message_read',"r");
	define('message_rcvd',"rcvd");

	define("CENSOR_WORDS", true);
	define('SHOW_ICONS', true);
	define('SHOW_ICON_DESC', true);
	define('RECENT_POST_IS_RAND', true);
	define('TWIN_MUST_HAVE_UPLOADED_PIC', false);

	define('icons_array', $icons_array);
	
	define('CHECK_HUMAN_DP', false);
	define('APP_REPORT_ACCT', "piccmaq_report");
	define("API_ADMIN_USERNAME", "piccmaq_api");
	define('APP_ANONYMOUS', 'piccmaq_anonym');

	if(IS_API){
		define('NO_OF_SEARCHES',14);
		define('MAX_MESSAGES', 14);	
	}else{
		define('NO_OF_SEARCHES',4);
		define('MAX_MESSAGES', 5);	
	}

	define('NO_OF_MSGS',5);
	define('VARR',8);
	define('NO_OF_HOME_RESULTS', 8);
      define("NO_OF_PAGES", 8);	
	define('MAX_MSG_PRVW_LEN', 243);
	define("MAX_LEN_OF_POSTS_ON_HOME", 200);
	define('MAX_SRCH_MSG_PRVW_LEN', 333);	
	define('COMMENTS_NO',4);
	define('CURRENT_PAGE', getCurrentPage());
	define('privacy_friends', 23939939);
	define('privacy_public', 3931939);
	define('privacy_private', 329393333);
	define('DEF_DP_PRIVACY', privacy_public);
	
	define('ONLINE_TIMEOUT', 600);//10mins
	define('JUST_NOW', 40);//1min
	define('DUMP_CLEAR_SECONDS', 70);//1min
	define('COOKIE_TIMEOUT', 3600*24*10*10);	//cookie
	define('DIFF_TIME', 300);
	define('TIME_TILL_SCRAMBLE_CHANGE', 720);//each 12mins	
	define('CURRENT_TIME', time());	
	define('WAITING_PERIOD_FOR_RESEMBLANCE', 3600*24*10);
	define("OLD_POST_AGO", CURRENT_TIME-(60 * 60 * 24 *30));
	define("ONE_DAY__", 24*3600);
	define('REFRESH_TIME', 60*30);
	

	define('CAN_UNFRND_ADMIN',false);	
	$admin_accts_dnfnfnnfnfjn=array("piccmaq", APP_REPORT_ACCT, 'harrowmykel', 'afreechat', USER_SCRAMBLE, APP_ANONYMOUS, API_ADMIN_USERNAME);
	define('ADMIN_USERNAME', $admin_accts_dnfnfnnfnfjn[0]);

	//smilies
	$icons_array=[];
	$replace_smilie_array_=icons_array;
	//put smaller down here arrange according to size
	$replace_string_array_=array("admin:fullname"=>"Aro Micheal Oluwaseun",
								"admin:country"=>"Nigeria",
								"admin:location"=>"Germany",
								"admin:nickname"=>"harrowmykel",
								"admin:name"=>"Aro Micheal Oluwaseun",
								"name"=>APP_NAME,
								"url"=>DEF_URL,
								"time"=>time(),
								"admin"=>"@piccmaq");

	function getAdmin_accts_dnfnfnnfnfjn(){
		global $admin_accts_dnfnfnnfnfjn;
		return $admin_accts_dnfnfnnfnfjn;
	}
	
	define("ADMIN_LIST", $admin_accts_dnfnfnnfnfjn);
	
	function logServerVar(){
	    fwrite(fopen(DUMP_DIR."posts.txt", "a+"), json_encode($_POST));
	    fwrite(fopen(DUMP_DIR."gets.txt", "a+"), json_encode($_GET));
	   // fwrite(fopen("posts.txt", "a+"), json_encode($_POST));
	}
	
	function logServerFile(){
	    fwrite(fopen(DUMP_DIR."apifile.txt", "a+"), $_SERVER["PHP_SELF"]);
	}
	
	if(SAVE_LOG!=0){
	    $saveLogFile=false;
    	if(SAVE_LOG==1 && IS_API){
    	    $saveLogFile=true;
    	}else if(SAVE_LOG==2 && !IS_API){
    	    $saveLogFile=true;
    	}else if(SAVE_LOG==3){
    	    $saveLogFile=true;
    	}
    	if($saveLogFile){
    	    logServerVar();
    	}
	}
	
	if(SAVE_LOG_FILE!=0){
	    $saveLogFile=false;
    	if(SAVE_LOG_FILE==1 && IS_API){
    	    $saveLogFile=true;
    	}else if(SAVE_LOG_FILE==2 && !IS_API){
    	    $saveLogFile=true;
    	}else if(SAVE_LOG_FILE==3){
    	    $saveLogFile=true;
    	}
    	if($saveLogFile){
    	    logServerFile();
    	}
	}
	
	$DP_USER_ARRAY=array("a"=>"00L3NlIYMQ0Mji.png",
                        "b"=>"0AKLHx8704OYV7Rtl.png",
                        "c"=>"0B4n566Fq90oS6e9.png",
                        "d"=>"0C60ZAqlPF0Eod67.png",
                        "e"=>"0CX62hXcW6492n6Gs.png",
                        "f"=>"0CwKDzU20U.png",
                        "g"=>"0DnFhe711nfK.png",
                        "h"=>"0E5iLNdVHXe7YYDt.png",
                        "i"=>"0GFKaw9OCrMCBWhJu.png",
                        "j"=>"0Gdxk4cyzJWDWfGVu.png",
                        "k"=>"0HiRx9Abjn.png",
                        "l"=>"0IwY3v1tPo.png",
                        "m"=>"0JovFwxm8.png",
                        "n"=>"0K9Q4PgXr1ra4.png",
                        "o"=>"0KLO32UrsxC36aY4Wf.png",
                        "p"=>"0LHlXdigZW0u31v9T.png",
                        "q"=>"0N17108OjuyJ7T.png",
                        "r"=>"0N1zB6L3kyTu.png",
                        "s"=>"0N2yKuMcKGP9RQ.png",
                        "t"=>"0bO37CJpNL7UJs1h.png",
                        "u"=>"0cwb527zCa4xmq.png",
                        "v"=>"0d87v9o46Rm.png",
                        "w"=>"0d9SB6F2q4GF44l1.png",
                        "x"=>"0dTgK1E9CEu.png",
                        "y"=>"0eC73K08KtY.png",
                        "z"=>"0fdd8v7d2m.png",
                        "0"=>"0g6rd6usfA4076t3L.png",
                        "1"=>"0hWM2oC17jZO49LR.png",
                        "2"=>"0i5aC4Cok.png",
                        "3"=>"0iED2ZU6bx3w.png",
                        "4"=>"0in7B0C6qi1.png",
                        "5"=>"0io8DQp55U.png",
                        "6"=>"0kcL8tmI7.png",
                        "7"=>"0m2j536GrOb4S.png",
                        "8"=>"0mH7y6NkfTgNA0Bv6PZ.png",
                        "9"=>"0naMnb103H9zheW9gY.png");
                        
    define("DP_USER_ARRAY_", $DP_USER_ARRAY);


	?>