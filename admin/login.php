<?php

// 非http请求,用相对路径
require_once '../config.php';

// 开启Session操作
session_start();

function login()
{
    if (empty($_POST['email'])) {
        $GLOBALS['message']  = '请输入账号';
        return;
    }
    if (empty($_POST['password'])) {
        $GLOBALS['message']  = '请输入密码';
        return;
    }

    $username = $_POST['email'];
    $password = $_POST['password'];

    // 连接数据库
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conn) {
        exit("<h1>连接数据库失败</h1>");
    }

    // 处理中文数据
    mysqli_set_charset($conn, 'utf8');

    // 查询数据库
    $query = mysqli_query($conn, "select * from `users` where `email` = '{$username}' limit 1;");
    if (!$query) {
        exit("<h1>登录失败</h1>");
    }

    // 获取数据
    $user = mysqli_fetch_assoc($query);

    if (!$user) {
        // 用户名不存在
        $GLOBALS['message'] = '用户名和密码不匹配';
        return;
    }

    if ($user['password'] !== $password) {
        // 密码不对
        $GLOBALS['message'] = '用户名和密码不匹配';
        return;
    }

    // Session保存当前用户登录标识
    // (为了后面直接拿到用户信息,建议保存用户信息)
    $_SESSION['current_login_user'] = $user;

    // 跳转,默认index.php
    header('Location: /admin/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login();
}

// 退出登录 (退出登录按钮通过GET传参)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    // 删除登录标识
    unset($_SESSION['current_login_user']);
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>

<body>
  <div class="login">
    <!-- 摇动插件, 阻止表单提示, 阻止表单填充 -->
    <form class="login-wrap<?php echo isset($message)? " shake animated":"" ?>"
      action="<?php $_SERVER['PHP_SELF'] ?>"
      method='post' novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 展示错误信息 -->
      <?php if (isset($message)): ?>
      <div class="alert alert-danger">
        <strong>
          <?php echo $message; ?>
        </strong>
      </div>
      <?php endif; ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <!-- 登录状态保持 -->
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email'])? $_POST['email']:'' ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.html">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function() {
      // 数组保存上一次账号
      var valueArr = [];
      // 输入框失去焦点事件
      $('#email').on('blur', function() {
        // 邮箱格式
        var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+(\.[a-zA-Z0-9]+)+$/;
        var value = $(this).val();

        for (var i = 0; i < valueArr.length; i++) {
          // 如果当前账号跟上一次账号相同,停止事件,不再重复获取
          if (value === valueArr[i]) return;
        }
        // 如果不同,先清空数组
        valueArr.splice(0, valueArr.length);
        // 再把账号加进去 (这两步用来保证数组中只有一个元素)
        valueArr.push(value);

        // 为空或者不是邮箱
        if (!value || !emailFormat.test(value)) return;
        // 发送Get请求
        $.get('/admin/api/avatar.php', {
          email: value
        }, function(res) {
          // 如果获取数据为空
          if (!res) return;
          // 原元素淡出去(图片)
          $('.avatar').fadeOut(function() {
            // 图片加载完毕之后
            $(this).on('load', function() {
              // 元素淡出来
              $(this).fadeIn();
              // 设置图片显示
            }).attr('src', res);
          });
        });
      });
    });
  </script>
</body>

</html>