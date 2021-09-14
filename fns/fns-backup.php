<?php

function backup_get_xml_name() {
  $user_id = user_get_signin_id();
  $name = user_get_name_by_id($user_id);
  date_default_timezone_set('PRC');
  $time = date('YmdHis');
  return $name."-".$time;
}

function backup_get_xml_context() {
  $user_id = user_get_signin_id();
  $output = backup_get_xml_header();
  $output .= backup_get_xml_addressbook($user_id);
  $output .= backup_get_xml_facility($user_id);
  $output .= backup_get_xml_club($user_id);
  $output .= backup_get_xml_footer();
  return $output;
}

function backup_get_xml_header() {
  $output = XML_CN_HEADER.
    "<".XML_TAG_ROOT.">\n".
    INDENT."<".XML_TAG_HEADER.">\n".
    INDENT2."<".XML_TAG_VER.">".BACKUP_VERSION."</".XML_TAG_VER.">\n".
    INDENT."</".XML_TAG_HEADER.">\n";
  return $output;
}

function backup_get_xml_footer() {
  $output = "</".XML_TAG_ROOT.">\n";
  return $output;
}

function backup_get_xml_addressbook($user_id) {
  $r = member_get_all_same_user_with_user($user_id, "id", "a", 0, NULL);
  $output = INDENT."<".XML_TAG_ADDRESSBOOK.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $output .= INDENT2."<".XML_TAG_PEOPLE.">\n".
        INDENT3."<".XML_TAG_EMAIL.">".$row['email']."</".XML_TAG_EMAIL.">\n".
        INDENT3."<".XML_TAG_NAME.">".$row['name']."</".XML_TAG_NAME.">\n".
        INDENT3."<".XML_TAG_GENDER.">".$row['gender']."</".XML_TAG_GENDER.">\n".
        INDENT3."<".XML_TAG_PHONE.">".$row['phone']."</".XML_TAG_PHONE.">\n".
        INDENT3."<".XML_TAG_PHOTO.">".$row['photo_url']."</".XML_TAG_PHOTO.">\n".
        INDENT2."</".XML_TAG_PEOPLE.">\n";
    }
  }
  $output .= INDENT."</".XML_TAG_ADDRESSBOOK.">\n";
  return $output;
}

function backup_get_xml_facility($user_id) {
  $r = facility_get_all($user_id, "id", "a", 0, NULL);
  $output = INDENT."<".XML_TAG_FACILITY_LIST.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $output .= INDENT2."<".XML_TAG_FACILITY.">\n".
        INDENT3."<".XML_TAG_NAME.">".$row['name']."</".XML_TAG_NAME.">\n".
        INDENT3."<".XML_TAG_ADDRESS.">".$row['address']."</".XML_TAG_ADDRESS.">\n".
        INDENT3."<".XML_TAG_PHONE.">".$row['phone']."</".XML_TAG_PHONE.">\n".
        INDENT2."</".XML_TAG_FACILITY.">\n";
    }
  }
  $output .= INDENT."</".XML_TAG_FACILITY_LIST.">\n";
  return $output;
}

function backup_get_xml_club($user_id) {
  $r = club_get_all_by_user_id($user_id);
  $output = INDENT."<".XML_TAG_CLUB_LIST.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $club_id = $row['id'];
      $output .= INDENT2."<".XML_TAG_CLUB.">\n".
        INDENT3."<".XML_TAG_NAME.">".$row['name']."</".XML_TAG_NAME.">\n".
        INDENT3."<".XML_TAG_CREATED.">".$row['created_date']."</".XML_TAG_CREATED.">\n".
        INDENT3."<".XML_TAG_LOGO.">".$row['logo_url']."</".XML_TAG_LOGO.">\n".
        INDENT3."<".XML_TAG_EMAIL.">".$row['club_mail']."</".XML_TAG_EMAIL.">\n";
      $output .= backup_get_xml_club_member($user_id, $club_id);
      $output .= backup_get_xml_club_event($user_id, $club_id);
      $output .= backup_get_xml_club_trans($club_id);
      $output .= INDENT2."</".XML_TAG_CLUB.">\n";
    }
  }
  $output .= INDENT."</".XML_TAG_CLUB_LIST.">\n";
  return $output;
}

