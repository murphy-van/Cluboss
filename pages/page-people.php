<?php

function do_people() {
  do_html_header("通讯录 - ".APP_NAME, "navbar", false);
  do_page_people_navbar();
  do_page_people();
  do_page_footer();
  do_html_footer();
}

function do_page_people_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("通讯录");
  do_page_navbar_right();
}

function do_page_people() {
  $req_tab = request_get("tab", "m");
  $member_id = request_get("mid", NULL);
  $act = request_get("act", NULL);
  if ($member_id) {
    $member_name = member_get_name_by_id($member_id);
  } else {
    $member_id = 0;
  }
?>
        <div class="jumbotron">
          <div class="panel panel-default">
<?php
  if (($member_id > 0) && ((!$act)||($act != "del"))) {
?>
            <div class="panel-heading"><strong><a href="people.php?tab=m">通讯录</a>&nbsp;>&nbsp;<?php echo $member_name?></strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "m") { ?>class="active"<?php } ?>>
                <a href="people.php?mid=<?php echo $member_id?>&tab=m">联系人信息</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "e") { ?>class="active"<?php } ?>>
                <a href="people.php?mid=<?php echo $member_id?>&tab=e">修改联系人</a></li>
            </ul>
            <br />
<?php
            switch ($req_tab) {
              case "m":
                do_page_people_info($member_id);
                break;
              case "e":
                do_page_people_edit($member_id);
                break;
              default:
                break;
            }
  } else {
?>
            <div class="panel-heading"><strong>通讯录</strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "m") { ?>class="active"<?php } ?>>
                <a href="people.php?tab=m">我的通讯录</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "a") { ?>class="active"<?php } ?>>
                <a href="people.php?tab=a">添加新联系人</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "v") { ?>class="active"<?php } ?>>
                <a href="people.php?tab=v">导入联系人</a></li>
            </ul>
            <br />
<?php
            switch ($req_tab) {
              case "m":
                do_page_people_my_list();
                break;
              case "a":
                do_page_people_new();
                break;
              case "v":
                do_page_people_import();
                break;
              default:
                break;
            }
?>
<?php
  }
?>
            </div>
          </div>
        </div>
<?php
}

function do_page_people_my_list() {
  $user_id = user_get_signin_id();
  $page = request_get("page", 1);
  $pages = member_get_pages_same_user($user_id, ITEM_PER_PAGE_PEOPLE);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = member_get_all_same_user($user_id, "convert(name using gbk)", "a", $page, ITEM_PER_PAGE_PEOPLE);
  do_page_list_page_move("convert(name,using gbk)", "a", $page, $pages, "people.php?tab=m&");
  do_page_people_list_($r, "address_book", NULL, $page, NULL);
  do_page_list_page_move("convert(name,using gbk)", "a", $page, $pages, "people.php?tab=m&");
?>
          <a class="btn btn-primary" role="button" href="vcard.php" onclick="return confirm('这可能需要一些时间来生成电子名片文件。现在下载吗？');"><span class="glyphicon glyphicon-save" aria-hidden="true">&nbsp;下载整个通讯录的电子名片</span></a>
<?php
}

function do_page_people_list_($r, $type, $event_id, $page, $club_id) {
?>
            <div class="media">
              <ul class="list-group">
<?php
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      do_page_people_list_row($r, $type, $page, $event_id, $club_id);
    } /* of for i */
  } /* of r */
?>
              </ul>
            </div>
<?php
}

function do_page_people_list_row($r, $type, $page, $event_id, $club_id) {
  $row = $r->fetch_assoc();
  $email = $row['email'];
  $member_id = $row['id'];
  $name = $row['name'];
  $phone = $row['phone'];
  if ($type == "club_search_member_list") {
    $club_id = $row['club_id'];
    $club_name = club_get_name_by_id($club_id);
  }
?>
              <li class="list-group-item">
                <div class="media-left">
                  <a href="people.php?mid=<?php echo $member_id?>">
                    <img class="media-object" src="<?php echo $row['photo_url']?>" width="42" height="50" alt="照片">
                  </a>
                </div>
                <div class="media-body" id="u<?php echo $member_id?>">
                  <h4 class="media-heading"><a href="people.php?mid=<?php echo $member_id?>"><?php echo $name?></a>
<?php
  switch ($type) {
    case "club_member_list":
      do_page_people_list_club_member_list_line1($member_id, $club_id, $page, $name);
      break;
    case "club_search_member_list":
      do_page_people_list_club_search_member_list_line1($club_id, $club_name);
      break;
    case "address_book":
      do_page_people_list_address_book_line1($member_id, $name, $page);
      break;
  }
?>
                  </h4>
<?php
  switch ($type) {
    case "club_member_edit":
      do_page_people_list_club_member_edit($club_id, $event_id, $member_id, $page);
      break;
    case "club_event_member":
      do_page_people_list_club_event_member($member_id, $event_id);
      break;
    case "club_member_list":
    case "club_search_member_list":
      do_page_people_list_club_member_list_line2($member_id, $club_id, $page, $name);
      do_page_people_list_address_book_line2($email, $phone, $member_id);
      break;
    case "address_book":
      do_page_people_list_address_book_line2($email, $phone, $member_id);
      break;
    case "club_member_add":
      do_page_people_list_club_member_add($club_id, $member_id, $page);
      break;
  }
?>
                </div>
              </li>
<?php
}

