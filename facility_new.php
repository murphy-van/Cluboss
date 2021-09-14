<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $facility_id = facility_new();
  if ($facility_id) {
    header("Location:facility.php?fid=".$facility_id);
  } else {
    header("Location:facility.php?tab=a");
  }
