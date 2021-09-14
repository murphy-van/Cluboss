<?php

function mail_template_header() {
  return "".
"<!--100% body table-->\n".
"<table width='100%' border='0' cellspacing='0' cellpadding='0'>\n".
"    <tr>\n".
"        <td bgcolor='#d8e7ea' style='background-color: #d8e7ea;'>\n";
}
function mail_template_intro($name) {
  return "".
"            <!--intro-->\n".
"            <table width='620' border='0' align='center' cellpadding='0' cellspacing='0'>\n".
"                <tr>\n".
"                    <td valign='middle' width='11' height='100'></td>\n".
"                    <td valign='middle' height='100'>\n".
"                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n".
"                            <tr>\n".
"                                <td width='64%' height='100'>\n".
"                                    <h1 style='font-family: Arial, Helvetica, sans-serif; font-size: 42px; margin: 0; padding: 0; color: #0d2d42; text-shadow: 1px 1px 1px #fff;'>".$name."</h1>\n".
"                                </td>\n".
"                                <td width='36%' height='100' valign='top'>\n".
"                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n".
"                                        <tr>\n".
"                                            <td valign='bottom' height='70'>\n".
"                                                <p style='text-transform: uppercase; font-size: 14px;  color: #333333; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; float: right;'>\n".
"                                                </p>\n".
"                                            </td>\n".
"                                        </tr>\n".
"                                    </table>\n".
"                                </td>\n".
"                            </tr>\n".
"                        </table>\n".
"                    </td>\n".
"                </tr>\n".
"            </table>\n".
"            <!--/intro-->\n";
}

function mail_template_content_section($title, $content) {
  return "".
"            <!--content section-->\n".
"            <table width='620' border='0' align='center' cellpadding='0' cellspacing='0'>\n".
"                <tr>\n".
"                    <td height='82' width='11' valign='middle'><img style='margin: 0; padding: 0; display: block;' src='".BASE_URL."/images/side-corner.png' width='11' height='83'></td>\n".
"                    <td height='82' bgcolor='#FFFFFF' valign='middle'>\n".
"                        <table width='594' border='0' cellspacing='0' cellpadding='0'>\n".
"                            <tr>\n".
"                                <td style='background-image: url(images/bar-end.png); background-repeat: no-repeat; background-position: right;' valign='middle' height='37' bgcolor='#cc0000'>\n".
"                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n".
"                                        <tr>\n".
"                                            <td style='background-color:#cc0000;' bgcolor='#cc0000' width='25' height='37'></td>\n".
"                                            <td height='37'>\n".
"                                                <h2 style='color: #fff; font-size: 21px; font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; text-shadow: 1px 1px 1px #000;'>".$title."</h2>\n".
"                                            </td>\n".
"                                        </tr>\n".
"                                    </table>\n".
"                                </td>\n".
"                            </tr>\n".
"                        </table>\n".
"                    </td>\n".
"                </tr>\n".
"                <tr>\n".
"                    <td valign='top'></td>\n".
"                    <td bgcolor='#FFFFFF' valign='top'>\n".
"                        <table width='560' border='0' align='center' cellpadding='0' cellspacing='0'>\n".
"                            <tr>\n".
"                                <td valign='top'>\n".
$content.
"                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n".
"                                        <tr>\n".
"                                            <td height='15'></td>\n".
"                                        </tr>\n".
"                                    </table>\n".
"                                </td>\n".
"                            </tr>\n".
"                        </table>\n".
"                    </td>\n".
"                </tr>\n".
"            </table>\n".
"            <!--/content section-->\n";
}

