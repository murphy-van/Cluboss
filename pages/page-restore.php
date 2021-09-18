<?php

function do_restore() {
  do_html_header("恢复数据 - ".APP_NAME, "navbar", false);
  do_page_navbar();
  do_page_restore();
  do_page_footer();
  do_html_footer();
}

function do_page_restore() {
?>
        <div class="jumbotron">
          <div class="panel panel-default">
            <div class="panel-heading"><strong>导入数据备份文件</strong></div>
            <div class="panel-body">
              <br />
              <form class="form-horizontal" role="form" method="post"
                    action="restore_upload.php" enctype="multipart/form-data">
                <div class="form-group">
                  <label class="col-sm-3 control-label">数据备份文件</label>
                  <div class="col-sm-6">
                    <input name="xmlfile" type="file" class="form-control"
                           aria-describedby="basic-addon1" accept="text/xml"
                         size="100%" required>
                  </div>
                  <div class="col-sm-3"></div>
                </div>
                <br />
                <div class="form-group">
                  <div class="col-sm-4"></div>
                  <div class="col-sm-4">
                    <button class="btn btn-primary btn-block" type="submit" onclick="return confirm('如果上传的文件格式正确将会覆盖所有已有的数据。确认导入么？');">导入并覆盖</button>
                  </div>
                  <div class="col-sm-4"></div>
                </div>
              </form>
            </div>
          </div>
        </div>
<?php
}
