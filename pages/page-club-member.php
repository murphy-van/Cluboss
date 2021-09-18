<?php

function do_page_club_member_list($club_id) {
  $user_id = user_get_signin_id();
  $page = request_get("page", 1);
  $pages = member_get_pages_same_club($user_id, $club_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = member_get_all_same_club($user_id, $club_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&tab=m&");
  do_page_people_list_($r, "club_member_list", NULL, $page, $club_id);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&tab=m&");
?>
              <a class="btn btn-primary" role="button" href="mailto:<?php echo member_get_email_same_club($user_id, $club_id) ?>"><span class="glyphicon glyphicon-envelope" aria-hidden="true">&nbsp;群发邮件给所有成员&nbsp;&nbsp;</span></a>
              &nbsp;&nbsp;<a class="btn btn-primary" role="button" href="club.php?cid=<?php echo $club_id?>&tab=m&page=<?php echo $page?>&mail=report90" onclick="return confirm('这可能需要较长的时间来完成。确定发送季报邮件给本页成员吗？');"><span class="glyphicon glyphicon-envelope" aria-hidden="true">&nbsp;发送季报邮件给本页成员</span></a>
              &nbsp;&nbsp;<a class="btn btn-primary" role="button" href="club.php?cid=<?php echo $club_id?>&tab=m&page=<?php echo $page?>&mail=report365" onclick="return confirm('这可能需要较长的时间来完成。确定发送年报邮件给本页成员吗？');"><span class="glyphicon glyphicon-envelope" aria-hidden="true">&nbsp;发送年报邮件给本页成员</span></a>
<?php
}

function do_page_club_add_member($club_id) {
  $user_id = user_get_signin_id();
  $page = request_get("page", 1);
  $pages = member_get_pages_diff_club($user_id, $club_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = member_get_all_diff_club($user_id, $club_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&tab=a&set=m&");
  do_page_people_list_($r, "club_member_add", NULL, $page, $club_id);
  do_page_list_page_move("name", "a", $page, $pages, "club.php?cid=".$club_id."&tab=a&set=m&");
}

