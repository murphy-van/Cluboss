<?php

function msg_confirm($obj, $ask, $target) {
  if (($obj) && ($ask) && ($target)) {
?>
  <!-- bootbox code -->
  <script src="js/bootbox.min.js"></script>
  <script>
    $(document).on("click", ".<?php echo $obj?>", function(e) {
      bootbox.confirm("<?php echo $ask?>", function(result) {
        if (result) {
          window.location = "<?php echo $target?>";
        }
      }); 
    });
  </script>
<?php
  }
}

function msg_alert($obj, $alert) {
  if (($obj) && ($alert)) {
?>
  <!-- bootbox code -->
  <script src="js/bootbox.min.js"></script>
  <script>
    $(document).on("click", ".<?php echo $obj?>", function(e) {
      bootbox.alert("<?php echo $alert?>", function() {
      }); 
    });
  </script>
<?php
  }
}

function msg_bar($type, $msg) {
  if (!$type) {
    if (isset($_SESSION['msg_bar_type'])) {
      $type = $_SESSION['msg_bar_type'];
      unset($_SESSION['msg_bar_type']);
      if (isset($_SESSION['msg_bar_type2'])) {
        $_SESSION['msg_bar_type'] = $_SESSION['msg_bar_type2'];
        unset($_SESSION['msg_bar_type2']);
      }
    } else {
      return;
    }
  }
  if (!$msg) {
    if (isset($_SESSION['msg_bar'])) {
      $msg = $_SESSION['msg_bar'];
      unset($_SESSION['msg_bar']);
      if (isset($_SESSION['msg_bar2'])) {
        $_SESSION['msg_bar'] = $_SESSION['msg_bar2'];
        unset($_SESSION['msg_bar2']);
      }
    } else {
      return;
    }
  }
?>
    <div class="alert <?php echo $type?> alert-dismissible fade in" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php echo $msg?>
    </div>
<?php
}

function msg_bar_noclose($type, $msg) {
?>
    <div class="alert <?php echo $type?> alert-dismissible fade in" role="alert">
      <?php echo $msg?>
    </div>
<?php
}

function msg_bar_success($msg) {
  msg_bar(SUCCESS,"<strong>信息：</strong>".$msg);
}

function msg_bar_info($msg) {
  msg_bar(INFO,"<strong>信息：</strong>".$msg);
}

function msg_bar_warning($msg) {
  msg_bar(WARNING,"<strong>警告：</strong>".$msg);
}

function msg_bar_error($msg) {
  msg_bar(DANGER,"<strong>错误：</strong>".$msg);
}

function msg_bar_success_delay($msg) {
  if (!isset($_SESSION['msg_bar'])) {
    $_SESSION['msg_bar'] = "<strong>成功：</strong>".$msg;
  } else {
    $_SESSION['msg_bar2'] = "<strong>成功：</strong>".$msg;
  }
  if (!isset($_SESSION['msg_bar_type'])) {
    $_SESSION['msg_bar_type'] = SUCCESS;
  } else {
    $_SESSION['msg_bar_type2'] = SUCCESS;
  }
}

function msg_bar_info_delay($msg) {
  if (!isset($_SESSION['msg_bar'])) {
    $_SESSION['msg_bar'] = "<strong>信息：</strong>".$msg;
  } else {
    $_SESSION['msg_bar2'] = "<strong>信息：</strong>".$msg;
  }
  if (!isset($_SESSION['msg_bar_type'])) {
    $_SESSION['msg_bar_type'] = INFO;
  } else {
    $_SESSION['msg_bar_type2'] = INFO;
  }
}

function msg_bar_warning_delay($msg) {
  if (!isset($_SESSION['msg_bar'])) {
    $_SESSION['msg_bar'] = "<strong>警告：</strong>".$msg;
  } else {
    $_SESSION['msg_bar2'] = "<strong>警告：</strong>".$msg;
  }
  if (!isset($_SESSION['msg_bar_type'])) {
    $_SESSION['msg_bar_type'] = WARNING;
  } else {
    $_SESSION['msg_bar_type2'] = WARNING;
  }
}

function msg_bar_error_delay($msg) {
  if (!isset($_SESSION['msg_bar'])) {
    $_SESSION['msg_bar'] = "<strong>错误：</strong>".$msg;
  } else {
    $_SESSION['msg_bar2'] = "<strong>错误：</strong>".$msg;
  }
  if (!isset($_SESSION['msg_bar_type'])) {
    $_SESSION['msg_bar_type'] = DANGER;
  } else {
    $_SESSION['msg_bar_type2'] = DANGER;
  }
}

function msg_bar_success_noclose($msg) {
  msg_bar_noclose(SUCCESS,"<strong>成功：</strong>".$msg);
}

function msg_bar_info_noclose($msg) {
  msg_bar_noclose(INFO,"<strong>信息：</strong>".$msg);
}

function msg_bar_warning_noclose($msg) {
  msg_bar_noclose(WARNING,"<strong>警告：</strong>".$msg);
}

function msg_bar_error_noclose($msg) {
  msg_bar_noclose(DANGER,"<strong>错误：</strong>".$msg);
}

function msg_pop($title, $msg) {
?>
  <script type="text/javascript">
    window.onload = (function() {
      $("#msgModal").modal('show');
    });
  </script>
  <div class="modal fade" id="msgModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?php echo $title?></h4>
        </div>
        <div class="modal-body">
          <p><?php echo $msg?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
<?php
}

function msg_pop_info($msg) {
  msg_pop("信息", $msg);
}

function msg_pop_warning($msg) {
  msg_pop("警告", $msg);
}

function msg_pop_error($msg) {
  msg_pop("错误", $msg);
}