function backup_get_xml_club_member($user_id, $club_id) {
  $user_member_id = user_get_member_id_by_id($user_id);
  $r = member_get_all_same_club($user_id, $club_id, "id", "a", 0, NULL);
  $output = INDENT3."<".XML_TAG_MEMBER_LIST.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $output .= INDENT4."<".XML_TAG_MEMBER.">\n".
        INDENT5."<".XML_TAG_EMAIL.">".$row['email']."</".XML_TAG_EMAIL.">\n".
        INDENT4."</".XML_TAG_MEMBER.">\n";
    }
  }
  $output .= INDENT3."</".XML_TAG_MEMBER_LIST.">\n";
  return $output;
}

function backup_get_xml_club_event($user_id, $club_id) {
  $r = event_get_all_same_club($club_id, "id", "a", 0, NULL);
  $output = INDENT3."<".XML_TAG_EVENT_LIST.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $facility_name = facility_get_name_by_id($user_id, $row['facility_id']);
      $output .= INDENT4."<".XML_TAG_EVENT.">\n".
        INDENT5."<".XML_TAG_START_TIME.">".$row['start_time']."</".XML_TAG_START_TIME.">\n".
        INDENT5."<".XML_TAG_DURATION.">".$row['duration']."</".XML_TAG_DURATION.">\n".
        INDENT5."<".XML_TAG_FACILITY.">".$facility_name."</".XML_TAG_FACILITY.">\n".
        INDENT5."<".XML_TAG_TOTAL.">".$row['total']."</".XML_TAG_TOTAL.">\n".
        INDENT5."<".XML_TAG_SHARE.">".$row['share']."</".XML_TAG_SHARE.">\n".
        INDENT5."<".XML_TAG_NOTES.">".$row['notes']."</".XML_TAG_NOTES.">\n";
      $output .= backup_get_xml_event_attendee($user_id, $row['id']);
      $output .= INDENT4."</".XML_TAG_EVENT.">\n";
    }
  }
  $output .= INDENT3."</".XML_TAG_EVENT_LIST.">\n";
  return $output;
}

function backup_get_xml_event_attendee($user_id, $event_id) {
  $r = member_get_all_same_event($user_id, $event_id, "id", "a", 0, NULL);
  $output = INDENT5."<".XML_TAG_ATTENDEE_LIST.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $output .= INDENT6."<".XML_TAG_ATTENDEE.">\n".
        INDENT7."<".XML_TAG_EMAIL.">".$row['email']."</".XML_TAG_EMAIL.">\n".
        INDENT7."<".XML_TAG_PAID.">".(member_get_event_pay_users($row['id'], $event_id))."</".XML_TAG_PAID.">\n".
        INDENT6."</".XML_TAG_ATTENDEE.">\n";
    }
  }
  $output .= INDENT5."</".XML_TAG_ATTENDEE_LIST.">\n";
  return $output;
}

function backup_get_xml_club_trans($club_id) {
  $r = trans_get_all($club_id, 0, "id", "a", 0, NULL);
  $output = INDENT3."<".XML_TAG_TRANS_LIST.">\n";
  if ($r) {
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      if ($row['autogen']) {
        continue;
      }
      $email = member_get_email_by_id($row['member_id']);
      $type_name = trans_type_id2name($row['type_id']);
      $event_time = event_get_start_time_by_id($row['event_id']);
      $output .= INDENT4."<".XML_TAG_TRANS.">\n".
        INDENT5."<".XML_TAG_TRANS_DATE.">".$row['trans_date']."</".XML_TAG_TRANS_DATE.">\n".
        INDENT5."<".XML_TAG_EMAIL.">".$email."</".XML_TAG_EMAIL.">\n".
        INDENT5."<".XML_TAG_WITHDRAW.">".$row['withdraw']."</".XML_TAG_WITHDRAW.">\n".
        INDENT5."<".XML_TAG_DEPOSIT.">".$row['deposit']."</".XML_TAG_DEPOSIT.">\n".
        INDENT5."<".XML_TAG_TYPE.">".$type_name."</".XML_TAG_TYPE.">\n".
        INDENT5."<".XML_TAG_EVENT_TIME.">".$event_time."</".XML_TAG_EVENT_TIME.">\n".
        INDENT5."<".XML_TAG_NOTES.">".$row['notes']."</".XML_TAG_NOTES.">\n";
      $output .= INDENT4."</".XML_TAG_TRANS.">\n";
    }
  }
  $output .= INDENT3."</".XML_TAG_TRANS_LIST.">\n";
  return $output;
}

