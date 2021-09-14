<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  if (is_super_user()) {
    if (log_remove_all()) {
      msg_bar_success_delay("清除所有的日志成功！");
    } else {
      msg_bar_error_delay("清除所有的日志失败！");
    }
    header("Location:log.php");
  }
