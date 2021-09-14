<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $club_id = club_new();
  if ($club_id) {
    header("Location:club.php?cid=".$club_id);
  } else {
    header("Location:club.php?tab=c");
  }
