<?php

function do_page_club_balance($club_id) {
  $member_id = request_get("mid", 0);
  echo do_page_club_balance_($club_id, $member_id, false);
  if ($member_id) {
    do_page_club_trans_list($club_id, $member_id);
  } else {
    do_page_club_balance_all($club_id);
  }
  if (!$member_id) {
    $name = '俱乐部管理员';
    $type = 'balance';
  } else {
    $name = member_get_name_by_id($member_id);
    $type = 'trans';
  }
?>
        <a class="btn btn-primary" role="button" href="club.php?cid=<?php echo $club_id?>&tab=b&mid=<?php echo $member_id?>&mail=<?php echo $type?>" onclick="return confirm('这可能需要很长的时间来完成。确定发送对账邮件给<?php echo $name?>吗？');"><span class="glyphicon glyphicon-envelope" aria-hidden="true">&nbsp;发送对账邮件</span></a>
<?php
  if (($member_id)&&(member_get_balance_by_club($member_id, $club_id) <= BALANCE_TOO_LOW)) {
?>
        &nbsp;&nbsp;<a class="btn btn-primary" role="button" href="club.php?cid=<?php echo $club_id?>&tab=b&mid=<?php echo $member_id?>&mail=lowbalance" onclick="return confirm('确定发送充值提醒邮件给<?php echo $name?>吗？');"><span class="glyphicon glyphicon-envelope" aria-hidden="true">&nbsp;发送充值提醒</span></a>
<?php
  }
}

function do_page_club_balance_($club_id, $member_id, $mail) {
  $deposit_all = trans_get_deposit_all_by_club($club_id, $member_id);
  $withdraw_all = trans_get_withdraw_all_by_club($club_id, $member_id, NULL);
  $deposit_count = trans_get_deposit_count_by_club($club_id, $member_id);
  $withdraw_count = trans_get_withdraw_count_by_club($club_id, $member_id);
  $balance_all = $deposit_all-$withdraw_all;
  if ($deposit_count != 0) {
    $ave_deposit = round($deposit_all/$deposit_count,2);
  } else {
    $ave_deposit = 0;
  }
  if ($withdraw_count != 0) {
    $ave_withdraw = round($withdraw_all/$withdraw_count,2);
  } else {
    $ave_withdraw = 0;
  }
  if ($member_id == 0) {
    $account_name = "俱乐部账户";
  } else {
    $account_name = member_get_name_by_id($member_id)."的账户";
  }
  $output = "".
"          <div class='panel panel-default'>\n";
  if (!$mail) {$output .= "            <div class='panel-heading'><strong>".$account_name."</strong></div>\n";}
    $output .= "".
"            <table class='table table-striped table-bordered'";
  if ($mail) {$output .= " border='1' width='100%'";}
    $output .= "".
                 ">\n".
"              <tr>\n".
"                <td>总余额  （元）</td><td><strong>".$balance_all."</strong></td></tr>\n";
  if ($member_id) {
    $output .= "".
"              <tr>\n".
"                <td>充值总额（元）</td><td>".$deposit_all."</td></tr>\n".
"              <tr>\n".
"                <td>充值次数</td><td>".$deposit_count."</td></tr>\n".
"              <tr>\n".
"                <td>平均充值（元）</td><td>".$ave_deposit."</td></tr>\n".
"              <tr>\n".
"                <td>消费总额（元）</td><td>".$withdraw_all."</td></tr>\n".
"              <tr>\n".
"                <td>消费次数</td><td>".$withdraw_count."</td></tr>\n".
"              <tr>\n".
"                <td>平均消费（元）</td><td>".$ave_withdraw."</td></tr>\n";
  }
  $output .= "".
"            </table>\n".
"          </div><br \>\n";

  return $output;
}

function do_page_club_balance_all($club_id) {
  $orderby = request_get("orderby", "mid");
  $order = request_get("order", "a");
  $page = request_get("page", 1);
  $pages = club_get_balance_pages_all($club_id, ITEM_PER_PAGE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $url = "club.php?cid=".$club_id."&tab=b&";
  $r = club_get_balance_all($club_id, $orderby, $order, $page, ITEM_PER_PAGE);
  do_page_list_page_move($orderby, $order, $page, $pages, $url);
  echo do_page_club_balance_all_($r, $orderby, $order, $url, $club_id);
  do_page_list_page_move($orderby, $order, $page, $pages, $url);
}

function do_page_club_balance_all_($r, $orderby, $order, $url, $club_id) {
  if ($order == "a") {
    $icon = "glyphicon-triangle-top";
    $urlorder = "d";
  } else {
    $icon = "glyphicon-triangle-bottom";
    $urlorder = "a";
  }
  $output = "";
  if ($r) {
    $output .= "".
"            <table class='table table-striped table-bordered'";
  if (!$url) {$output .= " border='1' width='100%'";}
    $output .= ">\n".
"              <tr>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=mid&order=".$urlorder."#listnav'>";}
    $output .="<strong>姓名</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "mid") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=ts1&order=".$urlorder."#listnav'>";}
    $output .="<strong>存入总额</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "ts1") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=ts2&order=".$urlorder."#listnav'>";}
    $output .="<strong>支出总额</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "ts2") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=ba&order=".$urlorder."#listnav'>";}
    $output .="<strong>总余额</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "ba") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                </tr>\n";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $member_id = $row['mid'];
      $deposit = $row['ts1'];
      $withdraw = $row['ts2'];
      $output .= "".
