<?php

function club_role_id2name($role_id) {
  if (!$role_id) {
    return NULL;
  }
  $club_role = array(1=>'创建者', 2=>'会员');
  return $club_role[$role_id];
}

function club_role_name2id($role_name) {
  if (!$role_name) {
    return NULL;
  }
  $club_role = array("创建者"=>1, "会员"=>2);
  return $club_role[$role_name];
}

function club_get_all_by_user_id($user_id) {
  $member_id = user_get_member_id_by_id($user_id);
  if (!$member_id) {
    return NULL;
  }
  $creater_id = club_role_name2id("创建者");
  $r = db_q("select * from club where id in (select club_id from member_club where member_id =".$member_id." and role_id=".$creater_id.")");
  return $r;
}

function club_get_name_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from club where id=".$club_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['name'];
  }
  return NULL;
}

function club_get_public_uri_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from club where id=".$club_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['public_uri'];
  }
  return NULL;
}

function club_get_full_public_url_by_id($club_id) {
  return BASE_URL.club_get_public_url_by_id($club_id);
}

function club_get_public_url_by_id($club_id) {
  return "/club_public.php?club=".club_get_public_uri_by_id($club_id);
}

function club_get_public_link_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $url = club_get_full_public_url_by_id($club_id);
  $url = "<a href='".$url."'>".  club_get_name_by_id($club_id)."俱乐部统计报表（免登录）</a>";
  return $url;
}

function club_get_id_by_public_uri($public_uri) {
  if (!$public_uri) {
    return NULL;
  }
  $r = db_q("select * from club where public_uri='".$public_uri."'");
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['id'];
  }
  return NULL;
}

function club_get_id_by_name_and_admin($name, $user_id) {
  if (!$name) {
    return NULL;
  }
  $user_member_id = user_get_member_id_by_id($user_id);
  $role_id = club_role_name2id("创建者");
  $r = db_q("select * from club where name='".$name."' and id in (select club_id from member_club where member_id=".$user_member_id." and role_id=".$role_id.")");
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['id'];
  }
  return NULL;
}

function club_edit() {
  club_edit_member();
  club_edit_member_pay();
  $form_key = post_get("form_key");
  club_edit_event($form_key);
  club_edit_info($form_key);
  return false;
}

function club_edit_member() {
  $act = request_get("act", NULL);
  if (!$act) {
    return;
  }
  if (($act == "del") || ($act == "add")) {
    $mid = request_get("mid", NULL);
    $cid = request_get("cid", NULL);
    if ((!$mid) || (!$cid)) {
      return false;
    }
    $name = member_get_name_by_id($mid);
    $club_name = club_get_name_by_id($cid);
  }
  $user_id = user_get_signin_id();
  if ($act == "del") {
    if ((member_get_balance_by_club($mid, $cid) > 0) || ($mid == club_get_admin_member_id($cid))) {
      return false;
    }
    if (member_remove_from_club($user_id, $mid, $cid)) {
      msg_bar_success_delay("成员（".$name.")从俱乐部（".$club_name.")移除！");
      log_r("成员（".$name."）从俱乐部（".$club_name.")移除", "成功！");
      return true;
    } else {
      msg_bar_warning_delay("成员（".$name."）从俱乐部（".$club_name.")移除失败！");
      log_r("成员（".$name."）从俱乐部（".$club_name.")移除", "失败！");
      return false;
    }
  }
  if ($act == "add") {
    if (member_add_to_club($user_id, $mid, $cid)) {
      msg_bar_success_delay("成员（".$name."）添加到俱乐部（".$club_name.")成功！");
      log_r("成员（".$name."）添加到俱乐部（".$club_name.")", "成功！");
      return true;
    } else {
      msg_bar_info_delay("成员（".$name."）添加到俱乐部（".$club_name.")失败！");
      log_r("成员（".$name."）添加到俱乐部（".$club_name.")", "失败！");
      return true;
    }
  }
}

