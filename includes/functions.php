<?php
 $dbhost  = 'localhost';    // Unlikely to require changing 
  $dbname  = 'piccmaq';   // Modify these...
  $dbuser  = 'root';   // ...variables according
  $dbpass  = '';   // ...to your installation
  $appname = "Robin's Nest"; // ...and preference
  $imgsu="mobile/dep/images";
$messu="mobile/dep/mess";
$bgsu="mobile/dep/bgs";
$homeu="mobile/dep/home";
$hret="";
$hrety="../";
  

//$dbhost  = 'mysql9.000webhost.com';    // Unlikely to require changing 
//  $dbname  = 'a7971163_piccmaq';   // Modify these...
//  $dbuser  = 'a7971163_micheal';   // ...variables according
//  $dbpass  = 'w190GajB3n';   // ...to your installation
//  $appname = "Robin's Nest"; // ...and preference
//$hrt="home/a7971163/public_html/web/mobile";
//  $imgsu=$imgu=$imgu="$hrt/dep/images";
//$messu="$hrt/mess";
//$bgsu="$hrt/bgs";
//$homeu="$hrt/home";


//$dbhost  = 'www.piccmaq.com.ng';    // Unlikely to require changing 
//  $dbname  = 'piccmaqc_piccmaq';   // Modify these...
//  $dbuser  = 'piccmaqc_micheal';   // ...variables according
//  $dbpass  = '08036660086';   // ...to your installation
//  $appname = "Robin's Nest"; // ...and preference
//$hret="home/pimmaqc/public_html/
//$hrety="$hret/web";
//$hrt="home/pimmaqc/public_html/web/mobile";
//$imgsu="$hrt/dep/images";
//$messu="$hrt/mess";
//$bgsu="$hrt/bgs";

  $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($connection->connect_error) die($connection->connect_error);
   session_start();

  function createTable($name, $query)
    {   global $imgsu; global $messu; global $bgsu;
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
  }

  function queryMysql($query)
  {
    global $connection;
    $result = $connection->query($query);
    if (!$result) die($connection->error);
    return $result;
  }

  function destroySession()
  {
    $_SESSION=array();
    session_destroy();
   
    
    if (isset($_COOKIE['userpiccmaq']) 
    || isset($_COOKIE['passpiccmaq']))
      setcookie('userpiccmaq', '', time()-2592000, '/');

  
      // Delete the user ID and username cookies by setting their expirations to an hour ago (3600)
  setcookie('userpiccmaq', '', time() - 545450);
  setcookie('passpiccmaq', '', time() - 35676676);
  
  }

  function sanitizeString($var)
  {
    global $connection;
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return $connection->real_escape_string($var);
  }

 function checknum($query){
    $resy=queryMysql("$query");
     $num= $resy-> num_rows;
       echo "$num";}
      
 function showProfile($user)
    {   global $imgsu; global $messu; global $bgsu;
   if (file_exists("$imgsu/$user.jpg"))
      echo "<a href='#'><img class='img-responsive thumbnail' src='$imgsu/$user.jpg' width='500' height='500'/></a>";
      else{
        $result = queryMysql("SELECT * FROM members WHERE user='$user'");
  $num    = $result->num_rows; 
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
  
    if ($peac == 'male')
        echo "<a href='#'><img class='img-circle thumbnail img-responsive' src='$imgsu/male.jpg' width='150' height='150'/></a>";
     else
    
      echo "<a href='#'><img class='img-circle thumbnail img-responsive' src='$imgsu/female.jpg' width='150' height='150'/></a>";
   }}


function showPr($view)
    {   global $imgsu; global $messu; global $bgsu;if (file_exists("$imgsu/$view.jpg"))
      echo "<img class='img-circle img-responsive' src='$imgsu/$view.jpg' width='150' height='150'/><br>"; //refined
      else
       {
      echo "<img class='img-circle img-responsive' src='$imgsu/default.jpg' width='150' height='150'/>";
   }}



