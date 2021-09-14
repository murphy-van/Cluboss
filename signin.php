<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  user_signin();

  if (user_get_signin_id()) {
    header("Location:club.php");
    exit;
  } else {
    do_signin();
  }