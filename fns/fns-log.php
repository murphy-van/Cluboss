<?php
function _log($user, $fromip, $url, $action, $msg) {
  if ($user) {
    $insert = "insert into log values (NULL, NOW(), '"
      .$user."', '".$fromip."', '".$action."', '".$msg."', '".$url."', '".url2page($url)."')";
    $r = db_q($insert);
    if ($r) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function log_r($action, $msg) {
  $user_id = user_get_signin_id();
  if (!$user_id) {
    $user = "匿名";
  } else {
    $user = "[".$user_id."]".user_get_name_by_id($user_id);
  }
  $fromip .= server_get("REMOTE_ADDR");
  $user .= "@".gethostbyaddr($fromip);
  $url = server_get("REQUEST_URI");
  if ((strstr($url, "/log.php")) || (strstr($url, "/system.php"))) {
    return true;
  }
  if (!$action) {
    $action = "未知动作";
  }
  if (!$msg) {
    $msg = "未知信息";
  }
  return _log($user, $fromip, $url, $action, $msg);
}

function log_page() {
  return log_r("打开网页", "成功！");
}

function log_get_all($orderby, $order, $page, $item_per_page, $opt) {
  if ($order == 'd') {
    $order = "desc";
  } else {
    $order = "asc";
  }
  if ($page > 0) {
    $off = (($page-1)*$item_per_page);
    $rows = $item_per_page;
    if ($opt == "nopageopen") {
      $r = db_q("select * from log where action != '打开网页' order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r = db_q("select * from log order by ".$orderby." ".$order." limit ".$off.",".$rows);
    }
  } else {
    if ($opt == "nopageopen") {
      $r = db_q("select * from log where action != '打开网页' order by ".$orderby." ".$order);
    } else {
      $r = db_q("select * from log order by ".$orderby." ".$order);
    }
  }
  return $r;
}

function log_get_count($opt) {
  $r = log_get_all("id", "a", 0, NULL, $opt);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function log_get_pages($item_per_page, $opt) {
  $count = log_get_count($opt);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function log_get_top10_ip() {
  return db_q("select fromip, count(fromip) from log group by fromip order by count(fromip) desc limit 10");
}

function log_get_top10_user() {
  return db_q("select user, count(user) from log group by user order by count(user) desc limit 10");
}

function log_get_top10_url() {
  return db_q("select url, count(url) from log group by url order by count(url) desc limit 10");
}

function log_get_top10_page() {
  return db_q("select page, count(page) from log group by page order by count(page) desc limit 10");
}

function log_get_time_count($interval, $seq) {

  switch ($interval) {
    case LOG_SECOND:
      $early = "date_sub(now(), interval '0 0:0:".$seq."' day_second)";
      $late = "date_sub(now(), interval '0 0:0:".($seq-1)."' day_second)";
      break;
    case LOG_MINUTE:
      $early = "date_sub(now(), interval '0 0:".$seq.":0' day_second)";
      $late = "date_sub(now(), interval '0 0:".($seq-1).":0' day_second)";
      break;
    case LOG_HOUR:
      $early = "date_sub(now(), interval '0 ".$seq.":0:0' day_second)";
      $late = "date_sub(now(), interval '0 ".($seq-1).":0:0' day_second)";
      break;
    case LOG_DAY:
      $early = "date_sub(now(), interval '".$seq." 0:0:0' day_second)";
      $late = "date_sub(now(), interval '".($seq-1)." 0:0:0' day_second)";
      break;
    case LOG_WEEK:
      $early = "date_sub(now(), interval '".($seq*7)." 0:0:0' day_second)";
      $late = "date_sub(now(), interval '".(($seq-1)*7)." 0:0:0' day_second)";
      break;
    case LOG_MONTH:
      $early = "date_sub(now(), interval '".($seq*30)." 0:0:0' day_second)";
      $late = "date_sub(now(), interval '".(($seq-1)*30)." 0:0:0' day_second)";
      break;
    case LOG_YEAR:
      $early = "date_sub(now(), interval '".($seq*365)." 0:0:0' day_second)";
      $late = "date_sub(now(), interval '".(($seq-1)*365)." 0:0:0' day_second)";
      break;
  }
  $r = db_q("select count(log_time) from log where log_time > ".$early." and log_time < ".$late);
  if ($r->num_rows) {
    $row = $r->fetch_assoc();
    return $row['count(log_time)'];
  }
  return 0;
}

function url2page($url) {
  if (!$url) {
    return NULL;
  }
  $full_len = strlen($url);
  $q = strstr($url, "?");
  if (!$q) {
    return $url;
  } else {
    $second_len = strlen($q);
    if ($full_len > $second_len) {
      $first_len = $full_len - $second_len;
      return substr($url, 0, $first_len);
    }
  }
  return NULL;
}

function log_remove_all() {
  if (!is_super_user()) {
    return false;
  }
  $r = db_q("delete from log");
  if ($r) {
    log_r("清除所有的日志", "成功！");
    return true;
  } else {
    return false;
  }
}