<?php

require_once '../functions.php';

xiu_get_current_user();

// 表单都是通过POST提交的
// 添加表单 => 不传递ID
// 编辑表单 => 传递ID
if (empty($_GET['id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        add_categor(); // 处理添加
    }
} else {
    // 获取指定的数据
    $current_edit_categor = xiu_fetch_one("select * from `categories` where `id` = '{$_GET["id"]}';");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        edit_categor(); // 处理编辑
    }
}

// 1.先修改
// 处理添加
function add_categor()
{
    if (empty($_POST['name'])) {
        $GLOBALS['message'] = '请输入分类名称';
        $GLOBALS['flag'] = false;
        return;
    }
    if (empty($_POST['slug'])) {
        $GLOBALS['message'] = '请输入别名';
        $GLOBALS['flag'] = false;
        return;
    }

    $categor_name = $_POST['name'];
    $categor_slug = $_POST['slug'];

    // 添加
    $row = xiu_execute("insert into `categories` values (null,'{$categor_slug}','{$categor_name}');");

    $GLOBALS['flag'] = $row > 0;
    $GLOBALS['message'] = $row <= 0 ? '添加失败' : '添加成功';
}

// 处理编辑
function edit_categor()
{
    global $current_edit_categor;

    $edit_name = empty($_POST['name']) ? $current_edit_categor['name'] : $_POST['name'];
    $edit_sulg = empty($_POST['slug']) ? $current_edit_categor['slug'] : $_POST['slug'];
    $edit_id = $current_edit_categor['id'];

    // 修改
    $row = xiu_execute("update `categories` set `name` = '{$edit_name}' , `slug` = '{$edit_sulg}' where `id` = '{$edit_id}';");

    $GLOBALS['flag'] = $row > 0;
    $GLOBALS['message'] = $row <= 0 ? '修改失败' : '修改成功';
}

// 2.再查询数据
$categories = xiu_fetch_all('select * from `categories`');

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>

      <!-- 根据 $flag 判断是否成功, 来显示 -->
      <?php if (isset($message)) :?>
      <?php if ($flag): ?>

      <div class="alert alert-success">
        <strong>成功！</strong>
      </div>

      <?php else: ?>

      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>

      <?php endif; ?>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-4">

          <?php if (isset($current_edit_categor)): ?>

          <!-- 编辑表单 -->
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>?id=<?php echo $current_edit_categor['id']; ?>"
            method="post" autocomplete='off'>
            <!-- GET传ID -->
            <h2>编辑 “ <?php echo $current_edit_categor['name']; ?>
              ”</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_categor['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_categor['slug'] ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>

          <?php else: ?>

          <!-- 添加表单(默认) -->
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>"
            method="post" autocomplete='off'>
            <!-- GET不传ID -->
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>

          <?php endif; ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="btn_delete" href="/admin/categor_delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item) :?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id'] ?>"></td>
                <td>
                  <?php echo $item['name'] ?>
                </td>
                <td>
                  <?php echo $item['slug'] ?>
                </td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $item['id'] ?>"
                    class="btn btn-info btn-xs">编辑</a>
                  <a href="categor_delete.php?id=<?php echo $item['id'] ?>"
                    class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories'; ?>
  <?php include "inc/asibar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function() {

      var allBtn = $('thead input');
      var checkBox = $('tbody input');
      var btnDelete = $('#btn_delete');

      // 数组保存所有选中状态的checkbox
      var allCheckeds = [];

      // 监听所有表单项发生的变化
      checkBox.on('change', function() {
        // 获取自定义属性id(dataset属性)
        var id = $(this).data('id');
        // 属性值为 true/false 用prop()
        if ($(this).prop('checked')) {
          // 加入状态数组
          allCheckeds.push(id);
        } else {
          // 从状态数组中删除
          allCheckeds.splice(allCheckeds.indexOf(id), 1);
        }
        // 判断显示
        allCheckeds.length ? btnDelete.fadeIn() : btnDelete.fadeOut();

        // 通过js传递参数(设置a元素的search属性)
        btnDelete.prop('search', "?id=" + allCheckeds);
      });

      // 监听总选框
      $('thead input').on('change', function() {
        // 获取总选框的选中状态
        var checked = $(this).prop('checked');
        // 分别同步给所有复选框 (在此之前,最好先清空一下状态数组,因为trigger触发事件,会导致重复传递参数)
        allCheckeds.splice(0, allCheckeds.length);
        checkBox.prop('checked', checked).trigger('change');
      });

    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>