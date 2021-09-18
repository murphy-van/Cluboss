<?php
function do_page_club_event_list($club_id) {
  $orderby = request_get("orderby", "id");
  $order = request_get("order", "d");
  $page = request_get("page", 1);
  $pages = event_get_pages_same_club($club_id, ITEM_PER_PAGE_EVENT);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = event_get_all_same_club($club_id, $orderby, $order, $page, ITEM_PER_PAGE_EVENT);
  if ($r) {
    do_page_list_page_move($orderby, $order, $page, $pages, "club.php?cid=".$club_id."&tab=e&");
    do_page_club_event_list_($r, "club.php?cid=".$club_id, $orderby, $order, false);
    do_page_list_page_move($orderby, $order, $page, $pages, "club.php?cid=".$club_id."&tab=e&");
  } /* of if r */
}

function do_page_club_event_list_($r, $url, $orderby, $order, $display_club_name) {
  $user_id = user_get_signin_id();
  if ($order == "a") {
    $icon = "glyphicon-triangle-top";
    $urlorder = "d";
  } else {
    $icon = "glyphicon-triangle-bottom";
    $urlorder = "a";
  }
  if ($r->num_rows <= 0) {
    return;
  }
?>
            <table class="table table-striped table-bordered">
              <tr>
<?php
  if ($display_club_name) {
?>
                <td><strong>俱乐部</strong>
<?php
  }
?>          
                </td>
                <td><a href="<?php echo $url?>&tab=e&orderby=start_time&order=<?php echo $urlorder?>"><strong>开始时间</strong></a>
<?php
    if ($orderby == "start_time") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="<?php echo $url?>&tab=e&orderby=facility_id&order=<?php echo $urlorder?>"><strong>场所</strong></a>
<?php
    if ($orderby == "facility_id") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="<?php echo $url?>&tab=e&orderby=attendee&order=<?php echo $urlorder?>"><strong>参加人数</strong></a>
<?php
    if ($orderby == "attendee") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                </tr>
<?php
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $event_id = $row['id'];
      $club_id = event_get_club_id($event_id);
      $club_name = club_get_name_by_id($club_id);
      $attendee = event_get_attendee_count($event_id);
      $pay_users = event_get_pay_users_count($event_id);
?>
              <tr>
<?php
      if ($display_club_name) {
?>
                <td><a href="club.php?cid=<?php echo $club_id?>"><?php echo $club_name?></a></td>
<?php
      }
?>
                <td><a href="club.php?cid=<?php echo $club_id?>&tab=e&eid=<?php echo $event_id?>"><?php echo $row['start_time']?></a></td>
                <td><?php echo facility_get_name_by_id($user_id, $row['facility_id'])?></td>
                <td><?php echo $attendee?>
<?php
      if ($pay_users > $attendee) {
?>
                (+<?php echo ($pay_users-$attendee)?>)
<?php
      }
?>
                </tr>
<?php
    } /* of for i */
?>
                </table>
<?php
}

function do_page_club_event_detail($club_id, $event_id) {
  $attendee = event_get_attendee_count($event_id);
  echo do_page_club_event_detail_($event_id, false);
?>
            <div class="row">
              <div class="col-sm-4">
                <a href="club.php?cid=<?php echo $club_id?>&tab=e&eid=<?php echo $event_id?>&act=e" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-pencil" aria-hidden="true">&nbsp;编辑活动&nbsp;&nbsp;</span></a>
              </div>
              <div class="col-sm-4">
                <a href="club.php?cid=<?php echo $club_id?>&tab=e&eid=<?php echo $event_id?>&act=em" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-user" aria-hidden="true">&nbsp;修改参加者&nbsp;</span></a>
              </div>
              <div class="col-sm-4">
                <a href="club.php?cid=<?php echo $club_id?>&tab=e&eid=<?php echo $event_id?>&mail=event" class="btn btn-primary btn-block" onclick="return confirm('这可能需要较长的时间来完成。确定发送确认邮件给参加者吗？');"><span class="glyphicon glyphicon-envelope" aria-hidden="true">&nbsp;发送确认邮件</span></a>
              </div>
            </div>
            <br />
<?php
  if ($attendee > 0) {
    do_page_club_event_member_list($event_id);
  }
}

