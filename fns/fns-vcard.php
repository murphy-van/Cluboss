<?php

function vcard_file_parse($filename) {
  $lines = file($filename);
  if (!$lines) {
    return NULL;
  }
  $vcards = array();
  foreach ($lines as $line_num => $line) {
    $line = rtrim($line);
    $tmp = split_quoted_string(":", $line, 2);
    if (count($tmp) == 2) {
      switch ($tmp[0]) {
        case "BEGIN":
          $vcard = array();
          break;
        case "END":
          array_push($vcards, $vcard);
          break;
        default:
          if (strstr($tmp[0], "FN")) {
            $vcard["FN"] = iconv('GBK', 'UTF-8', $tmp[1]);
          } else if ((strstr($tmp[0], "TEL")) || (strstr($tmp[0], "CELL")) || (strstr($tmp[0], "VOICE"))) {
            $vcard["TEL"] = $tmp[1];
          } else if ((strstr($tmp[0], "EMAIL")) || (strstr($tmp[0], "PREF")) || (strstr($tmp[0], "INTERNET"))) {
            $vcard["EMAIL"] = $tmp[1];
          }
          break;
      }
    }
  }
  return $vcards;
}

function file_get_extension($filename) 
{ 
  return substr(strrchr($filename, '.'), 1); 
}

function split_quoted_string($d, $s, $n = 0)
{
    $quote = false;
    $len = strlen($s);
    for ($i = 0; $i < $len && ($n == 0 || $n > 1); $i++) {
        $c = $s{$i};
        if ($c == '"') {
            $quote = !$quote;
        } else if (!$quote && $c == $d) {
            $s{$i} = "\x00";
            if ($n > 0) {
                $n--;
            }
        }
    }
    return explode("\x00", $s);
}