function club_edit_member_pay() {
  $edit = request_get("edit", NULL);
  if (!$edit) {
    return false;
  }
  $mid = request_get("mid", NULL);
  $eid = request_get("eid", NULL);
  $cid = request_get("cid", NULL);
  $act = request_get("act", NULL);
  if ((!$mid) || (!$eid) || (!$cid) || (!$act)) {
    return false;
  }
  if ($act != "em") {
    return false;
  }
  if (event_get_club_id($eid) != $cid) {
    return false;
  }
  $name = member_get_name_by_id($mid);
  switch ($edit) {
    case "minus":
      if (member_is_in_a_event($mid, $eid)) {
        $pay_users = member_get_event_pay_users($mid, $eid);
        if ($pay_users == 1) {
          if (member_remove_from_event($mid, $eid)) {
            msg_bar_success_delay("参加者(".$name.")从活动（".$eid.")移除！");
            log_r("参加者(".$name.")从活动（".$eid.")移除！", "成功！");
            return true;
          }
        } else if ($pay_users > 1){
          $pay_users--;
          if (member_set_event_pay_users($mid, $eid, $pay_users)) {
            msg_bar_success_delay("参加者(".$name.")在活动（".$eid.")中付费人数减少为".$pay_users."！");
            log_r("参加者(".$name.")在活动（".$eid.")中付费人数减少为".$pay_users."！", "成功！");
            return true;
          }
        }
      }
      break;
    case "plus":
      if (member_is_in_a_event($mid, $eid)) {
        $pay_users = member_get_event_pay_users($mid, $eid);
        if (member_set_event_pay_users($mid, $eid, $pay_users+1)) {
            msg_bar_success_delay("参加者(".$name.")在活动（".$eid.")中付费人数增加为".$pay_users."！");
            log_r("参加者(".$name.")在活动（".$eid.")中付费人数增加为".$pay_users."！", "成功！");
            return true;
         }
      } else {
        if (member_add_to_event($mid, $eid)) {
            msg_bar_success_delay("参加者(".$name.")添加到活动（".$eid.")中！");
            log_r("参加者(".$name.")添加到活动（".$eid.")中！", "成功！");
            return true;
        }
      }
      break;
  }
}

function club_edit_event($form_key) {
  $event_id = post_get("event_id");
  if (($event_id) && ($form_key == "event_edit"))  {
    $start_time = post_get("start_time");
    $duration = post_get("duration");
    $facility = post_get("facility");
    $facility_id = facility_get_id_by_name(user_get_signin_id(), $facility);
    $fee_type = post_get("fee_type");
    $fee = round(floatval(post_get("fee")), 2);
    $notes = post_get("notes");
    if (($fee <= TRANS_FEE_MIN) || ($fee >= TRANS_FEE_MAX)) {
      msg_bar_warning_delay("消费额必须大于".TRANS_FEE_MIN."元，小于".TRANS_FEE_MAX."元");
      return false;
    }
    if ($fee_type == "总消费额") {
      $total = $fee;
      $share = 0;
    } else {
      $total = 0;
      $share = $fee;
    }
    if (($duration > 24) || ($duration <= 0)) {
      msg_bar_warning_delay("活动时长不能大于24小时或小于等于0");
      return false;
    }
    if (event_update($event_id, $start_time, $duration, $facility_id, $total, $share, $notes)) {
      msg_bar_success_delay("活动(".$event_id.")修改成功！");
      log_r("修改活动(".$event_id.")", "成功！");
      return true;
    } else {
      msg_bar_info_delay("活动(".$event_id.")修改失败！");
      log_r("修改活动(".$event_id.")", "失败！");
      return false;
    }
  }
}

