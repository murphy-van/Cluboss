<?php

function reset_token() {
  /* set user token for change the password */
  $reset_email = post_get("reset_email");
  if ($reset_email) {
    $_SESSION['reset_email'] = $reset_email;
  } else {
    return false;
  }
  $post_vercode = post_get("vercode");
  if (($post_vercode) && isset($_SESSION['vercode'])) {
    if ($post_vercode != $_SESSION['vercode']) {
      msg_bar_error_delay("验证码输入错误！");
      return false;
    }
  } else {
    return;
  }
  $reset_user_name = user_get_name_by_email($reset_email);
  if (!$reset_user_name) {
    msg_bar_warning_delay("输入的电子邮箱（".$reset_email."）没有注册！");
    return false;
  }

  $reset_user_id = user_name2id($reset_user_name);
  $token = random_pwd(10, 7);
  user_set_token($reset_user_id, $token);
  if (notify_token($reset_user_id, $token)) {
    log_r("(".$reset_email.")发送登录提醒", "成功！");
    msg_bar_success_delay("密码提醒成功！请去电子邮箱（".$reset_email."）点击链接重置密码！");
  }
  return true;
}

function random_pwd($length=9, $strength=0) {
  /* return false or new random password */
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}

function notify_pwd($user_id, $pwd) {
  if (($user_id) && ($pwd)) {
    $email = user_get_mail_by_id($user_id);
    $name = user_get_name_by_id($user_id);
    $pwd_notify_info = "您的密码已经被重置为（<strong>".$pwd."</strong>)。<br />请登录后修改！<br />";
    $body = get_mail_pwd_notify($name, $pwd_notify_info);
    $mailto = $email." ".$name;
    return send_mail($mailto, NULL, "密码重置", $body);
  }
  return false;
}

function notify_token($user_id, $token) {
  if (($user_id) && ($token)) {
    $email = user_get_mail_by_id($user_id);
    $name = user_get_name_by_id($user_id);
    $reset_url = BASE_URL."/reset_pwd.php?email=".$email."&token=".$token;
    $notify_info = "请点击<a href='".$reset_url."'>链接</a>或者手工拷贝下面的链接到浏览器，重新设置密码！<br /><br />";
    $notify_info .= $reset_url."<br /><br />";
    $notify_info .= "请在".(TOKEN_TIMEOUT/60)."分钟内使用以上链接！<br />";
    $body = get_mail_pwd_notify($name, $notify_info);
    $mailto = $email." ".$name;
    return send_mail($mailto, NULL, "密码重置", $body);
  }
  return false;
}

function reset_pwd() {
  $req_user_id = post_get("user_id");
  if (!$req_user_id) {
    return false;
  }
  $new_pwd = post_get("new_pwd");
  if (!$new_pwd) {
    return false;
  }
  $new_pwd2 = post_get("new_pwd2");
  if (!$new_pwd2) {
    return false;
  }  
  if ($new_pwd != $new_pwd2) {
    msg_bar_warning_delay("两个新密码不匹配！");
    return false;
  }
  if (strlen($new_pwd) <= MIN_PWD_LEN) {
    msg_bar_warning_delay("密码长度必须大于".MIN_PWD_LEN);
    return false;
  }

  $salt = random_pwd(10, 15);
  if (user_set_salt_by_id($req_user_id, $salt)) {
    $r = db_q("update user set passwd = sha1('".$salt.$new_pwd."') where id = ".$req_user_id);
    if (!$r) {
      msg_bar_error_delay("密码重置失败！");
      return false;
    }
    log_r("重置(".user_get_name_by_id($req_user_id).")的登录密码", "成功！");
    return true;
  }
  return false;
}