function show($live)
    {   global $imgsu; global $messu; global $bgsu; $result2 = queryMysql("SELECT * FROM members WHERE user='$live'");
  $num    = $result2->num_rows; 
  $row = $result2->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
    if (file_exists("$imgsu/$live.jpg"))
      echo "<br><img class='featurette-image img-responsive center-block' src='$imgsu/$live.jpg' style='float:left;'>";
      else{
    if ($peac == 'male')
        echo "<img class='img-circle featurette-image img-responsive center-block' src='$imgsu/default.jpg' width='100' height='100' class='img-responsive'>";
     else
      echo "<img class='img-circle featurette-image img-responsive center-block' src='$imgsu/default.jpg' width='100' height='100' class='img-responsive'/>";
   }
      $result = queryMysql("SELECT * FROM profiles WHERE user='$live'");
   $result2 = queryMysql("SELECT * FROM members WHERE user='$live'");
     if ($result->num_rows)
    {
      $row = $result2->fetch_array(MYSQLI_ASSOC);
       echo "\t  ";
       echo ucfirst(stripslashes($row['first_name'])) . "\t <style='clear:left;'>";
       echo  "<br>" . ucfirst(stripslashes($row['country'])) . "<style='clear:left;'>";
      
    } 

}

function showimg($user)
    {   global $imgsu; global $messu; global $bgsu;  $result = queryMysql("SELECT * FROM members WHERE user='$user'");
  $num    = $result->num_rows; 
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
    if (file_exists("$imgsu/$user.jpg"))
      echo "<br><img  class='img-circle' src='$imgsu/$user.jpg' style='width: 50px; 
                  height: 50px;'>";
      else{
    if ($peac == 'male')
        echo "<img class='img-circle' src='$imgsu/male.jpg' width='50' height='50' class='img-responsive'>";
     else
      echo "<img class='img-circle' src='$imgsu/female.jpg' width='50' height='50' class='img-responsive'>";
   }}
                  
function sho()
    {   global $imgsu; global $messu; global $bgsu;$search = sanitizeString($_POST['search']);

  $result = queryMysql("SELECT * FROM members WHERE user='$search' OR first_name='$search'");
  $num    = $result->num_rows; 
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
    if (file_exists("$imgsu/" . $row['user'] . ".jpg"))
      echo "<br><img  class='img-circle' src='$imgsu/" . $row['user'] . "jpg' style='width: 25px; 
                  height: 40px;'>";
      else{
    if ($peac == 'male')
        echo "<img class='img-circle' src='$imgsu/male.jpg' width='40' height='40' class='img-responsive'>";
     else
      echo "<img class='img-circle' src='$imgsu/female.jpg' width='40' height='40' class='img-responsive'>";
    
        echo "\t $onlin";}}
                  
                  

