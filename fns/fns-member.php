<?php

function member_get_all_same_event($user_id, $event_id, $orderby, $order, $page, $item_per_page) {
  if ($event_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r= db_q("select * from member where id in (select member_id from member_event where event_id = ".$event_id.") and user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from member where id in (select member_id from member_event where event_id = ".$event_id.") and user_id=".$user_id." order by ".$orderby." ".$order);
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function member_get_count_same_event($user_id, $event_id) {
  $r = member_get_all_same_event($user_id, $event_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function member_get_pages_same_event($user_id, $event_id, $item_per_page) {
  $count = member_get_count_same_event($user_id, $event_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function member_get_all_name_same_event($user_id, $event_id) {
  $r = member_get_all_same_event($user_id, $event_id, "convert(name using gbk)", "a", 0, NULL);
  if ($r) {
    $names = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $name = $row['name'];
      $member_id = $row['id'];
      $pay_users = member_get_event_pay_users($member_id, $event_id);
      $names .= $name;
      if ($pay_users > 1) {
        $names .= "(+".($pay_users-1).")";
      }
      if ($i < ($r->num_rows-1)) {
        $names .= ",&nbsp;&nbsp;";
      }
      if ((($i+1)%4)==0) {
        $names .= "<br />";
      }
    }
    return $names;
  }
  return NULL;
}

function member_get_all_same_user($user_id, $orderby, $order, $page, $item_per_page) {
  if ($user_id) {
    $user_member_id = user_get_member_id_by_id($user_id);
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r= db_q("select * from member where user_id = ".$user_id." and id != ".$user_member_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from member where user_id = ".$user_id." and id != ".$user_member_id." order by ".$orderby." ".$order);
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function member_get_all_same_user_with_user($user_id, $orderby, $order, $page, $item_per_page) {
  if ($user_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r= db_q("select * from member where user_id = ".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from member where user_id = ".$user_id." order by ".$orderby." ".$order);
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function member_get_count_same_user($user_id) {
  $r = member_get_all_same_user($user_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function member_get_pages_same_user($user_id, $item_per_page) {
  $count = member_get_count_same_user($user_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function member_get_all_diff_club($user_id, $club_id, $orderby, $order, $page, $item_per_page) {
  if ($club_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r= db_q("select * from member where id not in (select member_id from member_club where club_id = ".$club_id." and remove_date is null) and user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from member where id not in (select member_id from member_club where club_id = ".$club_id." and remove_date is null) and user_id=".$user_id." order by ".$orderby." ".$order);
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function member_get_count_diff_club($user_id, $club_id) {
  $r = member_get_all_diff_club($user_id, $club_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function member_get_pages_diff_club($user_id, $club_id, $item_per_page) {
  $count = member_get_count_diff_club($user_id, $club_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function member_get_all_same_club($user_id, $club_id, $orderby, $order, $page, $item_per_page) {
  if ($club_id) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r= db_q("select * from member where id in (select member_id from member_club where club_id = ".$club_id." and remove_date is null) and user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from member where id in (select member_id from member_club where club_id = ".$club_id." and remove_date is null) and user_id=".$user_id." order by ".$orderby." ".$order);
    }
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function member_get_count_same_club($user_id, $club_id) {
  $r = member_get_all_same_club($user_id, $club_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function member_get_pages_same_club($user_id, $club_id, $item_per_page) {
  $count = member_get_count_same_club($user_id, $club_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function member_get_email_same_club($user_id, $club_id) {
  $r = member_get_all_same_club($user_id, $club_id, "id", "a", 0, NULL);
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

function member_get_name_by_id($member_id) {
  if ($member_id) {
    $r = db_q("select * from member where id = ".$member_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['name'];
    }
  }
  return NULL;
}

function member_get_email_by_name($name) {
  if ($name) {
        $r = db_q("select * from member where name = '".$name."'");
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['email'];
    }
  }
  return NULL;
}

function member_get_email_by_id($member_id) {
  if ($member_id) {
        $r = db_q("select * from member where id = ".$member_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['email'];
    }
  }
  return NULL;
}

function member_get_name_by_email($email) {
  if ($email) {
    $r = db_q("select * from member where email = '".$email."'");
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['name'];
    }
  }
  return NULL;
}

function member_get_id_by_email($email) {
  if ($email) {
    $r = db_q("select * from member where email = '".$email."'");
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function member_get_id_by_email_and_user($email, $user_id) {
  if (($email)&&($user_id)) {
    $r = db_q("select * from member where email = '".$email."' and user_id=".$user_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function member_get_id_by_name_and_user($name, $user_id) {
  if (($name)&&($user_id)) {
    $r = db_q("select * from member where name = '".$name."' and user_id=".$user_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function member_get_gender_by_id($member_id) {
  if ($member_id) {
    $r = db_q("select * from member where id = ".$member_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
        return $row['gender'];
    }
  }
  return NULL;
}

function member_get_phone_by_id($member_id) {
  if ($member_id) {
    $r = db_q("select * from member where id = ".$member_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
        return $row['phone'];
    }
  }
  return NULL;
}

function member_get_club_event_count($member_id, $club_id) {
  if (($member_id) && ($club_id)) {
    $r = db_q("select * from member_event where member_id = ".$member_id.
      " and event_id in ( select id from event where club_id = ".$club_id.")");
    return $r->num_rows;
  }
  return 0;
}

function member_get_all_event_count($member_id) {
  if ($member_id) {
    $r = db_q("select * from member_event where member_id = ".$member_id);
    return $r->num_rows;
  }
  return 0;
}

function member_get_user_id_by_id($member_id) {
  if ($member_id) {
    $r = db_q("select * from member where id=".$member_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['user_id'];
    }
  }
}

function member_get_photo_url_by_id($member_id) {
  if ($member_id) {
    $r = db_q("select * from member where id = ".$member_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
        return $row['photo_url'];
    }
  }
  return NULL;
}

function member_get_added_date_by_id($member_id) {
  if ($member_id) {
    $r = db_q("select * from member where id = ".$member_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
        return $row['added_date'];
    }
  }
  return NULL;
}

function member_update_by_id($member_id, $email,  $name, $gender, $phone, $photo_url) {
  if (($member_id) && ($name) && ($gender)) {
    $r = db_q("update member set email = '".$email."', name='".$name."',gender='".$gender."',phone='".$phone."',photo_url='".$photo_url."' where id = ".$member_id);
    if ($r) {
      return true;
    }
  }
  return false;
}

function member_changeinfo() {
  $req_member_id = post_get("member_id");
  if (!$req_member_id) {
    return false;
  }
  $req_email = post_get("email");
  if (!$req_email) {
    $req_email = member_get_email_by_id($req_member_id);
  }
  $req_name = post_get("name");
  if (!$req_name) {
    return false;
  }
  $req_gender = post_get("gender");
  if (!$req_gender) {
    return false;
  }
  $req_phone = post_get("phone");
  $req_photo_url = post_get("photo_url");
  
  $req_user_id = member_get_user_id_by_id($req_member_id);
  $user_id = user_get_signin_id();
  if ($user_id != $req_user_id) {
    if (!is_super_user()) {
      msg_bar_warning_delay("您不能修改其他人的信息/通讯录！");
      return false;
    }
  }
  
  if (!member_update_by_id($req_member_id, $req_email, $req_name, $req_gender, $req_phone, $req_photo_url)) { 
    msg_bar_error_delay("信息/通讯录修改失败！");
    return false;
  }
  
  log_r("修改[".$req_member_id."](".$req_name.")的信息/通讯录", "成功！");
  msg_bar_success_delay("信息/通讯录修改成功！");
  return true;
}

function member_is_in_a_event($member_id, $event_id) {
  if (($member_id) && ($event_id)) {
    $r = db_q("select * from member_event where member_id=".$member_id." and event_id=".$event_id);
    if ($r->num_rows > 0) {
      return true;
    }
  }
  return false;
}

function member_is_in_any_club($member_id) {
  if ($member_id) {
    $r = db_q("select * from member_club where member_id=".$member_id." and remove_date is null");
    if ($r->num_rows > 0) {
      return true;
    }
  }
  return false;
}

function member_is_in_any_event($member_id) {
  if ($member_id) {
    $r = db_q("select * from member_event where member_id=".$member_id);
    if ($r->num_rows > 0) {
      return true;
    }
  }
  return false;
}

function member_get_event_pay_users($member_id, $event_id) {
  if (($member_id) && ($event_id)) {
    $r = db_q("select * from member_event where member_id=".$member_id." and event_id=".$event_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['pay_users'];
    }
  }
  return NULL;
}

function member_remove_from_event($member_id, $event_id) {
  if (($member_id) && ($event_id)) {
    $r = db_q("select * from member_event where member_id=".$member_id." and event_id=".$event_id);
    if ($r->num_rows > 0) {
      $r2 = db_q("delete from member_event where member_id=".$member_id." and event_id=".$event_id);
      if ($r2) {
        return true;
      }
    }
  }
  return false;
}

function member_add_to_event($member_id, $event_id) {
  if (($member_id) && ($event_id)) {
    $r = db_q("select * from member_event where member_id=".$member_id." and event_id=".$event_id);
    if ($r->num_rows == 0) {
      $r2 = db_q("insert into member_event values (NULL, ".$member_id.", ".$event_id.", 1)");
      if ($r2) {
        return true;
      }
    }
  }
  return false;
}

function member_set_event_pay_users($member_id, $event_id, $pay_users) {
  if (($member_id) && ($event_id) && ($pay_users)) {
    $r = db_q("select * from member_event where member_id=".$member_id." and event_id=".$event_id);
    if ($r->num_rows > 0) {
      $r2 = db_q("update member_event set pay_users=".$pay_users." where member_id=".$member_id." and event_id=".$event_id);
      if ($r2) {
        return true;
      }
    }
  }
  return false;
}

function member_get_event_count_same_club($member_id, $club_id, $days) {
  if (($member_id) && ($club_id)) {
    if (!$days) {
      $r = db_q("select * from member_event where member_id=".$member_id." and event_id in (select id from event where club_id=".$club_id.")");
    } else {
      $r = db_q("select * from member_event where member_id=".$member_id." and event_id in (select id from event where club_id=".$club_id." and TIMESTAMPDIFF(DAY, start_time, NOW())<=".$days.")");
    }
    return $r->num_rows;
  }
  return NULL;
}

function member_get_balance_by_club($member_id, $club_id) {
  if (!$member_id) {
    return 0;
  }
  if ($club_id == 0) {
    $r = db_q("select * from trans where member_id=".$member_id);
  } else {
    $r = db_q("select * from trans where member_id=".$member_id." and club_id=".$club_id);
  }
  $withdraw = 0;
  $deposit = 0;
  for ($i;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $withdraw += $row['withdraw'];
    $deposit += $row['deposit'];
  }
  return ($deposit-$withdraw);
}

function member_get_balance_all($member_id) {
  return member_get_balance_by_club($member_id, 0);
}

function member_remove_from_club($user_id, $member_id, $club_id) {
  if ((!$member_id) || (!$club_id)) {
    return false;
  }
  $r = db_q("select * from member_club where member_id=".$member_id." and club_id=".$club_id);
  if ($r->num_rows == 0) {
    return false;
  }
  $r2 = db_q("update member_club set remove_date=NOW() where member_id=".$member_id." and club_id=".$club_id);
  if ($r2) {
    return true;
  }
  return false;
}

function member_add_to_club($user_id, $member_id, $club_id) {
  if ((!$member_id) || (!$club_id)) {
    return false;
  }
  $member_count = club_get_member_count_by_id($club_id);
  if ($member_count >= user_get_limit("MPC")) {
    return false;
  }
  $r = db_q("select * from member_club where member_id=".$member_id." and club_id=".$club_id);
  if ($r->num_rows > 0) {
    $r2 = db_q("update member_club set added_date=NOW(),remove_date=NULL where member_id=".$member_id." and club_id=".$club_id);
    if ($r2) {
      return true;
    }

  } else {
    $r2 = db_q("insert into member_club values (NULL, ".$member_id.", ".$club_id.", NOW(), NULL, '".club_role_name2id("会员")."')");
    if ($r2) {
      return true;
    }
  }
  return false;
}

function member_new() {
  $req_email = post_get("email");
  if (!$req_email) {
    return NULL;
  }
  $req_name = post_get("name");
  if (!$req_name) {
    return NULL;
  }
  $req_gender = post_get("gender");
  if (!$req_gender) {
    return NULL;
  }
  $req_phone = post_get("phone");
  if (!$req_phone) {
    $req_phone = "";
  }
  $req_photo_url = post_get("photo_url");
  if (!$req_photo_url) {
    $req_photo_url = "";
  }
  
  $_SESSION['email'] = $req_email;
  $_SESSION['name'] = $req_name;
  $_SESSION['gender'] = $req_gender;
  $_SESSION['phone'] = $req_phone;
  $_SESSION['photo_url'] = $req_photo_url;

  $member_id = member_add($req_email, $req_name, $req_gender, $req_phone, $req_photo_url);
  if ($member_id) {
    unset($_SESSION['email']);
    unset($_SESSION['name']);
    unset($_SESSION['gender']);
    unset($_SESSION['phone']);
    unset($_SESSION['photo_url']);
    msg_bar_success_delay("添加(".$name.")的通讯录成功！");
    return $member_id;
  }
  msg_bar_error_delay("通讯录添加失败！");
  return NULL;
}

function member_add($email, $name, $gender, $phone, $photo_url) {
  $user_id = user_get_signin_id();
  $member_id = member_get_id_by_email_and_user($email, $user_id);
  if ($member_id) {
    $r = db_q("update member set name='".$name."',gender='".$gender."',phone='".$phone."',photo_url='".$photo_url."' where id=".$member_id);
    if ($r) {
      log_r("更新(".$name.")的通讯录", "成功！");
      return $member_id;
    }
    return NULL;
  }
  $member_count = member_get_count_same_user($user_id);
  if ($member_count >= user_get_limit("APU")) {
    msg_bar_warning_delay("已达到用户允许创建的通讯录条目上限（".user_get_limit("APU")."）！");
    return NULL;
  }
  $r = db_q("insert into member values (NULL, '".$email."', '".$name."', '".$gender."', '".$phone."', '".$photo_url."', NOW(), ".$user_id.")");
  if ($r) { 
    $member_id = member_get_id_by_email_and_user($email, $user_id);
    if ($member_id) {
      log_r("添加(".$name.")的通讯录", "成功！");
      return $member_id;
    }
  }
  return NULL;
}

function member_remove() {
  $act = request_get("act", NULL);
  if ($act) {
    if ($act == "del")  {
      $mid = request_get("mid", NULL);
      if (!$mid) {
        return true;
      }
      $name = member_get_name_by_id($mid);
      if (!$name) {
        msg_bar_warning_delay("该条目不在通讯录中，可能已经在其它页面删除！");
        return true;
      }
      $r = db_q("delete from member where id=".$mid);
      if ($r) {
        msg_bar_success_delay("（".$name.")从通讯录成功移除！");
        log_r("（".$name."）从通讯录移除", "成功！");
      } else {
        msg_bar_error_delay("（".$name."）从通讯录移除失败！");
        log_r("（".$name."）从通讯录移除", "失败！");
      }
      return true;
    }
  }
  return false;
}

function member2vcard($member_id) {
  $user_id = user_get_signin_id();
  $member_user_id = member_get_user_id_by_id($member_id);
  if ($user_id != $member_user_id) {
    return NULL;
  }
  
  $name = member_get_name_by_id($member_id);
  $phone = member_get_phone_by_id($member_id);
  $email = member_get_email_by_id($member_id);

  return "BEGIN:VCARD\n"
  ."VERSION:3.0\n"
  ."FN;CHARSET=gb2312:".$name."\n"
  ."TEL;CELL;VOICE:".$phone."\n"
  ."EMAIL;PREF;INTERNET:".$email."\n"
  ."REV:2015-2-27T08:30:02Z\n"
  ."END:VCARD\n\n";
}

function member2vcard_all() {
  $user_id = user_get_signin_id();
  $r = member_get_all_same_user($user_id, "convert(name using gbk)", "a", 0, NULL);
  for ($i=0;$i<$r->num_rows;$i++) {
    $row = $r->fetch_assoc();
    $member_id = $row['id'];
    $output .= member2vcard($member_id);
  }
  return $output;
}

function member_import() {
  $user_id = user_get_signin_id();
  if (user_is_temp($user_id)) {
    msg_bar_warning_delay("临时用户不能从电子名片文件导入通讯录");
    return NULL;
  }
  $user_member_id = user_get_member_id_by_id($user_id);
  $name_list = $_POST["FN"];  /* filter_input(INPUT_POST, 'FN') does not work here */
  $email_list = $_POST["EMAIL"];
  $phone_list = $_POST["TEL"];
  $post_count = count($name_list);
  for ($i=0;$i<$post_count;$i++) {
    $name = htmlspecialchars($name_list[$i], ENT_NOQUOTES, "UTF-8");
    $email = htmlspecialchars($email_list[$i], ENT_NOQUOTES, "UTF-8");
    $phone = htmlspecialchars($phone_list[$i], ENT_NOQUOTES, "UTF-8");
    if (($name) && ($email)) {
      $member_id = member_get_id_by_name_and_user($name, $user_id);
      if ($member_id == $user_member_id) {
        continue;
      }
      if ($member_id) {
        $r = db_q("update member set email='".$email."',phone='".$phone."' where id=".$member_id);
      } else {
        $member_count = member_get_count_same_user($user_id);
        if ($member_count >= user_get_limit("APU")) {
          msg_bar_warning_delay("已达到用户允许创建的通讯录条目上限（".user_get_limit("APU")."）！已导入".$i."条记录");
          return NULL;
        }
        $r = db_q("insert into member values (NULL, '".$email."', '".$name."', '保密', '".$phone."', '', NOW(), ".$user_id.")");
      }
      if ($r) {
        if ($member_id) {
          log_r("导入(".$name.")覆盖已有联系人", "成功！");
        } else {
          log_r("导入联系人(".$name.")", "成功！");
        }
      } else {
        log_r("导入联系人(".$name.")", "失败！已导入".$i."条记录");
        msg_bar_error_delay("导入联系人(".$name.")失败！已导入".$i."条记录");
      }
    }
  }

  msg_bar_success_delay("导入联系人完成！（数量：".$post_count."）");
  return true;
}