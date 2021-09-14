<?php
function user_get_signin_id() {
  /* Check if a valid user logged in already */
  if (isset($_SESSION['valid_email'])) {
    if (isset($_SESSION['valid_email_ts'])) {
      date_default_timezone_set('PRC');
      $now = date('Y-m-d H:i:s');
      if ((strtotime($now) - strtotime($_SESSION['valid_email_ts'])) > SIGNIN_TIMEOUT_SECOND) {
        unset($_SESSION['valid_email']);
        unset($_SESSION['valid_email_ts']);
        return NULL;
      } else {
        $_SESSION['valid_email_ts'] = $now;
      }
    }
    $valid_email = $_SESSION['valid_email'];
    return user_get_id_by_email($valid_email);
  } else if ((isset($_COOKIE['email']))&&(isset($_COOKIE['pwd']))){
    $email = $_COOKIE['email'];
    $pwd = $_COOKIE['pwd'];
    try {
      login_from_cookie($email, $pwd);
      $_SESSION['valid_email'] = $email;
      date_default_timezone_set('PRC');
      $_SESSION['valid_email_ts'] = date('Y-m-d H:i:s');
      return user_get_id_by_email($email);
    }
    catch(Exception $e) {
      return user_get_temp_signin_id();
    }
  } else {
    return user_get_temp_signin_id();
  }
  return NULL;
}

function user_get_temp_signin_id() {
  $user_id = user_get_id_by_email(server_get("REMOTE_ADDR"));
  if ($user_id) {
    $last_signin_time = user_get_last_signin_time_by_id($user_id);
    date_default_timezone_set('PRC');
    $now = date('Y-m-d H:i:s');
    if (($last_signin_time)&&((strtotime($now) - strtotime($last_signin_time)) > TEMP_USER_TIMEOUT_SECOND)) {
      user_remove_all($user_id, false);
      msg_bar_info_delay("临时用户超时退出！");
      log_r("临时用户(".$email.")超时退出", "成功！");
      return NULL;
    } else {
      user_set_last_signin_time_by_id($user_id);
    }
    return $user_id;
  }
  return NULL;
}

function user_signin() {
  $email = post_get("email");
  if ($email) {
    user_set_recent($email);
    if (post_get("remember-me")) {
      user_set_remember(true);
    } else {
      user_set_remember(false);
    }
  }
  $pwd = post_get("passwd");
  $post_vercode = post_get("vercode");
  if (isset($_SESSION['vercode'])) {
    $sess_vercode = $_SESSION['vercode'];
    unset($_SESSION['vercode']);
  } else {
    $sess_vercode = NULL;
  }
  if (($post_vercode) && ($sess_vercode) && ($post_vercode != $sess_vercode)) {
    $_SESSION['failed_email'] = $email;
    msg_bar_error_delay("验证码输入错误!");
    return false;
  }
  if (($email) && ($pwd)) {
    $_SESSION['signin_email'] = $email;
    try {
      log_r("(".$email.")登录", "尝试...");
      login($email, $pwd);
      $_SESSION['valid_email'] = $email;
      date_default_timezone_set('PRC');
      $_SESSION['valid_email_ts'] = date('Y-m-d H:i:s');
      if (user_get_remember()) {
        setcookie("email", $email, time()+3600*24*7);
        $salt = user_get_salt_by_id(user_get_id_by_email($email));
        setcookie("pwd", sha1($salt.$pwd), time()+3600*24*7);
      }
      log_r("(".$email.")登录", "成功！");
      $user_id = user_get_id_by_email($email);
      user_signin_count_add_by_id($user_id);
      user_set_last_signin_ip_by_id($user_id, server_get("REMOTE_ADDR"));
      user_set_last_signin_time_by_id($user_id);
      user_clear_token($user_id);
      return true;
    }
    catch(Exception $e) {
      log_r("(".$email.")登录", "失败！");
      $_SESSION['failed_email'] = $email;
      msg_bar_error_delay("登录失败!请确认您登录的电子邮箱已经注册和输入正确的密码");
      return false;
    }
  } else {
    return false;
  }
}