function restore_upload() {
  $file_type = $_FILES["xmlfile"]["type"];
  $file_name = iconv('UTF-8', 'GBK', $_FILES["xmlfile"]["name"]);
  $file_size = $_FILES["xmlfile"]["size"];
  $file_error = $_FILES["xmlfile"]["error"];
  $file_tmp_name = $_FILES["xmlfile"]["tmp_name"];

  if (($file_type != "text/xml") || (file_get_extension($file_name) != "xml")) {
    msg_bar_warning_delay("文件类型错误！");
  } else if ($file_size > XML_FILE_SIZE_MAX) {
    msg_bar_warning_delay("文件大小不能超过".ceil(XML_FILE_SIZE_MAX/1024)."KB！");
  } else if ($file_error > 0) {
    msg_bar_warning_delay("文件上传出错（".$file_error.")！");
  } else {
    date_default_timezone_set('PRC');
    $start = date('Y-m-d H:i:s');    
    $r = restore_file_parse($file_tmp_name, true);
    if ($r) {
      $user_id = user_get_signin_id();
      user_remove_all($user_id, true);
      $r = restore_file_parse($file_tmp_name, false);
    }
    $end = date('Y-m-d H:i:s');
    $duration = strtotime($end)-strtotime($start);
    if ($r) {
      msg_bar_success_delay("备份导入并恢复成功！耗时".$duration."秒！");
      log_r("从备份文件（".$_FILES["xmlfile"]["name"]."）导入恢复", "成功！");
      return true;
    } else {
      msg_bar_error_delay("备份导入或恢复失败！耗时".$duration."秒！");
      log_r("从备份文件（".$_FILES["xmlfile"]["name"]."）导入恢复", "失败！");
    }
  }
  return false;
}

function restore_file_parse($filename, $is_validate) {
  $file = $filename;

  if (!($fp = fopen($file, "r"))) {
    return false;
  }

  $data = fread($fp, filesize($file));
  fclose($fp);
  
  $obj = xml_to_object($data);
  if (!restore_data($obj, $is_validate)) {
    return false;
  }
  return true;
}

class XmlElement {
  var $name;
  var $attributes;
  var $content;
  var $children;
};

function xml_to_object($xml) {
  $parser = xml_parser_create();
  xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
  xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
  xml_parse_into_struct($parser, $xml, $tags);
  xml_parser_free($parser);

  $elements = array();  // the currently filling [child] XmlElement array
  $stack = array();
  foreach ($tags as $tag) {
    $index = count($elements);
    if ($tag['type'] == "complete" || $tag['type'] == "open") {
      $elements[$index] = new XmlElement;
      $elements[$index]->name = $tag['tag'];
      $elements[$index]->attributes = $tag['attributes'];
      $elements[$index]->content = $tag['value'];
      if ($tag['type'] == "open") {  // push
        $elements[$index]->children = array();
        $stack[count($stack)] = &$elements;
        $elements = &$elements[$index]->children;
      }
    }
    if ($tag['type'] == "close") {  // pop
      $elements = &$stack[count($stack) - 1];
      unset($stack[count($stack) - 1]);
    }
  }
  return $elements[0];  // the single top-level element
}

function obj2text($obj) {
  if (!$obj) {
    return NULL;
  }
  $out = "Name:".$obj->name.",Attr:".$obj->attributes.",Content=".$obj->content.";";
  return $out;
}

