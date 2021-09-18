<?php

function do_setup() {
  do_html_header("设置 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_setup();
  do_page_footer();
  do_html_footer();
}

function do_page_setup() {
  $req_tab = request_get("tab", "i");
  $req_user_id = request_get("uid", user_get_signin_id());
  $req_member_id = user_get_member_id_by_id($req_user_id);
  $req_member_name = member_get_name_by_id($req_member_id);
  $user_id = user_get_signin_id();
  if (!$user_id) {
    msg_bar_warning_noclose("您还未登录！");
    return;
  }
  if ($user_id != $req_user_id) {
    if (!is_super_user()) {
      msg_bar_warning_noclose("您不能修改其他人的设置！");
      return;
    }
  }

?>
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong><?php echo $req_member_name;?></strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "i") { ?>class="active"<?php } ?>>
                <a href="setup.php?uid=<?php echo $req_user_id?>&tab=i">个人信息</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "p") { ?>class="active"<?php } ?>>
                <a href="setup.php?uid=<?php echo $req_user_id?>&tab=p">修改密码</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "d") { ?>class="active"<?php } ?>>
                <a href="setup.php?uid=<?php echo $req_user_id?>&tab=d">用户数据</a></li>
            </ul>
            <br />
<?php
            switch ($req_tab) {
              case "p": /* Password */
                do_page_setup_pwd($req_tab, $req_user_id);
                break;
              case "i": /* Personal Info */
                do_page_setup_info($req_tab, $req_user_id);
                break;
              case "d": /* Data Info */
                do_page_setup_data($req_tab, $req_user_id);
                break;
              default:
                break;
            }
?>
            </div>
          </div>
        </div>
<?php
}

function do_page_setup_info($req_tab, $user_id) {
  $email = user_get_mail_by_id($user_id);
  $member_id = user_get_member_id_by_id($user_id);
  $name = member_get_name_by_id($member_id);
  $gender = member_get_gender_by_id($member_id);
  $phone = member_get_phone_by_id($member_id);
  $photo_url = member_get_photo_url_by_id($member_id);
?>
          <form class="form-horizontal" role="form" method="post"
                action="setup.php?tab=i&uid=<?php echo $user_id?>">
            <div class="form-group">
              <input name="member_id" type="hidden" class="form-control" value="<?php echo $member_id?>">
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label"><?php echo APP_NAME?>注册电子邮箱</label>
              <div class="col-sm-6">
                <input name="email" type="email" class="form-control"
                     placeholder="<?php echo APP_NAME?>注册电子邮箱（必填）" aria-describedby="basic-addon1"
                     size="100%" required autofocus value="<?php echo $email?>"
                     maxlength="50" disabled>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">姓名</label>
              <div class="col-sm-6">
                <input name="name" type="text" class="form-control"
                     placeholder="姓名（必填）" aria-describedby="basic-addon1"
                     size="100%" required autofocus value="<?php echo $name?>"
                     maxlength="10">
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
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
              <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;修改信息</span></button>
            </div>
            <div class="col-sm-3">
              <button class="btn btn-primary btn-block" type="reset"><span class="glyphicon glyphicon-repeat" aria-hidden="true">&nbsp;重新设定</span></button>
            </div>
            <div class="col-sm-3"></div>
          </form>
<?php
}

function do_page_setup_pwd($req_tab, $user_id) {
?>
          <form class="form-horizontal" role="form" method="post"
                action="setup.php?tab=p&uid=<?php echo $user_id?>">
            <div class="form-group">
              <input name="user_id" type="hidden" class="form-control"
                     value="<?php echo $user_id?>">
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">旧密码</label>
              <div class="col-sm-6">
                <input name="old_pwd" type="password" class="form-control" 
                     placeholder="旧密码（必填）" aria-describedby="basic-addon1" 
                     size="100%" required autofocus maxlength="40">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
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
              <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;修改密码</span></button>
            </div>
            <div class="col-sm-4"></div>
          </form>
<?php
}

function do_page_setup_data($req_tab, $user_id) {
  $member_count = member_get_count_same_user($user_id);
  $club_count = club_get_count_same_user($user_id);
  $facility_count = facility_get_count($user_id);
  $event_count = event_get_count_same_user($user_id);
  $trans_count = trans_get_count_same_user($user_id);
?>
          <div class="panel panel-default">
            <div class="panel-heading"><strong>数据一览</strong></div>
            <table class="table table-striped table-bordered">
              <tr>
                <td>通讯录条目</td><td><?php echo $member_count ?></td></tr>
              <tr>
                <td>场所商户数量</td><td><?php echo $facility_count ?></td></tr>
              <tr>
                <td>俱乐部数量</td><td><?php echo $club_count ?></td></tr>
              <tr>
                <td>活动数量</td><td><?php echo $event_count ?></td></tr>
              <tr>
                <td>交易数量</td><td><?php echo $member_count ?></td></tr>
            </table>
          </div>
          <a class="btn btn-primary" role="button" href="backup.php" onclick="return confirm('这可能需要一些时间来生成备份文件。现在备份用户数据吗？');"><span class="glyphicon glyphicon-save" aria-hidden="true">&nbsp;备份数据到本地</span></a>
          &nbsp;&nbsp;<a class="btn btn-primary" role="button" href="restore.php"><span class="glyphicon glyphicon-open" aria-hidden="true">&nbsp;从本地数据恢复</span></a>
          &nbsp;&nbsp;<a class="btn btn-primary" role="button" href="remove_data.php" onclick="return confirm('数据清空后不可恢复，建议先备份到本地再清空。现在清空所有用户数据吗？');"><span class="glyphicon glyphicon-trash" aria-hidden="true">&nbsp清空所有的数据</span></a>
<?php
}