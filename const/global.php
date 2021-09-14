<?php

require_once("mail-template.php");
require_once("server.php");

/* Page Display */
define("ITEM_PER_PAGE", 15);
define("ITEM_PER_PAGE_PEOPLE", 7);
define("ITEM_PER_PAGE_EVENT", 24);
define("ITEM_PER_PAGE_TRANS", 15);
define("ITEM_PER_PAGE_FACILITY", 20);

/* Message */
define("SUCCESS", "alert-success");
define("INFO", "alert-info");
define("WARNING", "alert-warning");
define("DANGER", "alert-danger");

/* Log Statistic Interval */
define("LOG_SECOND", 1);
define("LOG_MINUTE", 2);
define("LOG_HOUR", 3);
define("LOG_DAY", 4);
define("LOG_WEEK", 5);
define("LOG_MONTH", 6);
define("LOG_YEAR", 7);

/* Sign In */
define("SIGNIN_TIMEOUT_SECOND", 86400);
define("TEMP_USER_TIMEOUT_SECOND", 86400);

/* Reset Password */
define("TOKEN_TIMEOUT", 600);
define("MIN_PWD_LEN", 6);

/* Account */
define("BALANCE_TOO_LOW", 10);
define("TRANS_FEE_MIN", 0.00);
define("TRANS_FEE_MAX", 10000.00);

/* VCARD */
define("VCARD_FILE_SIZE_MAX", 50000);

/* Scaling */
define("MAX_MEMBER_PER_CLUB", 100);
define("MAX_CLUB_PER_USER", 5);
define("MAX_EVENT_PER_CLUB", 1000);
define("MAX_ADDR_PER_USER", 100);
define("MAX_FACILITY_PER_USER", 20);
define("MAX_TRANS_PER_CLUB", 10000);
define("TEMP_MEMBER_PER_CLUB", 3);
define("TEMP_CLUB_PER_USER", 1);
define("TEMP_EVENT_PER_CLUB", 3);
define("TEMP_ADDR_PER_USER", 3);
define("TEMP_FACILITY_PER_USER", 3);
define("TEMP_TRANS_PER_CLUB", 10);
define("MAX_PUBLIC_URI_RETRY", 100);

/* Bankup XML */
define("XML_FILE_SIZE_MAX", 1000000);
define("XML_CN_HEADER", "<?xml version='1.0' encoding='gb2312'?>\n");
define("BACKUP_VERSION", 1);
define("INDENT", "\t");
define("INDENT2", "\t\t");
define("INDENT3", "\t\t\t");
define("INDENT4", "\t\t\t\t");
define("INDENT5", "\t\t\t\t\t");
define("INDENT6", "\t\t\t\t\t\t");
define("INDENT7", "\t\t\t\t\t\t\t");
define("XML_TAG_ROOT", "CLUBOSS");
define("XML_TAG_HEADER", "HEADER");
define("XML_TAG_VER", "VER");

define("XML_TAG_ADDRESSBOOK", "ADDRESS-BOOK");
define("XML_TAG_PEOPLE", "PEOPLE");
define("XML_TAG_ID", "ID");
define("XML_TAG_EMAIL", "EMAIL");
define("XML_TAG_NAME", "NAME");
define("XML_TAG_GENDER", "GENDER");
define("XML_TAG_PHONE", "PHONE");
define("XML_TAG_PHOTO", "PHOTO");

define("XML_TAG_FACILITY_LIST", "FACILITY-LIST");
define("XML_TAG_FACILITY", "FACILITY");
define("XML_TAG_ADDRESS", "ADDRESS");

define("XML_TAG_CLUB_LIST", "CLUB-LIST");
define("XML_TAG_CLUB", "CLUB");
define("XML_TAG_CREATED", "CREATED");
define("XML_TAG_LOGO", "LOGO");

define("XML_TAG_MEMBER_LIST", "MEMBER-LIST");
define("XML_TAG_MEMBER", "MEMBER");

define("XML_TAG_EVENT_LIST", "EVENT-LIST");
define("XML_TAG_EVENT", "EVENT");
define("XML_TAG_START_TIME", "START-TIME");
define("XML_TAG_DURATION", "DURATION");
define("XML_TAG_FACILITY", "FACILITY");
define("XML_TAG_TOTAL", "TOTAL");
define("XML_TAG_SHARE", "SHARE");

define("XML_TAG_ATTENDEE_LIST", "ATTENDEE-LIST");
define("XML_TAG_ATTENDEE", "ATTENDEE");
define("XML_TAG_PAID", "PAID");

define("XML_TAG_TRANS_LIST", "TRANS-LIST");
define("XML_TAG_TRANS", "TRANS");
define("XML_TAG_TRANS_DATE", "TRANS-DATE");
define("XML_TAG_WITHDRAW", "WITHDRAW");
define("XML_TAG_DEPOSIT", "DEPOSIT");
define("XML_TAG_TYPE", "TYPE");
define("XML_TAG_EVENT_TIME", "EVENT-TIME");
define("XML_TAG_NOTES", "NOTES");

/* Transactions */
define("TRANS_NOTES_ATTEND_EVENT", "参加活动");

/* Weixin */
define("WEIXIN_TOKEN", "cluboss");