function restore_data($obj, $is_validate) {
  if (!$obj) {
    msg_bar_error_delay("根节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_ROOT) {
    msg_bar_error_delay("根节点标记错误！");
    return false;
  }
  $header_count = 0;
  $addressbook_count = 0;
  $facility_list_count = 0;
  $club_list_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_HEADER:
        if (!restore_data_header($child, $is_validate)) {
          return false;
        }
        $header_count++;
        break;
      case XML_TAG_ADDRESSBOOK:
        if (!restore_data_addressbook($child, $is_validate)) {
          return false;
        }
        $addressbook_count++;
        break;
      case XML_TAG_FACILITY_LIST:
        if (!restore_data_facilitylist($child, $is_validate)) {
          return false;
        }
        $facility_list_count++;
        break;
      case XML_TAG_CLUB_LIST:
        if (!restore_data_clublist($child, $is_validate)) {
          return false;
        }
        $club_list_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($header_count != 1) {
    msg_bar_error_delay("头结点数目（".$header_count."）必须且只能有（1）个！");
    return false;
  }
  if ($addressbook_count != 1) {
    msg_bar_error_delay("通讯录结点数目（".$addressbook_count."）必须且只能有（1）个！");
    return false;
  }
  if ($facility_list_count != 1) {
    msg_bar_error_delay("场所商户列表结点数目（".$facility_list_count."）必须且只能有（1）个！");
    return false;
  }
  if ($club_list_count != 1) {
    msg_bar_error_delay("俱乐部列表结点数目（".$club_list_count."）必须且只能有（1）个！");
    return false;
  }
  return true;
}

function restore_data_header($obj, $is_validate) {
  if (!$obj) {
    msg_bar_error_delay("头节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_HEADER) {
    msg_bar_error_delay("根节点标记错误！");
    return false;
  }
  $ver_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_VER:
        if (!restore_data_header_version($child, $is_validate)) {
          return false;
        }
        $ver_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($ver_count != 1) {
    msg_bar_error_delay("版本结点数目（".$ver_count."）必须且只能有（1）个！");
    return false;
  }
  return true;
}

function restore_data_header_version($obj, $is_validate) {
  if (!$obj) {
    msg_bar_error_delay("版本节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_VER) {
    msg_bar_error_delay("版本节点标记错误！");
    return false;
  }
  if ($obj->content != BACKUP_VERSION) {
    msg_bar_error_delay("版本错误！");
    return false;
  }
  return true;
}

function restore_data_addressbook($obj, $is_validate) {
  if (!$obj) {
    msg_bar_error_delay("通讯录节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_ADDRESSBOOK) {
    msg_bar_error_delay("通讯录节点标记错误！");
    return false;
  }
  $people_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_PEOPLE:
        if (!restore_data_addressbook_people($child, $is_validate, $people_count)) {
          return false;
        }
        $people_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($people_count > user_get_limit("APU")) {
    msg_bar_error_delay("通讯录表项数目（".$people_count."）超过限制（".user_get_limit("APU")."）！");
    return false;
  }
  return true;
}

function restore_data_addressbook_people($obj, $is_validate, $count) {
  if (!$obj) {
    msg_bar_error_delay("通讯录[".($count+1)."]表项节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_PEOPLE) {
    msg_bar_error_delay("通讯录[".($count+1)."]表项节点标记错误！");
    return false;
  }
  $email_count = 0;
  $name_count = 0;
  $gender_count = 0;
  $phone_count = 0;
  $photo_count = 0;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_EMAIL:
        if (!email_preg_check($content)) {
          msg_bar_error_delay("通讯录[".($count+1)."]表项电子邮箱（".$content."）格式错误！");    
          return false;
        }
        $email_count++;
        $email = $content;
        break;
      case XML_TAG_NAME:
        if (false) {
          return false;
        }
        $name_count++;
        $name = $content;
        break;
      case XML_TAG_GENDER:
        switch ($content) {
          case "男":
          case "女":
          case "保密":
            break;
          default:
            msg_bar_error_delay("通讯录[".($count+1)."]表项性别（".$content."）错误！"); 
            return false;
        }
        $gender_count++;
        $gender = $content;
        break;
      case XML_TAG_PHONE:
        if (($content)&&(false)) {
          msg_bar_error_delay("通讯录[".($count+1)."]表项电话号码（".$content."）格式错误！");    
          return false;
        }
        $phone_count++;
        $phone = $content;
        break;
      case XML_TAG_PHOTO:
        if (false) {
          return false;
        }
        $photo_count++;
        $photo = $content;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($email_count != 1) {
    msg_bar_error_delay("通讯录[".($count+1)."]电子邮件结点数目（".$email_count."）必须且只能有（1）个！");
    return false;
  }
  if ($name_count != 1) {
    msg_bar_error_delay("通讯录[".($count+1)."]姓名结点数目（".$name_count."）必须且只能有（1）个！");
    return false;
  }
  if ($gender_count != 1) {
    msg_bar_error_delay("通讯录[".($count+1)."]性别结点数目（".$gender_count."）必须且只能有（1）个！");
    return false;
  }
  if ($phone_count != 1) {
    msg_bar_error_delay("通讯录[".($count+1)."]电话结点数目（".$phone_count."）必须且只能有（1）个！");
    return false;
  }
  if ($photo_count != 1) {
    msg_bar_error_delay("通讯录[".($count+1)."]照片结点数目（".$photo_count."）必须且只能有（1）个！");
    return false;
  }
  if (!$is_validate) {
    if (!member_add($email, $name, $gender, $phone, $photo)) {
      return false;
    }
  }
  return true;
}

function restore_data_facilitylist($obj, $is_validate) {
  if (!$obj) {
    msg_bar_error_delay("场所商户节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_FACILITY_LIST) {
    msg_bar_error_delay("场所商户节点标记错误！");
    return false;
  }
  $facility_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_FACILITY:
        if (!restore_data_facility($child, $is_validate, $facility_count)) {
          return false;
        }
        $facility_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($facility_count > user_get_limit("FPU")) {
    msg_bar_error_delay("场所商户数目（".$facility_count."）超过限制（".user_get_limit("FPU")."）！");
    return false;
  }
  return true;
}

function restore_data_facility($obj, $is_validate, $count) {
  if (!$obj) {
    msg_bar_error_delay("场所商户[".($count+1)."]表项节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_FACILITY) {
    msg_bar_error_delay("场所商户[".($count+1)."]表项节点标记错误！");
    return false;
  }
  $name_count = 0;
  $address_count = 0;
  $phone_count = 0;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_NAME:
        if (!$content) {
          msg_bar_error_delay("场所商户[".($count+1)."]名称空错误！");
          return false;
        }
        $name_count++;
        $name = $content;
        break;
      case XML_TAG_ADDRESS:
        if (false) {
          return false;
        }
        $address_count++;
        $address = $content;
        break;
      case XML_TAG_PHONE:
        if (($content)&&(false)) {
          msg_bar_error_delay("场所商户[".($count+1)."]电话号码（".$content."）格式错误！");    
          return false;
        }
        $phone_count++;
        $phone = $content;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($name_count != 1) {
    msg_bar_error_delay("场所商户[".($count+1)."]名称结点数目（".$name_count."）必须且只能有（1）个！");
    return false;
  }
  if ($address_count != 1) {
    msg_bar_error_delay("场所商户[".($count+1)."]地址结点数目（".$address_count."）必须且只能有（1）个！");
    return false;
  }
  if ($phone_count != 1) {
    msg_bar_error_delay("场所商户[".($count+1)."]电话结点数目（".$phone_count."）必须且只能有（1）个！");
    return false;
  }
  if (!$is_validate) {
    if (!facility_add($name, $address, $phone)) {
      return false;
    }
  }
  return true;
}

function restore_data_clublist($obj, $is_validate) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部列表节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_CLUB_LIST) {
    msg_bar_error_delay("俱乐部列表节点标记错误！");
    return false;
  }
  $club_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_CLUB:
        if (!restore_data_club($child, $is_validate, $club_count)) {
          return false;
        }
        $club_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($club_count > user_get_limit("CPU")) {
    msg_bar_error_delay("俱乐部数目（".$club_count."）超过限制（".user_get_limit("CPU")."）！");
    return false;
  }
  return true;
}

function restore_data_club($obj, $is_validate, $count) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_CLUB) {
    msg_bar_error_delay("俱乐部[".($count+1)."]节点标记错误！");
    return false;
  }
  $name_count = 0;
  $created_count = 0;
  $logo_count = 0;
  $email_count = 0;
  $member_list_count = 0;
  $event_list_count = 0;
  $trans_list_count = 0;
  $club_id = NULL;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_NAME:
        if (!$content) {
          msg_bar_error_delay("俱乐部[".($count+1)."]名称空错误！");
          return false;
        }
        $name_count++;
        $name = $content;
        break;
      case XML_TAG_CREATED:
        if (false) {
          return false;
        }
        $created_count++;
        $created = $content;
        break;
      case XML_TAG_LOGO:
        if (false) {
          return false;
        }
        $logo_count++;
        $logo = $content;
        break;
      case XML_TAG_EMAIL:
        if (false) {
          return false;
        }
        $email_count++;
        $email = $content;
        break;
      case XML_TAG_MEMBER_LIST:
        if (!restore_data_club_memberlist($child, $is_validate, $club_id, $count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]成员列表错误！");    
          return false;
        }
        $member_list_count++;
        break;
      case XML_TAG_EVENT_LIST:
        if (!restore_data_club_eventlist($child, $is_validate, $club_id, $count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]活动列表错误！");    
          return false;
        }
        $event_list_count++;
        break;
      case XML_TAG_TRANS_LIST:
        if (!restore_data_club_translist($child, $is_validate, $club_id, $count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]交易列表错误！");    
          return false;
        }
        $trans_list_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
    if (!$is_validate) {
      if (($email_count) && ($created_count) && ($logo_count) && ($name_count) && (!$club_id)) {
        $club_id = club_add($name, $created, $logo, $email);
        if (!$club_id) {
          msg_bar_error_delay("俱乐部[".($count+1)."]创建失败！");
          return false;
        }
      }
    }
  }
  if ($name_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]名称电话结点数目（".$name_count."）必须且只能有（1）个！");
    return false;
  }
  if ($logo_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]标识结点数目（".$logo_count."）必须且只能有（1）个！");
    return false;
  }
  if ($email_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]电子邮箱结点数目（".$email_count."）必须且只能有（1）个！");
    return false;
  }
  if ($member_list_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]成员列表结点数目（".$member_list_count."）必须且只能有（1）个！");
    return false;
  }
  if ($event_list_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动列表结点数目（".$event_list_count."）必须且只能有（1）个！");
    return false;
  }
  if ($trans_list_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易列表结点数目（".$trans_list_count."）必须且只能有（1）个！");
    return false;
  }
  return true;
}

