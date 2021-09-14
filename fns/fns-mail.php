<?php
require_once ('PHPMailer/PHPMailerAutoload.php');

function send_mail($mailto, $mailcc, $subject, $body) {
  if ((!$mailto) || (!$subject) || (!$body)) {
    return false;
  }
  $mail = new PHPMailer;

  //$mail->SMTPDebug = 3;       // Enable verbose debug output

  $mail->isSMTP();              // Set mailer to use SMTP
  $mail->Host = SMTP_SERVER;    // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;       // Enable SMTP authentication
  $mail->Username = SMTP_USER;  // SMTP username
  $mail->Password = SMTP_PWD;   // SMTP password
  //$mail->SMTPSecure = 'tls';  // Enable TLS encryption, `ssl` also accepted
  $mail->Port = SMTP_PORT;      // TCP port to connect to
  $mail->setLanguage('zh_cn');

  $mail->CharSet = 'UTF-8';
  $mail->From = SMTP_FROM;
  $mail->FromName = SMTP_NAME;
  $mail->addReplyTo(SMTP_FROM, SMTP_NAME);
  
  if ($mailto) {
    mail_add_to($mail, $mailto);
  }
  if ($mailcc) {
    mail_add_cc($mail, $mailcc);
  }
  $mail->addBCC(SMTP_BCC);

  //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
  //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
  $mail->isHTML(true);                                    // Set email format to HTML

  $mail->Subject = $subject;
//  $mail->Body    = MAIL_DEAR.$mailto_name."<br /><br />".$body.BODY_SIGNATURE;
  $mail->Body    = $body;
//  $mail->Body = $body;
  //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  if(!$mail->send()) {
    msg_bar_error_delay("邮件发送失败:".$mail->ErrorInfo);
    return false;
  } else {
    return true;
  }
}

function mail_add_to($mail, $mailto) {
  if (($mail)&&($mailto)) {
    $toarray = explode(';', $mailto);
    foreach ($toarray as $name => $value) {
      $mailname = explode(' ', $value);
      switch (sizeof($mailname)) {
        case 0:
          break;
        case 1:
          $mail->addAddress($mailname[0], '');
          break;
        case 2:
          $mail->addAddress($mailname[0], $mailname[1]);
          break;
      }
    }
  }
}

function mail_add_cc($mail, $mailcc) {
  if (($mail)&&($mailcc)) {
    $toarray = explode(';', $mailcc);
    foreach ($toarray as $name => $value) {
      $mailname = explode(' ', $value);
      switch (sizeof($mailname)) {
        case 0:
          break;
        case 1:
          $mail->addCC($mailname[0], '');
          break;
        case 2:
          $mail->addCC($mailname[0], $mailname[1]);
          break;
      }
    }
  }
}