function user_signout() {
  if (isset($_SESSION['valid_email'])) {
    $old_email = $_SESSION['valid_email'];
  }

  if (!empty($old_email)) {
    /* if they were logged in and are now logged out */
    log_r("(".$old_email.")退出登录", "成功！");
    unset($_SESSION['valid_email']);
    if (isset($_COOKIE['email'])) {
      setcookie("email", "", time()-3600);
    }
    if (isset($_COOKIE['pwd'])) {
      setcookie("pwd", "", time()-3600);
    }
    return true;
  } else {
    $user_id = user_get_signin_id();
    if (user_is_temp($user_id)){
      $email = user_get_mail_by_id($user_id);
      user_remove_all($user_id, false);
      log_r("临时用户(".$email.")退出登录", "成功！");
      return true;
    } else {
      /* if they weren't logged in but came to this page somehow */
      log_r("(".$old_email.")退出登录", "失败！");
      unset($_SESSION['valid_email']);
      return false;
    }
  }
}

function login($email, $password) {
  /* Check E-Mail and Password with Database */
  /* If correct, return true */
  /* else throw exception */

  /* Check if information is correct */
  $user_id = user_get_id_by_email($email);
  $salt = user_get_salt_by_id($user_id);
  $result = db_q("select * from user where email = '".$email.
                          "' and passwd = sha1('".$salt.$password.
                          "')");

  if ($result->num_rows > 0) {
    return true;
  } else {
    throw new Exception('登录失败');
  }
}

function login_from_cookie($email, $password) {
  /* Check E-Mail and Password with Database */
  /* If correct, return true */
  /* else throw exception */

  /* Check if information is correct */
  $result = db_q("select * from user where email = '".$email.
                          "' and passwd = '".$password."'");

  if ($result->num_rows > 0) {
    return true;
  } else {
    throw new Exception('登录失败');
  }
}

function user_set_token($user_id, $token) {
  if ((!$user_id) || (!$token)) {
    return false;
  }
  $r = db_q("update user set token='".$token."',token_time=NOW() where id=".$user_id);
  if ($r) {
    return true;
  }
}

function user_clear_token($user_id) {
  if (!$user_id) {
    return false;
  }
  $r = db_q("update user set token=NULL and token_time=NULL where id=".$user_id);
  if ($r) {
    return true;
  }
}

function user_get_token($user_id) {
  if (!$user_id) {
    return NULL;
  }
  $r = db_q("select * from user where id=".$user_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['token'];
  }
  return NULL;
}

function user_get_token_time($user_id) {
  if (!$user_id) {
    return NULL;
  }
  $r = db_q("select * from user where id=".$user_id);
  if ($r) {
    $row = $r->fetch_assoc();
    return $row['token_time'];
  }
  return NULL;
}


function user_set_recent($email) {
  $_SESSION['recent_user'] = $email;
}

function user_get_recent() {
  if (isset($_SESSION['recent_user'])) {
    return $_SESSION['recent_user'];
  }
  return NULL;
}

function user_set_remember($rem) {
    $_SESSION['remember_me'] = $rem;
}

function user_get_remember() {
  if (isset($_SESSION['remember_me'])) {
    return $_SESSION['remember_me'];
  }
  return NULL;
}

function user_name2id ($name) {
  if ($name) {
    $email = member_get_email_by_name($name);
    if ($email) {
      return user_get_id_by_email($email);
    }
  }
  return 0;
}

function user_get_member_id_by_id($user_id) {
  if ($user_id) {
    $email = user_get_mail_by_id($user_id);
    $r = db_q("select * from member where user_id=".$user_id." and email='".$email."'");
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return 0;
}

function user_get_mail_by_id($user_id) {
  if ($user_id) {
    $result = db_q("select * from user where id = ".$user_id);
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      return $row['email'];
    }
  }
  return "";
}