function restore_data_club_memberlist($obj, $is_validate, $club_id, $count) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]成员列表节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_MEMBER_LIST) {
    msg_bar_error_delay("俱乐部[".($count+1)."]成员列表节点标记错误！");
    return false;
  }
  $member_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_MEMBER:
        if (!restore_data_club_member($child, $is_validate, $club_id, $count, $member_count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]成员错误！");
          return false;
        }
        $member_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($member_count > user_get_limit("MPC")) {
    msg_bar_error_delay("俱乐部[".($count+1)."]成员数目（".$member_count."）超过限制（".user_get_limit("MPC")."）！");
    return false;
  }
  return true;
}

function restore_data_club_member($obj, $is_validate, $club_id, $count, $count2) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部成员节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_MEMBER) {
    msg_bar_error_delay("俱乐部成员节点标记错误！");
    return false;
  }
  $email_count = 0;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_EMAIL:
        if (!email_preg_check($content)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]成员[".($count2+1)."]电子邮箱（".$content."）格式错误！");    
          return false;
        }
        $email_count++;
        $email = $content;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($email_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]成员[".($count2+1)."]电子邮箱节点数目（".$email_count."）必须且只能有（1）个！");
    return false;
  }
  if (!$is_validate) {
    $user_id = user_get_signin_id();
    $member_id = member_get_id_by_email_and_user($email, $user_id);
    if (($member_id)&&($club_id)) {
      $member_name = member_get_name_by_id($member_id);
      $club_name = club_get_name_by_id($club_id);
      if (!member_add_to_club($user_id, $member_id, $club_id)) {
        msg_bar_error_delay("俱乐部（".$club_name."）成员（".$member_name."）添加失败！");
        log_r("俱乐部（".$club_name."）成员（".$member_name."）添加", "失败！");
        return false;
      }
      log_r("俱乐部（".$club_name."）成员（".$member_name."）添加", "成功！");
    }
  }
  return true;
}

