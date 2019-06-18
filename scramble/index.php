<?php
  $rel____="../";


  require_once $rel____.'includes/incl.php';
  require_once $rel____.'includes/scramble_incl.php';
  require_once $rel____.'includes/notification_incl.php';
  require_once $rel____.'includes/profile_incl.php';
  $array=getScrambleBasic();
  $array2=getScrambleMessages();
  // saveGameScore("rhjjjtkvgvhjkbtjklbvj", 0, "scramble");

?>
<!DOCTYPE html>
<html>
<head>
<?php include $rel____.'views/head.php';?>
</head>
<body>
  <?php include $rel____.'views/open_body.php';?>
  <?php include $rel____.'views/header_.php';?>
  <div id="login">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div class="card full-width no-padd">
            <table>
              <thead>
                <tr>
                  <td>
                    <img src="<?php echo $array['image'];?>" class="img-rounded afree-img-thmb"/>
                  </td>
                  <td> 
                  <p class="shift-10">
                    <table>
                      <thead>
                        <tr>
                          <td>
                            <a href="#" class="shift-10"><?php echo $array['fullname'];?></a> (<?php echo $array['subtitle'];?>)
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="<?php echo 'index.php';?>" class="btn btn-sm btn-default shift-marg-10"><?php echo translate('refresh');?></a>
                            <a href="<?php echo 'leaderboard.php';?>" class="btn btn-sm btn-default shift-marg-10"><?php echo translate('leader_board');?></a>
                          </td>
                        </tr>
                      </thead>
                    </table>
                  <p>
                  </td>
                </tr>
              </thead>
            </table>
          </div>
          <br/>
          <br/>

          <?php  
          $y=0;
          $re=[];
          for($i=0; $i<count($array2); $i++) {
            //rmv for after the other
            //rmv for after the other
            $value=$array2[$i];
            $r=getThisUser();
            $ms=$array2[$i]['message'];
            $usr=$array2[$i]['auth_username'];

            if(empty($usr)){
              continue;
            }


            $this_auth=$usr;
            $nxt=$i+1;
            if(isset($array2[$nxt])){
              $nxt_auth=$array2[$nxt]['auth_username'];
              while($this_auth==$nxt_auth && count($array2)>$nxt){              
                if(empty($ms))
                  $ms=$array2[$nxt]['message'];
                else
                  $ms=$ms."<br>".$array2[$nxt]['message'];
                $nxt++;
                $nxt_auth=$array2[$nxt]['auth_username'];
                $i++;
              }
            }

            $a_=$value['auth_username'];
            $b_=$value['auth'];
            $c_=$ms;
            $d_=$value['time'];
echo <<<_END
    <div class="card full-width no-padd">
      <b class="shift-2 shift-top-2">$b_</b>
      <p class="shift-5">$c_<br><h5><small class="shift-5">$d_</small></h5></p>
    </div>
_END;
              }?>
          <br/>
          <table class="full-width">
            <tr class="full-width">
              <td style="width:50%"><a href="<?php echo url_rewrite_query('page='.getPrevPage());?>" class="btn btn-default full-width"><?php echo translate('prev');?></a></td>
              <td style="width:50%"><a  href="<?php echo url_rewrite_query('page='.getNextPage());?>" class="btn btn-default full-width"><?php echo translate('next');?></a></td>
            </tr>
          </table>
          <br/>

          <form method="post" action="<?php echo $rel____.'a/scramble/index.php?todo=sendscramblemsg&amp;refid=12';?>">
            <table class="full-width">
              <tr class="full-width">
                <td style="width:75%">
                  <div class="form-group">
                    <textarea name="body" class="full-width form-control"></textarea>
                  </div>
                </td>
                <td style="width:25%">
                  <div class="form-group text-center">
                    <input type="submit" class="btn btn-sm btn-default btn-purp full-width text-center form-control " value="<?php echo translate('send');?>">
                  </div>
                </td>
              </tr>
            </table>
          </form>
          <br/>

          <table class="full-width">
            <thead class="full-width">
            <tr>
              <th colspan="2" class="text-uppercase afree-topic full-width"><?php echo translate('more_opt');?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td colspan="2" class="text-capitalize full-width">
                <a href="<?php echo $rel____."scramble/index.php" ?>"><?php echo translate('close');?></a>
              </td>  
            </tr>            
            </tbody>
          </table>
          <br/>
          <?php include $rel____.'views/bottom_most.php';?>
        </div>
      </div>
    </div>
  </div>
  <?php include $rel____.'views/close_body.php';?>
  <?php include $rel____.'views/bottom.php';?>
</body>
</html>