"              <tr>\n".
"                <td><a href='".BASE_URL."/club.php?cid=".$club_id."&tab=b&mid=".$member_id."'>".member_get_name_by_id($member_id)."</a></td>\n".
"                <td>".$deposit."</td>\n".
"                <td>".$withdraw."</td>\n".
"                <td>".($deposit-$withdraw)."</td>\n";
"              </tr>\n";
    } /* of for i */
    $output .= "".
"                </table>\n";
  }
  return $output;
}

function do_page_club_trans_list($club_id, $member_id) {
  $orderby = request_get("orderby", "trans_date");
  $order = request_get("order", "d");
  $page = request_get("page", 1);
  $pages = trans_get_pages_all($club_id, ITEM_PER_PAGE_TRANS);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  if ($member_id) {
    $url = "club.php?cid=".$club_id."&mid=".$member_id."&tab=b&";
  } else {
    $url = "club.php?cid=".$club_id."&tab=b&";
  }
  $r = trans_get_all($club_id, $member_id, $orderby, $order, $page, ITEM_PER_PAGE_TRANS);
  do_page_list_page_move($orderby, $order, $page, $pages, $url);
  echo do_page_club_trans_list_($r, $orderby, $order, $url, false);
  do_page_list_page_move($orderby, $order, $page, $pages, $url);
}

function do_page_club_trans_list_($r, $orderby, $order, $url, $mail) {
  if ($order == "a") {
    $icon = "glyphicon-triangle-top";
    $urlorder = "d";
  } else {
    $icon = "glyphicon-triangle-bottom";
    $urlorder = "a";
  }
  $output = "";
  if ($r) {
    $output .= "".
"            <table class='table table-striped table-bordered'";
  if (!$url) {$output .= " border='1' width='100%'";}
    $output .= ">\n".
"              <tr>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=trans_date&order=".$urlorder."#listnav'>";}
    $output .="<strong>交易时间</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "trans_date") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=member_id&order=".$urlorder."#listnav'>";}
    $output .="<strong>成员</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "member_id") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=withdraw&order=".$urlorder."#listnav'>";}
    $output .="<strong>支出</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "withdraw") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=deposit&order=".$urlorder."#listnav'>";}
    $output .="<strong>存入</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "deposit") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=type_id&order=".$urlorder."#listnav'>";}
    $output .="<strong>类型</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "type_id") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n";
    if (!$mail) {
      $output .=
"                <td>";
      if ($url) {$output .= "<a href='".$url."orderby=event_id&order=".$urlorder."#listnav'>";}
      $output .="<strong>活动时间</strong>";
      if ($url) {$output .="</a>";}
      $output .= "\n";
      if ($orderby == "event_id") {
        $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
      }
      $output .= "".
"                </td>\n";
    }
    $output .=
"                <td>";
    if ($url) {$output .= "<a href='".$url."orderby=notes&order=".$urlorder."#listnav'>";}
    $output .="<strong>备注</strong>";
    if ($url) {$output .="</a>";}
    $output .= "\n";
    if ($orderby == "notes") {
      $output .= "".
"                    <span class='glyphicon ".$icon." aria-hidden='true'></span>\n";
    }
    $output .= "".
"                </td>\n".
"                </tr>\n";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $trans_id = $row['id'];
      $trans_date = $row['trans_date'];
      $member_id = $row['member_id'];
      $withdraw = $row['withdraw'];
      $deposit = $row['deposit'];
      $type_id = $row['type_id'];
      $event_id = $row['event_id'];
      $club_id = $row['club_id'];
      $notes = $row['notes'];
      $output .= "".
"              <tr>\n".
"                <td>".$trans_date."</td>\n".
"                <td>".member_get_name_by_id($member_id)."</td>\n".
"                <td>".$withdraw."</td>\n".
"                <td>".$deposit."</td>\n".
"                <td>".trans_type_id2name($type_id)."</td>\n";
      if (!$mail) {
        $output .=
"                <td>".  event_get_start_time_by_id($event_id)."</td>\n";
      }
      $output .=
"                <td>".$notes."</td>\n".
"              </tr>\n";
    } /* of for i */
    $output .= "".
"                </table>\n";
  }
  return $output;
}

