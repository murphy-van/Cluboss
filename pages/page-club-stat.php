<?php

function do_page_club_stat_event($club_id, $public) {
  $chart = request_get("chart", 1);
  if (($chart<1)||($chart>5)) {
    $chart = 1;
  }

  if ($public) {
    $url = "club_public.php?club=".club_get_public_uri_by_id($club_id);
  } else {
    $url = "club.php?cid=".$club_id;
  }
  $quarter = date2quarter("2014-6-4");
  $r = event_get_all_same_club($club_id, 'start_time', 'a', 0, NULL);
?>
            <div id="canvasDiv"></div>
            <br />
            <table class="table table-striped table-bordered">
              <tr>
                <td><strong>季度</strong></td>
                <td><a href="<?php echo $url?>&tab=s&set=e&chart=1"><strong>参加人次</strong></a></td>
                <td><a href="<?php echo $url?>&tab=s&set=e&chart=2"><strong>扣款人次</strong></a></td>
                <td><a href="<?php echo $url?>&tab=s&set=e&chart=3"><strong>活跃人数</strong></a></td>
                <td><a href="<?php echo $url?>&tab=s&set=e&chart=4"><strong>活动次数</strong></a></td>
                <td><a href="<?php echo $url?>&tab=s&set=e&chart=5"><strong>平均人数</strong></a></td>
                <td><strong>环比</strong></td>
                <td><strong>同比</strong></td>
              </tr>
<?php
  if ($r) {
    $club_stat = array();
    $quarter = "";
    $last_ave = 0;
    $last_ave4 = 0;
    $last_ave3 = 0;
    $last_ave2 = 0;
    $last_ave1 = 0;
    $last_ave4_count = 0;
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
      $q = date2quarter($row['start_time']);
      if ($q != $quarter) {
        if ($quarter != "") {
          $ave = round($attendee/$event, 2);
          $active_users = event_get_member_count_by_2id($first_event, $last_event, $club_id);
?>
              <tr>
                <td><?php echo $quarter ?></td>
                <td><?php echo $attendee ?></td>
                <td><?php echo $pay_users ?></td>
                <td><?php echo $active_users ?></td>
                <td><?php echo $event ?></td>
                <td><?php echo $ave ?></td>
<?php
          $club_stat_row = array ($quarter, $attendee, $pay_users, $active_users, $event, $ave);
          array_push($club_stat, $club_stat_row);
          if ($last_ave == 0) {
?>
                <td>N/A</td>
<?php
          } else {
?>
                <td><?php echo round(($ave-$last_ave)*100/$last_ave, 2) ?>%</td>
<?php
          }
          if ($last_ave4_count < 4) {
?>
                <td>N/A</td>
<?php
          } else {
?>
                <td><?php echo round(($ave-$last_ave1)*100/$last_ave1, 2) ?>%</td>
<?php
          }
?>
              </tr>
<?php
          $last_ave = $ave;
          if ($last_ave4_count == 0) {
            $last_ave1 = $ave;
          } else if ($last_ave4_count == 1) {
            $last_ave2 = $ave;
          } else if ($last_ave4_count == 2) {
            $last_ave3 = $ave;
          } else if ($last_ave4_count == 3) {
            $last_ave4 = $ave;
          } else {
            $last_ave1 = $last_ave2;
            $last_ave2 = $last_ave3;
            $last_ave3 = $last_ave4;
            $last_ave4 = $ave;
          }
          $last_ave4_count++;
        } /* of quarter */
        $first_event = $row['id'];
        $quarter = $q;
        $attendee = 0;
        $pay_users = 0;
        $event = 1;
      } else {
        $event++;
      } /* of q */
      $attendee += event_get_attendee_count($row['id']);
      $pay_users += event_get_pay_users_count($row['id']);
      $last_event = $row['id'];
    } /* of for i */
    $ave = round($attendee/$event, 2);
    $active_users = event_get_member_count_by_2id($first_event, $last_event, $club_id);
?>
              <tr>
                <td><?php echo $quarter ?></td>
                <td><?php echo $attendee ?></td>
                <td><?php echo $pay_users ?></td>
                <td><?php echo $active_users ?></td>
                <td><?php echo $event ?></td>
                <td><?php echo round($attendee/$event, 2) ?></td>
<?php
          $club_stat_row = array ($quarter, $attendee, $pay_users, $active_users, $event, $ave);
          array_push($club_stat, $club_stat_row);
          if ($last_ave == 0) {
?>
                <td>N/A</td>
<?php
          } else {
?>
                <td><?php echo round(($ave-$last_ave)*100/$last_ave, 2) ?>%</td>
<?php
          }
          if ($last_ave4_count < 4) {
?>
                <td>N/A</td>
<?php
          } else {
?>
                <td><?php echo round(($ave-$last_ave1)*100/$last_ave1, 2) ?>%</td>
<?php
          }
?>
              </tr>
<?php
  } /* of r */
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
  for ($j=count($club_stat)-12;$j<count($club_stat);$j++) {
    if ($j<0) {
      continue;
    }
    $stat_row = $club_stat[$j];
?>
					      {name : '<?php echo $stat_row[0]?>',value : <?echo $stat_row[$chart]?>,color:'#cbab4f'},
<?php
  }
