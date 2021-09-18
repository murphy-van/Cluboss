<?php

function do_facility() {
  do_html_header("场所商户 - ".APP_NAME, "navbar", false);
  do_page_facility_navbar();
  do_page_facility();
  do_page_footer();
  do_html_footer();
}

function do_page_facility_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("场所商户");
  do_page_navbar_right();
}

function do_page_facility() {
  $facility_id = request_get("fid", NULL);
  $facility_name = facility_get_name_by_id(user_get_signin_id(), $facility_id);
  $req_tab = request_get("tab", NULL);
  if (!$req_tab) { 
    if ($facility_id) {
      $req_tab = "i";
    } else {
      $req_tab = "l";
    }
  }
  $act = request_get("act", NULL);
?>
        <div class="jumbotron">
          <div class="panel panel-default">
<?php
  if ($facility_id > 0) {
    $user_id = user_get_signin_id();
    $name = facility_get_name_by_id($user_id, $facility_id);
    if (!$name) {
      msg_bar_error_noclose("指定的场所商户不存在！");
?>
          </div>
        </div>
<?php
      return;
    }
?>
            <div class="panel-heading"><strong><a href="facility.php?tab=l">场所商户</a>&nbsp;>&nbsp;<?php echo $facility_name?></strong></div>
            <div class="panel-body">
            <br />
<?php
    if ($act) {
      switch ($act) {
        case "e":
          do_page_facility_edit($facility_id);
          break;
        case "d":
          do_page_facility_del($facility_id);
          break;
        default:
          break;
      }
    } else {
      switch ($req_tab) {
        case "i":
          do_page_facility_info($facility_id);
          break;
        default:
          break;
      }
    }
  } else {
?>
            <div class="panel-heading"><strong>场所商户</strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "l") { ?>class="active"<?php } ?>>
                <a href="facility.php?tab=l">所有场所商户</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "a") { ?>class="active"<?php } ?>>
                <a href="facility.php?tab=a">添加新场所商户</a></li>
            </ul>
            <br />
<?php
    switch ($req_tab) {
      case "l":
        do_page_facility_list();
        break;
      case "a":
        do_page_facility_new();
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

function do_page_facility_info($facility_id) {
  $user_id = user_get_signin_id();
  $name = facility_get_name_by_id($user_id, $facility_id);
  $address = facility_get_address_by_id($user_id, $facility_id);
  $phone = facility_get_phone_by_id($user_id, $facility_id);
  $event_count = facility_get_event_count($user_id, $facility_id);
?>
            <table class="table table-striped table-bordered">
              <tr>
                <td>名称</td><td><?php echo $name ?></td></tr>
              <tr>
                <td>地址</td><td><?php echo $address ?></td></tr>
              <tr>
                <td>电话</td><td><?php echo $phone ?></td></tr>
              <tr>
                <td>活动次数（所有俱乐部）</td><td><?php echo $event_count ?></td></tr>
            </table>
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
              <a href="facility.php?fid=<?php echo $facility_id?>&act=e" class="btn btn-primary btn-block">编辑</a>
            </div>
            <div class="col-sm-4"></div>
            <br />
<?php
}

function do_page_facility_list() {
  $orderby = request_get("orderby", "id");
  $order = request_get("order", "a");
  $page = request_get("page", 1);
  $user_id = user_get_signin_id();
  $pages = facility_get_pages($user_id, ITEM_PER_PAGE_FACILITY);
  if (($page > $pages) || ($page <= 0)) {
    $page = 1;
  }
  $r = facility_get_all($user_id, $orderby, $order, $page, ITEM_PER_PAGE_EVENT);
  if ($r) {
    do_page_list_page_move($orderby, $order, $page, $pages, "facility.php?tab=l&");
    do_page_facility_list_($r, "facility.php?tab=l", $orderby, $order);
    do_page_list_page_move($orderby, $order, $page, $pages, "facility.php?tab=l&");
  } /* of if r */
}

function do_page_facility_list_($r, $url, $orderby, $order) {
  if ($order == "a") {
    $icon = "glyphicon-triangle-top";
    $urlorder = "d";
  } else {
    $icon = "glyphicon-triangle-bottom";
    $urlorder = "a";
  }
?>
            <table class="table table-striped table-bordered">
              <tr>
                <td><a href="<?php echo $url?>&orderby=name&order=<?php echo $urlorder?>"><strong>名称</strong></a>
<?php
    if ($orderby == "name") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="<?php echo $url?>&orderby=address&order=<?php echo $urlorder?>"><strong>地址</strong></a>
<?php
    if ($orderby == "address") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="<?php echo $url?>&orderby=event_count&order=<?php echo $urlorder?>"><strong>活动次数（所有俱乐部）</strong></a>
<?php
    if ($orderby == "event_count") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                </tr>
<?php
    $user_id = user_get_signin_id();
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $facility_id = $row['id'];
      $name = $row['name'];
      $address = $row['address'];
      $event_count = facility_get_event_count($user_id, $facility_id);
?>
              <tr>
                <td><a href="facility.php?tab=i&fid=<?php echo $facility_id?>"><?php echo $name?></a></td>
                <td><?php echo $address?></td>
                <td><?php echo $event_count?>
              </tr>
<?php
    } /* of for i */
?>
                </table>
<?php
}