function do_page_club_event_detail_($event_id, $mail) {
  $user_id = user_get_signin_id();
  $total = event_get_total_by_id($event_id);
  $share = event_get_share_by_id($event_id);
  $attendee = event_get_attendee_count($event_id);
  $pay_users = event_get_pay_users_count($event_id);
  $notes = event_get_notes_by_id($event_id);
  $output = "".
"            <table class='table table-striped table-bordered'";
  if ($mail) {$output .= " border='1' width='100%'";}
  $output .= ">\n".
"              <tr>\n".
"                <td>开始时间</td>\n".
"                <td>".event_get_start_time_by_id($event_id)."</td>\n".
"              </tr>\n".
"              <tr>\n".
"                <td>活动时长（小时）</td>\n".
"                <td>".event_get_duration_by_id($event_id)."</td>\n".
"              </tr>\n".
"              <tr>\n".
"                <td>俱乐部</td>\n".
"                <td>".club_get_name_by_id(event_get_club_id($event_id))."</td>\n".
"              </tr>\n".
"              <tr>\n".
"                <td>场所</td>\n".
"                <td>".facility_get_name_by_id($user_id, event_get_facility_id_by_id($event_id))."</td>\n".
"              </tr>\n".
"              <tr>\n";
  if ($total != 0) {
    $output .= "".
"                <td>总消费额（人民币）</td>\n".
"                <td>".$total."</td>\n";
  } else {
    $output .= "".
"                <td>每人分摊额（人民币）</td>\n".
"                <td>".$share."</td>\n";
  }
  $output .= "".
"              </tr>\n".
"              <tr>\n".
"                <td>参加人数</td>\n".
"                <td>".$attendee; 
  if ($pay_users > $attendee) {
    $output .= "(+".($pay_users-$attendee).")";
  }
  $output .= "".
"            </td>\n".
"              </tr>\n".
"              <tr>\n".
"                <td>参加者</td>\n".
"                <td>".member_get_all_name_same_event($user_id, $event_id)."</td>\n".
"              </tr>\n".
"            </td>\n".
"              </tr>\n".
"              <tr>\n".
"                <td>备注</td>\n".
"                <td>".$notes."</td>\n".
"              </tr>\n".
"            </table>\n";
  return $output;
}

function do_page_club_event_edit($club_id, $event_id) {
  $total = event_get_total_by_id($event_id);
  $share = event_get_share_by_id($event_id);
  if ($total != 0) {
    $fee = $total;
    $fee_type = "总消费额";
  } else {
    $fee = $share;
    $fee_type = "分摊额";
  }
  $attendee = event_get_attendee_count($event_id);
  $pay_users = event_get_pay_users_count($event_id);
  $notes = event_get_notes_by_id($event_id);
?>
          <form class="form-horizontal" role="form"  method="post" action="club.php?cid=<?php echo $club_id?>&tab=e&eid=<?php echo $event_id?>">
            <div class="form-group">
              <input name="event_id" type="hidden" class="form-control"
                     value="<?php echo $event_id?>">
            <input name="form_key" type="hidden" class="form-control"
                   value="event_edit">          
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">活动开始时间</label>
              <div class="col-sm-6">
                <input name="start_time" type="datetime" class="form-control" placeholder="活动开始时间（必填）" value="<?php echo event_get_start_time_by_id($event_id)?>" maxlength="20" data-toggle="tooltip" data-placement="bottom" title="格式YYYY-MM-DD HH:MM:SS">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">活动时长（小时）</label>
              <div class="col-sm-6">
                <input name="duration" type="number" class="form-control" value="<?php echo event_get_duration_by_id($event_id)?>" maxlength="2">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">场所</label>
              <div class="col-sm-6">
                <select name="facility" class="form-control">
<?php
  $facility_id = event_get_facility_id_by_id($event_id);
  $user_id = user_get_signin_id();
  $r = facility_get_all($user_id, "id", "a", 0, NULL);
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
?>
                  <option <?php if($facility_id==$i+1){echo "selected='selected'";}?>><?php echo $row['name'];?></option>
<?php
  }
?>
                </select>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <div class="col-sm-3">
                <select name="fee_type" class="form-control" multiple="">
                  <option <?php if($fee_type=="总消费额"){echo "selected='selected'";}?>>总消费额</option>
                  <option <?php if($fee_type=="分摊额"){echo "selected='selected'";}?>>分摊额</option>
                </select>
              </div>
              <div class="col-sm-6">
                <input name="fee" type="text" class="form-control" maxlength="8" 
                     placeholder="<?php echo $fee_type?>（元）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $fee?>" required>
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
            <div class="col-sm-3">
              <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;提交修改</span></button>
            </div>
            <div class="col-sm-3">
              <button class="btn btn-primary btn-block" type="reset"><span class="glyphicon glyphicon-repeat" aria-hidden="true">&nbsp;重新设定</span></button>
            </div>
            <div class="col-sm-3">
<?php
  if ($attendee > 0) {
    $can_del = " disabled";
  } else {
    $can_del = "";
  }
