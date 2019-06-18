<?php
  $rel____="../";

  require_once $rel____.'includes/incl.php';
  require_once $rel____.'includes/notification_incl.php';
  require_once $rel____.'includes/profile_incl.php'; 
  require_once $rel____.'includes/scramble_incl.php';  
  $array=getAllTopWinnersScramble();
  $bool=true;
?>
<!DOCTYPE html>
<html>
<head>
<?php include $rel____.'views/head.php';?>
</head>
<body>
  <?php include $rel____.'views/open_body.php';?>
  <?php include $rel____.'views/header_.php';?>
  <div>
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <table class="table table-condensed table-striped full-width">
            <thead class="full-width">
            <tr>
              <th class="text-uppercase afree-topic"><?php echo translate_var('leaderboard_', array(translate('scramble')));?></th>
            </tr>
            </thead>
            <tbody>     
            <tr>
              <td class="text-capitalize">
              <?php
              $e=0;
              $t_=$count= count($array);
              for($a=0;$a<$count;$a++){
                # code...
                $value=$array2_=$array[$e];
                $fname__=$array2_['fullname'];
                $usert_=$array2_['username'];

                $value=$array[$e];
                $trans=$array2_['scramble'];
                $lonk=$rel____.'a/index.php?uid='.$usert_.'&amp;todo=sendmsg&amp;refid=12&amp;is_bday=4';
                $lonk_=$rel____.'profile/index.php?uid='.$usert_;
                
                echo <<<_END
                <div class="card full-width no-padd">
                  <table class=" full-width">
                      <tr class="full-width">
                        <td style="width:50%">
                           <a class="shift-10" href="$lonk_" class="shift-10">$fname__</a>
                        </td>
                        <td class="text-center" style="width:50%">
                          <div class="text-center">
                              <span class="btn btn-sm btn-default btn-purp full-width text-center form-control text-white">$trans</span>
                          </div>
                        </td>
                      </tr>
                  </table>
                </div>
_END;
                $e++;
              }
              ?>

              </td>
            </tr>    
            </tbody>
          </table>
          <table class="full-width">
            <tr class="full-width">
              <td style="width:50%"><a href="<?php echo url_rewrite_query('page='.getPrevPage());?>" class="btn btn-default full-width"><?php echo translate('prev');?></a></td>
              <td style="width:50%"><a  href="<?php echo url_rewrite_query('page='.getNextPage());?>" class="btn btn-default full-width"><?php echo translate('next');?></a></td>
            </tr>
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