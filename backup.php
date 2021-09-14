<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $user_id = user_get_signin_id();
  if ($user_id) {
    $output = backup_get_xml_context();
    $name = backup_get_xml_name();

    header('Content-Type: text/xml'); 
    header('Content-Disposition: attachment; filename='.urlencode($name.".xml"));

    echo iconv('UTF-8', 'GBK', $output);
  }