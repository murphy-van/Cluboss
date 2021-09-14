<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  if (is_super_user()) {
    date_default_timezone_set('PRC');
    $now = date('YmdHis');
    $filename = APP_NAME."-日志-".$now.".csv";

    $output = log2csv();

    header('Content-Type: text/csv'); 
    header('Content-Disposition: attachment; filename='.urlencode($filename));

    echo "\xEF\xBB\xBF".$output;
  }