function showim($peace)
    {   global $imgsu; global $messu; global $bgsu;
    if (file_exists("$imgsu/$peace.jpg")) //refined
      echo "<img class='img-circle thumbnail' src='$imgsu/$peace.jpg' width='100' height='100' class='img-responsive'/>";
     else      echo "<img class='img-circle thumbnail' src='$imgsu/default.jpg' width='100' height='100' class='img-responsive'/>";
      }
   
  function showProfil($friend)
    {   global $imgsu; global $messu; global $bgsu;
    if (file_exists("$imgsu/$friend.jpg"))
      echo "<img class='img-circle' src='$imgsu/$friend.jpg' width='60' height='60' class='img-responsive'>";
      else{
          $result2 = queryMysql("SELECT * FROM members WHERE user='$friend'");
     $row = $result2->fetch_array(MYSQLI_ASSOC);
    $gen = $row['gender'];
    if ($gen == 'male')
        echo "<img class='img-circle' src='$imgsu/male.jpg' width='60' height='60' class='img-responsive'>";
     else
      echo "<img class='img-circle' src='$imgsu/female.jpg' width='60' height='60' class='img-responsive'>";
   } 
   
   $result = queryMysql("SELECT * FROM profiles WHERE user='$friend'");
   $result2 = queryMysql("SELECT * FROM members WHERE user='$friend'");
     if ($result->num_rows)
    {
      $row = $result2->fetch_array(MYSQLI_ASSOC);
       echo "<br>"; 
         if ($row['approved'] == '2'){
               $app = "<span class='glyphicon glyphicon-ok-circle'>approved</span>";}
               else $app= "";
            
       echo "</div>\t \t Name = \t <style='clear:left;'>";
       echo ucfirst(stripslashes($row['first_name'])) . "\t \t\t <style='clear:left;'>";
       echo ucfirst(stripslashes($row['last_name']));
       echo " <br>\n <style='clear:left;'>";
        
      echo "\t \t City = \n <style='clear:left;'>";
       echo stripslashes($row['city']) ;
      
    } 
  }
   
   function msg($auth)  {   global $imgsu; global $messu; global $bgsu;
      if (file_exists("$imgsu/$auth.jpg"))
      echo "<a href='#'><img class='img-responsive thumbnail' src='$imgsu/$auth.jpg' width='50' height='50'/></a>";
      else{
        $result = queryMysql("SELECT * FROM members WHERE user='$auth'");
  $num    = $result->num_rows; 
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
  
    if ($peac == 'male')
        echo "<a href='#'><img class='img-circle thumbnail img-responsive' src='$imgsu/male.jpg' width='50' height='50'/></a>";
     else
    
      echo "<a href='#'><img class='img-circle thumbnail img-responsive' src='$imgsu/female.jpg' width='50' height='50'/></a>";
   } }
  
  
 function showPro($view)
    {   global $imgsu; global $messu; global $bgsu;
   if (file_exists("$imgsu/$view.jpg"))
      echo "<img class='center-block' src='$imgsu/$view.jpg' width='150' height='150'>";
      else{
          $result2 = queryMysql("SELECT * FROM members WHERE user='$view'");
     $row = $result2->fetch_array(MYSQLI_ASSOC);
    $gen = $row['gender'];
    if ($gen == 'male')
        echo "<img class='img-circle' src='$imgsu/male.jpg' width='60' height='60' class='img-responsive'>";
     else
      echo "<img class='img-circle' src='$imgsu/female.jpg' width='60' height='60' class='img-responsive'>";
   }   }
   
    function showmsge($user)  {   global $imgsu; global $messu; global $bgsu;
$che=queryMysql("SELECT * FROM pmesages WHERE reciv='$user' AND confirm ='w'");
$total = $che->num_rows;
if ($total > 0){
echo "($total)";}
else echo "";
}
function showfrnds($user)  {   global $imgsu; global $messu; global $bgsu;
$che=queryMysql("SELECT * FROM friends WHERE recip='$user' AND confirm ='1'");
$total = $che->num_rows;
if ($total > 0){
echo "($total)";}
else echo "";
}

  
   function showPic($view)
    {   global $imgsu; global $messu; global $bgsu;
   if (file_exists("$imgsu/$view.jpg"))
      echo "<a href='#'><img class='thumbnail'  src='$imgsu/$view.jpg' width=100 height=120/></a> ";
       else
      echo "<a href='#'><img class='thumbnail' src='$imgsu/default.jpg' width=100 height=120/></a>";
        
  }
     function showPice($view)
    {   global $imgsu; global $messu; global $bgsu;
   if (file_exists("$imgsu/$view.jpg"))
      echo "<a href='#'><img class='thumbnail'  src='$imgsu/$view.jpg' width=100 height=200/></a> ";
       else
      echo "<a href='#'><img class='thumbnail' src='$imgsu/default.jpg' width=100 height=200/></a>";
        
  }
   function showtxt($id, $view)  {   global $imgsu; global $messu; global $bgsu;
      $rew=queryMysql("Select * from messages where id='$id'");
      $num=$rew->num_rows;
      $rowe=$rew->fetch_array(MYSQLI_ASSOC);
      $ure=$rowe['picture'];
      
      
      if (file_exists("$messu/$ure.jpg"))
      {
      echo "<a href='members.php?view=$view&pro=23'><img class='img-responsive thumbnail' src='$messu/$ure.jpg' width='0' height='0'/></a>";
  
      }//if exists
      }
      
      
      
  function showPich($view)
    {   global $imgsu; global $messu; global $bgsu;
   if (file_exists("$imgsu/$view.jpg"))
      echo "<a href='profile.php'><img class='img-circle thumbnail'  src='$imgsu/$view.jpg' width=100 height=100/></a> ";
       else
      echo "<a href='profile.php'><img class='img-circle thumbnail' src='$imgsu/default.jpg' height=200/></a>";
        
  }
 

    
    function shoppi($view)
      {   global $imgsu; global $messu; global $bgsu;      
      if (isset($_GET['view'])){
        $view=$_GET['view'];}
        else
    $view= $_POST['view'];
    $resu = querymysql("SELECT * FROM members WHERE user='$view'");
    $timo =$resu->fetch_array(MYSQLI_ASSOC);
 
    
      $approve=$timo['approved'];
    if($approve==2)
            $app="[<span class='glyphicon glyphicon-ok-circle'></span>]";
            else
            $app="";
      
    $resut = querymysql("SELECT * FROM profiles WHERE user='$view'");
    $timot =$resu->fetch_array(MYSQLI_ASSOC);
    
    $online= $timot['online'];
        if ($online == '1')
    $onlin = "<span class='glyphicon glyphicon-signal'>online</span>";
           else
        if ($online != '1')
    $onlin = '<span class="glyphicon glyphicon-record">offline</span>';
    
    echo "<table class='table table-striped'>";
    echo "<tr><th>Name = \t ";
       echo ucfirst(stripslashes($timo['first_name'])) . "\t";
       echo ucfirst(stripslashes($timo['last_name'])) . "</th></tr>";
        echo "<tr><th> Username = \t ";
       echo ucfirst(stripslashes($timo['user'])) . "$app</th></tr>";
       echo "<tr><th>  ";
       echo "$onlin </th></tr>"; //$onli
        $the = $timo['time_out'];
       if ($timo['time_out'] != '2'){
       echo "<tr><th>Last seen @ \t $the";
       echo "\t</th></tr>";}
       echo "<tr><th> Gender : " . ucfirst(stripslashes($timo['gender'])) . " </th></tr> ";
        echo "<tr><th> City: " .               
                  ucfirst(stripslashes($timo['city'])) . ""; //onlyn if friends
      echo "</th></tr><tr><th>\t \t State = ";
       echo stripslashes($timo['state']) . ",\t"; //onlyn if friends
       echo  stripslashes(ucfirst($timo['country'])) . "<br>";//onlyn if friends
       echo " </th></tr><tr><th>Star Level = \n ";
        echo  " 100, 100%</th></tr>";
         echo "<tr><th>\t \t Date of Birth = ";
       echo "<br>";//onlyn if friends
       echo "</th></tr><tr><th> ";?>
       
       <form method='POST' action='about_user.php'>
     <input type='hidden' name='view' value='<?php echo "$view";?>' />
    <input class='btn btn-sm btn-primary'  type='submit' value='More' /> </form>   <?php
      echo "</th></tr></table>";
    
        }
 
  function showPre($user)
    {   global $imgsu; global $messu; global $bgsu;
   if (file_exists("$imgsu/$user.jpg"))
      echo "<a href='profile.php'><img class='img-responsive thumbnail' src='$imgsu/$user.jpg' width='100' height='100'/></a>";
      else{
        $result = queryMysql("SELECT * FROM members WHERE user='$user'");
  $num    = $result->num_rows; 
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
  
    if ($peac == 'male')
        echo "<a href='profile.php'><img class='img-circle thumbnail img-responsive' src='$imgsu/male.jpg' width='100' height='100'/></a>";
     else
    
      echo "<a href='profile.php'><img class='img-circle thumbnail img-responsive' src='$imgsu/female.jpg' width='100' height='100'/></a>";
   }}
   
   
   
   
   
   
   //codes for messenger begins here
   
   function showPrp($user)
    {   global $imgsu; global $messu; global $bgsu; $self= $_SERVER['PHP_SELF'];
   if (file_exists("$imgsu/$user.jpg"))
      echo "<a href='profile.php'><img class='img-responsive thumbnail' src='$imgsu/$user.jpg' width='100' height='100'/></a>";
      else{
        $result = queryMysql("SELECT * FROM members WHERE user='$user'");
  $num    = $result->num_rows; 
  $row = $result->fetch_array(MYSQLI_ASSOC);
  $peac = $row['gender'];
  
    if ($peac == 'male')
        echo "<a href='profile.php'><img class='img-circle thumbnail img-responsive' src='$imgsu/male.jpg' width='100' height='100'/></a>";
     else
    
      echo "<a href='profile.php'><img class='img-circle thumbnail img-responsive' src='$imgsu/female.jpg' width='100' height='100'/></a>";
   }}
   
   
   
     function generate_page_links($view, $cur_page, $num_pages)   {   global $imgsu; global $messu; global $bgsu; 
         if(isset($view))
         $view="$view";
         else 
         $view= "$user";
         
         $self= $_SERVER['PHP_SELF'];
    $page_links = '<td>';

    // If this page is not the first page, generate the "previous" link
    if ($cur_page > 1) {$tyi=($cur_page - 1);
      
 echo "<a href='$self?view=$view&page=$tyi'>previous\t</a>";
 

    }
    else {
     $page_links .= '';
    }

    // Loop through the pages generating the page number links
    for ($i = 1; $i <= $num_pages; $i++) {
      if ($cur_page == $i) {
        $page_links .= ' ' . $i . '</td>';
      }
      else {
         
echo "<a href='$self?view=$view&page=$i'>$i\t</a>";
    
    
      }
    }

    // If this page is not the last page, generate the "next" link
    if ($cur_page < $num_pages) { $ro=($cur_page + 1);
     echo "<a href='$self?view=$view&page=$ro'>next\t</a>";
        }
    else {
     '' . $page_links .= ' ->' . '';
    }

    return $page_links;
  }
  
  
