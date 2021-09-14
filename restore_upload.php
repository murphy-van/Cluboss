<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  if (restore_upload()) {
    header("Location:club.php");
  } else {
    header("Location:restore.php");
  }