function club_edit_info($form_key) {
  if ($form_key == "club_edit") {
    $club_id = post_get("club_id");
    if (!$club_id) {
      return false;
    }
    $oldname = club_get_name_by_id($club_id);
    if (!$oldname) {
      return false;
    }
    $name = post_get("name");
    if (!$name) {
      return false;
    }
    $exist_club_id = club_get_id_by_name_and_admin($name, user_get_signin_id());
    if (($exist_club_id) && ($exist_club_id != $club_id)) {
      $_SESSION['club_edit_info_fail'] = "俱乐部名称（".$name.")已被使用，编辑失败！";
      return false;
    }
    $logo_url = post_get("logo_url");
    if (!$logo_url) {
      $logo_url = "";
    }
    $club_mail = post_get("club_mail");
    if (!$club_mail) {
      $club_mail = "";
    }
    $r = db_q("update club set name='".$name."', logo_url='".$logo_url."', club_mail='".$club_mail."' where id=".$club_id);
    if ($r) {
      msg_bar_success_delay("俱乐部（".$name.")编辑成功！");
      log_r("俱乐部（".$name.")编辑", "成功！");
      return true;
    } else {
      msg_bar_error_delay("俱乐部（".$name.")编辑失败！");
      return false;
    }
  }
}

function club_mail() {
  $mail = request_get("mail", NULL);
  if ($mail) {
    set_time_limit(1800);
    switch ($mail) {
      case "trans":
      case "balance":
      case "report90":
      case "report365":
        club_mail_trans_or_report($mail);
        break;
      case "lowbalance":
        club_mail_low_balance($mail);
        break;
      case "event":
        club_mail_event($mail);
        break;
    }
  }
  return false;
}

function club_mail2type($mail) {
  switch ($mail) {
    case "trans":
      $type = "对账";
      break;
    case "report90":
      $type = "季报";
      break;
    case "report365":
      $type = "年报";
      break;
  }
  return $type;
}

function club_mail_trans_or_report($mail) {
  $member_id = request_get("mid", NULL);
  $page = request_get("page", 0);
  $club_id = request_get("cid", NULL);
  if (!club_mail_permission($club_id)) {
    return false;
  }

  $type = club_mail2type($mail);
  if ($member_id) {
    if (club_mail_send_($club_id, $member_id, $mail)) {
      msg_bar_success_delay(member_get_name_by_id($member_id)."的".$type."邮件发送成功！");
      log_r("发送".member_get_name_by_id($member_id)."的".$type."邮件", "成功！");
    }
  } else if ($page == 0) {
    if (club_mail_send_($club_id, 0, $mail)) {
      msg_bar_success_delay("俱乐部管理员的".$type."邮件发送成功！");
      log_r("发送俱乐部管理员的".$type."邮件", "成功！");
    }
  } else {
    date_default_timezone_set('PRC');
    $start = date('Y-m-d H:i:s');
    $r = member_get_all_same_club(user_get_signin_id(), $club_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $member_id = $row['id'];
      if (!club_mail_send_($club_id, $member_id, $mail)) {
        break;
      }
    }
    $end = date('Y-m-d H:i:s');
    $duration = strtotime($end)-strtotime($start);
    if ($i==$r->num_rows) {
      msg_bar_success_delay("群发第".$page."页成员的".$type."邮件发送成功！共发送".$i."封。用时".$duration."秒！");
      log_r("群发第".$page."页的".$type."邮件！共发送".$i."封。用时".$duration."秒", "成功！");
    } else {
      msg_bar_error_delay("群发第".$page."页的".$type."邮件发送失败！共发送".$i."封。用时".$duration."秒！");
      log_r("群发第".$page."页的".$type."邮件！共发送".$i."封。用时".$duration."秒", "失败！");
    }
  }
}

function club_mail_low_balance($mail) {
  $member_id = request_get("mid", NULL);
  $club_id = request_get("cid", NULL);
  if (!club_mail_permission($club_id)) {
    return false;
  }
  $name = member_get_name_by_id($member_id);
  if (club_mail_send_($club_id, $member_id, $mail)) {
    msg_bar_success_delay($name."的充值提醒邮件发送成功！");
    log_r("发送".$name."的充值提醒邮件", "成功！");
  } else {
    msg_bar_error_delay($name."的充值提醒邮件发送失败！");
    log_r("发送".$name."的充值提醒邮件", "失败！");
  }
}