function mail_template_footer($footer_msg) {
  return "".
"            <!--footer-->\n".
"            <table width='620' border='0' align='center' cellpadding='20' cellspacing='0'>\n".
"                <tr>\n".
"                    <td valign='top'>\n".
"                        <p style='font-size: 12px; color: #666; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif;'>".$footer_msg."\n".
"                        </p>\n".
"                    </td>\n".
"                    <td valign='top'>\n".
"                        <!--button-->\n".
"                        <table width='200' border='0' cellspacing='0' cellpadding='10'>\n".
"                            <tr>\n".
"                                <td style='border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; -khtml-border-radius: 6px; color: #ffffff; text-align: right;'>\n".
"                                <a href='".HOME_URL."'>".
  generateQRfromGoogle(HOME_URL, 80)."</a>\n".
//"                                    <a href='".HOME_URL."'><forwardtoafriend style='text-decoration: none; color: #FFF; font-family: Arial, Helvetica, sans-serif; font-size: 16px;'>".APP_NAME."</forwardtoafriend></a>\n".
"                                </td>\n".
"                            </tr>\n".
"                        </table>\n".
"                        <!--/button-->\n".
"                    </td>\n".
"                </tr>\n".
"            </table>\n".
"            <!--footer-->\n".
"            <!--break-->\n".
"            <table width='100%' border='0' cellspacing='0' cellpadding='0'>\n".
"                <tr>\n".
"                    <td height='25'></td>\n".
"                </tr>\n".
"            </table>\n".
"            <!--/break-->\n".
"        </td>\n".
"    </tr>\n".
"</table>\n".
"<!--/100% body table-->\n";
}

function get_mail_low_balance($name, $account_info, $public_link, $club_name, $admin_mail) {
  $body = mail_template_header();
  $body .= mail_template_intro($name);
  $body .= mail_template_content_section("账户信息", $account_info);
  $body .= mail_template_content_section("统计报表", $public_link);
  $body .= mail_template_footer_balance($club_name, $admin_mail);
  return $body;
}

function get_mail_trans($name, $account_info, $account_trans, $public_link, $club_name, $admin_mail) {
  $body = mail_template_header();
  $body .= mail_template_intro($name);
  $body .= mail_template_content_section("账户信息", $account_info);
  $body .= mail_template_content_section("交易记录", $account_trans);
  $body .= mail_template_content_section("统计报表", $public_link);
  $body .= mail_template_footer_balance($club_name, $admin_mail);
  return $body;
}

function get_mail_balance($name, $account_info, $account_balance, $public_link, $club_name, $admin_mail) {
  $body = mail_template_header();
  $body .= mail_template_intro($name);
  $body .= mail_template_content_section("账户信息", $account_info);
  $body .= mail_template_content_section("余额记录", $account_balance);
  $body .= mail_template_content_section("统计报表", $public_link);
  $body .= mail_template_footer_balance($club_name, $admin_mail);
  return $body;
}

function get_mail_report($name, $account_info, $public_link, $club_name, $admin_mail, $report) {
  $body = mail_template_header();
  $body .= mail_template_intro($name);
  $body .= mail_template_content_section("定期报告", $report);
  $body .= mail_template_content_section("账户信息", $account_info);
  $body .= mail_template_content_section("统计报表", $public_link);
  $body .= mail_template_footer_balance($club_name, $admin_mail);
  return $body;
}

function get_mail_event_confirm($event_info, $public_link, $club_name, $admin_mail) {
  $body = mail_template_header();
  $body .= mail_template_content_section("活动信息", $event_info);
  $body .= mail_template_content_section("统计报表", $public_link);
  $body .= mail_template_footer_balance($club_name, $admin_mail);
  return $body;
}

function mail_template_footer_balance($club_name, $admin_mail) {
  $footer_msg = "您收到了这封邮件是因为您被添加为".$club_name."俱乐部的成员。如果您不想继续接收类似的邮件，请联系该俱乐部的\n".
"                            <a href='mailto:".$admin_mail."'><unsubscribe style='color: #cc0000; font-family: Arial, Helvetica, sans-serif font-size: 14px; text-decoration: none; margin: 0; padding: 0;'>管理员</unsubscribe></a>。";
  return mail_template_footer($footer_msg);
}

function get_mail_pwd_notify($name, $pwd_notify_info) {
  $body = mail_template_header();
  $body .= mail_template_intro($name);
  $body .= mail_template_content_section("密码重置", $pwd_notify_info);
  $body .= mail_template_footer_pwd();
  return $body;
}

function mail_template_footer_pwd() {
  $footer_msg = "您收到了这封邮件是因为您正尝试重置在".APP_NAME."的密码。如果不是您本人进行的操作，请联系该网站的\n".
"                            <a href='mailto:".SMTP_FROM."'><unsubscribe style='color: #cc0000; font-family: Arial, Helvetica, sans-serif font-size: 14px; text-decoration: none; margin: 0; padding: 0;'>管理员</unsubscribe></a>。";
  return mail_template_footer($footer_msg);
}