function generate_page($view, $cur_page, $num_pages)   {   global $imgsu; global $messu; global $bgsu; 
         if(isset($view))
         $view="$view";
         else 
         $view= "$user";
         
         $self= $_SERVER['PHP_SELF'];
    $page_links = '<td>';

    // If this page is not the first page, generate the "previous" link
    if ($cur_page > 1) {$tyi=($cur_page - 1);
      
 echo "<a href='$self?view=$view&page=$tyi'>previous\t</a>";
 

    }
    else {
     $page_links .= '';
    }

    // Loop through the pages generating the page number links
    for ($i = 1; $i <= $num_pages; $i++) {
      if ($cur_page == $i) {
        $page_links .= ' ' . $i . '</td>';
      }
      else {
         
echo "<a href='$self?view=$view&page=$i'>$i\t</a>";
    
    
      }
    }

    // If this page is not the last page, generate the "next" link
    if ($cur_page < $num_pages) { $ro=($cur_page + 1);
     echo "<a href='$self?view=$view&page=$ro'>next\t</a>";
        }
    else {
     '' . $page_links .= ' ->' . '';
    }

    return $page_links;
  }
  
  
function need($pagin){
     // Calculate pagination information
  $cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
  $results_per_page = 15;  // number of results per page
  $skip = (($cur_page - 1) * $results_per_page);
  $num_pages = ceil($total / $results_per_page);
 //  LIMIT $skip, $results_per_page
   
    echo "<table><tr class='pull-right'>";
  
    if ($num_pages > 1) {
    echo generate_page_links($cur_page, $num_pages);
    
  }echo "</tr></table>";}
  
  
 function follow($view, $user)  {   global $imgsu; global $messu; global $bgsu;
       $ruty= queryMysql("SELECT * FROM follow WHERE send='$user' and reciv='$view'");
      $rutynumh=$ruty->num_rows;
      if ($rutynumh == 1){ 
        queryMysql("DELETE FROM follow WHERE send='$user' and reciv='$view'");}
        else 
      if ($rutynumh==0){
       queryMysql("INSERT INTO follow values('NULL', '$user', '$view')");}
       
        }
       
  
