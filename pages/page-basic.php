<?php
function do_html_header($title, $style, $autoback) {
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Club&Joy">
    <meta name="author" content="Murphy Wan">
<?php
  $backurl = request_get("backurl", NULL);
  if (($autoback) && ($backurl)) {
?>
    <meta http-equiv="Refresh" content="3; url=<?php echo $backurl?>">
<?php
  }
?>
    <link rel="icon" href="images/Logo.ico">

    <title><?php echo $title ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/paper.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/<?php echo $style ?>.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
    
    <!-- iChartJS 1.2 -->
    <script type="text/javascript" src="js/ichart.1.2.min.js"></script>
    
    <!-- Bootbox -->
    <script type="text/javascript" src="js/bootbox.min.js"></script>
  </head>

  <body>

<?php
}

function do_html_footer() {
  log_page();
?>
    <!-- Bootstrap core JavaScript
    ================================================== -->
  </body>
</html>
<?php
}

function do_page_navbar_head($name) {
?>
    <div class="container">

      <!-- Static navbar -->
      <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed"
                    data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="home.php"><span class="glyphicon glyphicon-home" aria-hidden="true">&nbsp;<?php echo $name?></span></a>
          </div>
<?php
}

function do_page_navbar_left($active) {
  $user_id = user_get_signin_id();
?>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
<?php
  if ($user_id) {
?>
              <li <?php if ($active == "俱乐部") {echo "class='active'";}?>>
                <a href="club.php">俱乐部</a></li>
              <li <?php if ($active == "通讯录") {echo "class='active'";}?>>
                <a href="people.php">通讯录</a></li>
<!--              <li <?php if ($active == "我的账户") {echo "class='active'";}?>>
                <a href="account.php">我的账户</a></li>-->
              <li <?php if ($active == "场所商户") {echo "class='active'";}?>>
                <a href="facility.php">场所商户</a></li>
              <form class="navbar-form navbar-left" role="search" action="search.php">
                <div class="form-group">
                  <input type="text" name="key" class="form-control" placeholder="搜索..." required data-toggle="tooltip" data-placement="bottom" title="回车启动搜索">
                </div>
              </form>
<?php
  }
  if (is_super_user()) {
?>
              <li <?php if ($active == "系统") {echo "class='active'";}?>>
                <a href="system.php">系统</a></li>
              <li <?php if ($active == "日志") {echo "class='active'";}?>>
                <a href="log.php">日志</a></li>
<?php
  }
?>
            </ul>
<?php
}

function do_page_navbar_right() {
  $user_id = user_get_signin_id();
  if ($user_id) {
    $name = user_get_name_by_id($user_id);
?>
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown" >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                   role="button" aria-expanded="false"><?php echo $name ?><span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
<?php
    if (user_is_temp($user_id)) {
?>
                  <li><a href="register.php"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;注册</a></li>
<?php
    } else {
?>
                  <li><a href="user.php?uid=<?php echo $user_id?>"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;关于我</a></li>
                  <li><a href="setup.php?uid=<?php echo $user_id?>"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;设置</a></li>
<?php
    }
?>
                  <li class="divider"></li>
                  <li><a href="signout.php"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;退出</a></li>
                </ul>
<?php
  } else {
?>
            <ul class="nav navbar-nav navbar-right">
              <li class="dropdown" >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                   role="button" aria-expanded="false">登录<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="signin.php"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;登录</a></li>
                  <li><a href="register.php"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;注册</a></li>
                  <li><a href="forget_pwd.php"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;&nbsp;忘记密码?</a></li>
                </ul>
<?php
  }
?>
              </li>

            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
<?php
  if (user_is_temp($user_id)) {
    msg_bar_info("您正在使用临时用户！如果页面未操作超过".floor(TEMP_USER_TIMEOUT_SECOND/3600)."小时或主动退出，所有临时数据都将被系统删除！如果想要保存这些临时数据或扩大存储容量，请选择转换到注册用户！");
  }
  msg_bar(NULL);
  msg_bar(NULL);
}