function do_page_facility_new() {
  if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
    unset($_SESSION['name']);
  }
  if (isset($_SESSION['address'])) {
    $address = $_SESSION['address'];
    unset($_SESSION['address']);
  }
  if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone'];
    unset($_SESSION['phone']);
  }
?>
          <div class="panel panel-default">
            <div class="panel-heading">新场所商户</div>
            <br />
          <form class="form-horizontal" role="form" method="post"
                action="facility_new.php">
            <div class="form-group">
              <label class="col-sm-3 control-label">名称</label>
              <div class="col-sm-6">
                <input name="name" type="text" class="form-control"
                     placeholder="名称（必填）" aria-describedby="basic-addon1"
                     size="100%" required value="<?php echo $name?>"
                     maxlength="30">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">地址</label>
              <div class="col-sm-6">
                <input name="address" type="text" class="form-control"
                     placeholder="地址（可选）" aria-describedby="basic-addon1"
                     size="100%" value="<?php echo $address?>"
                     maxlength="100">
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
            <br />
            <div class="form-group">
              <div class="col-sm-3"></div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="submit">添加</button>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="reset">重新设定</button>
              </div>
              <div class="col-sm-3"></div>
            </div>
            <br />
          </form>
          </div>
<?php
}

function do_page_facility_edit($facility_id) {
  $user_id = user_get_signin_id();
  $name = facility_get_name_by_id($user_id, $facility_id);
  $address = facility_get_address_by_id($user_id, $facility_id);
  $phone = facility_get_phone_by_id($user_id, $facility_id);
  if (isset($_SESSION['name'])) {
    $name = $_SESSION['name'];
    unset($_SESSION['name']);
  }
?>
          <form class="form-horizontal" role="form" method="post"
                action="facility_edit.php">
            <div class="form-group">
              <input name="facility_id" type="hidden" class="form-control" value="<?php echo $facility_id?>">
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">名称</label>
              <div class="col-sm-6">
                <input name="name" type="text" class="form-control"
                     placeholder="名称（必填）" aria-describedby="basic-addon1"
                     size="100%" required value="<?php echo $name?>"
                     maxlength="30">
              </div>
              <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">地址</label>
              <div class="col-sm-6">
                <input name="address" type="text" class="form-control"
                     placeholder="地址（可选）" aria-describedby="basic-addon1"
                     size="100%" required value="<?php echo $address?>"
                     maxlength="100">
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
            <br />
            <div class="form-group">
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="submit">修改信息</button>
              </div>
              <div class="col-sm-3">
                <button class="btn btn-primary btn-block" type="reset">重新设定</button>
              </div>
              <div class="col-sm-3">
<?php
  $event_count = facility_get_event_count($user_id, $facility_id);
  if ($event_count > 0) {
    $can_del = " disabled";
  } else {
    $can_del = "";
  }
?>
              <a href="facility.php?fid=<?php echo $facility_id?>&act=d" class="btn btn-primary btn-block<?php echo $can_del?>" onclick="return confirm('确定要删除该场所商户吗？')">删除</a>
              </div>
              <div class="col-sm-3">
                <a href="facility.php?tab=i&fid=<?php echo $facility_id?>" class="btn btn-primary btn-block">取消修改</a>
              </div>
            </div>
          </form>
<?php
}

function do_page_facility_del($facility_id) {
  $user_id = user_get_signin_id();
  $event_count = facility_get_event_count($user_id, $facility_id);
  if ($event_count) {
    msg_bar_warning_noclose("删除场所商户失败，还有".$event_count."个活动记录在这个场所商户上！");
  } else if (facility_del($user_id, $facility_id)){
    msg_bar_success_noclose("删除场所商户成功！");
  }
}