?>
              <a href="event_del.php?eid=<?php echo $event_id?>" class="btn btn-primary btn-block<?php echo $can_del?>" onclick="return confirm('确定删除这个活动吗？');"><span class="glyphicon glyphicon-trash" aria-hidden="true">&nbsp;删除活动</span></a>
            </div>
            <div class="col-sm-3">
              <a href="club.php?cid=<?php echo $club_id?>&tab=e&eid=<?php echo $event_id?>" class="btn btn-primary btn-block"><span class="glyphicon glyphicon-remove" aria-hidden="true">&nbsp;取消修改</span></a>
            </div>
          </form>
<?php
}

function do_page_club_event_member_list($event_id) {
  $user_id = user_get_signin_id();
  $club_id = event_get_club_id($event_id);
  $page = request_get("page", 1);
  $pages = member_get_pages_same_event($user_id, $event_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = member_get_all_same_event($user_id, $event_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&tab=e&eid=".$event_id."&");
  do_page_people_list_($r, "club_event_member", $event_id, $page, $club_id);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&tab=e&eid=".$event_id."&");
}

function do_page_club_event_member_edit($club_id, $event_id) {
  $user_id = user_get_signin_id();
  $page = request_get("page", 1);
  $pages = member_get_pages_same_club($user_id, $club_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = member_get_all_same_club($user_id, $club_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&eid=".$event_id."&tab=e&act=em&");
  do_page_people_list_($r, "club_member_edit", $event_id, $page, $club_id);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&eid=".$event_id."&tab=e&act=em&");
}

function do_page_club_add_event($club_id) {
  if (isset($_SESSION['event_time'])) {
    $event_time = $_SESSION['event_time'];
    unset($_SESSION['event_time']);
  } else {
    date_default_timezone_set('PRC');
    $event_time = date('Y-m-d H:i:s');
  }
  if (isset($_SESSION['duration'])) {
    $duration = $_SESSION['duration'];
    unset($_SESSION['duration']);
  } else {
    $duration = 1;
  }
  if (isset($_SESSION['facility_id'])) {
    $facility_id = $_SESSION['facility_id'];
    unset($_SESSION['facility_id']);
  } else {
    $facility_id = 1;
  }
  if (isset($_SESSION['fee'])) {
    $fee = $_SESSION['fee'];
    unset($_SESSION['fee']);
  } else {
    $fee = NULL;
  }
  if (isset($_SESSION['fee_type'])) {
    $fee_type = $_SESSION['fee_type'];
    unset($_SESSION['fee_type']);
  } else {
    $fee_type = "分摊额";
  }
  if (isset($_SESSION['notes'])) {
    $notes = $_SESSION['notes'];
    unset($_SESSION['notes']);
  } else {
    $notes = "";
  }
  
?>
      <div class="panel panel-default">
        <div class="panel-heading"><strong>新活动</strong></div>
        <div class="panel-body">
          <form class="form-horizontal" method="post" action="club.php?cid=<?php echo $club_id?>&tab=a&set=e" >
            <div class="form-group">
              <input name="club_id" type="hidden" class="form-control"
                     value="<?php echo $club_id?>">
              <input name="form_key" type="hidden" class="form-control"
                     value="event_new">            
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">活动开始时间</label>
              <div class="col-sm-6">
                <input name="event_time" type="datetime" class="form-control" placeholder="活动开始时间（必填）" aria-describedby="basic-addon1" size="100%" required autofocus value="<?php echo $event_time?>" maxlength="20" data-toggle="tooltip" data-placement="bottom" title="格式YYYY-MM-DD HH:MM:SS">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">活动时长（小时）</label>
              <div class="col-sm-6">
                <input name="duration" type="number" class="form-control" placeholder="活动时长（可选）" aria-describedby="basic-addon1" size="100%" maxlength="2" value="<?php echo $duration?>">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">场所</label>
              <div class="col-sm-6">
                <select name="facility" class="form-control">
<?php
  $user_id = user_get_signin_id();
  $r = facility_get_all($user_id, "id", "a", 0, NULL);
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
?>
                  <option <?php if($facility_id==$i+1){echo "selected='selected'";}?>><?php echo $row['name'];?></option>
<?php
  }
?>
                </select>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <div class="col-sm-3">
                <select name="fee_type" class="form-control" multiple="">
                  <option <?php if($fee_type=="总消费额"){echo "selected='selected'";}?>>总消费额</option>
                  <option <?php if($fee_type=="分摊额"){echo "selected='selected'";}?>>分摊额</option>
                </select>
              </div>
              <div class="col-sm-6">
                <input name="fee" type="text" class="form-control" maxlength="8" 
                     placeholder="<?php echo $fee_type?>（元）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $fee?>" required>
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
              <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;创建活动</span></button>
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