function do_page_footer() {
?>
      <footer class="footer">
        <p align="center"><a href="privacy.php">隐私策略</a>&nbsp;&nbsp;
          <a href="license.php">版权说明</a>&nbsp;&nbsp;
          <a href="disclaimer.php">免责声明</a>&nbsp;&nbsp;
          <a href="contact.php">联系我们</a></p>
        <p align="center">&copy 2015&nbsp;<?php echo APP_NAME?>&nbsp;&nbsp;保留一切权利&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.miitbeian.gov.cn/">京ICP备15016178号</a></p>
      </footer>
    </div> <!-- /container -->
<?php
}

function do_signin_form($return_url) {
  if (isset($_SESSION['signin_email'])) {
    $signin_email = $_SESSION['signin_email'];
    unset($_SESSION['signin_email']);
  }
?>
    <div class="container">
      <br />
      <form class="form-signin" method="post" action="<?php echo $return_url ?>">
        <h2 class="form-signin-heading">登录<?php echo APP_NAME?></h2>
        <input type="email" name="email" id="inputEmail" class="form-control"
               placeholder="<?php echo APP_NAME?>注册电子邮箱（必填）" required autofocus
               value="<?php if (user_get_recent()) {echo user_get_recent();} else {echo $signin_email;}?>">
        <input type="password" name="passwd" id="inputPassword" class="form-control"
               placeholder="密码（必填）" required maxlength="40">
        <input name="shapwd" type="hidden" class="form-control">
        <div class="checkbox">
          <label>
            <input type="checkbox" name="remember-me"
              <?php if (user_get_remember()) {echo "checked='checked'";}?>>7天内自动登录</label>
        </div>
<?php
  if (isset($_SESSION['failed_email'])) {
    unset($_SESSION['failed_email']);
?>
        <div class="form-group">
          <label class="control-label"><img src="captcha.php"></label>
          <input name="vercode" type="text" class="form-control"
                 placeholder="请输入左边图片中的数字（必填）" aria-describedby="basic-addon1"
                 size="100%" required maxlength="50">
        </div>
<?php
  }
?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">登录</button>
        <div class="row">
          <div class="col-md-8">
            <a href="forget_pwd.php">
              忘记密码？</a>
          </div>
          <div class="col-md-4" class="text-right">
            <a href="register.php">注册新用户</a>
          </div>
        </div>
        </form>
      <br /><br /><br /><br />
    </div> <!-- /container -->
<?php
}

function do_signin() {
  do_html_header("登录 - ".APP_NAME, "signin", false);
  do_page_navbar();
  do_signin_form("signin.php");
  do_page_footer();
  do_html_footer();
}

function do_page_navbar() {
  do_page_navbar_head(APP_NAME);
  do_page_navbar_left("");
  do_page_navbar_right();
}

