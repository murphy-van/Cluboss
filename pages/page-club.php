<?php

require_once("page-club-event.php");
require_once("page-club-member.php");
require_once("page-club-trans.php");
require_once("page-club-stat.php");

function do_club() {
  do_html_header("俱乐部 - ".APP_NAME, "navbar", false);
  do_page_club_navbar();
  do_page_club(false);
  do_page_footer();
  do_html_footer();
}

function do_club_public() {
  do_html_header("俱乐部 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_club(true);
  do_page_footer();
  do_html_footer();
}

function do_page_club_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("俱乐部");
  do_page_navbar_right();
}

function do_page_club($public) {
  if ($public) {
    $req_tab = request_get("tab", "s");
    if (($req_tab != "i")&&($req_tab != "s")) {
      $req_tab = "s";
    }
    $public_uri = request_get("club", NULL);
    $club_id = club_get_id_by_public_uri($public_uri);
    $club_name = club_get_name_by_id($club_id);
    if (!$club_name) {
        msg_bar_warning_delay("俱乐部不存在！");
        return;
    }
    $set = request_get("set", "e");
  } else {
    $club_id = request_get("cid", NULL);
    if ($club_id) {
      $club_name = club_get_name_by_id($club_id);
      if (!$club_name) {
        msg_bar_warning_delay("俱乐部不存在！");
        return;
      }
      $default_tab = "i";
    } else {
      $default_tab = "m";
      $club_id = 0;
    }
    $req_tab = request_get("tab", $default_tab);
    $event_id = request_get("eid", NULL);
    if ($event_id) {
      if ($club_id != event_get_club_id($event_id)) {
        $event_id = NULL;
      }
    }
    if ($event_id) {
      $req_action = request_get("act", "v");
    }
    $set = request_get("set", "m");
  }
?>
        <div class="jumbotron">
          <div class="panel panel-default">
<?php
  if ($club_id > 0) {
    if ($public) {
?>
            <div class="panel-heading"><strong>俱乐部&nbsp;>&nbsp;<?php echo $club_name?></strong></div>
<?php
    } else if ($event_id) {
      if ($req_action == "em") {
?>
            <div class="panel-heading"><strong><a href="club.php?tab=m">俱乐部</a>&nbsp;>&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=<?php echo $req_tab?>"><?php echo $club_name?></a>&nbsp;>&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=<?php echo $req_tab?>&eid=<?php echo $event_id?>"><?php echo event_get_start_time_by_id($event_id)?></a>&nbsp;>&nbsp;修改参加名单（人数：<?php echo event_get_attendee_count($event_id)?>）</strong></div>
<?php
      } else if ($req_action == "e") {
?>
            <div class="panel-heading"><strong><a href="club.php?tab=m">俱乐部</a>&nbsp;>&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=<?php echo $req_tab?>"><?php echo $club_name?></a>&nbsp;>&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=<?php echo $req_tab?>&eid=<?php echo $event_id?>"><?php echo event_get_start_time_by_id($event_id)?></a>&nbsp;>&nbsp;编辑活动</strong></div>
<?php
      } else {
?>
            <div class="panel-heading"><strong><a href="club.php?tab=m">俱乐部</a>&nbsp;>&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=<?php echo $req_tab?>"><?php echo $club_name?></a>&nbsp;>&nbsp;<?php echo event_get_start_time_by_id($event_id)?></strong></div>
<?php
      }
    } else if ($req_tab == "a") {
      switch ($set) {
        case "m":
          $add_name = "添加成员";
          break;
        case "e":
          $add_name = "添加活动";
          break;
        case "f":
          $add_name = "添加费用";
          break;
        case "c":
          $add_name = "活动记录转费用";
          break;
        case "i":
          $add_name = "修改俱乐部";
          break;
      }
?>
            <div class="panel-heading"><strong><a href="club.php?tab=m">俱乐部</a>&nbsp;>&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=i"><?php echo $club_name?></a>&nbsp;>&nbsp;<?php echo $add_name?></strong></div>
<?php
    } else {
?>
            <div class="panel-heading"><strong><a href="club.php?tab=m">俱乐部</a>&nbsp;>&nbsp;<?php echo $club_name?></strong></div>
<?php
    } /* of public */
?>
            <div class="panel-body">
            <ul class="nav nav-tabs">
<?php
    if (!$public) {
      if (!$event_id) {
?>
              <li role="presentation" 
                <?php if ($req_tab == "i") { ?>class="active"<?php } ?>>
                <a href="club.php?cid=<?php echo $club_id?>&tab=i">信息</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "m") { ?>class="active"<?php } ?>>
                <a href="club.php?cid=<?php echo $club_id?>&tab=m">成员</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "e") { ?>class="active"<?php } ?>>
                <a href="club.php?cid=<?php echo $club_id?>&tab=e">活动</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "b") { ?>class="active"<?php } ?>>
                <a href="club.php?cid=<?php echo $club_id?>&tab=b">账户</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "sh") { ?>class="active"<?php } ?>>
                <a href="club.php?cid=<?php echo $club_id?>&tab=sh">分享</a></li>
              <li role="presentation" class="dropdown <?php if ($req_tab=='s') { echo 'active';}?>">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                  统计&nbsp;<span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=s&set=e">活动</a></li>
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=s&set=m">成员</a></li>
                </ul>
              </li>
              <li role="presentation" class="dropdown <?php if ($req_tab=='a') { echo 'active';}?>">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                  设置&nbsp;<span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=a&set=f">添加费用</a></li>
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=a&set=m">添加成员</a></li>
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=a&set=e">添加活动</a></li>
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=a&set=c" onclick="return confirm('确定要覆盖之前已有的自动转换费用记录吗？');">活动记录转费用</a></li>
                  <li class="divider"></li>
                  <li><a href="club.php?cid=<?php echo $club_id?>&tab=a&set=i">修改俱乐部</a></li>
                </ul>
              </li>
