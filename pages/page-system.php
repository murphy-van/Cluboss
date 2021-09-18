<?php
function do_system() {
  do_html_header("系统 - ".APP_NAME, "navbar", false);
  do_page_sys_navbar();
  do_page_system();
  do_page_footer();
  do_html_footer();
}

function do_page_sys_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("系统");
  do_page_navbar_right();
}

function do_page_system() {
  $req_tab = request_get("tab", "u");
  if (!is_super_user()) {
    msg_bar_warning_noclose("您没有访问这个网页的权限！");
    return;
  }
?>
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>系统信息</strong></div>
            <div class="panel-body">
            <ul class="nav nav-tabs">
              <li role="presentation" 
                <?php if ($req_tab == "u") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=u">注册用户</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "st") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=st">统计-时间</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "su") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=su">统计-用户</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "si") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=si">统计-IP</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "sp") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=sp">统计-网页</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "sr") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=sr">统计-URL</a></li>
              <li role="presentation" 
                <?php if ($req_tab == "v") { ?>class="active"<?php } ?>>
                <a href="system.php?tab=v">系统变量</a></li>
            </ul>
            <br />
<?php
            switch ($req_tab) {
              case "u": /* User */
                do_page_system_user();
                break;
              case "st": /* Statistics - Time */
                do_page_sys_stat_time();
                break;
              case "su": /* Statistics - User*/
                do_page_sys_stat_user();
                break;
              case "si": /* Statistics - IP */
                do_page_sys_stat_ip();
                break;
              case "sp": /* Statistics - Page */
                do_page_sys_stat_page();
                break;
              case "sr": /* Statistics - URL */
                do_page_sys_stat_url();
                break;
              case "v": /* Statistics - Variables */
                do_page_sys_variables();
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

function do_page_system_user() {
  $orderby = request_get("orderby", "id");
  $order = request_get("order", "a");
  $page = request_get("page", 1);
  if (($page > user_get_pages(ITEM_PER_PAGE)) || ($page <= 0)) {
    $page = 1;
  }

  $r = user_get_all($orderby, $order, $page, ITEM_PER_PAGE);
  do_page_system_user_($r, $orderby, $order, $page);
}

function do_page_system_user_($r, $orderby, $order, $page) {
  if ($order == "a") {
    $icon = "glyphicon-triangle-top";
    $urlorder = "d";
  } else {
    $icon = "glyphicon-triangle-bottom";
    $urlorder = "a";
  }
  if ($r) {
    do_page_list_page_move($orderby, $order, $page, user_get_pages(ITEM_PER_PAGE), "system.php?tab=u&");
?>
            <table class="table table-striped table-bordered">
              <tr>
                <td>
                </td>
                <td><a href="system.php?orderby=email&order=<?php echo $urlorder?>&page=<?php echo $page?>"><strong>注册电子邮箱</strong></a>
<?php
    if ($orderby == "email") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><strong>姓名</strong>
<?php
    if ($orderby == "name") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><strong>性别</strong>
<?php
    if ($orderby == "gender") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><strong>电话</strong>
<?php
    if ($orderby == "phone") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="system.php?orderby=register_date&order=<?php echo $urlorder?>&page=<?php echo $page?>"><strong>注册日期</strong></a>
<?php
    if ($orderby == "register_date") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="system.php?orderby=signin_count&order=<?php echo $urlorder?>&page=<?php echo $page?>"><strong>登录次数</strong></a>
<?php
    if ($orderby == "signin_count") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="system.php?orderby=last_signin_time&order=<?php echo $urlorder?>&page=<?php echo $page?>"><strong>上次登录时间</strong></a>
<?php
    if ($orderby == "last_signin_time") {
?>
                    <span class="glyphicon <?php echo $icon?>" aria-hidden="true"></span>
<?php
    }
?>
                </td>
                <td><a href="system.php?orderby=last_signin_ip&order=<?php echo $urlorder?>&page=<?php echo $page?>"><strong>上次登录IP</strong></a>
<?php
    if ($orderby == "last_signin_ip") {
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
      $user_id = $row['id'];
      $mail = $row['email'];
      $member_id = user_get_member_id_by_id($user_id);
      $name = member_get_name_by_id($member_id);
      $gender = member_get_gender_by_id($member_id);
      $phone = member_get_phone_by_id($member_id);
      $photo_url = member_get_photo_url_by_id($member_id);
?>
              <tr>
                <td><img src="<?php echo $photo_url?>" alt="照片" width="30" height="37"></td>
                <td><a href="mailto:<?php echo $mail?>"><?php echo $mail?></a></td>
                <td><a href="user.php?uid=<?php echo $user_id?>"><?php echo $name?></a></td>
                <td><?php echo $gender?></td>
                <td><?php echo $phone?></td>
                <td><?php echo $row['register_date']?></td>
                <td><?php echo $row['signin_count']?></td>
                <td><?php echo $row['last_signin_time']?></td>
                <td><?php echo $row['last_signin_ip']?></td>
              </tr>
<?php
    }
?>
            </table>
<?php
    do_page_list_page_move($orderby, $order, $page, user_get_pages(ITEM_PER_PAGE), "system.php?tab=u&");
  }
}

function do_page_sys_stat_time(){
  $t = request_get("t", LOG_DAY);
  $unit = array("", "秒", "分", "小时", "日", "周", "月", "年");
?>
            <ul class="nav nav-pills">
<?php
  for ($i=LOG_MINUTE;$i<=LOG_YEAR;$i++) {
?>
              <li role="presentation" <?php if ($t==$i) echo "class='active'"?>>
                <a href="system.php?tab=st&t=<?php echo $i?>"><?php echo $unit[$i]?></a></li>
<?php              
  }
?>
            </ul>
            <br />
            <div id="canvasDiv"></div>
            <br />
            <table class="table table-striped table-bordered">
              <tr>
                <td><strong>最近</strong></td>
                <td><strong>访问次数</strong></td>
              </tr>

<?php
  for ($i=1;$i<=10;$i++) {
    echo "              <tr>\n";
    echo "                <td>前".$i.$unit[$t]."</td>\n";
    echo "                <td>".log_get_time_count($t, $i)."</td>\n";
    echo "              </tr>\n";
  }
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
  for ($i=1;$i<=10;$i++) {
?>
					      {name : '<?php echo "前".$i.$unit[$t]?>',value : <?php echo log_get_time_count($t, $i)?>,color:'#cbab4f'},
<?php
  }
?>
				        	];
		        	
					new iChart.Bar2D({
						render : 'canvasDiv',
						data: data,
						title : '访问统计 - 时间',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
              width:580,
              height:240,
              grid_color:'#4e5464',
              axis:{
                color:'#4e5464',
                width:[0,0,8,1]
              },
              scale:[{
                 position:'bottom',	
                 start_scale:0,
                 end_scale:40,
                 scale_space:5,
                 label:{color:'#ffffff'},
                 listeners:{
                  parseText:function(t,x,y){
                    return {text:t+""}
                  }
                 }
              }]
            },
            label:{color:'#dcdcdc'},
            background_color : '#3c4251',
            sub_option:{
              listeners:{
                parseText:function(r,t){
                  return t+"";
                }
              }
            },
            animation: true
					}).draw();
			});
				
			</script>