function do_page_people_list_club_member_list_line1($member_id, $club_id, $page, $name) {
  if ((member_get_balance_by_club($member_id, $club_id) <= 0) && ($member_id != club_get_admin_member_id($club_id))) {
    $cfmalert = "return confirm('确定将（".$name.")移除出俱乐部（".club_get_name_by_id($club_id)."）吗？');";
    $act = "&act=del";
  } else {
    $cfmalert = "return alert('（".$name."）在俱乐部（".club_get_name_by_id($club_id)."）的余额未清零！');";
    $act = "";
  }
?>
                    &nbsp;&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=m&orderby=convert(name,using gbk)&order=a&page=<?php echo $page?><?php echo $act?>&mid=<?php echo $member_id?>" type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="<?php echo $cfmalert?>"><span align="right" aria-hidden="true">&times;</span></a>
<?php
}

function do_page_people_list_club_search_member_list_line1($club_id, $club_name) {
?>
                    &nbsp;&nbsp;@&nbsp;<a href="club.php?cid=<?php echo $club_id?>"><span align="right" aria-hidden="true"><?php echo $club_name?></span></a>
<?php
}

function do_page_people_list_club_member_list_line2($member_id, $club_id, $page, $name) {
?>
                   <h5>参加活动：<?php echo member_get_event_count_same_club($member_id, $club_id, NULL);?>次&nbsp;&nbsp;余额：<?php echo member_get_balance_by_club($member_id, $club_id)?>元&nbsp;&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=b&mid=<?php echo $member_id?>">对账单</a>&nbsp;&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=a&set=f&mid=<?php echo $member_id?>">添加交易</a>&nbsp;&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=m&page=<?php echo $page?>&mail=report90&mid=<?php echo $member_id?>" onclick="return confirm('确定发送季报邮件给<?php echo $name?>吗？');">发送季报邮件</a>&nbsp;&nbsp;<a href="club.php?cid=<?php echo $club_id?>&tab=m&page=<?php echo $page?>&mail=report365&mid=<?php echo $member_id?>" onclick="return confirm('确定发送年报邮件给<?php echo $name?>吗？');">发送年报邮件</a></h5>
<?php
}

function do_page_people_list_address_book_line1($member_id, $name, $page) {
  if ((!member_is_in_any_club($member_id)) && (!member_is_in_any_event($member_id))) {
    $cfmalert = "return confirm('确定将（".$name.")从通讯录删除吗？');";
    $act = "&act=del&mid=".$member_id;
  } else {
    $cfmalert = "return alert('（".$name.")已经在俱乐部或者活动中引用，无法从通讯录删除!请先在俱乐部和活动中移除！');";
    $act = "";
  }
?>
                    &nbsp;&nbsp;<a href="people.php?tab=m&orderby=convert(name,using gbk)&order=a&page=<?php echo $page?><?php echo $act?>" type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="<?php echo $cfmalert?>"><span align="right" aria-hidden="true">&times;</span></a>
<?php
}

function do_page_people_list_address_book_line2($email, $phone, $member_id) {
?>
                  <h5><a href="mailto:<?php echo $email?>"><?php echo $email?></a>&nbsp;&nbsp;<?php echo $phone?>&nbsp;&nbsp;<a href="sip:<?php echo $email?>">发送Lync消息</a>&nbsp;&nbsp;<a href="vcard.php?mid=<?php echo $member_id?>">下载电子名片</a></h5>
<?php
}