<?php
      }
    } else {
?>
              <li role="presentation" 
                <?php if ($req_tab == "i") { ?>class="active"<?php } ?>>
                <a href="club_public.php?club=<?php echo $public_uri?>&tab=i">信息</a></li>
              <li role="presentation" class="dropdown <?php if ($req_tab=='s') { echo 'active';}?>">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                  统计&nbsp;<span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="club_public.php?club=<?php echo $public_uri?>&tab=s&set=e">活动</a></li>
                  <li><a href="club_public.php?club=<?php echo $public_uri?>&tab=s&set=m">成员</a></li>
                </ul>
              </li>
<?php
    } /* of public */
?>
            </ul>
            <br />
<?php
    switch ($req_tab) {
      case "i":
        do_page_club_info($club_id);
        break;
      case "m":
        do_page_club_member_list($club_id);
        break;
      case "e":
        if ($event_id) {
          if ($req_action == "e") {
            do_page_club_event_edit($club_id, $event_id);
          } else if ($req_action == "em") {
            do_page_club_event_member_edit($club_id, $event_id);
          } else {
            do_page_club_event_detail($club_id, $event_id);
          }
        } else {
          do_page_club_event_list($club_id);
        }
        break;
      case "s":
        switch ($set) {
          default:
          case "e":
            do_page_club_stat_event($club_id, $public);
            break;
          case "m":
            do_page_club_stat_member($club_id, $public);
            break;
        }
        break;
      case "b":
        do_page_club_balance($club_id);
        break;
      case "sh":
        do_page_club_share($club_id);
        break;
      case "a":
        switch ($set) {
          default:
          case "m":
            do_page_club_add_member($club_id);
            break;
          case "e":
            $user_id = user_get_signin_id();
            $r = facility_get_all($user_id, "id", "a", 0, NULL);
            if ((!$r) || ($r->num_rows == 0)) {
              msg_bar_warning_noclose("添加活动前，请先添加至少一个场所商户！");
            } else {
              do_page_club_add_event($club_id);
            }
            break;
          case "f":
            do_page_club_add_trans($club_id);
            break;
          case "c":
            do_page_club_build_fee($club_id);
            break;
          case "i":
            do_page_club_edit($club_id);
            break;
        }
        break;
      default:
        break;
    }
  } else if (!$public){
?>
            <div class="panel-heading"><strong>俱乐部</strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "m") { ?>class="active"<?php } ?>>
                <a href="club.php?tab=m">我的俱乐部</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "c") { ?>class="active"<?php } ?>>
                <a href="club.php?tab=c">创建新俱乐部</a></li>
            </ul>
            <br />