function do_privacy() {
  do_html_header("隐私策略 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_privacy();
  do_page_footer();
  do_html_footer();
}

function do_license() {
  do_html_header("版权说明 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_license();
  do_page_footer();
  do_html_footer();
}

function do_disclaimer() {
  do_html_header("免责说明 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_disclaimer();
  do_page_footer();
  do_html_footer();
}

function do_contact() {
  do_html_header("联系我们 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_contact();
  do_page_footer();
  do_html_footer();
}

function do_page_privacy() {
?>
  <div class="page-header">
    <h4>隐私声明</h4>
  </div>
    <p><?php echo APP_NAME?>非常重视对您的个人隐私保护，有时候我们需要某些信息才能为您提供您请求的服务，本隐私声明解释了这些情况下的数据收集和使用情况。本隐私声明适用于<?php echo APP_NAME?>的所有相关服务，其内容可由<?php echo APP_NAME?>随时更新，毋须另行通知。更新后的隐私声明一旦在网页上公布即有效代替原来的隐私声明。</p>
  <h5>我们收集哪些信息</h5>
    <p>通常，您在匿名的状态下访问本网站。当我们需要能识别您的个人信息或者可以与您联系的信息时，我们会征求您的同意。通常，在您联系我们时，我们可能收集这些信息：姓名，Email，住址和电话号码，并征求您的确认。</p>
  <h5>关于您的个人信息</h5>
    <p><?php echo APP_NAME?>严格保护您个人信息的安全。我们尽量使用各种安全技术和程序来保护您的个人信息不被未经授权的访问、使用或泄露。</p>
    <p><?php echo APP_NAME?>会在法律要求或符合<?php echo APP_NAME?>的相关服务条款、软件许可使用协议约定的情况下透露您的个人信息，或者有充分理由相信必须这样做才能：(a) 满足法律或行政法规的明文规定，或者符合<?php echo APP_NAME?>网站适用的法律程序；（b）符合<?php echo APP_NAME?>相关服务条款、软件许可使用协议的约定；(c) 保护<?php echo APP_NAME?>的权利或财产，以及 (d) 在紧急情况下保护<?php echo APP_NAME?>员工、<?php echo APP_NAME?>产品或服务的用户或大众的个人安全。</p>
    <p><?php echo APP_NAME?>不会未经您的允许将这些信息与第三方共享，本声明已经列出的上述情况除外。</p>
  <h5>Cookie的使用</h5>
  　<p>使用 Cookie 能帮助您实现您的联机体验的个性化，您可以接受或拒绝 Cookie ，大多数 Web 浏览器会自动接受 Cookie，但您通常可根据自己的需要来修改浏览器的设置以拒绝 Cookie。<?php echo APP_NAME?>有时会使用 Cookie 以便使您在访问<?php echo APP_NAME?>时能得到更好的服务。Cookie不会跟踪个人信息。来自<?php echo APP_NAME?>的 Cookie 只能被<?php echo APP_NAME?>读取。</p>
　　<p>如果您的浏览器被设置为拒绝 Cookie，您仍然能够访问<?php echo APP_NAME?>的大多数网页。</p>
  <h5>关于免责说明</h5>
    <p>就下列相关事宜的发生，<?php echo APP_NAME?>不承担任何法律责任：</p>
    <p>由于您将用户密码告知他人或与他人共享注册帐户，由此导致的任何个人信息的泄露，或其他非因<?php echo APP_NAME?>原因导致的个人信息的泄露；<br /><?php echo APP_NAME?>根据法律规定或政府相关政策要求提供您的个人信息；<br />任何第三方根据<?php echo APP_NAME?>各服务条款及声明中所列明的情况使用您的个人信息，由此所产生的纠纷；<br />任何由于黑客攻击、电脑病毒侵入或政府管制而造成的暂时性网站关闭；<br />因不可抗力导致的任何后果；<br /><?php echo APP_NAME?>在各服务条款及声明中列明的使用方式或免责情形。</p>
  <br />
<?php
}

function do_page_license() {
?>
  <div class="page-header">
    <h4>版权说明</h4>
  </div>
  <h5>总则</h5>
    <p>用户在接受<?php echo APP_NAME?>服务之前，请务必仔细阅读本条款并同意本声明。</p>
    <p>用户直接或通过各类方式（如站外API引用等）间接使用<?php echo APP_NAME?>服务和数据的行为，都将被视作已无条件接受本声明所涉全部内容；若用户对本声明的任何条款有异议，请停止使用<?php echo APP_NAME?>所提供的全部服务。</p>
  <h5>声明内容</h5>
    <p>1.作者发表在<?php echo APP_NAME?>的原创文章、评论、图片等内容的版权均归作者及<?php echo APP_NAME?>共有。</p>
    <p>2.<?php echo APP_NAME?>提供的网络服务中包含的标识、版面设计、排版方式、文本、图片、图形等均受版权、商标及其它法律保护，未经相关权利人（含<?php echo APP_NAME?>及其他原始权利人）同意，上述内容均不得在任何平台被直接或间接发布、使用、出于发布或使用目的的改写或再发行，或被用于其他任何商业目的。</p>
    <p>3.<?php echo APP_NAME?>尊重权利人的知识产权和合法权益。若权利人认为<?php echo APP_NAME?>用户上传的内容侵犯自身版权或其他合法权益，可依法向联系<?php echo APP_NAME?>（ /#contactModal ）发出书面申请。<?php echo APP_NAME?>在书面审核相关齐备材料后，有权在不事先通知相应发布作者的情况下自行删除相关内容，并依法保留相关数据。在<?php echo APP_NAME?>发布内容，即视为作者同意<?php echo APP_NAME?>就前述情况所采取的相应措施，<?php echo APP_NAME?>不因此而承担任何违约、侵权或其他法律责任。</p>
  <h5>附则</h5>
    <p><?php echo APP_NAME?>对于上述声明内容拥有解释权和修订权。</p>
  <br />
<?php
}

function do_page_disclaimer () {
?>
  <div class="page-header">
    <h4>免责声明</h4>
  </div>  
  <h5>总则</h5>
    <p>用户在接受<?php echo APP_NAME?>服务之前，请务必仔细阅读本条款并同意本声明。</p>
    <p>用户直接或通过各类方式（如站外API引用等）间接使用<?php echo APP_NAME?>服务和数据的行为，都将被视作已无条件接受本声明所涉全部内容；若用户对本声明的任何条款有异议，请停止使用<?php echo APP_NAME?>所提供的全部服务。</p>
  <h5>第一条</h5>
    <p>用户以各种方式使用<?php echo APP_NAME?>服务和数据的过程中，不得以任何方式利用<?php echo APP_NAME?>直接或间接从事违反中国法律、以及社会公德的行为，且用户应当恪守下述承诺：</p>
    <p>1. 发布、转载或提供的内容符合中国法律、社会公德；</p> 　　
    <p>2. 不得干扰、损害和侵犯<?php echo APP_NAME?>的各种合法权利与利益；</p> 　　
    <p>3. 遵守<?php echo APP_NAME?>以及与之相关的网络服务的协议、指导原则、管理细则等；</p>
    <p><?php echo APP_NAME?>有权对违反上述承诺的内容予以删除。</p>
  <h5>第二条</h5>
    <p>1. <?php echo APP_NAME?>不保证发布的内容满足您的要求，不保证<?php echo APP_NAME?>的服务不会中断。因网络状况、通讯线路、第三方网站或管理部门的要求等任何原因而导致您不能正常使用<?php echo APP_NAME?>，<?php echo APP_NAME?>不承担任何法律责任。</p>
  　<p>2. 用户在<?php echo APP_NAME?>发表的内容仅表明其个人的立场和观点，并不代表<?php echo APP_NAME?>的立场或观点。作为内容的发表者，需自行对所发表内容负责，因所发表内容引发的一切纠纷，由该内容的发表者承担全部法律及连带责任。<?php echo APP_NAME?>不承担任何法律及连带责任。</p>
    <p>3. 用户在<?php echo APP_NAME?>发布侵犯他人知识产权或其他合法权益的内容，<?php echo APP_NAME?>有权予以删除，并保留移交司法机关处理的权利。</p>
　　<p>4. 个人或单位如认为<?php echo APP_NAME?>上存在侵犯自身合法权益的内容，应准备好具有法律效应的证明材料，及时与<?php echo APP_NAME?>取得联系，以便<?php echo APP_NAME?>迅速做出处理。</p>
  <h5>附则</h5>
　　<p>对免责声明的解释、修改及更新权均属于<?php echo APP_NAME?>所有。</p>
  <br />
<?php
}

function do_page_contact() {
?>
  <address>
    <strong><?php echo APP_NAME?></strong><br>
    北京<br>
    中国<br>
    <!--<abbr title="Phone">电话:</abbr>-->
  </address>

  <address>
    <strong>服务</strong><br>
    <a href="mailto:<?php echo SMTP_FROM?>"><?php echo SMTP_FROM?></a>
  </address>
  <br />
<?php
}

/**
 *检查IP及蜘蛛真实性
 * (check_spider('66.249.74.44',$_SERVER['HTTP_USER_AGENT']));
 * @copyright  http://blog.chacuo.net
 * @author 8292669
 * @param string $ip IP地址
 * @param string $ua ua地址
 * @return false|spidername  false检测失败不在指定列表中
 */
function check_spider($ip,$ua)
{
    static $spider_list=array(
    'google'=>array('Googlebot','googlebot.com'),
    'baidu'=>array('Baiduspider','.baidu.'),
    'yahoo'=>array('Yahoo!','inktomisearch.com'),
    'msn'=>array('MSNBot','live.com'),
    'bing'=>array('bingbot','msn.com')
    );

    if(!preg_match('/^(\d{1,3}\.){3}\d{1,3}$/',$ip)) return false;
    if(empty($ua)) return false;

    foreach ($spider_list as $k=>$v)
    {
        ///如果找到了
        if(stripos($ua,$v[0])!==false)
        {
            $domain = gethostbyaddr($ip);

            if($domain && stripos($domain,$v[1])!==false)
            {
                return $k;
            }
        }
    }
    return false;
}