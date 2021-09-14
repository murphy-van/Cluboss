<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $user_id = user_get_signin_id();
  if (($user_id)&&(!user_is_temp($user_id))) {
    user_signout();
  }
  if (user_register()) {
    do_register_success();
  } else {
    do_register();
  }