?>
				        	];
		        	
					new iChart.Column2D({
						render : 'canvasDiv',
						data: data,
						title : '<?php echo club_get_name_by_id($club_id)?> - <?php echo event_chart2title($chart)?>',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
							background_color:'#fefefe',
							scale:[{
								 position:'left',	
								 start_scale:0,
								 end_scale:20,
								 scale_space:50,
								 listeners:{
									parseText:function(t,x,y){
										return {text:t+""}
									}
								}
							}]
						},
            animation: true
					}).draw();
			});
				
			</script>
<?php
}

function event_chart2title($chart) {
  switch($chart) {
    case 1:
      return "参加人次";
    case 2:
      return "扣款人次";
    case 3:
      return "活跃人数";
    case 4:
      return "活动次数";
    case 5:
      return "平均人数";
    default:
      return "";
  }
}

function do_page_club_stat_member($club_id, $public) {
  $r = club_get_all_member_by_attendee_count($club_id);
?>
            <div id="canvasDiv"></div>
            <br />
            <table class="table table-striped table-bordered">
              <tr>
                <td><strong>排名</strong></td>
                <td><strong>姓名</strong></td>
                <td><strong>活动次数</strong></td>
              </tr>
<?php
    $ichartdata = "";
    for ($i=0;$i<$r->num_rows;$i++) {
      $row = $r->fetch_assoc();
?>
              <tr>
                <td><?php echo ($i+1)?></td>
                <td><?php echo $row['name']?></td>
                <td><?php echo $row['count(member.name)']?></td>
              </tr>
<?php
      if ($i < 10) {
        $ichartdata .= "					      {name : '".$row['name']."',value : ".$row['count(member.name)'].",color:'#cbab4f'},\n";
      }
    }
?>
            </table>
            <script type="text/javascript">
            $(function(){
              var data = [
<?php
    echo $ichartdata;
?>
				        	];
		        	
					new iChart.Column2D({
						render : 'canvasDiv',
						data: data,
						title : '<?php echo club_get_name_by_id($club_id)?> - 活动次数',
						showpercent:false,
						decimalsnum:2,
						width : 787,
						height : 300,
						coordinate:{
							background_color:'#fefefe',
							scale:[{
								 position:'left',	
								 start_scale:0,
								 end_scale:20,
								 scale_space:50,
								 listeners:{
									parseText:function(t,x,y){
										return {text:t+""}
									}
								}
							}]
						},
            animation: true
					}).draw();
			});
				
			</script>
<?php
}