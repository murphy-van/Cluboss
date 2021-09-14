<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");
  
  if (club_del()) {
    header("Location:club.php");
  } else {
    $club_id = request_get("cid", NULL);
    header("Location:club.php?cid=".$club_id."&tab=a&set=i");
  }