<?php
}
              
function do_page_sys_stat_user() {
  $r = log_get_top10_user();
  if ($r) {
?>
            <div id="canvasDiv"></div>
            <br />
            <table class="table table-striped table-bordered">
              <tr>
                <td><strong>用户</strong></td>
                <td><strong>访问次数</strong></td>
              </tr>
<?php
    $ichartdata = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
?>
              <tr>
                <td><?php echo $row['user']?></td>
                <td><?php echo $row['count(user)']?></td>
              </tr>
<?php
      $ichartdata .= "					      {name : '".$row['user']."',value : ".$row['count(user)'].",color:'#cbab4f'},\n";
    }
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
    echo $ichartdata;
?>
				        	];
		        	
					new iChart.Bar2D({
						render : 'canvasDiv',
						data: data,
						title : '访问统计 - 用户',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
              width:350,
              height:240,
              grid_color:'#4e5464',
              axis:{
                color:'#4e5464',
                width:[0,0,8,1]
              },
              scale:[{
                 position:'bottom',	
                 start_scale:0,
                 end_scale:40,
                 scale_space:5,
                 label:{color:'#ffffff'},
                 listeners:{
                  parseText:function(t,x,y){
                    return {text:t+""}
                  }
                 }
              }]
            },
            label:{color:'#dcdcdc'},
            background_color : '#3c4251',
            sub_option:{
              listeners:{
                parseText:function(r,t){
                  return t+"";
                }
              }
            },
            animation: true
					}).draw();
			});
				
			</script>
<?php
  }
}

function do_page_sys_stat_ip() {
  $r = log_get_top10_ip();
  if ($r) {
?>
            <div id="canvasDiv"></div>
            <br />
            <table class="table table-striped table-bordered">
              <tr>
                <td><strong>IP地址</strong></td>
                <td><strong>访问次数</strong></td>
              </tr>
<?php
    $ichardata = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
?>
              <tr>
                <td><?php echo $row['fromip']?></td>
                <td><?php echo $row['count(fromip)']?></td>
              </tr>
<?php
      $ichartdata .= "					      {name : '".$row['fromip']."',value : ".$row['count(fromip)'].",color:'#cbab4f'},\n";
    }
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
    echo $ichartdata;
?>
				        	];
		        	
					new iChart.Bar2D({
						render : 'canvasDiv',
						data: data,
						title : '访问统计 - IP',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
              width:580,
              height:240,
              grid_color:'#4e5464',
              axis:{
                color:'#4e5464',
                width:[0,0,8,1]
              },
              scale:[{
                 position:'bottom',	
                 start_scale:0,
                 end_scale:40,
                 scale_space:5,
                 label:{color:'#ffffff'},
                 listeners:{
                  parseText:function(t,x,y){
                    return {text:t+""}
                  }
                 }
              }]
            },
            label:{color:'#dcdcdc'},
            background_color : '#3c4251',
            sub_option:{
              listeners:{
                parseText:function(r,t){
                  return t+"";
                }
              }
            },
            animation: true
					}).draw();
			});
				
			</script>
