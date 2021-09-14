<?php
  session_start();
  require_once("const/global.php");
  require_once("fns/fns.php");
  require_once("pages/pages.php");

  $member_id = request_get("mid", NULL);
  if ($member_id) {
    $output = member2vcard($member_id);
    $name = member_get_name_by_id($member_id);
  } else {
    $output = member2vcard_all();
    $user_id = user_get_signin_id();
    $member_id = user_get_member_id_by_id($user_id);
    $name = member_get_name_by_id($member_id)."通讯录";
  }
  
  header('Content-Type: text/x-vcard'); 
  header('Content-Disposition: attachment; filename='.urlencode($name.".vcf"));

  echo iconv('UTF-8', 'GBK', $output);