function do_page_club_add_trans($club_id) {
  $user_id = user_get_signin_id();
  if (isset($_SESSION['trans_date'])) {
    $trans_date =$_SESSION['trans_date'];
    unset($_SESSION['trans_date']);
  } else {
    date_default_timezone_set('PRC');
    $trans_date = date('Y-m-d');
  }
  $trans_member_id = request_get("mid", NULL);
  if (!$trans_member_id) {
    if (isset($_SESSION['member_id'])) {
      $trans_member_id =$_SESSION['member_id'];
      unset($_SESSION['member_id']);
    } else {
      $trans_member_id = NULL;
    }
  }
  if (isset($_SESSION['withdraw'])) {
    $withdraw =$_SESSION['withdraw'];
    unset($_SESSION['withdraw']);
  } else {
    $withdraw = 0;
  }
  if (isset($_SESSION['deposit'])) {
    $deposit =$_SESSION['deposit'];
    unset($_SESSION['deposit']);
  } else {
    $deposit = 0;
  }
  if (isset($_SESSION['type_id'])) {
    $type_id =$_SESSION['type_id'];
    unset($_SESSION['type_id']);
  } else {
    $type_id = 2;
  }
  if (isset($_SESSION['event_id'])) {
    $trans_event_id =$_SESSION['event_id'];
    unset($_SESSION['event_id']);
  } else {
    $trans_event_id = 0;
  }
  if (isset($_SESSION['notes'])) {
    $notes =$_SESSION['notes'];
    unset($_SESSION['notes']);
  } else {
    $notes = "";
  }

?>
      <div class="panel panel-default">
        <div class="panel-heading"><strong>新交易</strong></div>
        <div class="panel-body">
          <form class="form-horizontal" role="form" method="post" action="club.php?cid=<?php echo $club_id?>&tab=a&set=f" >
            <div class="form-group">
              <input name="club_id" type="hidden" class="form-control"
                     value="<?php echo $club_id?>">
              <input name="form_key" type="hidden" class="form-control"
                     value="trans_new">
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">交易日期</label>
              <div class="col-sm-6">
                <input name="trans_date" type="datetime" class="form-control" placeholder="交易时间（必填）" aria-describedby="basic-addon1" size="100%" required autofocus value="<?php echo $trans_date?>" maxlength="20" data-toggle="tooltip" data-placement="bottom" title="格式YYYY-MM-DD HH:MM:SS">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">成员</label>
              <div class="col-sm-6">
                <select name="member_id" class="form-control" multiple="">
<?php
  $r = member_get_all_same_club($user_id, $club_id, "convert(name using gbk)", "a", 0, NULL);
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $member_id = $row['id'];
    $name = $row['name'];
    if (!$trans_member_id) {
      $trans_member_id = $member_id;
    }
?>
                  <option <?php if($member_id==$trans_member_id){echo "selected='selected'";}?> value="<?php echo $member_id?>"><?php echo $name;?></option>
<?php
  }
?>
                </select>
              </div>
<div class="col-sm-3"><img src="<?php echo member_get_photo_url_by_id($trans_member_id)?>" alt="照片" width="30" height="37"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">支出</label>
              <div class="col-sm-6">
                <input name="withdraw" type="text" class="form-control"
                     placeholder="支出（可选*）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $withdraw?>" maxlength="8" data-toggle="tooltip" data-placement="bottom" title="与存入项同时必须且只能填写一项">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">存入</label>
              <div class="col-sm-6">
                <input name="deposit" type="text" class="form-control"
                     placeholder="存入（可选*）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $deposit?>" maxlength="8" data-toggle="tooltip" data-placement="bottom" title="与支出项同时必须且只能填写一项">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">类型</label>
              <div class="col-sm-6">
                <select name="type" class="form-control" multiple="">
<?php
  $trans_type = trans_type_get_all();
  foreach($trans_type as $key=>$value) {
?>
                  <option <?php if($type_id==$key){echo "selected='selected'";}?>><?php echo $value;?></option>
<?php
  }
?>
                </select>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group ">
              <label class="col-sm-3 control-label">活动</label>
              <div class="col-sm-6">
                <select name="event_id" class="form-control" multiple="">
<?php
  $r3 = event_get_all_same_club($club_id, "start_time", "d", 0, NULL);
  for ($i=0;$i<$r3->num_rows;$i++) {
    $row = $r3->fetch_assoc();
    $event_id = $row['id'];
    $start_time = $row['start_time'];
?>
                  <option <?php if($event_id==$trans_event_id){echo "selected='selected'";}?> value="<?php echo $event_id?>"><?php echo "[".$event_id."]".$start_time;?></option>
<?php
  }
?>
                </select>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">备注</label>
              <div class="col-sm-6">
                <input name="notes" type="text" class="form-control"
                     placeholder="备注（可选）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $notes?>">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
              <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;添加&nbsp;&nbsp;</span></button>
            </div>
            <div class="col-sm-3">
              <button class="btn btn-primary btn-block" type="reset"><span class="glyphicon glyphicon-repeat" aria-hidden="true">&nbsp;重新设定</span></button>
            </div>
            <div class="col-sm-3"></div>
          </form>
        </div>
      </div>
<?php
}

