<?php

require_once '../functions.php';

xiu_get_current_user();

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Password reset &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>

<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include "inc/navbar.php" ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <div id="error" class="alert alert-danger" style="display:none">
        <strong>错误！</strong>
      </div>
      <div id="success" class="alert alert-success" style="display:none">
        <strong>成功！ </strong>
      </div>
      <form class="form-horizontal">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" class="form-control" type="password" placeholder="旧密码">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" class="form-control" type="password" placeholder="新密码">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" class="form-control" type="password" placeholder="确认新密码">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="button" id="button" class="btn btn-primary" data-id="<?php echo $_SESSION['current_login_user']['id']; ?>">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include "inc/asibar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function() {

      $('#button').on('click', function() {

        var oldPwd = $('#old').val();
        var password = $('#password').val();
        var confirm = $('#confirm').val();

        // 判断完整表单项
        if (!oldPwd || !password || !confirm) {
          $('#success').fadeOut(100);
          $('#error').fadeIn().html('请完整输入');
          return;
        } else if (password !== confirm) {
          $('#success').fadeOut(100);
          $('#error').fadeIn().html('两次输入的密码不相同');
          return;
        }

        // Ajax获取旧密码
        $.get('/admin/api/password-reset.php', {
          id: $('#button').data('id')
        }, function(res) {
          if (oldPwd != res.password) {
            $('#success').fadeOut(100);
            $('#error').fadeIn().html('输入的旧密码错误');
            return;
          } else if (password == res.password) {
            $('#success').fadeOut(100);
            $('#error').fadeIn().html('输入的密码与原密码相同');
            return;
          }

          // 一切就绪, 再发送密码进行修改
          $.post('/admin/api/password-reset.php', {
            id: $('#button').data('id'),
            password: password
          }, function(res) {
            if (!res) {
              $('#success').fadeOut(100);
              $('#error').fadeIn().html('修改失败');
            } else {
              $('#error').fadeOut(100);
              $('#success').fadeIn().html('修改成功');
            }
          });
        });
      });
    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>