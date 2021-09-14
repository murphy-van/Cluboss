<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $user_id = user_get_signin_id();
  if (!$user_id) {
    msg_bar_warning_delay("请先登录或注册成为新用户！");
    do_home();
  } else if (user_is_temp($user_id)){
    msg_bar_warning_delay("临时用户请直接退出！");
    do_home();
  } else {
    $del_user_id = request_get("uid", NULL);
    if (($del_user_id != $user_id) || !is_super_user()) {
      user_remove_all($del_user_id, false);
      if ($user_id == $del_user_id) {
        unset($_SESSION['valid_email']);
        if (isset($_COOKIE['email'])) {
          setcookie("email", "", time()-3600);
        }
        if (isset($_COOKIE['pwd'])) {
          setcookie("pwd", "", time()-3600);
        }
      }
    }
    do_home();
  }