function do_page_people_list_club_member_edit($club_id, $event_id, $member_id, $page) {
  $url = "club.php?cid=".$club_id."&eid=".$event_id."&mid=".$member_id."&tab=e&act=em&page=".$page;
  if (member_is_in_a_event($member_id, $event_id)) {
    $pay_users = member_get_event_pay_users($member_id, $event_id);
?>
                  <a href="<?php echo $url?>&edit=minus&n=<?php echo ($pay_users-1)?>#u<?php echo $member_id?>">
                    <span class="glyphicon glyphicon-minus" aria-hidden="true"></span></a>
                    &nbsp;<?php echo $pay_users?>&nbsp;
                   <a href="<?php echo $url?>&edit=plus&n=<?php echo ($pay_users+1)?>#u<?php echo $member_id?>">
                     <span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
<?php
  } else {
?>
                   <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                    &nbsp;0&nbsp;
                   <a href="<?php echo $url?>&edit=plus&n=<?php echo ($pay_users+1)?>#u<?php echo $member_id?>">
                     <span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
<?php
  }
}

function do_page_people_list_club_event_member($member_id, $event_id) {
?>
                   <h5>分摊人数：<?php echo member_get_event_pay_users($member_id, $event_id)?></h5>
<?php
}

function do_page_people_list_club_member_add($club_id, $member_id, $page) {
  $url = "club.php?cid=".$club_id."&mid=".$member_id."&tab=a&set=m&act=add&page=".$page;
?>
                   <a href="<?php echo $url?>">
                     <span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
<?php
}

function do_page_people_info($member_id) {
  $photo_url = member_get_photo_url_by_id($member_id);
  $name = member_get_name_by_id($member_id);
  $email = member_get_email_by_name($name);
  $gender = member_get_gender_by_id($member_id);
  $phone = member_get_phone_by_id($member_id);
?>
          <div class="panel panel-default">
            <div class="panel-heading"><strong>
                <img src="<?php echo $photo_url?>" alt="照片" width="150"
                  height="187"></strong></div>
            <table class="table table-striped table-bordered">
              <tr>
                <td>姓名</td><td><?php echo $name ?></td></tr>
              <tr>
                <td>电子邮箱</td><td><a href="mailto:<?php echo $email ?>"><?php echo $email ?></a></td></tr>
              <tr>
                <td>微软Lync</td><td><a href="sip:<?php echo $email ?>">启动消息</a>&nbsp;&nbsp;<a href="callto:tel:<?php echo $phone ?>">启动语音</a></td></tr>
              <tr>
                <td>电话</td><td><?php echo $phone ?></td></tr>
              <tr>
                <td>性别</td><td><?php echo $gender ?></td></tr>
            </table>
          </div>
<?php
}

function do_page_people_edit($member_id) {
  $email = member_get_email_by_id($member_id);          
  $name = member_get_name_by_id($member_id);
  $gender = member_get_gender_by_id($member_id);
  $phone = member_get_phone_by_id($member_id);
  $photo_url = member_get_photo_url_by_id($member_id);
?>
          <div class="panel panel-default">
            <div class="panel-heading">通讯录#<?php echo $member_id?></div>

          <form class="form-horizontal" role="form" method="post"
                action="people.php?tab=m&mid=<?php echo $member_id?>">
            <div class="form-group">
              <input name="member_id" type="hidden" class="form-control" value="<?php echo $member_id?>">
            </div>
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
                  <option <?php if (!$gender) 
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
            <br />
            <div class="form-group">
              <div class="col-sm-3"></div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;修改信息</span></button>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="reset"><span class="glyphicon glyphicon-repeat" aria-hidden="true">&nbsp;重新设定</span></button>
              </div>
              <div class="col-sm-3"></div>
            </div>
          </form>
          <br />
          </div>
<?php
}

function do_page_people_new() {
  if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    unset($_SESSION['email']);
  }
  if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
    unset($_SESSION['name']);
  }
  if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone'];
    unset($_SESSION['phone']);
  }
  if (isset($_SESSION['photo_url'])) {
    $photo_url = $_SESSION['photo_url'];
    unset($_SESSION['photo_url']);
  }
  if (isset($_SESSION['gender'])) {
    $gender = $_SESSION['gender'];
    unset($_SESSION['gender']);
  }
?>
          <div class="panel panel-default">
            <div class="panel-heading">新联系人</div>
            <br />
          <form class="form-horizontal" role="form" method="post"
                action="people_new.php">
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
                  <option <?php if (!$gender) 
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
            <br />
            <div class="form-group">
              <div class="col-sm-3"></div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-ok" aria-hidden="true">&nbsp;添加到通讯录</span></button>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="reset"><span class="glyphicon glyphicon-repeat" aria-hidden="true">&nbsp;重新设定&nbsp;&nbsp;</span></button>
              </div>
              <div class="col-sm-3"></div>
            </div>
          </form>
          <br />
          </div>