function lick($view)  {   global $imgsu; global $messu; global $bgsu;
    $ruty= queryMysql("SELECT * FROM follow WHERE reciv='$view'");
      $rutynum=$ruty->num_rows;
      echo "$rutynum";
      }


function unlick($view)  {   global $imgsu; global $messu; global $bgsu;
       $ruty= queryMysql("SELECT * FROM follow WHERE send='$view'");
      $rutynum=$ruty->num_rows;
      echo "$rutynum";
  
}

function foll($view, $user)  {   global $imgsu; global $messu; global $bgsu;
    $ruty= queryMysql("SELECT * FROM follow WHERE send='$user' and reciv='$view'");
      $rutynum=$ruty->num_rows;
      if ($rutynum==0){
     echo "follow:\t";}
     else 
     if ($rutynum > 0){ 
     echo "unfollow:\t";}
}

 
function refoll($view, $user)  {   global $imgsu; global $messu; global $bgsu;
    $ruty= queryMysql("SELECT * FROM follow WHERE send='$user' and reciv='$view'");
      $rutynum=$ruty->num_rows;
    $rutyy= queryMysql("SELECT * FROM follow WHERE reciv='$user' and send='$view'");
      $rutnum=$rutyy->num_rows;
      $ty= ($rutynum + $rutnum);
     if ($ty > 1){ 
         echo "\t <span class='btn btn-sm btn-primary'>";
      
        echo "<span class='glyphicon glyphicon-refresh'></span>";
        echo "</span>";}
        else
        {$rutyy= queryMysql("SELECT * FROM follow WHERE reciv='$user' and send='$view'");
      $rutnm=$rutyy->num_rows;
           if ($rutnm==1){
                echo "\t <span class='btn btn-sm btn-primary'>";
               echo "<span class='glyphicon glyphicon-import'></span>";
               echo "</span>";
               }
               else 
               if ($rutnm==0)
               { $ruty= queryMysql("SELECT * FROM follow WHERE send='$user' and reciv='$view'");
                $rutyum=$ruty->num_rows;
                if ($rutyum==1 ){
                     echo "\t <span class='btn btn-sm btn-primary'>";
                    echo "<span class='glyphicon glyphicon-export'></span>";
                    echo "</span>";
                    }
                   }
            }
} 
      
          function add($user, $vie)
      {   global $imgsu; global $messu; global $bgsu;$peac= $vie;
        $reurl=queryMysql("SELECT * FROM friends where user='$user' AND recip ='$peac' AND confirm='2' ");
     $resurt=queryMysql("SELECT * FROM friends where user='$peac' AND recip ='$user' AND confirm='2' ");  //check if friends continue
     $eumr=$reurl->num_rows;
      $eumrt=$resurt->num_rows;
        $eta= ($eumr + $eumrt);
        
      if ($eta > 0){
        echo "already friends \t [x]";}
            else 
        {if ($eta == 0)
            $remurl=queryMysql("SELECT * FROM friends where user='$user' AND recip ='$peac' AND confirm='1'");
            $resur=queryMysql("SELECT * FROM friends where user='$peac' AND recip ='$user' AND confirm='1'");  //check if friends continue
            $eumret=$remurl->num_rows;
            $eumrmt=$resur->num_rows;
            $erta= ($eumret + $eumrmt);
               if ($erta > 0)
            {echo "already sent request \t [x]";}
        else 
            {echo "add friend";}}
            
            }
            
            
          function ladd($user, $vie)
      {   global $imgsu; global $messu; global $bgsu;  $self= $_SERVER['PHP_SELF'];
    $peac= $vie;
    echo "<a href='$self?me=23&view=$vie'>";
        $reurl=queryMysql("SELECT * FROM friends where user='$user' AND recip ='$peac' AND confirm='2' ");
     $resurt=queryMysql("SELECT * FROM friends where user='$peac' AND recip ='$user' AND confirm='2' ");  //check if friends continue
     $eumr=$reurl->num_rows;
      $eumrt=$resurt->num_rows;
        $eta= ($eumr + $eumrt);
      if ($eta > 0){
        echo "<span class='btn btn-sm btn-success'>friends \t [x]</span>";}
            else 
        {if ($eta == 0)
            $remurl=queryMysql("SELECT * FROM friends where user='$user' AND recip ='$peac' AND confirm='1'");
            $resur=queryMysql("SELECT * FROM friends where user='$peac' AND recip ='$user' AND confirm='1'");  //check if friends continue
            $eumret=$remurl->num_rows;
            $eumrmt=$resur->num_rows;
            $erta= ($eumret + $eumrmt);
               if ($erta > 0)
            {echo "<span class='btn btn-sm btn-success'>request sent \t [x]</span>";}
        else 
            {$rutylj= queryMysql("SELECT * FROM privacy WHERE user='$peac'");
            $rutynmj=$rutylj->FETCH_ARRAY(MYSQLI_ASSOC);
            $ythj=$rutynmj['addt'];
            if ($ythj == 1){echo "<span class='btn btn-sm btn-success'>add friend</span>";}
            else if ($ythj == 0){
            echo "<span class='btn btn-sm btn-success'>unauthorised</span>";}
            }} echo "</a>";
           
            }
            
            
            