function club_mail_event($mail) {
  $event_id = request_get("eid", NULL);
  if (!$event_id) {
    return false;
  }
  $club_id = request_get("cid", NULL);
  if (!club_mail_permission($club_id)) {
    return false;
  }
  if (event_get_club_id($event_id) != $club_id) {
    return false;
  }
  $event_date = event_get_start_time_by_id($event_id);
  if (club_mail_send_($club_id, NULL, $mail)) {
    msg_bar_success_delay($event_date."（".$event_id."）活动的确认邮件发送成功！");
    log_r("发送".$event_date."（".$event_id."）活动的确认邮件", "成功！");
  } else {
    msg_bar_error_delay($event_date."（".$event_id."）活动的确认邮件发送失败！");
    log_r("发送".$event_date."（".$event_id."）活动的确认邮件", "失败！");
  }
}

function club_mail_permission($club_id) {
  if (!$club_id) {
    return false;
  }
  $op_user_id = user_get_signin_id();
  $op_member_id = user_get_member_id_by_id($op_user_id);
  if ($op_member_id != club_get_admin_member_id($club_id)) {
    msg_bar_warning_delay("您无权在这个俱乐部发送邮件！");
    return false;
  }
  if (user_is_temp($op_user_id)) {
    msg_bar_warning_delay("临时用户无法发送邮件！请注册后重试！");
    return false;
  }
  return true;
}

function club_mail_send_($club_id, $member_id, $mail) {
  $account_info = do_page_club_balance_($club_id, $member_id, true);
  $admin_email = club_get_admin_email($club_id);
  $club_name = club_get_name_by_id($club_id);
  $club_public_link = club_get_public_link_by_id($club_id);
  switch ($mail) {
    case "trans":
      return club_mail_send_trans($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link);
    case "balance":
      return club_mail_send_balance($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link);
    case "report90":
      return club_mail_send_report($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link, 90);
    case "report365":
      return club_mail_send_report($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link, 365);
    case "lowbalance":
      return club_mail_send_low_balance($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link);
    case "event":
      return club_mail_send_event($club_id, $club_name, $admin_email, $club_public_link);
  }
  return NULL;
}

function club_mail_send_trans($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link) {
  $r = trans_get_all($club_id, $member_id, "trans_date", "d", 0, NULL);
  $account_trans = do_page_club_trans_list_($r, "trans_date", "d", NULL, true);
  $body = get_mail_trans(club_mail_get_name($club_id, $member_id), $account_info, $account_trans, $club_public_link, $club_name, $admin_email);
  return club_mail_send($club_id, $member_id, NULL, $club_name."-对账单", $body);
}

function club_mail_send_balance($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link) {
  $r = club_get_balance_all($club_id, "mid", "a", 0, NULL);
  $account_balance = do_page_club_balance_all_($r, "trans_date", "d", NULL, $club_id);
  $body = get_mail_balance(club_mail_get_name($club_id, $member_id), $account_info, $account_balance, $club_public_link, $club_name, $admin_email);
  return club_mail_send($club_id, $member_id, NULL, "会费总账单 Club Account Balance", $body);
}

function club_mail_send_low_balance($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link) {
  $body = get_mail_low_balance(member_get_name_by_id($member_id), $account_info, $club_public_link, $club_name, $admin_email);
  return club_mail_send($club_id, $member_id, NULL, $club_name."-充值提醒", $body);
}

function club_mail_send_report($club_id, $club_name, $member_id, $account_info, $admin_email, $club_public_link, $days) {
  $report = club_member_get_report($club_id, $member_id, $days);
  $body = get_mail_report(member_get_name_by_id($member_id), $account_info, $club_public_link, $club_name, $admin_email, $report);
  return club_mail_send($club_id, $member_id, NULL, $club_name."-".$days."天定期报告", $body);
}

function club_mail_send_event($club_id, $club_name, $admin_email, $club_public_link) {
  $event_id = request_get("eid", NULL);
  $event_info = do_page_club_event_detail_($event_id, true);
  $body = get_mail_event_confirm($event_info, $club_public_link, $club_name, $admin_email);
  return club_mail_send($club_id, NULL, $event_id, $club_name."-活动确认", $body);
}

