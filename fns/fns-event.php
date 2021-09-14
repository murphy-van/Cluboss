<?php

function event_get_all_same_club($club_id, $orderby, $order, $page, $item_per_page) {
  if ($club_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      if ($orderby == "attendee") {
        $r = db_q("select event.id,event.start_time,event.facility_id,"
        . "count(event.id) from event left join member_event "
        . "on event.id = member_event.event_id where club_id = ".$club_id.
        " group by event.id  order by count(event.id) ".$order." limit ".$off.",".$rows);
      } else {
        $r= db_q("select * from event where club_id = ".$club_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
      }
    } else {
      if ($orderby == "attendee") {
        $r = db_q("select event.id,event.start_time,event.facility_id,"
        . "count(event.id) from event left join member_event "
        . "on event.id = member_event.event_id where club_id = ".$club_id.
        " group by event.id  order by count(event.id) ".$order);
      } else {
        $r= db_q("select * from event where club_id = ".$club_id." order by ".$orderby." ".$order);
      }
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function event_get_count_same_club($club_id) {
  $r = event_get_all_same_club($club_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function event_get_pages_same_club($club_id, $item_per_page) {
  $count = event_get_count_same_club($club_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function event_get_attendee_count($event_id) {
  if ($event_id) {
    $r = db_q("select * from member_event where event_id = ".$event_id);
    return $r->num_rows;
  }
  return 0;
}

function event_get_pay_users_count($event_id) {
  if ($event_id) {
    $count = 0;
    $r = db_q("select * from member_event where event_id = ".$event_id);
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $count += $row['pay_users'];
    }
    return $count;
  }
  return 0;
}

function event_get_id_by_time($event_time) {
  if ($event_time) {
    $r = db_q("select * from event where start_time = ".$event_time);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function date2quarter($date) {
  /* return the quarter like 2012Q3 */
  if ($date) {
    $d = date_parse_from_format("Y-m-d", $date);
    if ($d) {
      switch ($d['month']) {
        default:
        case 1:
        case 2:
        case 3:
          $quarter = "Q1";
          break;
        case 4:
        case 5:
        case 6:
          $quarter = "Q2";
          break;
        case 7:
        case 8:
        case 9:
          $quarter = "Q3";
          break;
        case 10:
        case 11:
        case 12:
          $quarter = "Q4";
          break;
      }
      return $d['year'].$quarter;
    }
  }
  return "";
}

function event_get_member_count_by_2id($first, $last, $club_id) {
  if (($first) && ($last)&&($club_id)) {
    $r = db_q("select * from member_event where event_id in (select id from event where id>=".$first." and id<=".$last." and club_id=".$club_id.") group by member_id");
    if ($r) {
      return $r->num_rows;
    }
  }
  return 0;
}

function event_get_club_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['club_id'];
  }
  return NULL;
}

function event_get_start_time_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['start_time'];
  }
  return NULL;
}

function event_get_duration_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['duration'];
  }
  return NULL;
}

function event_get_facility_id_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['facility_id'];
  }
  return NULL;
}

function event_get_total_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['total'];
  }
  return NULL;
}

function event_get_share_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['share'];
  }
  return NULL;
}

function event_get_notes_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = db_q("select * from event where id =".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['notes'];
  }
  return NULL;
}

function event_set_all_member_session($event_id) {
  if (!$event_id) {
    return false;
  }
  $r = db_q("select * from member_event where event_id=".$event_id);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    for ($i=0;$i<$r->num_rows;$i++) {
      event_set_member_session($member_id, $event_id);
    }
  }
  return true;
}

function event_get_all_member_session($event_id) {
  if (!$event_id) {
    return false;
  }
  $club_id = event_get_club_id($event_id);
  if (!$club_id) {
    return false;
  }
  $user_id = user_get_signin_id();
  $r = member_get_all_same_club($user_id, $club_id, "id", "a", 0, NULL);
  if (!$r) {
    return false;
  }
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $member_id = $row['id'];
    if (event_get_member_session($member_id, $event_id)) {
      if (!event_check_member($event_id, $member_id)) {
        event_add_member($event_id, $member_id);
      }
    } else {
      if (event_check_member($event_id, $member_id)) {
        event_remove_member($event_id, $member_id);
      }
    }
  }
  return true;
}

function event_set_member_session($member_id, $event_id) {
  if (($member_id) && ($event_id)) {
    $_SESSION['member'.$member_id] = $event_id;
  }
}