function check_add($user, $peace)
      {   global $imgsu; global $messu; global $bgsu;              
        $peac= $peace;
        $reurl=queryMysql("SELECT * FROM friends where user='$user' AND recip ='$peac' AND confirm='2' ");
     $resurt=queryMysql("SELECT * FROM friends where user='$peac' AND recip ='$user' AND confirm='2' ");  //check if friends continue
     $eumr=$reurl->num_rows;
      $eumrt=$resurt->num_rows;
        $eta= ($eumr + $eumrt);
      if ($eta > 0){
        queryMysql("DELETE FROM friends WHERE recip='$user' and user='$peace'");
        queryMysql("DELETE FROM friends WHERE user='$user' and recip='$peace'");}
            else 
        {if ($eta == 0)
            $remurl=queryMysql("SELECT * FROM friends where user='$user' AND recip ='$peac' AND confirm='1'");
            $resur=queryMysql("SELECT * FROM friends where user='$peac' AND recip ='$user' AND confirm='1'");  //check if friends continue
            $eumret=$remurl->num_rows;
            $eumrmt=$resur->num_rows;
            $erta= ($eumret + $eumrmt);
               if ($erta > 0)
        { 
        queryMysql("DELETE FROM friends WHERE recip='$user' and user='$peace'");
        queryMysql("DELETE FROM friends WHERE user='$user' and recip='$peace'");}
        else 
        {    $rutyl= queryMysql("SELECT * FROM privacy WHERE user='$peace'");
            $rutynm=$rutyl->FETCH_ARRAY(MYSQLI_ASSOC);
            $yth=$rutynm['addt'];
            if ($yth==1){
            queryMysql("INSERT INTO friends values (NULL, '$user', '$peace', '1')");
}
           }}
           }
           
