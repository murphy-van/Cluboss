<?php

function request_get($req, $alt) {
  if (($req) && (isset($_REQUEST[$req]))) {
    $data = htmlspecialchars($_REQUEST[$req], ENT_NOQUOTES, "UTF-8");
  } else {
    $data = $alt;
  }
  return $data;
}

function post_get($post) {
    $data = htmlspecialchars(filter_input(INPUT_POST, $post), ENT_NOQUOTES, "UTF-8");
    return $data;
}

function server_get($server) {
    $data = htmlspecialchars(filter_input(INPUT_SERVER, $server), ENT_NOQUOTES, "UTF-8");
    return $data;
}

function html_get($in_string) {
  $data = htmlspecialchars($in_string, ENT_NOQUOTES, "UTF-8");
    return $data;
}

function email_preg_check($email) {
  return (preg_match("/^[a-z0-9_\-]+(\.[_a-z0-9\-]+)*@([_a-z0-9\-]+\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)$/", $email) != 0);
}


function do_page_list_page_move($orderby, $order, $page, $pages, $url) {
  if ($pages > 1) {
    if ($page > 1) {
      $pre = $page-1;
    } else {
      $pre = 1;
    }
    if ($page < $pages) {
      $next = $page+1;
    } else {
      $next = $pages;
    }
    echo "<div id='listnav'>\n";
    echo "<a href='".$url."orderby=".$orderby."&order=".$order."&page=1#listnav'><span class='glyphicon glyphicon-step-backward' aria-hidden='true'></span></a>&nbsp;\n";
    echo "<a href='".$url."orderby=".$orderby."&order=".$order."&page=".$pre."#listnav'><span class='glyphicon glyphicon-backward' aria-hidden='true'></span></a>&nbsp;\n";
    echo "第".$page."页/共".$pages."页\n";
    echo "<a href='".$url."orderby=".$orderby."&order=".$order."&page=".$next."#listnav'><span class='glyphicon glyphicon-forward' aria-hidden='true'></span></a>&nbsp;\n";
    echo "<a href='".$url."orderby=".$orderby."&order=".$order."&page=".$pages."#listnav'><span class='glyphicon glyphicon-step-forward' aria-hidden='true'></span></a>\n";
    echo "</div>\n<br />\n";
  }
}
