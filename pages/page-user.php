<?php

function do_user() {
  do_html_header("用户 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_user();
  do_page_footer();
  do_html_footer();
}

function do_page_user() {
  $req_user_id = request_get("uid", user_get_signin_id());
  if (($req_user_id != user_get_signin_id()) && !is_super_user()) {
    msg_bar_warning_noclose("您无权查看其他用户的信息！");
    return false;
  }
  $name = user_get_name_by_id($req_user_id);
  if (!$name) {
    msg_bar_warning_noclose("用户没找到！");
    return false;
  }
  do_page_people_detail($req_user_id);
}

function do_page_people_detail($user_id) {
  $email = user_get_mail_by_id($user_id);
  $member_id = user_get_member_id_by_id($user_id);
  $name = member_get_name_by_id($member_id);
  $gender = member_get_gender_by_id($member_id);
  $phone = member_get_phone_by_id($member_id);
  $photo_url = member_get_photo_url_by_id($member_id);
  $register_date = user_get_register_date_by_id($user_id);
  $signin_count = user_get_signin_count_by_id($user_id);
  $last_signin_time = user_get_last_signin_time_by_id($user_id);
  $last_signin_ip = user_get_last_signin_ip_by_id($user_id);
?>
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>
                <img src="<?php echo $photo_url?>" alt="照片" width="150"
                  height="187"></strong></div>
            <table class="table table-striped table-bordered">
              <tr>
                <td>姓名</td><td><?php echo $name ?></td></tr>
              <tr>
                <td>注册电子邮箱</td><td><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></td></tr>
              <tr>
                <td>电话</td><td><?php echo $phone ?></td></tr>
              <tr>
                <td>性别</td><td><?php echo $gender ?></td></tr>
              <tr>
                <td>注册日期</td><td><?php echo $register_date?></td></tr>
              <tr>
                <td>登录次数</td><td><?php echo $signin_count?></td></tr>
              <tr>
                <td>上次登录时间</td><td><?php echo $last_signin_time?></td></tr>
              <tr>
                <td>上次登录IP</td><td><?php echo $last_signin_ip?></td></tr>
            </table>
          </div>
<?php
  if (($user_id == user_get_signin_id()) || is_super_user()) {
    if (is_super_user()) {
      $msg = "删除";
    } else {
      $msg = "删除并退出";
    }
?>
            <a href="setup.php?uid=<?php echo $user_id;?>&tab=i" class="btn btn-primary" role="button"><span class="glyphicon glyphicon-pencil" aria-hidden="true">&nbsp;编辑</span></a>
            <a href="user_del.php?uid=<?php echo $user_id;?>" class="btn btn-primary" role="button"><span class="glyphicon glyphicon-trash" aria-hidden="true" onclick="return confirm('<?php echo $msg?>后所有数据是不可恢复的，确定吗？');">&nbsp;<?php echo $msg?></span></a>
<?php
  }
?>
        </div>
      </div>
<?php
}