function chec($id)  {   global $imgsu; global $messu; global $bgsu;
    $rte = queryMysql("SELECT * FROM messages where id='$id'");
    $rety= $rte->fetch_array(MYSQLI_ASSOC);
    
    if ($rety['pm']==1){
       echo "<span class='glyphicon glyphicon-volume-off'></span>";}
        
    
    }


function followr($view, $user)  {   global $imgsu; global $messu; global $bgsu;
       $ruty= queryMysql("SELECT * FROM follow WHERE send='$user' and reciv='$view'");
      $rutynum=$ruty->num_rows;

      
      if ($rutynum==0){
       queryMysql("INSERT INTO follow values('NULL', '$user', '$view')");
       }
       else 
        if ($rutynum > 0){ 
            queryMysql("DELETE FROM follow WHERE send='$user' and reciv='$view'");
     }
}
  

function follr($view, $user)  {   global $imgsu; global $messu; global $bgsu;
    $ruty= queryMysql("SELECT * FROM follow WHERE send='$user' and reciv='$view'");
      $rutynum=$ruty->num_rows;
      if ($rutynum==0){
     
       echo "<span class='glyphicon glyphicon-edit'></span>\t";}
       else 
        if ($rutynum > 0){ 
        echo "<span class='glyphicon glyphicon-check'></span>:\t";}
}

