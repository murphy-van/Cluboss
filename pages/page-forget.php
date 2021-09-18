<?php

function do_forget() {
  do_html_header("忘记密码 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_forget();
  do_page_footer();
  do_html_footer();
}

function do_reset_pwd() {
  do_html_header("重置密码 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_reset_pwd();
  do_page_footer();
  do_html_footer();
}

function do_reset_pwd_success() {
  do_html_header("重置密码 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_reset_pwd_success();
  do_page_footer();
  do_html_footer();
}

function do_page_forget() {
  if (isset($_SESSION['reset_email'])) {
    $reset_email = $_SESSION['reset_email'];
    unset($_SESSION['reset_email']);
  } else {
    $reset_email = "";
  }
?>
      <div class="panel panel-default">
        <div class="panel-heading"><strong>忘记密码</strong></div>
        <div class="panel-body">
          <form class="form-horizontal" role="form" method="post" action="forget_pwd.php" >
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo APP_NAME?>注册电子邮箱</label>
              <div class="col-sm-6">
                <input name="reset_email" type="email" class="form-control"
                  placeholder="<?php echo APP_NAME?>注册电子邮箱（必填）" aria-describedby="basic-addon1"
                  size="100%" required autofocus maxlength="50"
                  value = "<?php echo $reset_email?>">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><img src="captcha.php"></label>
              <div class="col-sm-6">
                <input name="vercode" type="text" class="form-control"
                  placeholder="请输入左边图片中的数字（必填）" aria-describedby="basic-addon1"
                  size="100%" required maxlength="50">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
              <button class="btn btn-primary btn-block" type="submit">发送密码提醒邮件</button>
            </div>
            <div class="col-sm-4"></div>
          </form>
        </div>
      </div>
<?php
}

function do_page_reset_pwd() {
  $email = request_get("email", NULL);
  $token = request_get("token", NULL);
  if ((!$email) || (!$token)) {
    msg_bar_warning_delay("空参数错误！");
    return false;
  }
  $user_id = user_get_id_by_email($email);
  if (!$user_id) {
    msg_bar_error_delay("电子邮箱(".$email.")错误！");
    return false;
  }
  if ($token == user_get_token($user_id)) {
    $token_time = user_get_token_time($user_id);
    date_default_timezone_set('PRC');
    $now = date('Y-m-d H:i:s');
    if ((strtotime($now) - strtotime($token_time)) > TOKEN_TIMEOUT) {
      msg_bar_warning_delay("链接已过期！");
      return false;
    }
  } else {
    msg_bar_warning_delay("临时码错误！");
    return false;
  }
?>
      <div class="panel panel-default">
        <div class="panel-heading"><strong>重置密码</strong></div>
        <div class="panel-body">
          <form class="form-horizontal" role="form" method="post"
                action="reset_pwd.php?email=<?php echo $email?>&token=<?php echo $token?>">
            <div class="form-group">
              <input name="user_id" type="hidden" class="form-control"
                     value="<?php echo $user_id?>">
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">新密码</label>
              <div class="col-sm-6">
                <input name="new_pwd" type="password" class="form-control" 
                     placeholder="新密码（必填）" aria-describedby="basic-addon1" 
                     size="100%" required maxlength="40">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">重复新密码</label>
              <div class="col-sm-6">
                <input name="new_pwd2" type="password" class="form-control" 
                     placeholder="重复新密码（必填）" aria-describedby="basic-addon1" 
                     size="100%" required maxlength="40">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
              <button class="btn btn-primary btn-block" type="submit">提交密码</button>
            </div>
            <div class="col-sm-4"></div>
          </form>
        </div>
      </div>
<?php
}

function do_page_reset_pwd_success() {
  msg_bar_success_noclose("密码重置成功！");
}