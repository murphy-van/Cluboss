<?php
function trans_build_from_event_record($club_id) {
  $op_user_id = 0;
  if ($club_id == 0) {
    $r = db_q("delete from trans where type_id = 1 and autogen=true and user_id = 0");
    $r2 = db_q("select * from member_event");
  } else {
    
    $r = db_q("delete from trans where type_id = 1 and autogen=true and user_id = 0 and club_id=".$club_id);
    $r2 = db_q("select * from member_event where event_id in (select id from event where club_id=".$club_id.")");
  }
  $count = 0;
  for ($i=0;$i<$r2->num_rows;$i++) {
    $row = $r2->fetch_assoc();
    $member_id = $row['member_id'];
    $event_id = $row['event_id'];
    $pay_users = $row['pay_users'];
    $total = event_get_total_by_id($event_id);
    $pay_users_total = event_get_pay_users_count($event_id);
    if ($total != 0) {
      $share = round($total/$pay_users_total, 2);
    } else {
      $share = event_get_share_by_id($event_id);
    }
    $club_id = event_get_club_id($event_id);
    $date_event = event_get_start_time_by_id($event_id);
    if (($member_id) && ($share) && ($pay_users) && 
      ($event_id) && ($club_id) && ($date_event)) {
      if (db_q("insert into trans values (NULL, '".$date_event."', ".$member_id.", ".$op_user_id.", ".$share*$pay_users.", 0, 1, ".$event_id.", ".$club_id.", '".TRANS_NOTES_ATTEND_EVENT."', true)")) {
        $count++;
      }
    }
  }
  return $count;
}

function trans_get_first_autogen($club_id) {
  if ($club_id == 0) {
    $r = db_q("select * from trans where type_id=1 and autogen=true and user_id=0");
  } else {
    $r = db_q("select * from trans where type_id=1 and autogen=true and user_id=0 and club_id=".$club_id);
  }
  if (($r)&&($r->num_rows)) {
    $row = $r->fetch_assoc();
    return $row['id'];
  }
  return NULL;
}

function trans_get_deposit_all_by_club($club_id, $member_id) {
  if (!$club_id) {
    return NULL;
  }
  $deposit_all = 0;
  if ($member_id) {
    $r = db_q("select * from trans where club_id=".$club_id." and member_id=".$member_id);
  } else {
    $r = db_q("select * from trans where club_id=".$club_id);
  }
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $deposit_all += $row['deposit'];
  }
  return $deposit_all;
}


function trans_get_withdraw_all_by_club($club_id, $member_id, $days) {
  if (!$club_id) {
    return NULL;
  }
  $withdraw_all = 0;
  if ($member_id) {
    if (!$days) {
      $r = db_q("select * from trans where club_id=".$club_id." and member_id=".$member_id);
    } else {
      $r = db_q("select * from trans where club_id=".$club_id." and member_id=".$member_id." and TIMESTAMPDIFF(DAY, trans_date, NOW())<=".$days);
    }
  } else {
    if (!$days) {
      $r = db_q("select * from trans where club_id=".$club_id);
    } else {
      $r = db_q("select * from trans where club_id=".$club_id." and TIMESTAMPDIFF(DAY, trans_date, NOW())<=".$days);
    }
  }
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $withdraw_all += $row['withdraw'];
  }
  return $withdraw_all;
}

function trans_get_withdraw_count_by_club($club_id, $member_id) {
  if (!$club_id) {
    return NULL;
  }
  $withdraw_count = 0;
  if ($member_id) {
    $r = db_q("select * from trans where club_id=".$club_id." and member_id=".$member_id);
  } else {
    $r = db_q("select * from trans where club_id=".$club_id);
  }
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    if ($row['withdraw'] > 0) {
      $withdraw_count++;
    }
  }
  return $withdraw_count;
}

function trans_get_deposit_count_by_club($club_id, $member_id) {
  if (!$club_id) {
    return NULL;
  }
  $deposit_count = 0;
  if ($member_id) {
    $r = db_q("select * from trans where club_id=".$club_id." and member_id=".$member_id);
  } else {
    $r = db_q("select * from trans where club_id=".$club_id);
  }
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    if ($row['deposit'] > 0) {
      $deposit_count++;
    }
  }
  return $deposit_count;
}

