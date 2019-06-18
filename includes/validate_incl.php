    <?php

    function validateSignUpUser($fullname, $pass, $user, $bio, $isGroup){
        
        $error=mRegExpMatchVal($fullname, $user);
        if(empty($error)){
            $error="";
            $num_user=checkNum("SELECT * FROM profiles WHERE user='$user'");
            if($num_user>0){
                return "inv_user";
            }else{
                if($isGroup){
                    $error=mValidateSignUpGroup($fullname, $user, $bio);  
                }else{
                    $error=mValidateSignUpUser($fullname, $pass, $user, $bio);
                }
            }
        }
        return $error;
    }

    function mValidateSignUpUser($fullname,$pass, $user, $bio){
        $error= ""; 
        $group_val=0;
        $time=CURRENT_TIME;
        $email="jdhdhhdhj";
        $dob=10;
        $mob=12;
        $yob=1997;
        $gender=MALE;
        $now="15/2/17";
        $rth=getDwm2($time);
        $admin_accts_dnfnfnnfnfjn=ADMIN_LIST;
        $bio=translate_var("initial_bio", array("$user"));
        
        $persy = translate_var("reg_now", array($fullname, $rth, translate('app_name'), INITIAL_PICCOIN, $now));
        queryMysql("INSERT INTO members (user, pass, join_date,email, last_seen, total_seen_time, has_dp, dp_human) VALUES ('$user', '$pass', $time,'$email', $time, 0, 0, 0)");

        $dffg="INSERT INTO profiles (user_id, user, fullname, dob, mob, yob, gender, phone, place, approved, school, country, work, dream, hobby, piccoins, bio, prof_pic, ext_link, is_group) VALUES (NULL, '$user', '$fullname',  '$dob', '$mob', $yob, '$gender', '', '', '', '', '', '', '', '', ".INITIAL_PICCOIN.", '$bio', '', 0, $group_val)";
        queryMysql($dffg);
        foreach ($admin_accts_dnfnfnnfnfjn as $key => $value) {
            queryMysql("INSERT INTO follow (id, send, reciv, app) VALUES(NULL, '$user', '$value', 0)");
            queryMysql("INSERT INTO friends VALUES(NULL, '$user', '$value', '2')");
        }
        queryMysql("INSERT INTO privacy VALUES(NULL, '$user', '1', '1', '1')");
        queryMysql("INSERT INTO pmesages (user_id, reciv, aut, time, text, del_recip,del_auth, confirm, pic) VALUES(NULL, '$user', 'PiccMaq', $time, '$persy',0, 1, 'w', 1)");

        if(AUTOGENERATE_DP){
            autogenerateDp($user);
        }else if (AUTOGENERATE_DP_FROM_FILE) {
            $img=autogenerateDpFromFile($user, false);
            queryMysql("UPDATE profiles set prof_pic='$img' WHERE user='$user'");
        }
        return $error;
    }

    function mValidateSignUpGroup($fullname, $bio, $user){

        $group_val=1;
        $error= ""; 
        $time=CURRENT_TIME;
        $email=$pass=gnrtNewString(1,10);
        $dob=10;
        $mob=12;
        $yob=1997;
        $gender=MALE;

        queryMysql("INSERT INTO members (user, pass, join_date,email, last_seen, total_seen_time, has_dp, dp_human) VALUES ('$user', '$pass', $time,'$email', $time, 0, 0, 0)");
        $dffg="INSERT INTO profiles (user_id, user, fullname, dob, mob, yob, gender, phone, place, approved, school, country, work, dream, hobby, piccoins, bio, prof_pic, ext_link, is_group) VALUES (NULL, '$user', '$fullname', '$dob', '$mob', $yob, '$gender', '', '', '', '', '', '', '', '', ".INITIAL_PICCOIN.", '$bio', '', 0, $group_val)";
        queryMysql($dffg);
        if(AUTOGENERATE_DP){
            autogenerateDp($user);
        }else if (AUTOGENERATE_DP_FROM_FILE) {
            $img=autogenerateDpFromFile($user, true);
            queryMysql("UPDATE profiles set prof_pic='$img' WHERE user='$user'");
        }
        return $error;
    }

    function mValidateEditProf($fullname,$email,$gender,$dob,$mob,$yob,$pass,$user,$bio,$dream,$work, $country,$school,$hobby,$phone,$place){
        return mValidateEditProfP($fullname,$email,$gender,$dob,$mob,$yob,$pass,$user,$bio,$phone,$place);
    }

    function mValidateEditProfP($fullname,$email,$gender,$dob,$mob,$yob,$pass,$user,$bio,$phone,$place){
        $username=$user;
        $error="";
        $error=(preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@[a-zA-Z\._\-]+\.[a-zA-Z\.]+/', $email)) ?"":translate('inv_email');
        if(!empty($error))return $error;
        $error=(preg_match('/^[a-zA-Z0-9-\._]+$/', $user))?"":translate('inv_user');
        if(!empty($error))return $error;
        $error=(!empty($dob) && !empty($mob) && !empty($yob))?"":translate('fill_dob');//check d.o.b
        if(!empty($error))return $error;
        $error=(is_numeric($dob) && is_numeric($mob) && is_numeric($yob))?"":translate('num_dob');//"input number as date of birth";
        if(!empty($error))return $error;
        $error=(preg_match('/^[2][0][0-1][0-9]$/', $yob) || preg_match('/^[1][9][5-9][0-9]$/', $yob))?"":translate('inv_yob');
        if(!empty($error))return $error;
        $job =  checkdate($mob,$dob,$yob) ? 'true' :'false';// checks if date is corrr
        if(!empty($error))return $error;
        $error=($job == 'true')?"":translate('inv_dob');
        if(!empty($error))return $error;
        /*$error=(!empty($pass) && !empty($user) && !empty($email))?"":translate('inv_fields');//check if all field s wer ented*/
        if(!empty($error))return $error;
        $error=($pass == $pass)?"":translate('pass_nt_equal');// check password
        if(!empty($error))return $error;
        $error=(isUsernameValid($username))?"":translate('inv_user');//next check for availa user
        if(!empty($error))return $error;
        $time=CURRENT_TIME;
        $now="15/2/17";
        if (empty($error)){
            $rt = checknum("SELECT * FROM members where user='$user'");
            if ($rt==1){ 
                queryMysql("UPDATE members SET  email = '$email' where user='$user'");
                queryMysql("UPDATE profiles SET hobby = '$hobby',  school = '$school', work = '$work', dream = '$dream', bio='$bio', phone = '$phone', country = '$country',place='$place', dob = '$dob', mob = '$mob', yob = '$yob', gender = '$gender', fullname='$fullname'  where  user='$user'");
                $dob_=$dob.'-'.$mob.'-'.$yob;
                $bday=deserialise($dob_);
                saveBDay($user, $bday);
                refreshProfileCache();
            }

        }		    
        $error= translate('prof_saved'); 

        return $error;
    }

    function mRegExpMatch($fullname,$email,$gender,$dob,$mob,$yob,$pass,$user){

        global $admin_accts_dnfnfnnfnfjn;
        $username=$user;
        $error="";

        if(!EASY_REGISTER){
            $error=(preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@[a-zA-Z\._\-]+\.[a-zA-Z\.]+/', $email) && !isEmailValid($email)) ?"":('inv_email');
            if(!empty($error))return $error;
        $error=(!empty($dob) && !empty($mob) && !empty($yob))?"":('fill_dob');//check d.o.b
        if(!empty($error))return $error;
        $error=(is_numeric($dob) && is_numeric($mob) && is_numeric($yob))?"":('num_dob');//"input number as date of birth";
        if(!empty($error))return $error;
        $error=(preg_match('/^[2][0][0-1][0-9]$/', $yob) || preg_match('/^[1][9][5-9][0-9]$/', $yob))?"":('inv_yob');
        if(!empty($error))return $error;
        $job =  checkdate($mob,$dob,$yob) ? 'true' :'false';// checks if date is corrr
        if(!empty($error))return $error;
        $error=($job == 'true')?"":('inv_dob');
        if(!empty($error))return $error;
        $error=(!empty($pass) && !empty($user) && !empty($email))?"":('inv_fields');//check if all field s wer ented
        if(!empty($error))return $error;
    }else{
        $email=gnrtNewString(1,10);
        $dob=10;
        $mob=12;
        $yob=1997;
        $gender=MALE;
    }
    $error=(preg_match('/^[a-zA-Z0-9-\._]+$/', $user))?"":('inv_user');
    if(!empty($error))return $error;
        $error=(!empty($pass) && !empty($user))?"":('inv_fields');//check if all field s wer ented


        if(!empty($error))return $error;
        $error=($pass == $pass)?"":('pass_nt_equal');// check password
        if(!empty($error))return $error;
/*
        if (strpos($username, translate('app_name'))){
            $error=('inv_user');
        }*/

        if(!empty($error))return $error;

        $error=(isUsernameValid($username))?('inv_user'):"";//next check for availa user
        if(!empty($error))return $error;


      /*  foreach ($admin_accts_dnfnfnnfnfjn as $key => $value) { 
            if (ucfirst($username)==ucfirst($value)){
                $error=('inv_user');
            }
        }*/

        return $error;
    }

    function mRegExpMatchVal($fullname, $user){

        global $admin_accts_dnfnfnnfnfjn;
        $username=$user;
        $error="";
        $error=(preg_match('/^[a-zA-Z0-9-\._]+$/', $user))?"":('inv_user');
        if(!empty($error))return $error;
        
        if (strpos($username, translate('app_name'))){
            $error=('inv_user');
        }
        if(!empty($error))return $error;
        
        
        foreach ($admin_accts_dnfnfnnfnfjn as $key => $value) { 
            if (ucfirst($username)==ucfirst($value)){
                $error=('inv_user');
            }
        }

        return $error;
    }
?>