<?php
    switch ($req_tab) {
      case "m":
        do_page_club_my_list();
        break;
      case "c":
        do_page_club_new();
        break;
      default:
        break;
    }
?>
<?php
  }
?>
            </div>
          </div>
        </div>
<?php
}

function do_page_club_my_list() {
  $user_id = user_get_signin_id();
  $r = club_get_all_by_user_id($user_id);
  do_page_club_my_list_($r);
}

function do_page_club_my_list_($r) {
?>
            <div class="media">
              <ul class="list-group">
<?php
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $club_id = $row['id'];
?>
              <li class="list-group-item">
                <div class="media-left">
                  <a href="club.php?cid=<?php echo $club_id?>">
                    <img class="media-object" src="<?php echo $row['logo_url']?>" width="64" height="64" alt="Logo">
                  </a>
                </div>
                <div class="media-body">
                  <h4 class="media-heading"><a href="club.php?cid=<?php echo $club_id?>"><?php echo $row['name']?></a></h4>
                  <h6>创建于&nbsp;<?php echo $row['created_date']?>&nbsp;&nbsp;更新于&nbsp;<?php echo club_get_updated_date($club_id)?></h6>
                  <h6>成员：&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=m"><?php echo club_get_member_count_by_id($club_id)?></a>人&nbsp;&nbsp;活动：&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=e"><?php echo club_get_event_count_by_id($club_id)?></a>次&nbsp;&nbsp;余额：&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=b"><?php echo club_get_account_balance($club_id)?></a>元</h6>
                </div>
              </li>
<?php
    } /* of for i */
  } /* of r */
?>
              </ul>
            </div>
<?php
}

function do_page_club_new() {
  if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
    unset($_SESSION['name']);
  }
  if (isset($_SESSION['logo_url'])) {
    $logo_url = $_SESSION['logo_url'];
    unset($_SESSION['logo_url']);
  }
  if (isset($_SESSION['club_mail'])) {
    $club_mail = $_SESSION['club_mail'];
    unset($_SESSION['club_mail']);
  }
?>
          <div class="panel panel-default">
            <div class="panel-heading">新俱乐部</div>
            <br />
          <form class="form-horizontal" role="form" method="post"
                action="club_new.php">
            <input name="form_key" type="hidden" class="form-control" value="club_new">
            <div class="form-group">
              <label class="col-sm-3 control-label">名称</label>
              <div class="col-sm-6">
                <input name="name" type="text" class="form-control"
                     placeholder="名称（必填）" aria-describedby="basic-addon1"
                     size="100%" required autofocus value="<?php echo $name?>"
                     maxlength="30">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">LOGO链接</label>
              <div class="col-sm-6">
                <input name="logo_url" type="text" class="form-control"
                     placeholder="LOGO链接（可选）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $logo_url?>"
                     maxlength="200">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="form-group">
              <label class="col-sm-3 control-label">俱乐部抄送电子邮箱</label>
              <div class="col-sm-6">
                <input name="club_mail" type="email" class="form-control"
                     placeholder="俱乐部抄送电子邮箱（可选）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $club_mail?>"
                     maxlength="50">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="form-group">
              <div class="col-sm-3"></div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;新建俱乐部</span></button>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="reset"><span class="glyphicon glyphicon-repeat" aria-hidden="true">&nbsp;重新设定&nbsp;</span></button>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
          </form>
          </div>
<?php
}