function restore_data_club_eventlist($obj, $is_validate, $club_id, $count) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动列表节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_EVENT_LIST) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动列表节点标记错误！");
    return false;
  }
  $event_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_EVENT:
        if (!restore_data_club_event($child, $is_validate, $club_id, $count, $event_count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]活动错误！");
          return false;
        }
        $event_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($event_count > user_get_limit("EPC")) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动数目（".$event_count."）超过限制（".user_get_limit("EPC")."）！");
    return false;
  }
  return true;
}

function restore_data_club_event($obj, $is_validate, $club_id, $count, $count2) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_EVENT) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]节点标记错误！");
    return false;
  }
  $start_time_count = 0;
  $duration_count = 0;
  $facility_count = 0;
  $total_count = 0;
  $share_count = 0;
  $notes_count = 0;
  $attendee_list_count = 0;
  $event_id = NULL;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_START_TIME:
        if (false) {
          return false;
        }
        $start_time_count++;
        $start_time = $content;
        break;
      case XML_TAG_DURATION:
        if (false) {
          return false;
        }
        $duration_count++;
        $duration = $content;
        break;
      case XML_TAG_FACILITY:
        if (false) {
          return false;
        }
        $facility_count++;
        $facility = $content;
        break;
      case XML_TAG_TOTAL:
        if (false) {
          return false;
        }
        $total_count++;
        $total = $content;
        break;
      case XML_TAG_SHARE:
        if (false) {
          return false;
        }
        $share_count++;
        $share = $content;
        break;
      case XML_TAG_NOTES:
        if (false) {
          return false;
        }
        $notes_count++;
        $notes = $content;
        break;
      case XML_TAG_ATTENDEE_LIST:
        if (!restore_data_event_attendeelist($child, $is_validate, $event_id, $count, $count2)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者错误！");
          return false;
        }
        $attendee_list_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
    if (!$is_validate) {
      if (($start_time_count) && ($duration_count) && ($facility_count) && ($total_count) && ($share_count) && ($notes_count) && (!$event_id)) {
        $facility_id = facility_get_id_by_name(user_get_signin_id(), $facility);
        if (($facility_id)&&($club_id)) {
          $club_name = club_get_name_by_id($club_id);
          $event_id = event_add($start_time, $duration, $club_id, $facility_id, $total, $share, $notes);
          if (!$event_id) {
            msg_bar_error_delay("俱乐部（".$club_name."）活动（".$start_time."）添加失败！");
            log_r("俱乐部（".$club_name."）活动（".$start_time."）添加", "失败！");
            return false;
          }
          log_r("俱乐部（".$club_name."）活动（".$start_time."）添加", "成功！");
        }
      }
    }    
  }
  if ($start_time_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]开始时间节点数目（".$start_time_count."）必须且只能有（1）个！");
    return false;
  }
  if ($duration_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]时长节点数目（".$duration_count."）必须且只能有（1）个！");
    return false;
  }
  if ($facility_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count+1)."]场所商户节点数目（".$facility_count."）必须且只能有（1）个！");
    return false;
  }
  if ($total_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]总消费额节点数目（".$total_count."）必须且只能有（1）个！");
    return false;
  }
  if ($share_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]共享消费额节点数目（".$share_count."）必须且只能有（1）个！");
    return false;
  }
  if ($notes_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]备注节点数目（".$notes_count."）必须且只能有（1）个！");
    return false;
  }
  if ($attendee_list_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者列表节点数目（".$attendee_list_count."）必须且只能有（1）个！");
    return false;
  }
  return true;
}

