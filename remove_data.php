<?php

  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");
  
  $user_id = user_get_signin_id();
  if (!$user_id) {
    msg_bar_warning_delay("请先登录或注册成为新用户！");
    do_home();
  } else {
    if (user_remove_all($user_id, true)){
      msg_bar_success_delay("数据清空成功！");
    } else {
      msg_bar_error_delay("数据清空失败！");
    }
    header("Location:setup.php?uid=".$user_id."&tab=d");
  }