function msgable($peac, $user, $ch)  {   global $imgsu; global $messu; global $bgsu;
    $view=$peac;
    $self= $_SERVER['PHP_SELF'];
    //check if frnds
   // else
    //check if msgable
    $rutyi= queryMysql("SELECT * FROM friends WHERE user='$view' and recip='$user' and confirm='2'");
    $rum=$rutyi->num_rows;
    $rutyo= queryMysql("SELECT * FROM friends WHERE user='$user' and recip='$view' and confirm='2'");
    $rumy=$rutyo->num_rows;
    $add = $rum + $rumy;
    if ($add>0){
       //create form for msg
       ?>
 <a class='btn btn-sm btn-success' href='pmessage.php?view=<?php echo "$view&msg=23"; ?>'/>message</a>     <?php
        }
        else 
        if ($add<1)
        {
        $ruty= queryMysql("SELECT * FROM privacy WHERE user='$view'");
        $rutynum=$ruty->num_rows;
        $rit=$ruty->fetch_array(MYSQLI_ASSOC);
        $rut=$rit['addt'];
        if ($rut==1){
            ?>
        <a class='btn btn-sm btn-success' href='pmessage.php?view=<?php echo "$view&msg=23"; ?>'/>message</a>
            <?php
            }
        else
        if ($ch==2)
        { echo "<span class='glyphicon glyphicon-ban-circle'>add first</span>";
        }
        else if ($ch!=2){
        echo  "<span class='btn btn-sm btn-success'> Cant send a message</span><br>";
        }}}
 
 
     
  function like($like)   {   global $imgsu; global $messu; global $bgsu; $query  = "SELECT * FROM messages WHERE id='$like'";
    $result = queryMysql($query); 
    
    $num    = $result->num_rows;
      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $liker = $row['lik_e'];
      $likerr = 1+$liker;
      queryMysql("UPDATE messages SET lik_e='$likerr' WHERE id=$like");
      $like = "";}
      
 function tagcheck($user, $view, $pm, $time, $text, $ret){
          $rtty=sanitizestring($text);
           $self= $_SERVER['PHP_SELF'];
     
           queryMysql("INSERT INTO messages VALUES(NULL, '$user',
'$view', '$pm', $time, '$rtty', '$ret')");
    echo "<a href='$self' class='btn btn-sm btn-success'> Timeline Updated, Refresh!!</a>";

  $result=queryMysql("SELECT * FROM messages WHERE user='$user' AND time='$time')");
    $rora = $result->fetch_array(MYSQLI_ASSOC);
   $id=$rora['id'];

      $replacement=' ';
          $patter = '/^[^@#]*[ ]/';
          $pattern = '/[ ][^@#]*/';
   $new_phone = preg_replace($patter, $replacement, $rtty);
   $final = preg_replace($pattern, $replacement, $new_phone); 
          $new = preg_replace('/[@]/', ' @', $final);
         $newf = preg_replace('/[#]/', ' #', $new);
           
            // Extract the username keywords into an array
    $clean_search = str_replace(',', ' ', $newf);
    $search_words = explode(' ', $clean_search);
    $final_search_words = array();
    if (count($search_words) > 0) {
      foreach ($search_words as $word) {
        if (!empty($word)) {
          $final_search_words[] = $word;
        }
      }
    }
     
       if (count($final_search_words) > 0) {
      foreach($final_search_words as $word) {
         
        $new = preg_replace('/[@]/', '', $word);
         $wordh = preg_replace('/[#]/', '', $new);
         
          $know=querymysql("SELECT * from members where user='$wordh'");
          $num_know=$know->num_rows;
          
          if ($num_know>0){
              //chck if mentioned is fllwing user
        $knw=querymysql("SELECT * from follow where user='$wordh' AND reciv=$user");
          $num_knw=$knw->num_rows;
       
          if ($num_knw>0){
    queryMysql("INSERT INTO mentions VALUES(NULL, '$id', '$user', '$wordh')");
}
}else
 queryMysql("INSERT INTO trends VALUES(NULL, '$id', '$user', '$newfo')");


      }}}
?>      
 




