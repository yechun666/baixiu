<?php

require_once '../functions.php';

xiu_get_current_user();

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
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
        <h1>我的个人资料</h1>
      </div>

      <div id="error" class="alert alert-danger" style="display:none">
        <strong>错误！</strong>
      </div>
      <div id="success" class="alert alert-success" style="display:none">
        <strong>成功！ </strong>
      </div>

      <form class="form-horizontal" autocomplete='off'>
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file">
              <input id="hidden" type="hidden">
              <img src="/static/assets/img/default.png">
              <i class="mask fa fa-upload"></i>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="w@zce.me" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>

        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="zce" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>

        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="汪磊" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>

        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6">MAKE IT BETTER!</textarea>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <!-- 有传递过来的ID则取用、没有则默认取当前用户ID -->
            <button type="button" class="btn btn-primary" id='button' data-id="<?php echo empty($_GET['id']) ? $_SESSION['current_login_user']['id'] : $_GET['id']; ?>">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
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
      var id = $('#button').data('id');
      var avatar = $('#hidden');
      var slug = $('#slug');
      var nickname = $('#nickname');
      var bio = $('#bio');

      // Ajax展示数据
      $.get('/admin/api/profile-update.php', {
        id: id
      }, function(res) {
        $('#avatar').siblings('img').attr('src', res.data.avatar);
        $('#avatar').siblings('#hidden').val(res.data.avatar);
        $('#email').val(res.data.email);
        slug.val(res.data.slug);
        nickname.val(res.data.nickname);
        bio.val(res.data.bio);
      });

      // Ajax显示选取的图片
      $('#avatar').on('change', function() {
        var $this = $(this);
        // files是DOM属性,用prop获取
        var $files = $this.prop('files');
        // onchange事件在表单项发生变化时就触发,判断如果没有选中文件则不触发(return)
        if (!$files.length) return;
        // 获取选中的文件
        var $file = $files[0];
        // HTML5的新成员,专门用于配合Ajax上传二进制文件
        var form = new FormData();
        // 在返回值里存放文件
        form.append('avatar', $file);

        // Ajax上传文件
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/api/upload.php');
        xhr.send(form);
        xhr.onload = function() {
          $this.siblings('img').attr('src', this.responseText);
          // 给隐藏域设置文件的路径
          $this.siblings('#hidden').val(this.responseText);
        }
      });

      // Ajax更新数据
      $('#button').on('click', function() {
        $.post('/admin/api/profile-update.php', {
          id: id,
          avatar: avatar.val(),
          slug: slug.val(),
          nickname: nickname.val(),
          bio: bio.val()
        }, function(res) {
          if (!res.flag) {
            $('#success').fadeOut(100);
            $('#error').fadeIn().html(res.message);
            return;
          } else {
            $('#error').fadeOut(100);
            $('#success').fadeIn().html(res.message);
          }
        });
      });
    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>



</html>