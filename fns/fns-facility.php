<?php
function facility_get_by_id($user_id, $facility_id) {
  if (($user_id)&&($facility_id)) {
    $r = db_q("select * from facility where id = ".$facility_id." and user_id=".$user_id);
    if ($r->num_rows > 0) {
      return $r;
    }
  }
  return NULL;
}

function facility_get_name_by_id($user_id, $facility_id) {
  $r = facility_get_by_id($user_id, $facility_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['name'];
  }
  return NULL;
}

function facility_get_country_by_id($user_id, $facility_id) {
  $r = facility_get_by_id($user_id, $facility_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['country'];
  }
  return NULL;
}

function facility_get_city_by_id($user_id, $facility_id) {
  $r = facility_get_by_id($user_id, $facility_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['city'];
  }
  return NULL;
}

function facility_get_address_by_id($user_id, $facility_id) {
  $r = facility_get_by_id($user_id, $facility_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['address'];
  }
  return NULL;
}

function facility_get_phone_by_id($user_id, $facility_id) {
  $r = facility_get_by_id($user_id, $facility_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['phone'];
  }
  return NULL;
}

function facility_get_id_by_name($user_id, $facility_name) {
  if (($user_id)&&($facility_name)) {
    $r = db_q("select * from facility where name = '".$facility_name."' and user_id=".$user_id);
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function facility_get_event_count($user_id, $facility_id) {
  if (($user_id)&&($facility_id)) {
    $r = db_q("select * from event where facility_id in (select id from facility where id=".$facility_id." and user_id=".$user_id.")");
    if ($r) {
      return $r->num_rows;
    }
  }
  return 0;
}

function facility_get_all($user_id, $orderby, $order, $page, $item_per_page) {
  if ($order == 'd') {
    $order = "desc";
  } else {
    $order = "asc";
  }
  if ($page > 0) {
    $off = (($page-1)*$item_per_page);
    $rows = $item_per_page;
    if ($orderby == "event_count") {
      $r = db_q("select facility.id,facility.name,facility.address,count(facility.id),facility.user_id from facility left join event on facility.id = event.facility_id where facility.user_id=".$user_id." group by facility.id order by count(facility.id) ".$order." limit ".$off.",".$rows);
    } else {
      $r= db_q("select * from facility where user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    }
  } else {
    if ($orderby == "event_count") {
      $r = db_q("select facility.id,facility.name,facility.address,count(facility.id),facility.user_id from facility left join event on facility.id = event.facility_id where facility.user_id=".$user_id." group by facility.id order by count(facility.id) ".$order);
    } else {
      $r= db_q("select * from facility where user_id=".$user_id." order by ".$orderby." ".$order);
    }
  }
  if (($r)&&($r->num_rows > 0)) {
    return $r;
  }
  return NULL;
}

function facility_get_count($user_id) {
  $r = facility_get_all($user_id, "id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function facility_get_pages($user_id, $item_per_page) {
  $count = facility_get_count($user_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function facility_new() {
  $req_name = post_get("name");
  if (!$req_name) {
    return NULL;
  }
  $req_address = post_get("address");
  if (!$req_address) {
    $req_address = "";
  }
  $req_phone = post_get("phone");
  if (!$req_phone) {
    $req_phone = "";
  }
  
  $_SESSION['name'] = $req_name;
  $_SESSION['address'] = $req_address;
  $_SESSION['phone'] = $req_phone;

  $facility_id = facility_add($req_name, $req_address, $req_phone);
  if ($facility_id) {
    unset($_SESSION['name']);
    unset($_SESSION['address']);
    unset($_SESSION['phone']);
    msg_bar_success_delay("添加新的场所商户(".$req_name.")成功！");
    return $facility_id;
  }
  msg_bar_error_delay("场所商户添加失败！");
  return NULL;
}

function facility_add($name, $address, $phone) {
  $user_id = user_get_signin_id();
  $facility_id = facility_get_id_by_name($user_id, $name);
  if ($facility_id) {
    msg_bar_warning_delay("此场所商户已经存在");
    return NULL;
  }
  $facility_count = facility_get_count($user_id);
  if ($facility_count >= user_get_limit("FPU")) {
    msg_bar_warning_delay("已达到用户允许创建的场所商户上限（".user_get_limit("FPU")."）！");
    return NULL;
  }
  $r = db_q("insert into facility values (NULL, '".$name."', '".$address."', '".$phone."', ".$user_id.")");
  if ($r) { 
    $facility_id = facility_get_id_by_name($user_id, $name);
    if ($facility_id) {
      log_r("添加新的场所商户(".$name.")", "成功！");
      return $facility_id;
    }
  }
  return NULL;
}

function facility_edit($req_facility_id) {
  $user_id = user_get_signin_id();
  if (!facility_get_name_by_id($user_id, $req_facility_id)) {
    return NULL;
  }
  $req_name = post_get("name");
  if (!$req_name) {
    return NULL;
  }
  $fid = facility_get_id_by_name($user_id, $req_name);
  if (($fid) && ($fid != $req_facility_id)) {
    msg_bar_warning_delay("此名称已经被其它场所商户使用！");
    $_SESSION['name'] = $req_name;
    return NULL;
  }
  $req_address = post_get("address");
  if (!$req_address) {
    $req_address = "";
  }
  $req_phone = post_get("phone");
  if (!$req_phone) {
    $req_phone = "";
  }
  
    $r = db_q("update facility set name='".$req_name."',address='".$req_address."',phone='".$req_phone."' where id=".$req_facility_id);
  if (!$r) { 
    msg_bar_error_delay("场所商户修改失败！");
    $_SESSION['name'] = $req_name;
    return NULL;
  }
  
  log_r("修改场所商户[".$req_facility_id."](".$req_name.")", "成功！");
  msg_bar_success_delay("场所商户修改成功！");
  return $req_facility_id;
}

function facility_del($user_id, $facility_id) {
  if ($facility_id) {
    $name = facility_get_name_by_id($user_id, $facility_id);
    $r = db_q("delete from facility where id=".$facility_id." and user_id=".$user_id);
    if ($r) {
      log_r("修改场所商户[".$facility_id."](".$name.")", "成功！");
      return true;
    }
  }
  return false;
}