function user_get_id_by_email($email) {
  if ($email) {
    $r = db_q("select * from user where email='".$email."'");
    if ($r->num_rows > 0) {
      $row = $r->fetch_assoc();
      return $row['id'];
    }
  }
  return NULL;
}

function user_changepwd() {
  $req_user_id = post_get("user_id");
  if (!$req_user_id) {
    return false;
  }
  $old_pwd = post_get("old_pwd");
  if (!$old_pwd) {
    return false;
  }
  $new_pwd = post_get("new_pwd");
  if (!$new_pwd) {
    return false;
  }
  $new_pwd2 = post_get("new_pwd2");
  if (!$new_pwd2) {
    return false;
  }  
  if ($new_pwd != $new_pwd2) {
    msg_bar_warning_delay("两个新密码不匹配！");
    return false;
  }
  $user_id = user_get_signin_id();
  if ($user_id != $req_user_id) {
    if (!is_super_user()) {
      msg_bar_warning_delay("您不能修改其他人的密码！");
      return false;
    }
  }
  $salt = user_get_salt_by_id($req_user_id);
  $r1 = db_q("select * from user where id = ".$req_user_id.
    " and (passwd = sha1('".$salt.$old_pwd."'))");
  if ($r1->num_rows == 0) {
    msg_bar_warning_delay("旧密码输入错误！");
    return false;
  }
  
  if (strlen($new_pwd) <= MIN_PWD_LEN) {
    msg_bar_warning_delay("密码长度必须大于".MIN_PWD_LEN);
    return false;
  }
  
  $r2 = db_q("update user set passwd = sha1('".$salt.$new_pwd."') where id = "
    .$req_user_id);
  if (!$r2) {
    msg_bar_error_delay("密码修改失败！");
    return false;
  }
  log_r("修改登录密码", "成功！");
  msg_pop_info("密码修改成功！");
  return true;
}

function user_set_pwd($user_id, $new_pwd) {
  if (($user_id) && ($new_pwd)) {
    $salt = user_get_salt_by_id($user_id);
    $result = db_q("update user set passwd = sha1('".$salt.$new_pwd."') where id = ".$user_id);
    if ($result) {
      return true;
    }
  }
  return false;
}

function user_get_name_by_id($user_id) {
  if ($user_id) {
    $member_id = user_get_member_id_by_id($user_id);
    return member_get_name_by_id($member_id);
  }
  return NULL;
}

function is_super_user() {
  $user_id = user_get_signin_id();
  if ($user_id) {
    $q = "select * from user where id = ".$user_id;
    $r = db_q($q);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['super'];
    }
  }
  return false;
}

function user_get_name_by_email($email) {
  return member_get_name_by_email($email);
}

function user_get_signin_count_by_id($user_id) {
  if ($user_id) {
    $r = db_q("select * from user where id=".$user_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['signin_count'];
    }
  }
  return 0;
}

function user_signin_count_add_by_id($user_id) {
  if ($user_id) {
    $r = db_q("select * from user where id=".$user_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      $count = $row['signin_count'];
      $count++;
      $r2 = db_q("update user set signin_count=".$count." where id=".$user_id);
      if ($r2) {
        return true;
      }
    }
  }
  return false;
}

function user_get_last_signin_time_by_id($user_id) {
  if ($user_id) {
    $r = db_q("select * from user where id=".$user_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['last_signin_time'];
    }
  }
  return NULL;
}

function user_get_last_signin_ip_by_id($user_id) {
  if ($user_id) {
    $r = db_q("select * from user where id=".$user_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['last_signin_ip'];
    }
  }
  return NULL;
}

function user_set_last_signin_time_by_id($user_id) {
  if ($user_id) {
    $r = db_q("update user set last_signin_time = NOW() where id=".$user_id);
    if ($r) {
      return true;
    }
  }
  return false;
} 