function club_mail_get_mail_to($club_id, $member_id, $event_id) {
  if ($member_id) {
    $email = member_get_email_by_id($member_id);
  } else if ($event_id) {
    $email = event_get_email_by_id($event_id);
  } else {
    $admin_member_id = club_get_admin_member_id($club_id);
    $email = member_get_email_by_id($admin_member_id);
  }

  if (MAIL_DEBUG) {
    $email = MAIL_DEBUG;
  }
  return $email;
}

function club_mail_get_name($club_id, $member_id) {
  if ($member_id) {
    $name = member_get_name_by_id($member_id);
  } else {
    $name = club_get_name_by_id($club_id);
  }
  return $name;
}

function club_mail_get_mail_cc($club_id) {
  $admin_email = club_get_admin_email($club_id);
  $mailcc = $admin_email;
  $club_mail = club_get_club_mail_by_id($club_id);
  if ($club_mail) {
    $mailcc .= ";".$club_mail;
  }
  if (MAIL_DEBUG) {
    $mailcc = MAIL_DEBUG;
  }
  return $mailcc;
}

function club_mail_send($club_id, $member_id, $event_id, $subject, $body) {
  $mailto = club_mail_get_mail_to($club_id, $member_id, $event_id);
  $mailcc = club_mail_get_mail_cc($club_id);
  return send_mail($mailto, $mailcc, $subject, $body);
}

function club_get_logo_url_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from club where id=".$club_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['logo_url'];
  }
  return NULL;
}

function club_get_club_mail_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from club where id=".$club_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['club_mail'];
  }
  return NULL;
}

function club_get_created_date_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from club where id=".$club_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['created_date'];
  }
  return NULL;
}

function club_get_member_count_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from member_club where club_id=".$club_id." and remove_date is null");
  return $r->num_rows;
}

function club_get_event_count_by_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select * from event where club_id=".$club_id);
  return $r->num_rows;
}

function club_get_admin_member_id($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $admin_role_id = club_role_name2id("创建者");
  $r = db_q("select * from member_club where club_id=".$club_id." and role_id=".$admin_role_id);
  if ($r->num_rows > 0) {
    $row = $r->fetch_assoc();
    return $row['member_id'];
  }
  return NULL;
}

function club_get_admin_email($club_id) {
  $admin_member_id = club_get_admin_member_id($club_id);
  $admin_email = member_get_email_by_id($admin_member_id);
  if (user_email_is_ip($admin_email)) {
    return NULL;
  } else {
    return $admin_email;
  }
}

function club_new() {
  $form_key = post_get("form_key");
  if ($form_key != "club_new") {
    return NULL;
  }
  $name = post_get("name");
  if (!$name) {
    return NULL;
  }
  $logo_url = post_get("logo_url");
  if (!$logo_url) {
    $logo_url = "";
  }
  $club_mail = post_get("club_mail");
  if (!$club_mail) {
    $club_mail = "";
  }
  
  $_SESSION['name'] = $name;
  $_SESSION['logo_url'] = $logo_url;
  $_SESSION['club_mail'] = $club_mail;

  $club_id = club_add($name, NULL, $logo_url, $club_mail);
  if ($club_id) {
    msg_bar_success_delay("新建俱乐部(".$name.")成功！");
    unset($_SESSION['name']);
    unset($_SESSION['logo_url']);
    unset($_SESSION['club_mail']);
    return $club_id;
  }
  msg_bar_error_delay("新建俱乐部失败！");
  return NULL;
}

