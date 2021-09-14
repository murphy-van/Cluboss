<?php

function do_weixin() {
  if (!weixin_valid()) {
    return false;
  }
  weixin_response();
}

function weixin_check_signature()	{

  $signature = get_get("signature");
  $timestamp = get_get("timestamp");
  $nonce = get_get("nonce");

  if ((!$signature) || (!$timestamp) || (!$nonce)) {
    return false;
  }
  
  $token = WEIXIN_TOKEN;
  $tmpArr = array($token, $timestamp, $nonce);
  // use SORT_STRING rule
  sort($tmpArr, SORT_STRING);
  $tmpStr = implode( $tmpArr );
  $tmpStr = sha1( $tmpStr );

  if( $tmpStr == $signature ){
    return true;
  }else{
    return false;
  }
}

function weixin_valid() {
  $echostr = get_get("echostr");
  if (weixin_check_signature()) {
    log_r("微信验证(".$echostr.")", "成功！");
    echo $echostr;
    return true;
  } else {
    log_r("微信验证", "失败！");
    return false;
  }
}

function weixin_response() {
  //get post data, May be due to the different environments
  $post_str = $GLOBALS["HTTP_RAW_POST_DATA"];

  //extract post data
  if (!empty($post_str)){
    /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
       the best way is to check the validity of xml by yourself */
    libxml_disable_entity_loader(true);
    $post_obj = simplexml_load_string($post_str, 'SimpleXMLElement', LIBXML_NOCDATA);
    $from_name = $post_obj->FromUserName;
    $to_name = $post_obj->ToUserName;
    $keyword = trim($post_obj->Content);
    $time = time();
    
    $text_tpl = 
"<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Content><![CDATA[%s]]></Content>
  <FuncFlag>0</FuncFlag>
</xml>";
    
    if(!empty( $keyword )) {
      $msgType = "text";
      $contentStr = "欢迎访问聚乐账！(".BASE_URL.")";
      $resultStr = sprintf($text_tpl, $from_name, $to_name, $time, $msgType, $contentStr);
      echo $resultStr;
    }else{
      echo "Input something...";
    }

  }else {
    echo "";
    exit;
  }
}