function user_set_last_signin_ip_by_id($user_id, $ip) {
  if ($user_id) {
    $r = db_q("update user set last_signin_ip = '".$ip."' where id=".$user_id);
    if ($r) {
      return true;
    }
  }
  return false;  
}

function user_get_register_date_by_id($user_id) {
  if ($user_id) {
    $r = db_q("select * from user where id=".$user_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['register_date'];
    }
  }
  return NULL;
}

function user_set_salt_by_id($user_id, $salt) {
  if (($user_id) && ($salt)) {
    $r = db_q("update user set salt='".$salt."' where id=".$user_id);
    if ($r) {
      return true;
    }
  }
  return false;
}

function user_get_salt_by_id($user_id) {
  if ($user_id) {
    $r = db_q("select * from user where id=".$user_id);
    if ($r->num_rows) {
      $row = $r->fetch_assoc();
      return $row['salt'];
    }
  }
  return NULL;
}

function user_register() {
  $email = post_get("email");
  if (!$email) {
    return false;
  }
  $name = post_get("name");
  if (!$name) {
    return false;
  }
  $phone = post_get("phone");
  $gender = post_get("gender");
  $photo_url = post_get("photo_url");
  $_SESSION['reg_email'] = $email;
  $_SESSION['reg_name'] = $name;
  $_SESSION['reg_phone'] = $phone;
  $_SESSION['reg_gender'] = $gender;
  $_SESSION['reg_photo_url'] = $photo_url;
  $post_vercode = post_get("vercode");

  if (($post_vercode) && isset($_SESSION['vercode'])) {
    if ($post_vercode != $_SESSION['vercode']) {
      msg_bar_error_delay("验证码输入错误！");
      return false;
    }
  } else {
    return;
  }
  
  if (user_get_id_by_email($email)) {
    msg_bar_warning_delay("此电子邮箱已经注册！如果您忘记了密码，请在“忘记密码”功能中重置密码！");
    return false;
  }
  
  $user_id = user_get_signin_id();
  if (user_is_temp($user_id)) {
    $r = user_change_from_temp($user_id, $email, $name, $gender, $phone, $photo_url);
  } else {
    $r = user_add($email, $name, $gender, $phone, $photo_url);
  }
  if (!$r) {
    log_r("新用户(".$email.")注册","失败！");
    msg_bar_error_delay("新用户注册失败！");
    return false;
  } else {
    log_r("新用户(".$email.")注册","成功！");
    msg_bar_success_delay("新用户注册成功！密码链接已经发送到您的注册电子邮箱里。");
    return true;
  }
}

function user_register_temp() {
  $email = server_get("REMOTE_ADDR");
  if (!$email) {
    return false;
  }
  $exist_user_id = user_get_id_by_email($email);
  if ($exist_user_id) {
    user_remove_all($exist_user_id, false);
  }
  $name = $email;
  $phone = "";
  $gender = "保密";
  $photo_url = "";
  
  if (!user_add($email, $name, $gender, $phone, $photo_url)) {
    log_r("临时用户(".$email.")创建","失败！");
    msg_bar_error_delay("临时用户创建失败！");
    return false;
  }
  
  log_r("临时用户(".$email.")创建","成功！");
  msg_bar_success_delay("临时用户创建成功！");
  return true;
}

