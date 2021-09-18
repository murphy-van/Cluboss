<?php
function do_register() {
  do_html_header("注册 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_register();
  do_page_footer();
  do_html_footer();
}

function do_register_success() {
  do_html_header("注册 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_register_success();
  do_page_footer();
  do_html_footer();
}

function do_page_register() {
  if (isset($_SESSION['reg_email'])) {
    $email = $_SESSION['reg_email'];
    unset($_SESSION['reg_email']);
  }
  if (isset($_SESSION['reg_name'])) {
    $name = $_SESSION['reg_name'];
    unset($_SESSION['reg_name']);
  }
  if (isset($_SESSION['reg_phone'])) {
    $phone = $_SESSION['reg_phone'];
    unset($_SESSION['reg_phone']);
  }
  if (isset($_SESSION['reg_gender'])) {
    $gender = $_SESSION['reg_gender'];
    unset($_SESSION['reg_gender']);
  }
  if (isset($_SESSION['reg_photo_url'])) {
    $photo_url = $_SESSION['reg_photo_url'];
    unset($_SESSION['reg_photo_url']);
  }
  if (user_is_temp(user_get_signin_id())) {
    msg_bar_info("您正在通过临时用户注册！请注意如果选择继续注册，临时用户将不再可用。所有临时数据将会自动转存到成功的注册用户下！如果注册失败，临时数据将被删除！");
  }
?>
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>注册<?php echo APP_NAME?>账号</strong></div>
            <div class="panel-body">
            <br />
            <form class="form-horizontal" role="form" method="post"
                  action="register.php">
              <div class="form-group">
                <label class="col-sm-3 control-label">电子邮箱</label>
                <div class="col-sm-6">
                  <input name="email" type="email" class="form-control"
                       placeholder="电子邮箱（必填）" aria-describedby="basic-addon1"
                       size="100%" required autofocus value="<?php echo $email?>"
                       maxlength="50">
                </div>
                <div class="col-sm-3"></div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">姓名</label>
                <div class="col-sm-6">
                  <input name="name" type="text" class="form-control"
                       placeholder="姓名（必填）" aria-describedby="basic-addon1"
                       size="100%" required value="<?php echo $name?>"
                       maxlength="5">
                </div>
                <div class="col-sm-3"></div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">电话号码</label>
                <div class="col-sm-6">
                  <input name="phone" type="number" class="form-control"
                       placeholder="电话（可选）" aria-describedby="basic-addon1"
                       size="50%" value="<?php echo $phone?>" maxlength="15">
                </div>
                <div class="col-sm-3"></div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">性别</label>
                <div class="col-sm-6">
                  <select name="gender" class="form-control" multiple="">
                    <option <?php if ($gender == "男") 
                      { echo "selected='selected'";}?>>男</option>
                    <option <?php if ($gender == "女") 
                      { echo "selected='selected'";}?>>女</option>
                    <option <?php if ((!$gender) || ($gender == "保密")) 
                      { echo "selected='selected'";}?>>保密</option>
                  </select>
                </div>
                <div class="col-sm-3"></div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">照片链接</label>
                <div class="col-sm-6">
                  <input name="photo_url" type="text" class="form-control"
                       placeholder="照片链接（可选）" aria-describedby="basic-addon1"
                       size="100%" value="<?php echo $photo_url?>"
                       maxlength="200">
                </div>
                <div class="col-sm-3"><img src="<?php echo $photo_url?>" alt="照片" width="30" height="37"></div>
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
              <div class="col-sm-3"></div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="submit">注册</button>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="reset">重新设定</button>
              </div>
              <div class="col-sm-3"></div>
            </form>
            </div>
          </div>
        </div>
<?php

}

function do_page_register_success() {
  if (isset($_SESSION['reg_email'])) {
    $email = $_SESSION['reg_email'];
    unset($_SESSION['reg_email']);
  }
  if (isset($_SESSION['reg_name'])) {
    $name = $_SESSION['reg_name'];
    unset($_SESSION['reg_name']);
  }
  if (isset($_SESSION['reg_phone'])) {
    $phone = $_SESSION['reg_phone'];
    unset($_SESSION['reg_phone']);
  }
  if (isset($_SESSION['reg_gender'])) {
    $gender = $_SESSION['reg_gender'];
    unset($_SESSION['reg_gender']);
  }
  if (isset($_SESSION['reg_photo_url'])) {
    $photo_url = $_SESSION['reg_photo_url'];
    unset($_SESSION['reg_photo_url']);
  }
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
            </table>
          </div>
        </div>
      </div>
<?php
}