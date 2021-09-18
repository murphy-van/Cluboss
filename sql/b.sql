drop database if exists cluboss;
create database cluboss default charset 'utf8';
use cluboss;

/* User who can signin and create club/event and add members */
create table user (
  id int unsigned not null auto_increment primary key,
  email varchar(50) not null, /* Signin Key */
  passwd char(40) not null,   /* Clear or SHA1 */
  register_date date not null,/* First time register */
  signin_count int unsigned,  /* For statistic */
  last_signin_time datetime,  /* For recoding */
  last_signin_ip varchar(16), /* For recoding */
  super bool not null,        /* Is super user (system) */
  salt char(10) not null,     /* Passwd = H(salt+pwd) */
  token char(10),             /* For reset password */
  token_time datetime         /* The date and time token generated */
);

/* Members cannot signin but only get notification by SMS or E-Mail */
/* User will create a self member automatically */
create table member (
  id int unsigned not null auto_increment primary key,
  email varchar(50) not null,
  name varchar(16) character set utf8 not null,
  gender char(6),             /* 男 or 女, null is 未知 */
  phone char(15),
  photo_url varchar(200),
  added_date date not null,   /* First time to be added */
  user_id int unsigned not null /* Who added this member in his/her addressbook */
);

/* Clubs that can only be created by Users */
create table club (
  id int unsigned not null auto_increment primary key,
  name varchar(30) character set utf8 not null,
  logo_url varchar(200),
  created_date date,
  public_uri varchar(40) not null,
  club_mail varchar(50)     /* All club notification mail cc to this */
);

/* Event only belongs to a Club */
create table event (
  id int unsigned not null auto_increment primary key,
  start_time datetime not null,
  duration tinyint,            /* Hours, can be ignored if not important */
  club_id int unsigned not null,
  facility_id int unsigned,     /* Can be ignored if not important */
  total numeric(8,2),           /* Total Money for AA */
  share numeric(8,2),           /* Or every member should pay this */
  notes varchar(50) character set utf8
);

/* Facility could be shared between users */
create table facility (
  id int unsigned not null auto_increment primary key,
  name varchar(30) character set utf8 not null,
  address varchar(100),
  phone char(15),
  user_id int unsigned not null
);

/* Member can attend multiple Event */
create table member_event (
  id int unsigned not null auto_increment primary key,
  member_id int unsigned not null,
  event_id int unsigned not null,
  pay_users int unsigned not null   /* May bring more non member people in */
);

/* Member can be added to multiple Club */
create table member_club (
  id int unsigned not null auto_increment primary key,
  member_id int unsigned not null,
  club_id int unsigned not null,
  added_date date not null,
  remove_date date,
  role_id int unsigned not null
);

/* Transactions */
create table trans (
  id int unsigned not null auto_increment primary key,
  trans_date date not null,
  member_id int unsigned not null,
  user_id int unsigned not null,
  withdraw numeric(8,2),
  deposit numeric(8,2),
  type_id int unsigned not null,
  event_id int unsigned,
  club_id int unsigned not null,
  notes varchar(50) character set utf8,
  autogen bool not null
);

/* Access Logging for webmaster */
create table log (
  id int unsigned not null auto_increment primary key,
  log_time datetime not null,
  user varchar(32) character set utf8 not null,
  fromip varchar(16) not null,
  action varchar(50) character set utf8 not null,
  msg varchar(50) character set utf8 not null,
  url varchar(200) not null,
  page varchar(30) not null
);

/* Create MySQL user for PHP access */
grant select, insert, update, delete, lock tables
  on cluboss.*
  to clubossphp identified by "P2H0P1C5l0u2b1O4SS";

/*************************************/
/*              Data                 */
/*************************************/
insert into user values
  (1,'admin@cluboss.com', sha1('LUTe8EBUQYadmin'), '2015-10-19', NULL, NULL, NULL, true, 'LUTe8EBUQY', NULL, NULL);

insert into member values
  (1, 'admin@cluboss.com', 'Cluboss Admin', '男', '', '', '2015-10-19', 1);