function user_email_is_ip($email) {
  if (!$email) {
    return false;
  }
  return filter_var($email, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
}

function user_is_temp($user_id) {
  if (!$user_id) {
    return false;
  }
  return user_email_is_ip(user_get_mail_by_id($user_id));
}

function user_add($email, $name, $gender, $phone, $photo_url) {
  if (($email) && ($name)) {
    $salt = random_pwd(10, 15);
    $r = db_q("insert into user values (NULL, '".$email."', 'Club', NOW(), 0, NULL, NULL, false, '".$salt."', NULL, NULL)");
    if ($r) {
      $user_id = user_get_id_by_email($email);
      if ($user_id) {
        $r2 = db_q("insert into member values (NULL, '".$email."', '".$name."', '".$gender."', '".$phone."', '".$photo_url."', NOW(), ".$user_id.")");
        if ($r2) {
          if (!user_email_is_ip($email)) {
            $new_pwd = random_pwd(10, 15);
            user_set_pwd($user_id, $new_pwd);
            $token = random_pwd(10, 15);
            user_set_token($user_id, $token);
            notify_token($user_id, $token);
          }
          return true;
        }
      }
    }
  }
  return false;
}

function user_change_from_temp($user_id, $email, $name, $gender, $phone, $photo_url) {
  if (($user_id) && ($email) && ($name)) {
    $member_id = user_get_member_id_by_id($user_id);
    $r = db_q("update user set email='".$email."' where id=".$user_id);
    if ($r) {
      $r2 = db_q("update member set email='".$email."',name='".$name."',gender='".$gender."',phone='".$phone."',photo_url='".$photo_url."' where id=".$member_id);
      if ($r2) {
        $new_pwd = random_pwd(10, 15);
        user_set_pwd($user_id, $new_pwd);
        $token = random_pwd(10, 15);
        user_set_token($user_id, $token);
        notify_token($user_id, $token);
        return true;
      }
    }
  }
  return false;
}

function user_get_all($orderby, $order, $page, $item_per_page) {
  if ($order == 'd') {
    $order = "desc";
  } else {
    $order = "asc";
  }
  if ($page > 0) {
    $off = (($page-1)*$item_per_page);
    $rows = $item_per_page;
    $r = db_q("select * from user order by ".$orderby." ".$order." limit ".$off.",".$rows);
  } else {
    $r = db_q("select * from user order by ".$orderby." ".$order);
  }
  return $r;
}

function user_get_count() {
  $r = user_get_all("id", "a", 0, NULL);
  if ($r) {
    return $r->num_rows;
  }
  return 0;
}

function user_get_pages($item_per_page) {
  $count = user_get_count();
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function user_get_club_count_by_id($user_id) {
  if (!$user_id) {
    return NULL;
  }
  $member_id = user_get_member_id_by_id($user_id);
  $role_id = club_role_name2id("创建者");
  $r = db_q("select * from member_club where member_id=".$member_id." and role_id=".$role_id);
  if ($r) {
    return $r->num_rows;
  }
  return NULL;
}

function user_remove_all($user_id, $keepuser) {
  if (!$user_id) {
    return false;
  }
  $name = user_get_name_by_id($user_id);
  $r1 = user_remove_clubs($user_id);
  $r2 = user_remove_facilities($user_id);
  $r3 = user_remove_addressbook($user_id, $keepuser);
  if (!$keepuser) {
    $r = db_q("delete from user where id=".$user_id);
    $result = (($r) && ($r1) && ($r2) && ($r3));
  } else {
    $result = (($r1) && ($r2) && ($r3));
  }
  if ($result) {
    log_r("用户（".$name."）数据删除", "成功！");
    return true;
  } else {
    log_r("用户（".$name."）数据删除", "失败！");
    return false;
  }
}

function user_remove_clubs($user_id) {
  if (!$user_id) {
    return false;
  }
  $r = club_get_all_by_user_id($user_id);
  if ($r) {
    for($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $club_id = $row['id'];
      user_remove_club($club_id);
    }
  }
  return true;
}

function user_remove_club($club_id) {
  if (!$club_id) {
    return false;
  }
  $name = club_get_name_by_id($club_id);
  user_remove_club_trans($club_id);
  user_remove_club_events($club_id);
  user_remove_club_members($club_id);
  $r = db_q("delete from club where id=".$club_id);
  if ($r) {
    log_r("俱乐部（".$name."）删除", "成功！");
    return true;
  } else {
    log_r("俱乐部（".$name."）删除", "失败！");
    return false;
  }
}

function user_remove_club_trans($club_id) {
  if (!$club_id) {
    return false;
  }
  $name = club_get_name_by_id($club_id);
  $r = db_q("delete from trans where club_id=".$club_id);
  if ($r) {
    log_r("俱乐部（".$name."）交易删除", "成功！");
    return true;
  } else {
    log_r("俱乐部（".$name."）交易删除", "成功！");
    return false;
  }
}

function user_remove_club_events($club_id) {
  if (!$club_id) {
    return false;
  }
  $name = club_get_name_by_id($club_id);
  $r = event_get_all_same_club($club_id, "id", "a", 0, NULL);
  if ($r) {
    for($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $event_id = $row['id'];
      user_remove_club_event_member($event_id);
    }
  }
  $r2 = db_q("delete from event where club_id=".$club_id);
  if ($r2) {
    log_r("俱乐部（".$name."）活动删除", "成功！");
    return true;
  } else {
    log_r("俱乐部（".$name."）活动删除", "失败！");
    return false;
  }
}

function user_remove_club_event_member($event_id) {
  if (!$event_id) {
    return false;
  }
  $r = db_q("delete from member_event where event_id=".$event_id);
  if ($r) {
    return true;
  } else {
    log_r("俱乐部活动（".$event_id."）参加者删除", "失败！");
    return false;
  }
}

function user_remove_club_members($club_id) {
  if (!$club_id) {
    return false;
  }
  $name = club_get_name_by_id($club_id);
  $r = db_q("delete from member_club where club_id=".$club_id);
  if ($r) {
    log_r("俱乐部（".$name."）成员删除", "成功！");
    return true;
  } else {
    log_r("俱乐部（".$name."）成员删除", "失败！");
    return false;
  }
}
  
function user_remove_facilities($user_id) {
  if (!$user_id) {
    return false;
  }
  $name = user_get_name_by_id($user_id);
  $r = db_q("delete from facility where user_id=".$user_id);
  if ($r) {
    log_r("用户（".$name."）场所商户删除", "成功！");
    return true;
  } else {
    log_r("用户（".$name."）场所商户删除", "失败！");
    return false;
  }
}

function user_remove_addressbook($user_id, $keepuser) {
  if (!$user_id) {
    return false;
  }
  $name = user_get_name_by_id($user_id);
  if (!$keepuser) {
    $r = db_q("delete from member where user_id=".$user_id);
  } else {
    $member_id = user_get_member_id_by_id($user_id);
    if ($member_id) {
      $r = db_q("delete from member where id != ".$member_id." and user_id=".$user_id);
    } else {
      $r = NULL;
    }
  }
  if ($r) {
    log_r("用户（".$name."）通讯录删除", "成功！");
    return true;
  } else {
    log_r("用户（".$name."）通讯录删除", "失败！");
    return false;
  }
}

function user_get_limit($key) {
  if (!$key) {
    return NULL;
  }
  $user_id = user_get_signin_id();
  if (user_is_temp($user_id)) {
    $limit = array(
      "MPC"=>TEMP_MEMBER_PER_CLUB,
      "CPU"=>TEMP_CLUB_PER_USER,
      "EPC"=>TEMP_EVENT_PER_CLUB,
      "APU"=>TEMP_ADDR_PER_USER,
      "FPU"=>TEMP_FACILITY_PER_USER,
      "TPC"=>TEMP_TRANS_PER_CLUB
    );
  } else {
    $limit = array(
      "MPC"=>MAX_MEMBER_PER_CLUB,
      "CPU"=>MAX_CLUB_PER_USER,
      "EPC"=>MAX_EVENT_PER_CLUB,
      "APU"=>MAX_ADDR_PER_USER,
      "FPU"=>MAX_FACILITY_PER_USER,
      "TPC"=>MAX_TRANS_PER_CLUB
    );
  }
  return $limit[$key];
}