function event_clear_member_session($member_id) {
  if (($member_id) && ($event_id)) {
    if (isset($_SESSION['member'.$member_id])) {
      unset($_SESSION['member'.$member_id]);
    }
  }
}

function event_get_member_session($member_id, $event_id) {
  if (($member_id) && ($event_id)) {
    if (isset($_SESSION['member'.$member_id])) {
      if ($_SESSION['member'.$member_id] == $event_id) {
        return true;
      }
    }
  }
  return false;
}

function event_add_member($event_id, $member_id) {
  if (($event_id) && ($member_id)) {
    $r = db_q("select * from member_event where event_id=".$event_id." and member_id=".$member_id);
    if ($r->num_rows > 0) {
      $r2 = db_q("update member_event set pay_users=1 where event_id=".$event_id." and member_id=".$member_id);
    } else {
      $r2 = db_q("insert into member_event values (NULL, ".$member_id.", ".$event_id.", 1)");
    }
    if ($r2) {
      return true;
    }
  }
  return false;
}

function event_remove_member($event_id, $member_id) {
  if (($event_id) && ($member_id)) {
    $r = db_q("select * from member_event where event_id=".$event_id." and member_id=".$member_id);
    if ($r->num_rows > 0) {
      $r2 = db_q("delete from member_event where event_id=".$event_id." and member_id=".$member_id);
      if ($r2) {
        return true;
      }
    } 
  }
  return false;
}

function event_check_member($event_id, $member_id) {
  if (($event_id) && ($member_id)) {
    $r = db_q("select * from member_event where event_id=".$event_id." and member_id=".$member_id);
    if ($r->num_rows > 0) {
      return true;
    } 
  }
  return false;
}

function event_update($event_id, $start_time, $duration, $facility_id, $total, $share, $notes) {
  if (($event_id) && ($start_time) && ($duration)) {
    $r = db_q("select * from event where id=".$event_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      if (($start_time == $row['start_time']) && ($duration == $row['duration']) && ($facility_id == $row['facility_id']) && ($total == $row['total']) && ($share == $row['share']) && ($notes == $row['notes'])) {
        return true;
      }
      $r2 = db_q("update event set start_time='".$start_time."',duration=".$duration.",facility_id=".$facility_id.",total=".$total.",share=".$share.",notes='".$notes."' where id=".$event_id);
      if ($r2) {
        return true;
      }
    }
  }
  return false;
}

function event_new() {
  $user_id = user_get_signin_id();
  $tab = request_get("tab", NULL);
  $set = request_get("set", NULL);
  $req_clud_id = request_get("cid", NULL);
  if ((!$user_id) ||(!$tab) || (!$set) || (!$req_clud_id)) {
    return false;
  }
  $club_id = post_get("club_id");
  if (($tab != 'a') || ($set != 'e') || (!$club_id) || ($req_clud_id != $club_id)) {
    return false;
  }
  $event_time = post_get("event_time");
  $duration = post_get("duration");
  $facility = post_get("facility");
  $facility_id = facility_get_id_by_name($user_id, $facility);
  $fee_post = post_get("fee");
  $fee = round(floatval($fee_post), 2);
  if (($fee <= TRANS_FEE_MIN) || ($fee >= TRANS_FEE_MAX)) {
    msg_bar_warning_delay("消费额必须大于".TRANS_FEE_MIN."元，小于".TRANS_FEE_MAX."元");
    return false;
  }
  $fee_type = post_get("fee_type");
  if ($fee_type == "总消费额") {
    $total = $fee;
    $share = 0;
  } else if ($fee_type == "分摊额") {
    $total = 0;
    $share = $fee;
  } else {
    return false;
  }
  $notes = post_get("notes");
  
  $_SESSION['event_time'] = $event_time;
  $_SESSION['duration'] = $duration;
  $_SESSION['facility_id'] = $facility_id;
  $_SESSION['fee'] = $fee_post;
  $_SESSION['fee_type'] = $fee_type;
  $_SESSION['notes'] = $notes;
  
  $event_id = event_add($event_time, $duration, $club_id, $facility_id, $total, $share, $notes);
  if ($event_id) {
    msg_bar_success_delay("新活动(<a href='club.php?cid=".$club_id."&tab=e&eid=".$event_id."'>".$event_id."</a>)添加成功！");
    unset($_SESSION['event_time']);
    unset($_SESSION['duration']);
    unset($_SESSION['facility_id']);
    unset($_SESSION['fee']);
    unset($_SESSION['fee_type']);
    return true;
  }
  msg_bar_error_delay("新活动添加失败！");
  return false;
}