<?php
  }
}

function do_page_sys_stat_page() {
  $r = log_get_top10_page();
  if ($r) {
?>
            <div id="canvasDiv"></div>
            <br />
            <table class="table">
              <tr>
                <td><strong>页面</strong></td>
                <td><strong>访问次数</strong></td>
              </tr>
<?php
    $ichardata = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
?>
              <tr>
                <td><?php echo $row['page']?></td>
                <td><?php echo $row['count(page)']?></td>
              </tr>
<?php
      $ichartdata .= "					      {name : '".$row['page']."',value : ".$row['count(page)'].",color:'#cbab4f'},\n";
    }
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
    echo $ichartdata;
?>
				        	];
		        	
					new iChart.Bar2D({
						render : 'canvasDiv',
						data: data,
						title : '访问统计 - 网页',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
              width:500,
              height:240,
              grid_color:'#4e5464',
              axis:{
                color:'#4e5464',
                width:[0,0,8,1]
              },
              scale:[{
                 position:'bottom',	
                 start_scale:0,
                 end_scale:10,
                 scale_space:5,
                 label:{color:'#ffffff'},
                 listeners:{
                  parseText:function(t,x,y){
                    return {text:t+""}
                  }
                 }
              }]
            },
            label:{color:'#dcdcdc'},
            background_color : '#3c4251',
            sub_option:{
              listeners:{
                parseText:function(r,t){
                  return t+"";
                }
              }
            },
            animation: true
					}).draw();
			});
				
			</script>
<?php
  }
}

function do_page_sys_stat_url() {
  $r = log_get_top10_url();
  if ($r) {
?>
            <div id="canvasDiv"></div>
            <br />
            <table class="table table-striped table-bordered">
              <tr>
                <td><strong>URL</strong></td>
                <td><strong>访问次数</strong></td>
              </tr>
<?php
    $ichardata = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
?>
              <tr>
                <td><?php echo $row['url']?></td>
                <td><?php echo $row['count(url)']?></td>
              </tr>
<?php
      $ichartdata .= "					      {name : '".$row['url']."',value : ".$row['count(url)'].",color:'#cbab4f'},\n";
    }
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
    echo $ichartdata;
?>
				        	];
		        	
					new iChart.Bar2D({
						render : 'canvasDiv',
						data: data,
						title : '访问统计 - URL',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
              width:400,
              height:240,
              grid_color:'#4e5464',
              axis:{
                color:'#4e5464',
                width:[0,0,8,1]
              },
              scale:[{
                 position:'bottom',	
                 start_scale:0,
                 end_scale:10,
                 scale_space:5,
                 label:{color:'#ffffff'},
                 listeners:{
                  parseText:function(t,x,y){
                    return {text:t+""}
                  }
                 }
              }]
            },
            label:{color:'#dcdcdc'},
            background_color : '#3c4251',
            sub_option:{
              listeners:{
                parseText:function(r,t){
                  return t+"";
                }
              }
            },
            animation: true
					}).draw();
			});
				
			</script>
<?php
  }
}

function do_page_sys_variables() {
?>
      <table class="table table-striped table-bordered" >
        <tr>
          <td><strong>变量</strong></td>
          <td><strong>值</strong></td>
        </tr>
        <tr>
          <td>$php_errormsg</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($php_errormsg, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  foreach ($GLOBALS as $name => $value) {
    if (strlen($value)>0) {
?>
        <tr>
          <td>$GLOBALS[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
    }
  }
  foreach ($_SESSION as $name => $value) {
?>
        <tr>
          <td>$_SESSION[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  }
  foreach ($_REQUEST as $name => $value) {
?>
        <tr>
          <td>$_REQUEST[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  }
  foreach ($_GET as $name => $value) {
?>
        <tr>
          <td>$_GET[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  }
  foreach ($_FILES as $name => $value) {
?>
        <tr>
          <td>$_FILES[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  }
  foreach ($_ENV as $name => $value) {
?>
        <tr>
          <td>$_ENV[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  }
  foreach ($_SERVER as $name => $value) {
    if (strlen($value) > 0) {
?>
        <tr>
          <td>$_SERVER[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
    }
  }
  foreach ($_COOKIE as $name => $value) {
?>
        <tr>
          <td>$_COOKIE[<?php echo $name?>]</td>
          <td style="word-break:break-all;word-wrap:break-all;"><?php echo htmlspecialchars($value, ENT_NOQUOTES, "UTF-8")?></td>
        </tr>
<?php
  }
?>
      </table>
<?php
}
