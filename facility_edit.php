<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $facility_id = post_get("facility_id");
  if ($facility_id) {
    $r= facility_edit($facility_id);
  } else {
    $r = false;
  }
  if ($r) {
    header("Location:facility.php?fid=".$facility_id);
  } else {
    header("Location:facility.php?fid=".$facility_id."&act=e");
  }