function trans_type_id2name($type_id) {
  if (!$type_id) {
    return NULL;
  }
  $trans_type = array(
    1=>"活动",
    2=>"充值",
    3=>"退费",
    4=>"调整",
    5=>"其它");
  return $trans_type[$type_id];
}

function trans_type_name2id($type_name) {
  if (!$type_name) {
    return NULL;
  }
  $trans_type = array(
    "活动"=>1,
    "充值"=>2,
    "退费"=>3,
    "调整"=>4,
    "其它"=>5);
  return $trans_type[$type_name];
}

function trans_get_all($club_id, $member_id, $orderby, $order, $page, $item_per_page) {
  if ($club_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      if ($member_id == 0) {
        $r= db_q("select * from trans where club_id = ".$club_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
      } else {
        $r= db_q("select * from trans where club_id = ".$club_id." and member_id =".$member_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
      }
    } else {
      if ($member_id == 0) {
        $r= db_q("select * from trans where club_id = ".$club_id." order by ".$orderby." ".$order);
      } else {
        $r= db_q("select * from trans where club_id = ".$club_id." and member_id =".$member_id." order by ".$orderby." ".$order);
      }
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function trans_get_count_all($club_id, $member_id) {
  $r = trans_get_all($club_id, $member_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function trans_get_pages_all($club_id, $member_id, $item_per_page) {
  $count = trans_get_count_all($club_id, $member_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function trans_type_get_all() {
  $trans_type = array(
    1=>"活动",
    2=>"充值",
    3=>"退费",
    4=>"调整",
    5=>"其它");
  return $trans_type;
}

function trans_new() {
  $form_key = post_get("form_key");
  if ($form_key != "trans_new") {
    return false;
  }
  $trans_date = post_get("trans_date");
  $member_id = post_get("member_id");
  $withdraw = post_get("withdraw");
  $deposit = post_get("deposit");
  $notes = post_get("notes");
  $event_id = post_get("event_id");
  $club_id = post_get("club_id");
  $type = post_get("type");
  $type_id = trans_type_name2id($type);

  $_SESSION['trans_date'] = $trans_date;
  $_SESSION['member_id'] = $member_id;
  $_SESSION['withdraw'] = $withdraw;
  $_SESSION['deposit'] = $deposit;
  $_SESSION['notes'] = $notes;
  $_SESSION['type_id'] = $type_id;
  $_SESSION['event_id'] = $event_id;

  $op_user_id = user_get_signin_id();
  $op_member_id = user_get_member_id_by_id($op_user_id);
  if ($op_member_id != club_get_admin_member_id($club_id)) {
    msg_bar_warning_delay("您无权添加这个俱乐部的交易！");
  }
  if (!$trans_date) {
    msg_bar_warning_delay("交易日期不能为空！");
    return false;
  }
  if (!$member_id) {
    msg_bar_warning_delay("成员号不能为空！");
    return false;
  }

  if ($withdraw == NULL) {
    $withdraw = 0;
  } else {
    $withdraw = floatval($withdraw);
  }
  if ($deposit == NULL) {
    $deposit = 0;
  } else {
    $deposit = floatval($deposit);
  }
  if ($event_id == NULL) {
    $event_id = 0;
  }
  if ((($withdraw == 0) && ($deposit == 0)) || (($withdraw != 0) && ($deposit != 0))) {
    msg_bar_warning_delay("支出和存入至少且必须填入一项！");
    return false;
  }

  if ($type_id == 0) {
    msg_bar_warning_delay("交易类型错误！");
    return false;
  } else if ($type == "活动") {
    if ($withdraw == 0) {
      msg_bar_warning_delay("参加活动必须有支出！");
      return false;
    }
    if ($event_id == 0) {
      msg_bar_warning_delay("参加活动必须指明活动编号！");
      return false;
    }
  } else if ($type == "充值") {
    if ($deposit == 0) {
      msg_bar_warning_delay("充值必须有存入！");
      return false;
    }
    $event_id = 0;
    $_SESSION['event_id'] = 0;
  } else if ($type == "退费") {
    if ($withdraw == 0) {
      msg_bar_warning_delay("退费必须有支出！");
      return false;
    }
    $event_id = 0;
    $_SESSION['event_id'] = 0;
  }

  if (($event_id != 0) && (event_get_club_id($event_id) != $club_id)) {
    msg_bar_warning_delay("输入的活动号无效！");
    return false;
  }
  date_default_timezone_set('PRC');
  $ts = strtotime($trans_date);
  if ($ts == FALSE) {
    msg_bar_warning_delay("时间(".$trans_date.")不是合法的格式");
    return false;
  }

  if (trans_add($club_id, $member_id, $trans_date, $withdraw, $deposit, $event_id, $type_id, $notes, false)) {
    msg_bar_success_delay("新交易添加成功！");
    unset($_SESSION['trans_date']);
    unset($_SESSION['member_id']);
    unset($_SESSION['withdraw']);
    unset($_SESSION['deposit']);
    unset($_SESSION['notes']);
    unset($_SESSION['type_id']);
    unset($_SESSION['event_id']);
    return true;
  }
  msg_bar_error_delay("新交易添加失败(".db_error().")");
  return false;
}

function trans_add($club_id, $member_id, $trans_date, $withdraw, $deposit, $event_id, $type_id, $notes, $autogen) {
  if ((!$club_id) || (!$member_id) || (!$trans_date)) {
    return false;
  }
  if ($withdraw == NULL) {
    $withdraw = 0;
  } else {
    $withdraw = floatval($withdraw);
  }
  if ($deposit == NULL) {
    $deposit = 0;
  } else {
    $deposit = floatval($deposit);
  }
  if ($event_id == NULL) {
    $event_id = 0;
  }
  if ((($withdraw == 0) && ($deposit == 0)) || (($withdraw != 0) && ($deposit != 0))) {
    return false;
  }
  if ($type_id == 0) {
    return false;
  } else if ($type_id == trans_type_name2id("活动")) {
    if ($withdraw == 0) {
      return false;
    }
    if ($event_id == 0) {
      return false;
    }
  } else if ($type_id == trans_type_name2id("充值")) {
    if ($deposit == 0) {
      return false;
    }
    $event_id = 0;
  } else if ($type_id == trans_type_name2id("退费")) {
    if ($withdraw == 0) {
      return false;
    }
    $event_id = 0;
  }

  if (($event_id != 0) && (event_get_club_id($event_id) != $club_id)) {
    return false;
  }
  date_default_timezone_set('PRC');
  $ts = strtotime($trans_date);
  if ($ts == FALSE) {
    return false;
  }
  if ($autogen) {
    $autogen = 'true';
  } else {
    $autogen = 'false';
  }
  $r = db_query("insert into trans values (NULL, '".$trans_date."', ".$member_id.
    ", ".user_get_signin_id().", ".$withdraw.", ".$deposit.", ".$type_id.", ".$event_id.
    ", ".$club_id.", '".$notes."', $autogen)");
  if ($r) {
    return true;
  } 
  return false;
}

function trans_remove_autogen($club_id, $member_id, $event_id) {
  $type = trans_type_name2id("活动");
  $trans_id = trans_get_autogen_id($club_id, $member_id, $event_id, $type, true);
  return trans_remove($trans_id);
}

function trans_update_autogen_withdraw($club_id, $member_id, $event_id, $withdraw) {
  $type = trans_type_name2id("活动");
  $trans_id = trans_get_autogen_id($club_id, $member_id, $event_id, $type, true);
  return trans_update_withdraw($trans_id, $withdraw);
}

function trans_get_autogen_id($club_id, $member_id, $event_id, $type, $autogen) {
  if ((!$club_id) || (!$member_id) || (!$event_id) || (!$type) || (!$autogen)) {
    return NULL;
  }
  $r = db_q("select * from trans where club_id=".$club_id." and member_id=".$member_id." and event_id=".$event_id." and type=".$type." and autogen=".$autogen);
  if (($r) && ($r->num_rows)) {
    $row = $r->fetch_assoc();
    return $row['id'];
  } else {
    return NULL;
  }
}

function trans_remove($trans_id) {
  if ($trans_id) {
    $r = db_q("delete from trans where id=".$trans_id);
    if ($r) {
      return true;
    }
  }
  return false;
}

function trans_update_withdraw($trans_id, $withdraw) {
  if (($trans_id) && ($withdraw)) {
    $r = db_q("update trans set withdraw=".$withdraw." where id=".$trans_id);
    if ($r) {
      return true;
    }
  }
  return false;
}

function trans_get_count_same_user($user_id) {
  $count = 0;
  $r = club_get_all_by_user_id($user_id);
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $club_id = $row['id'];
      $count += trans_get_count_all($club_id, 0);
    }
  }
  return $count;
}