function restore_data_event_attendeelist($obj, $is_validate, $event_id, $count, $count2) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者列表节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_ATTENDEE_LIST) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者节点标记错误！");
    return false;
  }
  $attendee_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_ATTENDEE:
        if (!restore_data_event_attendee($child, $is_validate, $event_id, $count, $count2, $attendee_count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者错误！");
          return false;
        }
        $attendee_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($attendee_count > user_get_limit("MPC")) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者数目（".$attendee_count."）超过限制（".user_get_limit("MPC")."）！");
    return false;
  }
  return true;
}

function restore_data_event_attendee($obj, $is_validate, $event_id, $count, $count2, $count3) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者[".($count3+1)."]节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_ATTENDEE) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者[".($count3+1)."]节点标记错误！");
    return false;
  }
  $email_count = 0;
  $paid_count = 0;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_EMAIL:
        if (!email_preg_check($content)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者[".($count3+1)."]电子邮箱（".$content."）格式错误！");    
          return false;
        }
        $email_count++;
        $email = $content;
        break;
      case XML_TAG_PAID:
        if (false) {
          return false;
        }
        $paid_count++;
        $paid = $content;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($email_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者[".($count3+1)."]电子邮箱节点数目（".$email_count."）必须且只能有（1）个！");
    return false;
  }
  if ($paid_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]活动[".($count2+1)."]参加者[".($count3+1)."]付款数节点数目（".$paid_count."）必须且只能有（1）个！");
    return false;
  }
  if (!$is_validate) {
    $member_id = member_get_id_by_email_and_user($email, user_get_signin_id());
    if (($member_id) && ($event_id)) {
      $club_id = event_get_club_id($event_id);
      $club_name = club_get_name_by_id($club_id);
      $member_name = member_get_name_by_id($member_id);
      $start_time = event_get_start_time_by_id($event_id);
      $r = event_add_member($event_id, $member_id);
      if (!$r) {
        msg_bar_error_delay("俱乐部（".$club_name."）活动（".$start_time."）参加者（".$member_name."）添加失败！");
        log_r("俱乐部（".$club_name."）活动（".$start_time."）参加者（".$member_name."）添加", "失败！");
        return false;
      }
      if (!member_set_event_pay_users($member_id, $event_id, $paid)) {
        event_remove_member($event_id, $member_id);
        msg_bar_error_delay("俱乐部（".$club_name."）活动（".$start_time."）参加者（".$member_name."）付款数设置失败！");
        log_r("俱乐部（".$club_name."）活动（".$start_time."）参加者（".$member_name."）付款数设置", "失败！");
        return false;
      }
    }
  }
  return true;
}