<?php
}

function do_page_people_import() {
?>
          <br />
          <form class="form-horizontal" role="form" method="post"
                action="vcard_upload.php" enctype="multipart/form-data">
            <div class="form-group">
              <label class="col-sm-3 control-label">电子名片文件</label>
              <div class="col-sm-6">
                <input name="vcardfile" type="file" class="form-control"
                       aria-describedby="basic-addon1" accept="text/x-vcard"
                     size="100%" required>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
            <div class="form-group">
              <div class="col-sm-4"></div>
              <div class="col-sm-4">
                <button class="btn btn-primary btn-block" type="submit"><span class="glyphicon glyphicon-open" aria-hidden="true">&nbsp;导入到通讯录</span></button>
              </div>
              <div class="col-sm-4"></div>
            </div>
          </form>
    
<?php
}

function do_vcard() {
  do_html_header("导入联系人 - ".APP_NAME, "navbar", false);
  do_page_people_navbar();
  do_page_vcard();
  do_page_footer();
  do_html_footer();
}

function do_page_vcard() {
?>
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong><a href="people.php?tab=m">通讯录</a></strong>&nbsp;>&nbsp;<strong><a href="people.php?tab=v">导入联系人</a></strong>&nbsp;>&nbsp;选择要导入的联系人</div>
            <div class="panel-body">
            <br />
<?php
  do_page_vcard_list();
?>
            </div>
          </div>
        </div>
<?php
}

function do_page_vcard_list() {
  $file_type = $_FILES["vcardfile"]["type"];
  $file_name = iconv('UTF-8', 'GBK', $_FILES["vcardfile"]["name"]);
  $file_size = $_FILES["vcardfile"]["size"];
  $file_error = $_FILES["vcardfile"]["error"];
  $file_tmp_name = $_FILES["vcardfile"]["tmp_name"];
  if ((($file_type != "application/octet-stream") && ($file_type != "text/x-vcard"))  || (file_get_extension($file_name) != "vcf")) {
    msg_bar_warning_noclose("文件类型错误！（类型:".$file_type."，文件名：".$_FILES["vcardfile"]["name"].")");
  } else if ($file_size > VCARD_FILE_SIZE_MAX) {
    msg_bar_warning_noclose("文件大小不能超过".ceil(VCARD_FILE_SIZE_MAX/1024)."KB！");
  } else if ($file_error > 0) {
      msg_bar_warning_noclose("文件上传出错（".$file_error.")！");
  } else {
?>
                  <table class="table table-striped table-bordered">
                  <tr>
                    <td>文件名</td><td><?php echo iconv('GBK', 'UTF-8', $file_name) ?></td></tr>
                  <tr>
                    <td>类型</td><td><?php echo $file_type ?></td></tr>
                  <tr>
                    <td>大小</td><td><?php echo ceil($file_size/1024)." KB" ?></td></tr>
                  </table>
<?php
      $vcards = vcard_file_parse($file_tmp_name);
      if (count($vcards) > 0 ) {
        do_page_vcard_list_($vcards);
      }
  }
}

function do_page_vcard_list_($vcards) {
?>
                  <form class="form-horizontal" role="form" method="post" action="vcard_import.php">
                    <table class="table table-striped table-bordered">
                    <tr>
                      <td><strong>姓名</strong></td>
                      <td><strong>电子邮箱</strong></td>
                      <td><strong>电话</strong></td></tr>
<?php
  foreach($vcards as $card_num=>$card) {
    if (($card['FN']) && ($card['EMAIL'])) {
?>
                    <tr>
                      <td><?php echo $card['FN'] ?></td>
                      <td><?php echo $card['EMAIL'] ?></td>
                      <td><?php echo $card['TEL'] ?></td></tr>
                    <input type="hidden" name="FN[]" value="<?php echo $card['FN'] ?>">
                    <input type="hidden" name="EMAIL[]" value="<?php echo $card['EMAIL'] ?>">
                    <input type="hidden" name="TEL[]" value="<?php echo $card['TEL'] ?>">
<?php
    }
  }
?>
                    </table>
                    <div class="form-group">
                      <div class="col-sm-4"></div>
                      <div class="col-sm-4">
                        <button class="btn btn-primary btn-block"
                            type="submit">导入到通讯录</button>
                      </div>
                      <div class="col-sm-4"></div>
                    </div>
                  </form>
<?php
}
