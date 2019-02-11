<?php

require_once '../functions.php';

xiu_get_current_user();

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <div id="error" class="alert alert-danger" style="display:none">
        <strong>错误！</strong>
      </div>
      <div id="success" class="alert alert-success" style="display:none">
        <strong>成功！ </strong>
      </div>
      <div class="row">
        <div class="col-md-4">
          <form autocomplete="off">
            <h2>添加新用户</h2>

            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>

            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>

            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>

            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>

            <div class="form-group">
              <button class="btn btn-primary" type="button" id="button">添加</button>
            </div>

          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none" id="batchDelete">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include "inc/asibar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function() {

      // 展示用户列表
      function getUsers() {
        $.get('/admin/api/user-add.php', {}, function(res) {
          $('tbody').html('');
          $.each(res.users, function(i, e) {
            var status;
            switch (e.status) {
              case 'activated':
                status = '激活';
                break;
            }
            // 渲染
            var tr = $("<tr data-id=" + e.id + ">" +
              "<td class = 'text-center'><input type ='checkbox'></td>" +
              "<td class = 'text-center'><img class = 'avatar' src=" + e.avatar + "></td>" +
              "<td>" + e.email + "</td>" +
              "<td>" + e.slug + "</td> " +
              "<td>" + e.nickname + "</td>" +
              "<td>" + status + "</td>" +
              "<td class = 'text-center'>" +
              "<a href = /admin/profile.php?id=" + e.id + " class = 'btn btn-default btn-xs'>编辑</a> " +
              "<a href = 'javascript:;' class = 'btn btn-danger btn-xs'>删除</a>" +
              "</td></tr>"
            );
            $('tbody').append(tr);
          });
        });
      }

      // 添加用户
      $('#button').on('click', function() {
        $.post('/admin/api/user-add.php', {
          email: $('#email').val(),
          slug: $('#slug').val(),
          nickname: $('#nickname').val(),
          password: $('#password').val()
        }, function(res) {
          if (!res.flag) {
            $('#success').fadeOut(100);
            $('#error').fadeIn().html(res.message);
            return;
          } else {
            $('#error').fadeOut(100);
            $('#success').fadeIn().html(res.message);
            getUsers();
          }
        });
      });

      // 删除用户
      $('tbody').on('click', ' .btn-danger', function() {
        var id = $(this).parents('tr').data('id');
        $.get('/admin/api/user-delete.php', {
          id: id
        }, function(res) {
          if (!res) {
            $('#success').fadeOut(100);
            $('#error').fadeIn().html('删除失败');
            return;
          } else {
            $('#error').fadeOut(100);
            $('#success').fadeIn().html('删除成功');
            getUsers();
          }
        });
      });

      var checkedArr = [];
      var visiable = $('#batchDelete');

      // 复选框点击
      $('tbody').on('change', 'input', function() {
        var checked = $(this).prop('checked');
        var id = $(this).parents('tr').data('id');
        if (checked) {
          checkedArr.push(id);
        } else {
          checkedArr.splice(checkedArr.indexOf(id), 1);
        }
        visiable.data('id', checkedArr);
        checkedArr.length ? visiable.fadeIn() : visiable.fadeOut();
      });

      // 全选框点击
      $('thead input').on('change', function() {
        var checked = $(this).prop('checked');
        checkedArr.splice(0, checkedArr.length);
        $('tbody input').prop('checked', checked).trigger('change');
      });

      // 批量删除
      visiable.on('click', function() {
        var id = $(this).data('id');
        $.get('/admin/api/user-delete.php', {
          id: "" + id
        }, function(res) {
          if (!res) {
            $('#success').fadeOut(100);
            $('#error').fadeIn().html('删除失败');
            return;
          } else {
            $('#error').fadeOut(100);
            $('#success').fadeIn().html('删除成功');
            getUsers();
          }
        });
      });

      // 编辑用户
      // $('tbody').on('click', ' .btn-default', function() {
      //   var id = $(this).parents('tr').data('id');
      //   $.get('/admin/profile.php', {
      //     id: id
      //   });
      // });

      getUsers();

    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>