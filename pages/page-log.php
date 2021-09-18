<?php
function do_log() {
  do_html_header("日志 - ".APP_NAME, "navbar", false);
  do_page_log_navbar();
  do_page_log();
  do_page_footer();
  do_html_footer();
}

function do_page_log_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("日志");
  do_page_navbar_right();
}

function do_page_log() {
  $orderby = request_get("orderby", "id");
  $order = request_get("order", "d");
  $page = request_get("page", 1);
  $opt = request_get("opt", NULL);
  if ($opt) {
    $opturl="&opt=".$opt."&";
  } else {
    $opturl = NULL;
  }
  if (($page > log_get_pages(ITEM_PER_PAGE, $opt)) || ($page <= 0)) {
    $page = 1;
  }
?>
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
<?php
  if ($opt) {
    echo "<a href='log.php'>完整列表</a><br />";
  } else {
    echo "<a href='log.php?opt=nopageopen'>重要列表</a><br />";
  }
  $r = log_get_all($orderby, $order, $page, ITEM_PER_PAGE, $opt);
  do_page_list_page_move($orderby, $order, $page, log_get_pages(ITEM_PER_PAGE, $opt), "log.php?".$opturl);
  do_page_log_($r, $orderby, $order, $page, $opturl);
  do_page_list_page_move($orderby, $order, $page, log_get_pages(ITEM_PER_PAGE, $opt), "log.php?".$opturl);
?>
        <a class="btn btn-primary" role="button" href="log_export.php" onclick="return confirm('这可能需要一些时间来生成日志文件。现在下载吗？');"><span class="glyphicon glyphicon-save" aria-hidden="true">&nbsp;下载所有日志到本地</span></a>
        &nbsp;&nbsp;<a class="btn btn-primary" role="button" href="log_remove.php" onclick="return confirm('日志清除后不能被恢复。建议下载到本地备份后再清除。现在清除所有日志吗？');"><span class="glyphicon glyphicon-trash" aria-hidden="true">&nbsp;清除所有日志&nbsp;&nbsp;&nbsp;</span></a>
      </div>
<?php
}

function do_page_log_($r, $orderby, $order, $page, $opturl) {
  if ($order == "a") {
    $icon = "glyphicon-triangle-top";
    $urlorder = "d";
  } else {
    $icon = "glyphicon-triangle-bottom";
    $urlorder = "a";
  }
  if ($r) {
?>
            <table class="table table-striped table-bordered">
              <tr>
                <td><a href="log.php?orderby=log_time&order=<?php echo $urlorder?>&page=<?php echo $page?><?php echo $opturl?>"><strong>时间</strong></a>
<?php
    if ($orderby == "log_time") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="log.php?orderby=user&order=<?php echo $urlorder?>&page=<?php echo $page?><?php echo $opturl?>"><strong>用户</strong></a>
<?php
    if ($orderby == "user") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="log.php?orderby=fromip&order=<?php echo $urlorder?>&page=<?php echo $page?><?php echo $opturl?>"><strong>IP</strong></a>
<?php
    if ($orderby == "fromip") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="log.php?orderby=action&order=<?php echo $urlorder?>&page=<?php echo $page?><?php echo $opturl?>"><strong>动作</strong></a>
<?php
    if ($orderby == "action") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="log.php?orderby=msg&order=<?php echo $urlorder?>&page=<?php echo $page?><?php echo $opturl?>"><strong>信息</strong></a>
<?php
    if ($orderby == "msg") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="log.php?orderby=page&order=<?php echo $urlorder?>&page=<?php echo $page?><?php echo $opturl?>"><strong>页面</strong></a>
<?php
    if ($orderby == "page") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
              </tr>
<?php
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
?>
              <tr>
                <td><?php echo $row['log_time']?></td>
                <td><?php echo $row['user']?></td>
                <td><?php echo $row['fromip']?></td>
                <td><?php echo $row['action']?></td>
                <td><?php echo $row['msg']?></td>
                <td><?php echo $row['page']?></td>
              </tr>
<?php
    }
?>
            </table>
<?php
  }
}

function do_page_log_csv_($r) {
  if ($r) {
    $output = "时间, 用户, IP, 动作, 信息, 页面\n";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $log_time = str_replace(",", " ", $row['log_time']);
      $user = str_replace(",", " ", $row['user']);
      $fromip = str_replace(",", " ", $row['fromip']);
      $action = str_replace(",", " ", $row['action']);
      $msg = str_replace(",", " ", $row['msg']);
      $page = str_replace(",", " ", $row['page']);
      $output .= $log_time.", ".$user.", ".$fromip.", ".$action.", ".$msg.", ".$page."\n";
    }
  }
  return $output;
}

function log2csv() {
  $r = log_get_all("log_time", "d", 0, NULL, NULL);
  return do_page_log_csv_($r);
}