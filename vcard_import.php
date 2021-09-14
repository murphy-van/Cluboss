<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  if (member_import()) {
    header("Location:people.php?tab=m");
  } else {
    header("Location:people.php?tab=v");
  }
