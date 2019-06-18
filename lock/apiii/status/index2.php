<?php
ob_start();
session_start();

	$root="../";
	include $root. "incl/index.php";


$items=10000;

$sess="micheal_is_cool_10_19_18a";
$start=isset($_SESSION[$sess])?$_SESSION[$sess]:0;

$query="SELECT * FROM pmesages LIMIT $items OFFSET $start";
$num=checknum($query);
$array_users=array();

echo $query;
$res=queryMysql($query);
for ($i=0; $i<$num; $i++){ 
	$row=$res->fetch_array(MYSQLI_ASSOC);

	$recip=$row['reciv'];
	$auth=$row['aut'];

	$ra=strtolower($recip.$auth);
	$ar=strtolower($auth.$recip);
	if(in_array($ra, $array_users) || in_array($ar, $array_users)){
		saveProg($i+$start);
		continue;//pmessages_users pmesages
	}

	$query1="SELECT * FROM pmessages_users WHERE (auth='$recip' AND recip='$auth') OR (auth='$auth' AND recip='$recip')";
	if(checknum($query1)>0){
		saveProg($i+$start);
		continue;
	}

	$query2="INSERT INTO pmessages_users (id, auth, recip, crt_time, end_time, msg_count, score) VALUES (NULL, '$auth', '$recip', 0, 0, 0, 0)";
	queryMysql($query2);

	$array_users[]=$ar;
	$array_users[]=$ra;
	saveProg($i+$start);
}

function saveProg($pos){
	$sess="micheal_is_cool_10_19_18a";
	$_SESSION[$sess]=$pos;
}
echo "<br>";
echo $i+$start."/".$num;
// $self=$_SERVER[PHP_SELF];
// header("Location: $self");
// ob_flush();
?>