function do_page_club_edit($club_id) {
  $name = club_get_name_by_id($club_id);
  $logo_url = club_get_logo_url_by_id($club_id);
  $club_mail = club_get_club_mail_by_id($club_id);
  $admin_member_id = club_get_admin_member_id($club_id);
  if (member_get_user_id_by_id($admin_member_id) != user_get_signin_id()) {
    msg_bar_warning_noclose("您无权修改这个俱乐部！");
    return;
  }
?>
          <div class="panel panel-default">
            <div class="panel-heading"><?php echo $name?></div>
            <br />
          <form class="form-horizontal" role="form" method="post"
                action="club.php?tab=i&cid=<?php echo $club_id?>">
            <input name="form_key" type="hidden" class="form-control" value="club_edit">
            <div class="form-group">
              <input name="club_id" type="hidden" class="form-control" value="<?php echo $club_id?>">
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">名称</label>
              <div class="col-sm-6">
                <input name="name" type="text" class="form-control"
                     placeholder="名称（必填）" aria-describedby="basic-addon1"
                     size="100%" required autofocus value="<?php echo $name?>"
                     maxlength="30">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">LOGO链接</label>
              <div class="col-sm-6">
                <input name="logo_url" type="text" class="form-control"
                     placeholder="LOGO链接（可选）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $logo_url?>"
                     maxlength="200">
              </div>
              <div class="col-sm-3"><img src="<?php echo $logo_url?>" alt="照片" width="30" height="37"></div>
            </div>
            <br />
            <div class="form-group">
              <label class="col-sm-3 control-label">俱乐部抄送电子邮箱</label>
              <div class="col-sm-6">
                <input name="club_mail" type="email" class="form-control"
                     placeholder="俱乐部抄送电子邮箱（可选）" aria-describedby="basic-addon1"
                     size="100%" required value="<?php echo $club_mail?>"
                     maxlength="50">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="form-group">
              <div class="col-sm-4">
                <button class="btn btn-primary btn-block" type="submit">修改俱乐部</button>
              </div>
              <div class="col-sm-4">
                <button class="btn btn-primary btn-block" type="reset">重新设定</button>
              </div>
              <div class="col-sm-4">
<?php
  if ((club_get_event_count_by_id($club_id) > 0) || (club_get_member_count_by_id($club_id) > 1)) {
    $can_del = " disabled";
  } else {
    $can_del = "";
  }
?>
                <a href="club_del.php?cid=<?php echo $club_id?>" class="btn btn-primary btn-block<?php echo $can_del?>" onclick="return confirm('确定删除这个俱乐部吗？');">删除俱乐部</a>
              </div>
            </div>
            <br />
          </form>
          </div>
<?php
}

function do_page_club_share($club_id) {
?>
          <div class="panel panel-default">
            <div class="panel-heading"><strong><?php echo club_get_public_link_by_id($club_id)?></strong></div>
            <br />
            <a href="<?php echo club_get_full_public_url_by_id($club_id)?>">
<?php
            echo generateQRfromGoogle(club_get_full_public_url_by_id($club_id), 200);
?>
            </a>
          </div>
<?php
}

function do_page_club_build_fee($club_id) {
  date_default_timezone_set('PRC');
  $start = date('Y-m-d H:i:s');
  $count = trans_build_from_event_record($club_id);
  $end = date('Y-m-d H:i:s');
  $duration = strtotime($end)-strtotime($start);
  if ($count > 0) {
    msg_bar_success_noclose("根据活动记录创建交易成功！用时".$duration."秒，生成".$count."条交易！");
    log_r("根据活动记录创建交易。用时".$duration."秒，生成".$count."条交易！", "成功！");
  } else {
    msg_bar_warning_noclose("根据活动记录创建交易失败！用时".$duration."秒。");
    log_r("根据活动记录创建交易。用时".$duration."秒。", "失败！");
  }
}

function do_page_club_info($club_id) {
  $name = club_get_name_by_id($club_id);
  $logo_url = club_get_logo_url_by_id($club_id);
  $created_date = club_get_created_date_by_id($club_id);
  $member_count = club_get_member_count_by_id($club_id);
  $admin_member_id = club_get_admin_member_id($club_id);
  $admin_name = member_get_name_by_id($admin_member_id);
?>
          <div class="panel panel-default">
            <div class="panel-heading"><strong>
                <img src="<?php echo $logo_url?>" alt="Logo" width="150"
                  height="150"></strong></div>
            <table class="table table-striped table-bordered">
              <tr>
                <td>名称</td><td><?php echo $name ?></td></tr>
              <tr>
                <td>创建者</td><td><?php echo $admin_name ?></td></tr>
              <tr>
                <td>创建日期</td><td><?php echo $created_date ?></td></tr>
              <tr>
                <td>成员数（含创建者）</td><td><?php echo $member_count ?></td></tr>
              <tr>
                <td>统计报表</td><td><?php echo club_get_public_link_by_id($club_id)?></td></tr>
            </table>
          </div>
<?php
}
