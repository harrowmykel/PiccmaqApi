<?php
ob_start();
session_start();

	$root="../";
	include $root. "incl/index.php";
$items=1000;
define("sess", "micheal_is_");
$start=0;//isset($_SESSION[$sess])?$_SESSION[$sess]:

$query="SELECT * FROM pmessages_users";
$num=checknum($query);
$array_users=array();

$res=queryMysql($query);

echo getSql($query, "recip");
for ($i=0; $i<$num; $i++){ 
	$row=$res->fetch_array(MYSQLI_ASSOC);

	$recip=$row['recip'];
	$auth=$row['auth'];

	$query1="SELECT * FROM pmesages WHERE ((aut='$recip' AND reciv='$auth') OR (aut='$auth' AND reciv='$recip')) ORDER BY user_id DESC LIMIT 1";

	$query1="SELECT * FROM pmesages WHERE ((aut='$recip' AND reciv='$auth') OR (aut='$auth' AND reciv='$recip')) ORDER BY user_id ASC";
// 	$res1=queryMysql($query1);
// 	$row1=$res1->fetch_array(MYSQLI_ASSOC);
// 	$strt_time=$row1['time'];
	$coun=checknum($query1);
	$score= getConvoScore($coun);
	$im=getConvoScoreImage($score);
// 	echo $im;
	
// 	$q2="UPDATE pmessages_users SET crt_time=$strt_time, msg_count=$coun, score=$score, img='$im' WHERE (auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip')";
	
	$q2="UPDATE pmessages_users SET msg_count=$coun, score=$score, img='$im' WHERE ((auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip'))";
// 	$q2="UPDATE pmessages_users SET end_time=$strt_time WHERE (auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip')";
	queryMysql($q2);

	saveProg($i+$start);
}

function saveProg($pos){
	$_SESSION[sess]=$pos;
}
echo $i."/".$num;



$self=$_SERVER[PHP_SELF];
// header("Location: $self");
ob_flush();

?>