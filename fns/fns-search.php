<?php

function search_get_club_count($key, $user_id) {
  if (($key) && ($user_id)) {
    $r = search_get_club_all($key, $user_id);
    return $r->num_rows;
  }
  return 0;
}

function search_get_people_count($key, $user_id) {
  if (($key) && ($user_id)) {
    $r = search_get_people_all($key, $user_id, "id", "a", 0, NULL);
    return $r->num_rows;
  }
  return 0;
}

function search_get_event_count($key, $user_id) {
  if (($key) && ($user_id)) {
    $r = search_get_event_all($key, $user_id, "id", "a", 0, NULL);
    return $r->num_rows;
  }
  return 0;
}

function search_get_member_count($key, $user_id) {
  if (($key) && ($user_id)) {
    $r = search_get_member_all($key, $user_id, "id", "a", 0, NULL);
    return $r->num_rows;
  }
  return 0;
}

function search_get_facility_count($key, $user_id) {
  if (($user_id)&&($key)) {
    $r = search_get_facility_all($user_id, $key, "id", "a", 0, NULL);
    return $r->num_rows;
  }
  return 0;
}

function search_get_club_all($key, $user_id) {
  if (($key) && ($user_id)) {
    $member_id = user_get_member_id_by_id($user_id);
    if (!$member_id) {
      return NULL;
    }
    $creater_id = club_role_name2id("创建者");
    $r = db_q("select * from club where (name like '%".$key."%' or date_format(created_date, '%Y-%m-%d') like '%".$key."%') and id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id." )");
    return $r;
  }
  return NULL;
}

function search_get_people_all($key, $user_id, $orderby, $order, $page, $item_per_page) {
  if (($key) && ($user_id)) {
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r = db_q("select * from member where (email like '%".$key."%' or name like '%".$key."%' or gender like '%".$key."%' or phone like '%".$key."%') and user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r = db_q("select * from member where (email like '%".$key."%' or name like '%".$key."%' or gender like '%".$key."%' or phone like '%".$key."%') and user_id=".$user_id." order by ".$orderby." ".$order);
    }
    return $r;
  }
  return NULL;
}

