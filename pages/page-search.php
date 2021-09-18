<?php
function do_search() {
  do_html_header("搜索 - ".APP_NAME, "navbar", false);
  do_page_search_navbar();
  do_page_search();
  do_page_footer();
  do_html_footer();
}

function do_page_search_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("搜索");
  do_page_navbar_right();
}

function do_page_search() {
  $key = request_get("key", post_get("key"));
  if (!$key) {
    return;
  }
  if (strrchr($key, '%')) {
    $key = "";
  }
  $user_id = user_get_signin_id();
  if (!$user_id) {
    return;
  }
  $default_tab = NULL;
  $club_count = search_get_club_count($key, $user_id);
  if ((!$default_tab)&&($club_count)) {
    $default_tab = "c";
  }
  $member_count = search_get_member_count($key, $user_id);
  if ((!$default_tab)&&($member_count)) {
    $default_tab = "m";
  }
  $event_count = search_get_event_count($key, $user_id);
  if ((!$default_tab)&&($event_count)) {
    $default_tab = "e";
  }
  $people_count = search_get_people_count($key, $user_id);
  if ((!$default_tab)&&($people_count)) {
    $default_tab = "p";
  }
  $facility_count = search_get_facility_count($key, $user_id);
  if ((!$default_tab)&&($facility_count)) {
    $default_tab = "f";
  } 
  $req_tab = request_get("tab", NULL);
  if (!$req_tab) {
    if ($default_tab) {
      $req_tab = $default_tab;
    } else {
      $req_tab = "c";
    }
  }
  $total_result = $club_count + $member_count + $people_count + $event_count + $facility_count;
?>
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>搜索&nbsp;“<?php echo $key?>”&nbsp;共有<?php echo $total_result?>个相关条目</strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "c") { ?>class="active" <?php } ?>><a href="search.php?tab=c&key=<?php echo $key?>">俱乐部&nbsp;<span class="badge"><?php echo $club_count;?></span></a></li>
              <li role="presentation" 
                <?php if ($req_tab == "m") { ?>class="active" <?php } ?>><a href="search.php?tab=m&key=<?php echo $key?>">成员&nbsp;<span class="badge"><?php echo $member_count;?></span></a></li>
              <li role="presentation" 
                <?php if ($req_tab == "e") { ?>class="active" <?php } ?>><a href="search.php?tab=e&key=<?php echo $key?>">活动&nbsp;<span class="badge"><?php echo $event_count;?></span></a></li>
              <li role="presentation"
                <?php if ($req_tab == "p") { ?>class="active" <?php } ?>><a href="search.php?tab=p&key=<?php echo $key?>">通讯录&nbsp;<span class="badge"><?php echo $people_count;?></span></a></li>
              <li role="presentation" 
                <?php if ($req_tab == "f") { ?>class="active" <?php } ?>><a href="search.php?tab=f&key=<?php echo $key?>">场所商户&nbsp;<span class="badge"><?php echo $facility_count;?></span></a></li>
            </ul>
            <br />
<?php
  switch ($req_tab) {
   case "c":
   default:
     do_page_search_club($key, $user_id);
     break;
   case "m":
     do_page_search_member($key, $user_id);
     break;
   case "e":
     do_page_search_event($key, $user_id);
     break;
   case "p":
     do_page_search_people($key, $user_id);
     break;
   case "f":
     do_page_search_facility($key);
     break;
  }
?>
            </div>
          </div>
      </div>
<?php
}

function do_page_search_club($key, $user_id){
  $r = search_get_club_all($key, $user_id);
  do_page_club_my_list_($r);
}

function do_page_search_people($key, $user_id){
  $page = request_get("page", 1);
  $pages = search_get_people_all_pages($key, $user_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = search_get_people_all($key, $user_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  do_page_list_page_move("convert(name,using gbk)", "a", $page, $pages, "search.php?tab=p&key=".$key."&");
  do_page_people_list_($r, "address_book", NULL, $page, NULL);
  do_page_list_page_move("convert(name,using gbk)", "a", $page, $pages, "search.php?tab=p&key=".$key."&");
}

function do_page_search_member($key, $user_id) {
  $page = request_get("page", 1);
  $pages = search_get_member_all_pages($key, $user_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = search_get_member_all($key, $user_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  if ($r) {
    do_page_list_page_move("name", "a", $page, $pages, "search.php?key=".$key."&tab=m&");
    do_page_people_list_($r, "club_search_member_list", NULL, $page, 0);
    do_page_list_page_move("name", "a", $page, $pages, "search.php?key=".$key."&tab=m&");
  } /* of if r */
}

function do_page_search_event($key, $user_id) {
  $orderby = request_get("orderby", 'id');
  $order = request_get("order", "d");
  $page = request_get("page", 1);
  $pages = search_get_event_all_pages($key, $user_id, ITEM_PER_PAGE_EVENT);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = search_get_event_all($key, $user_id, $orderby, $order, $page, ITEM_PER_PAGE_EVENT);
  if ($r) {
    do_page_list_page_move($orderby, $order, $page, $pages, "search.php?key=".$key."&tab=e&");
    do_page_club_event_list_($r, "search.php?key=".$key, $orderby, $order, true);
    do_page_list_page_move($orderby, $order, $page, $pages, "search.php?key=".$key."&tab=e&");
  } /* of if r */
}

function do_page_search_facility($key) {
  $orderby = request_get("orderby", 'id');
  $order = request_get("order", "a");
  $page = request_get("page", 1);
  $user_id = user_get_signin_id();
  $pages = search_get_facility_all_pages($user_id, $key, ITEM_PER_PAGE_FACILITY);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = search_get_facility_all($user_id, $key, $orderby, $order, $page, ITEM_PER_PAGE_FACILITY);
  if ($r) {
    do_page_list_page_move($orderby, $order, $page, $pages, "search.php?key=".$key."&tab=f&");
    do_page_facility_list_($r, "search.php?tab=f&key=".$key, $orderby, $order);
    do_page_list_page_move($orderby, $order, $page, $pages, "search.php?key=".$key."&tab=f&");
  } /* of if r */
}
   