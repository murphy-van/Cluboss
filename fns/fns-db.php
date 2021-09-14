<?php

function db_connect() {
  $result = new mysqli(DB_IP, DB_USER, DB_PWD, DB_NAME);
  if (!$result) {
    throw new Exception('数据库连接失败');
  } else {
    return $result;
  }
}

function db_a($query) {
  $conn = db_connect();
  $conn->query("SET NAMES 'UTF8'");
  $result = $conn->query($query);
  $_SESSION['DB_QUERY_ERROR_MSG'] = $conn->error;
  return $result;
}

function db_q($sql_query) {
  $result = db_a($sql_query);
  if (!$result) {
    if (isset($_SESSION['DB_QUERY_ERROR'])) {
      $_SESSION['DB_QUERY_ERROR']++;
    } else {
      $_SESSION['DB_QUERY_ERROR'] = 1;
    }
    $_SESSION['DB_QUERY'] = $sql_query;
    throw new Exception('数据库查询失败:[['.$sql_query.']]');
  } else {
    if (isset($_SESSION['DB_QUERY_SUCCESS'])) {
      $_SESSION['DB_QUERY_SUCCESS']++;
    } else {
      $_SESSION['DB_QUERY_SUCCESS'] = 0;
    }
    return $result;
  }
}

function db_query($sql_query) {
  try {
    db_q($sql_query);
  } catch (Exception $ex) {
    $_SESSION['DBError'] = $ex;
    return false;
  }
  return true;
}

function db_error() {
  $error = $_SESSION['DBError'];
  unset($_SESSION['DBError']);
  return $error;  
}

function db_reset_increment($table, $index) {
  if (($table) && ($index)) {
    $r = db_query("ALTER TABLE ".$table." AUTO_INCREMENT = ".$index);
    if ($r) {
      return true;
    }
  }
  return false;
}