function restore_data_club_translist($obj, $is_validate, $club_id, $count) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易列表节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_TRANS_LIST) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易列表节点标记错误！");
    return false;
  }
  $trans_count = 0;
  foreach ($obj->children as $child) {
    switch ($child->name) {
      case XML_TAG_TRANS:
        if (!restore_data_club_trans($child, $is_validate, $club_id, $count, $trans_count)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]交易错误！");
          return false;
        }
        $trans_count++;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($trans_count > user_get_limit("TPC")) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易数目（".$trans_count."）超过限制（".user_get_limit("TPC")."）！");
    return false;
  }
  return true;
}

function restore_data_club_trans($obj, $is_validate, $club_id, $count, $count2) {
  if (!$obj) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]节点空错误！");
    return false;
  }
  if ($obj->name != XML_TAG_TRANS) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]节点标记错误！");
    return false;
  }
  $trans_date_count = 0;
  $email_count = 0;
  $withdraw_count = 0;
  $deposit_count = 0;
  $type_count = 0;
  $event_time_count = 0;
  $notes_count = 0;
  foreach ($obj->children as $child) {
    $content = html_get($child->content);
    switch ($child->name) {
      case XML_TAG_TRANS_DATE:
        if (false) {
          return false;
        }
        $trans_date_count++;
        $trans_date = $content;
        break;
      case XML_TAG_EMAIL:
        if (!email_preg_check($content)) {
          msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]电子邮箱（".$content."）格式错误！"); 
          return false;
        }
        $email_count++;
        $email = $content;
        break;
      case XML_TAG_WITHDRAW:
        if (false) {
          return false;
        }
        $withdraw_count++;
        $withdraw = $content;
        break;
      case XML_TAG_DEPOSIT:
        if (false) {
          return false;
        }
        $deposit_count++;
        $deposit = $content;
        break;
      case XML_TAG_TYPE:
        if (false) {
          return false;
        }
        $type_count++;
        $type = $content;
        break;
      case XML_TAG_EVENT_TIME:
        if (false) {
          return false;
        }
        $event_time_count++;
        $event_time = $content;
        break;
      case XML_TAG_NOTES:
        if (false) {
          return false;
        }
        $notes_count++;
        $notes = $content;
        break;
      default:
        msg_bar_error_delay("节点标记错误（".($child->name)."）！");
        return false;
    }
  }
  if ($trans_date_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]时间节点数目（".$trans_date_count."）必须且只能有（1）个！");
    return false;
  }
  if ($email_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]电子邮箱节点数目（".$email_count."）必须且只能有（1）个！");
    return false;
  }
  if ($withdraw_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]支出节点数目（".$withdraw_count."）必须且只能有（1）个！");
    return false;
  }
  if ($deposit_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]存入节点数目（".$deposit_count."）必须且只能有（1）个！");
    return false;
  }
  if ($type_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]类型节点数目（".$type_count."）必须且只能有（1）个！");
    return false;
  }
  if ($event_time_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]活动时间节点数目（".$event_time_count."）必须且只能有（1）个！");
    return false;
  }
  if ($notes_count != 1) {
    msg_bar_error_delay("俱乐部[".($count+1)."]交易[".($count2+1)."]说明节点数目（".$notes_count."）必须且只能有（1）个！");
    return false;
  }
  if (!$is_validate) {
    $member_id = member_get_id_by_email_and_user($email, user_get_signin_id());
    $event_id = event_get_id_by_time($event_time);
    if (!$event_id) {
      $event_id = 0;
    }
    if (($member_id)&&($club_id)) {
      if (!trans_add($club_id, $member_id, $trans_date, $withdraw, $deposit, $event_id, trans_type_name2id($type), $notes, false)) {
        $club_name = club_get_name_by_id($club_id);
        msg_bar_error_delay("俱乐部（".$club_name."）交易（".$trans_date."）添加失败！"); 
        log_r("俱乐部（".$club_name."）交易（".$trans_date."）添加", "失败！"); 
        return false;
      }
      log_r("俱乐部（".$club_name."）交易（".$trans_date."）添加", "成功！"); 
    }
  }
  return true;
}