function event_add($event_time, $duration, $club_id, $facility_id, $total, $share, $notes) {
  $op_user_id = user_get_signin_id();
  $op_member_id = user_get_member_id_by_id($op_user_id);
  if ($op_member_id != club_get_admin_member_id($club_id)) {
    msg_bar_warning_delay("您无权添加这个俱乐部的活动！");
  }
  if (!$event_time) {
    msg_bar_warning_delay("活动开始时间不能为空！");
    return NULL;
  }
  if ($facility_id == 0) {
    msg_bar_warning_delay("场所商户未选定！");
    return NULL;
  }
  date_default_timezone_set('PRC');
  $ts = strtotime($event_time);
  if ($ts == FALSE) {
    msg_bar_warning_delay("时间(".$event_time.")不是合法的格式");
    return NULL;
  }
  if (($duration > 24) || ($duration <= 0)) {
    msg_bar_warning_delay("活动时长不能大于24小时或小于等于0");
    return NULL;
  }
  if (!$total) {
    $total = 0;
  }
  if (!$share) {
    $share = 0;
  }
  if ((($total!=0)&&($share!=0)) || (($total==0)&&($share==0))) {
    msg_bar_warning_delay("总消费额和分摊额必须且只能有一项非空！");
    return NULL;
  }
  $r = db_q("select * from event where start_time ='".$event_time."' and club_id=".$club_id);
  if ($r->num_rows > 0) {
    msg_bar_warning_delay("这个时间（".$event_time."）的活动已经存在了！");
    return NULL;
  }
  $event_count = club_get_event_count_by_id($club_id);
  if ($event_count >= user_get_limit("EPC")) {
    msg_bar_warning_delay("已达到用户允许创建的俱乐部活动上限（".user_get_limit("EPC")."）！");
    return NULL;
  }
  $q = "insert into event values (NULL, '".$event_time."', ".$duration.", ".$club_id.", ".$facility_id.", ".$total.", ".$share.", '".$notes."')";
  $r2 = db_q($q);
  if ($r2) {
    $r3 = db_q("select * from event where start_time ='".$event_time."' and duration=".$duration." and club_id=".$club_id." and facility_id=".$facility_id." and total=".$total." and share=".$share." and notes='".$notes."'");
    if ($r3->num_rows > 0) {
      $row = $r3->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function event_del() {
  /* Del a event */
  $event_id = request_get("eid", NULL);
  if (!$event_id) {
    return false;
  }
  $club_id = event_get_club_id($event_id);
  $op_user_id = user_get_signin_id();
  $op_member_id = user_get_member_id_by_id($op_user_id);
  if ($op_member_id != club_get_admin_member_id($club_id)) {
    msg_bar_warning_delay("您无权删除这个俱乐部的活动！");
    return false;
  }
  $attendee = event_get_attendee_count($event_id);
  if ($attendee > 0) {
    msg_bar_warning_delay("请先移除这个活动的参加者，再尝试删除活动！");
    return false;
  }
  /*Remove a event */
  $r = db_q("delete from event where id = ".$event_id);
  if ($r) {
    msg_bar_success_delay("活动".$event_id."删除成功！");
    log_r("删除活动".$event_id, "成功！");
    header("Location:club.php?cid=".$club_id."&tab=e");
    return true;
  } 
  msg_bar_error_delay("活动".$event_id."删除失败！");
  header("Location:club.php?cid=".$club_id."&tab=e&eid=".$event_id."&act=e");
  return false;
}

function event_get_email_by_id($event_id) {
  if (!$event_id) {
    return NULL;
  }
  $r = member_get_all_same_event(user_get_signin_id(), $event_id, "convert(name using gbk)", "a", 0, NULL);
  if ($r) {
    $mail = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      if ($i == 0) {
        $mail .= $row['email'];
      } else {
        $mail .= ";".$row['email'];
      }
    }
    return $mail;
  }
  return NULL;
}

function event_get_count_same_user($user_id) {
  $count = 0;
  $r = club_get_all_by_user_id($user_id);
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $club_id = $row['id'];
      $r2 = event_get_all_same_club($club_id, "id", "a", 0, NULL);
      if ($r2) {
        $count += $r2->num_rows;
      }
    }
  }
  return $count;
}