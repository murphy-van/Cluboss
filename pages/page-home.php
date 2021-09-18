<?php
function do_home() {
  do_html_header("主页 - ".APP_NAME, "navbar", false);
  if (user_get_signin_id()) {
    do_page_home_navbar();
  } else {
    do_page_navbar();
  }
  do_page_home();
  do_page_footer();
  do_html_footer();
}

function do_page_home_navbar() {
  do_page_navbar_head(NULL);
  do_page_navbar_left("主页");
  do_page_navbar_right();
}

function do_page_home() {
?>
      <div class="jumbotron">
        <h1><?php echo APP_NAME?></h1>
        <div class="row">
          <div class="col-md-8">
            <br /><p>欢聚的时刻多么宝贵！<br />快乐的生活需要分享！<br />为AA制俱乐部和饭团管理帐目，分享账单，生成报表<br />成员通过电子邮件接收活动提醒，帐目余额和定期报告</p>
          </div>
          <div class="col-md-4">
            <img src="images/Home.jpg" alt="主页图例" class="img-thumbnail">
          </div>
        </div>
        <br />
        <p>
<?php
  if (user_get_signin_id()) {
?>
          <a class="btn btn-primary btn-lg" href="club.php?tab=m" role="button">管理我的俱乐部</a>&nbsp;&nbsp;
          <a class="btn btn-primary btn-lg" href="club.php?tab=c" role="button">创建新的俱乐部</a></p>
<?php
  } else {
?>
          <a class="btn btn-primary btn-lg" href="trial.php" role="button">免注册试用</a></p>
<?php
  }
?>
      </div>
<?php
}

