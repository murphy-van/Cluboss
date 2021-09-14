<?php

/* App Name */
define("APP_NAME", "聚乐账");

/* App URL */
define("BASE_URL", "http://".htmlspecialchars(filter_input(INPUT_SERVER, "HTTP_HOST"), ENT_NOQUOTES, "UTF-8"));
define("HOME_URL", BASE_URL."/home.php");

/* Database */
define("DB_NAME", "cluboss");
/* Database Server IP */
define("DB_IP", "127.0.0.1");
/* Database Server Username and Password*/
define("DB_USER", "clubossphp");
define("DB_PWD", "P2H0P1C5l0u2b1O4SS");

/* Mail */
/* To send notification mail, SMTP server parameter */
define("SMTP_SERVER", "smtp.cluboss.com");
define("SMTP_USER", "cluboss");
define("SMTP_PWD", "clubpass");
define("SMTP_PORT", 25);
define("SMTP_FROM", "cluboss@cluboss.com");
define("SMTP_NAME", APP_NAME);
define("SMTP_BCC", "admin@cluboss.net");
define("BODY_SIGNATURE", "<br \><p>------------------------</p><a href='".HOME_URL."'>".APP_NAME." - 俱乐部在线记账</a>");
define("MAIL_DEAR", "您好，");
define("MAIL_DEBUG", NULL);