function search_get_people_all_pages($key, $user_id, $item_per_page) {
  $count = search_get_people_count($key, $user_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function search_get_event_all($key, $user_id, $orderby, $order, $page, $item_per_page) {
  if (($key) && ($user_id)) {
    $member_id = user_get_member_id_by_id($user_id);
    if (!$member_id) {
      return NULL;
    }
    $creater_id = club_role_name2id("创建者");
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      if ($orderby == "attendee") {
        $r = db_q("select event.id,event.start_time,event.facility_id, count(event.id) from event left join member_event on event.id = member_event.event_id where (date_format(start_time, '%Y-%m-%d') like '%".$key."%' or club_id in (select id from club where name like '%".$key."%') or facility_id in (select id from facility where name like '%".$key."%' or address like '%".$key."%') or notes like '%".$key."%') and club_id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id." ) group by event.id  order by count(event.id) ".$order." limit ".$off.",".$rows);
      } else {
        $r = db_q("select * from event where (date_format(start_time, '%Y-%m-%d') like '%".$key."%' or club_id in (select id from club where name like '%".$key."%') or facility_id in (select id from facility where name like '%".$key."%' or address like '%".$key."%') or notes like '%".$key."%') and club_id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id." ) order by ".$orderby." ".$order." limit ".$off.",".$rows);
      }
    } else {
      if ($orderby == "attendee") {
        $r = db_q("select event.id,event.start_time,event.facility_id, count(event.id) from event left join member_event on event.id = member_event.event_id where (date_format(start_time, '%Y-%m-%d') like '%".$key."%' or club_id in (select id from club where name like '%".$key."%') or facility_id in (select id from facility where name like '%".$key."%' or address like '%".$key."%') or notes like '%".$key."%') and club_id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id." ) group by event.id  order by count(event.id) ".$order);
      } else {
        $r = db_q("select * from event where (date_format(start_time, '%Y-%m-%d') like '%".$key."%' or club_id in (select id from club where name like '%".$key."%') or facility_id in (select id from facility where name like '%".$key."%' or address like '%".$key."%') or notes like '%".$key."%') and club_id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id." ) order by ".$orderby." ".$order);
      }
    }
    return $r;
  }
  return NULL;
}

function search_get_event_all_pages($key, $user_id, $item_per_page) {
  $count = search_get_event_count($key, $user_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function search_get_facility_all($user_id, $key, $orderby, $order, $page, $item_per_page) {
  if ($order == 'd') {
    $order = "desc";
  } else {
    $order = "asc";
  }
  if ($page > 0) {
    $off = (($page-1)*$item_per_page);
    $rows = $item_per_page;
    if ($orderby == "event_count") {
        $r = db_q("select facility.id,facility.name,facility.address,count(facility.id) from facility left join event on facility.id = event.facility_id where (name like '%".$key."%' or address like '%".$key."%') and user_id=".$user_id." group by facility.id order by count(facility.id) ".$order." limit ".$off.",".$rows);
    } else {
      $r = db_q("select * from facility where (name like '%".$key."%' or address like '%".$key."%') and user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    }
  } else {
    if ($orderby == "event_count") {
        $r = db_q("select facility.id,facility.name,facility.address,count(facility.id) from facility left join event on facility.id = event.facility_id where (name like '%".$key."%' or address like '%".$key."%') and user_id=".$user_id." group by facility.id order by count(facility.id) ".$order);
    } else {
      $r = db_q("select * from facility where (name like '%".$key."%' or address like '%".$key."%') and user_id=".$user_id." order by ".$orderby." ".$order);
    }
  }
  return $r;
}

function search_get_facility_all_pages($user_id, $key, $item_per_page) {
  $count = search_get_facility_count($key, $user_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}

function search_get_member_all($key, $user_id, $orderby, $order, $page, $item_per_page) {
  if (($key) && ($user_id)) {
    $member_id = user_get_member_id_by_id($user_id);
    if (!$member_id) {
      return NULL;
    }
    $creater_id = club_role_name2id("创建者");
    if ($order == 'd') {
      $order = "desc";
    } else {
      $order = "asc";
    }
    if ($page > 0) {
      $off = (($page-1)*$item_per_page);
      $rows = $item_per_page;
      $r = db_q("select member.id,member.email,member.name,member.gender,member.phone,member.photo_url,member.added_date,member.user_id,member_club.club_id from member left join member_club on member.id=member_club.member_id where (member.email like '%".$key."%' or member.name like '%".$key."%' or member.gender like '%".$key."%' or member.phone like '%".$key."%') and member.id in (select member_id from member_club where club_id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id.") and remove_date is null) and member.user_id=".$user_id." order by ".$orderby." ".$order." limit ".$off.",".$rows);
    } else {
      $r = db_q("select member.id,member.email,member.name,member.gender,member.phone,member.photo_url,member.added_date,member.user_id,member_club.club_id from member left join member_club on member.id=member_club.member_id where (member.email like '%".$key."%' or member.name like '%".$key."%' or member.gender like '%".$key."%' or member.phone like '%".$key."%') and member.id in (select member_id from member_club where club_id in (select club_id from member_club where member_id=".$member_id." and role_id=".$creater_id.") and remove_date is null) and member.user_id=".$user_id." order by ".$orderby." ".$order);
    }
    return $r;
  }
  return NULL;
}

function search_get_member_all_pages($key, $user_id, $item_per_page) {
  $count = search_get_member_count($key, $user_id);
  if ($count > 0) {
    $pages = (($count-1)/$item_per_page)+1;
  } else {
    $pages = 1;
  }
  return floor($pages);
}