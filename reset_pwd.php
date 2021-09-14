<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  if (!reset_pwd()) {
    do_reset_pwd();
  } else {
    do_reset_pwd_success();
  }