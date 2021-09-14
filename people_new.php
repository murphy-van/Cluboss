<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $member_id = member_new();
  if ($member_id) {
    header("Location:people.php?mid=".$member_id);
  } else {
    header("Location:people.php?tab=a");
  }