function club_add($name, $created_date, $logo_url, $club_mail) {
  $user_id = user_get_signin_id();
  $club_count = user_get_club_count_by_id($user_id);
  if ($club_count >= user_get_limit("CPU")) {
    msg_bar_warning_delay("已达到用户允许创建的俱乐部上限（".user_get_limit("CPU")."）！");
    return NULL;
  }
  
  $club_id = club_get_id_by_name_and_admin($name, $user_id);
  if ($club_id) {
    msg_bar_warning_delay("俱乐部名称（".$name.")已被使用，新建失败！");
    return NULL;
  }
  $public_uri_times = 0;
  do {
    $public_uri = random_pwd(10, 15);
    $id = club_get_id_by_public_uri($public_uri);
    $public_uri_times++;
    if (!$id) {
      break;
    }
    $public_uri = NULL;
  }while ($public_uri_times < MAX_PUBLIC_URI_RETRY);
  if (!$public_uri) {
    msg_bar_error_delay("新建俱乐部公用链接生成失败！");
    return NULL;
  }
  if (!$created_date) {
    $created_date = "NOW()";
  } else {
    $created_date = "'".$created_date."'";
  }
  $r = db_q("insert into club values (NULL, '".$name."', '".$logo_url."', ".$created_date.", '".$public_uri."', '".$club_mail."')");
  if ($r) { 
    $club_id = club_get_id_by_public_uri($public_uri);
    if ($club_id) {
      $user_member_id = user_get_member_id_by_id($user_id);
      $role_id = club_role_name2id("创建者");
      $r2 = db_q("insert into member_club values (NULL, ".$user_member_id.", ".$club_id.", NOW(), NULL, ".$role_id.")");
      if ($r2) {
        log_r("新建俱乐部(".$name.")", "成功！");
        return $club_id;
      } else {
        db_q("delete from club where id=".$club_id);
      }
    }
  }
  log_r("新建俱乐部(".$name.")", "失败！");
  return NULL;
}

function club_get_top10_member_by_attendee_count($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select member.name,count(member.name) from member left join member_event on member.id=member_event.member_id where member_event.event_id in (select id from event where club_id=".$club_id.") and member.id in (select member_id from member_club where club_id=".$club_id." and remove_date is null) group by member.name order by count(member.name) desc limit 0, 10");
  return $r;
}

function club_get_all_member_by_attendee_count($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = db_q("select member.name,count(member.name) from member left join member_event on member.id=member_event.member_id where member_event.event_id in (select id from event where club_id=".$club_id.") and member.id in (select member_id from member_club where club_id=".$club_id." and remove_date is null) group by member.name order by count(member.name) desc");
  return $r;
}

function club_get_updated_date($club_id) {
  if (!$club_id) {
    return NULL;
  }
  $r = event_get_all_same_club($club_id, "start_time", "d", 0, NULL);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['start_time'];
  } else {
    return club_get_created_date_by_id($club_id);
  }
}

function club_member_get_report($club_id, $member_id, $days) {
  $event_count = member_get_event_count_same_club($member_id, $club_id, $days);
  $meet_member = club_get_event_member_count_by_member_id_and_days($club_id, $member_id, $days);
  $withdraw = trans_get_withdraw_all_by_club($club_id, $member_id, $days);
  $active_percentage = club_get_member_active_percentage_by_member_id_and_days($club_id, $member_id, $days, $event_count);
  $report = "".
    "<p>您在过去的<strong>".$days."</strong>天里</p>\n".
    "<p>......参加了<strong>".$event_count."</strong>次活动......</p>\n".
    "<p>......和<strong>".$meet_member."</strong>个不同的朋友见过面......</p>\n".
    "<p>......消费了<strong>".$withdraw."</strong>元人民币......</p>\n".
    "<p>......参与度超过了<strong>".$active_percentage."%</strong>的其他朋友......</p>\n";
  if ($active_percentage <= 40) {
    $report .= "<p>总是不来参加活动，朋友们都想您了！</p>\n";
  } else if ($active_percentage <= 60) {
    $report .= "<p>还记得上次的活动么，一起来吧！</p>\n";
  } else if ($active_percentage <= 80) {
    $report .= "<p>饭要多吃，身体要多动，朋友要多见！</p>\n";
  } else if ($active_percentage <= 90) {
    $report .= "<p>偶尔缺席几次以后一定要补回来哦！</p>\n";
  } else {
    $report .= "<p>感谢您的积极参与！</p>\n";
  }
  return $report;
}

