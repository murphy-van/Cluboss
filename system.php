<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  if (!user_get_signin_id()) {
    msg_bar_warning_delay("请先登录或注册成为新用户！");
    do_home();
  } else {
    do_system();
  }