function club_event_get_confirm($club_id, $event_id) {
  
}

function club_get_event_member_count_by_member_id_and_days($club_id, $member_id, $days) {
  if ((!$club_id) || (!$member_id) || (!$days)) {
    return NULL;
  }
  $r = db_q("select * from member_event where event_id in (select event_id from member_event where member_id=".$member_id." and event_id in (select id from event where club_id=".$club_id." and TIMESTAMPDIFF(DAY, start_time, NOW())<=".$days.")) group by member_id");
  if ($r) {
    return $r->num_rows;
  }
  return NULL;
}

function club_get_member_active_percentage_by_member_id_and_days($club_id, $member_id, $days, $event_count) {
  if ((!$club_id) || (!$member_id) || (!$days) || (!$event_count)) {
    return 0;
  }
  $total_member = club_get_member_count_by_id($club_id);
  $r = db_q("select * from (select member_id,count(event_id) c from member_event where event_id in (select id from event where club_id=".$club_id." and TIMESTAMPDIFF(DAY, start_time, NOW())<=".$days.") group by member_id) as q1 where c>".$event_count);
  if (!$r) {
    return 0;
  }
  return round(($total_member-$r->num_rows)*100/$total_member, 2);
}

function club_del() {
  $club_id = request_get("cid", NULL);
  if (!$club_id) {
    return false;
  }
  $name = club_get_name_by_id($club_id);
  $admin_member_id = club_get_admin_member_id($club_id);
  if (member_get_user_id_by_id($admin_member_id) != user_get_signin_id()) {
    msg_bar_warning_delay("您无权删除这个俱乐部（".$name."）！");
    return false;
  }
  if ((club_get_event_count_by_id($club_id) > 0) || (club_get_member_count_by_id($club_id) > 1)) {
    msg_bar_info_delay("请先删除俱乐部（".$name."）里面的活动和成员，再删除俱乐部！");
    return false;
  }
  $r = db_q("delete from member_club where member_id=".$admin_member_id." and club_id=".$club_id);
  if ($r) {
    $r2 = db_q("delete from club where id=".$club_id);
  } else {
    $r2 = NULL;
  }
  if ($r2) {
    msg_bar_success_delay("成功删除俱乐部（".$name."）！");
    log_r("删除俱乐部（".$name."）", "成功！");
    return true;
  } else {
    msg_bar_error_delay("俱乐部（".$name."）删除失败！");
    log_r("删除俱乐部（".$name."）", "失败！");
    return false;
  }
}

function club_get_count_same_user($user_id) {
  $r = club_get_all_by_user_id($user_id);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function club_get_account_balance($club_id) {
  $r = trans_get_all($club_id, 0, "id", "a", 0, NULL);
  $balance = 0;
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $withdraw = $row['withdraw'];
    $deposit = $row['deposit'];
    $balance += $deposit-$withdraw;
  }
  return $balance;
}

function club_get_balance_all($club_id, $orderby, $order, $page, $item_per_page) {
  if ($club_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r= db_q("select * from (select member.id as mid,t.s1 as ts1,t.s2 as ts2,(t.s1-t.s2) as ba from (select member_id,sum(deposit) as s1,sum(withdraw) as s2 from trans where club_id=".$club_id." group by member_id) as t left join member on member.id=t.member_id) as s where ba !=0 order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from (select member.id as mid,t.s1 as ts1,t.s2 as ts2,(t.s1-t.s2) as ba from (select member_id,sum(deposit) as s1,sum(withdraw) as s2 from trans where club_id=".$club_id." group by member_id) as t left join member on member.id=t.member_id) as s where ba !=0 order by ".$orderby." ".$order);
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function club_get_balance_all_count($club_id) {
  $r = club_get_balance_all($club_id, "mid", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function club_get_balance_pages_all($club_id, $item_per_page) {
  $count = club_get_balance_all_count($club_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}