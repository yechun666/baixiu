<?php

require_once '../functions.php';

xiu_get_current_user();

/**
 * =================
 * 处理分类筛选
 */

// 获取所有分类
$categor = xiu_fetch_all('SELECT * FROM `categories`;');

// 默认没有这个where语句 (结果为true,无视where语句)
$where = '1 = 1';
// search保存状态 (用于处理参数合并拼接的问题)
$search = '';

// 如果客户端传递了参数,则累积变量
if (isset($_GET['categories']) && $_GET['categories'] !== 'all') {
    $where .= ' and categories.id = ' .$_GET['categories'];
    $search .= '&categories='.$_GET['categories'];
}

if (isset($_GET['status']) && $_GET['status'] !== 'all') {
    // status是常量 drafted published tarshed ...
    // 不同于ID, SQL语句中需要用单引号引住, 注意拼接问题
    $where .= " and posts.status = '{$_GET["status"]}'";
    $search .= '&status='.$_GET['status'];
}

/**
 * =====================
 * 获取数据的总行数 (联合查询取交集)
 */

$total_count = (int)xiu_fetch_one(
  "SELECT COUNT(1) AS `num` 
FROM `posts` 
INNER JOIN categories ON posts.category_id = categories.id
INNER JOIN users ON posts.user_id = users.id
WHERE {$where}
;"
)['num'];

/**
 * =====================
 * 处理页码规则
 */

// 每页显示的行数 (自定义)
$length = 10;

// 最大的页码 (ceil向上取整,float类型)
$max_page = (int)ceil($total_count / $length);

// 接收当前页码 (默认第一页)
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

// 验证参数的合法性
$page = $page < 1 ? 1 : $page;
$page = $page > $max_page ? $max_page : $page;

// 越过的总行数
$skip = ($page -1) * $length;

$visiables = 3; // 显示的按钮数 (自定义)
$region = ($visiables - 1) / 2; // 区间值
$start = $page - $region; // 开始按钮值
$end = $page + $region; // 结束按钮值

// 开始按钮的值最小不能小于1, 否则循环渲染就会出Bug
if ($start < 1) {
    $start = 1;
    // 强制设置开始按钮的值后, 结束按钮的值也得跟着调整
    $end = $start + $visiables - 1;
}

// 结束按钮的值最大不能大于最大页码, 否则循环渲染就会出Bug
if ($end > $max_page) {
    $end = $max_page;
    // 强制设置结束按钮的值后, 开始按钮的值也得跟着调整
    $start = $end - $visiables + 1;

    // 如果数据太少,避免开始按钮为负数
    if ($start < 1) {
        $start = 1;
    }
}


/**
 * ================
 * 获取数据 (查询联合数据)
 */
$posts = xiu_fetch_all(
    "SELECT
  posts.id,
  posts.title,
  categories.name,
  posts.created,
  posts.status,
  users.nickname
FROM `posts` 
INNER JOIN categories ON posts.category_id = categories.id
INNER JOIN users ON posts.user_id = users.id
WHERE {$where}
ORDER BY posts.created DESC 
LIMIT {$skip}, {$length}
"
);


// =====================


// 状态转换(传递状态码)
function convert_status($status)
{
    $dict = array(
      'published' => '已发布',
      'drafted' => '草稿',
      'trashed' => '回收站'
    );
    return isset($dict[$status]) ? $dict[$status] : '未知状态';
}

// 时间转换(传递时间码)
function convert_date($created)
{
    $time = strtotime($created);
    // 单引号比双引号容易转换
    $date = date('Y年m月d日 <b\r> H:i:s', $time);
    return $date;
}

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="/admin/post-add.php" class="btn btn-info btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="/admin/posts-delete.php" style="display: none" id="batch_delete">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">

          <select name="categories" class="form-control input-sm">
            <!-- 默认分类给一个数据库不存在的ID -->
            <option value="<?php echo 'all'; ?>">所有分类</option>
            <?php foreach ($categor as $item): ?>
            <!-- 需要选择的分类,把ID传参过去, form表单默认是GET传参 -->
            <!-- 显示时, 如果接收的分类和遍历的分类对上的话, 设为 selected 选中状态 -->
            <option value="<?php echo $item['id']; ?>"
              <?php echo isset($_GET['categories']) && $_GET['categories'] === $item['id'] ? ' selected' : ''; ?>>
              <?php echo $item['name']; ?>
            </option>
            <?php endforeach; ?>
          </select>

          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted" <?php echo isset($_GET['status']) && $_GET['status'] === 'drafted' ? ' selected' : ''; ?>>草稿</option>
            <option value="published" <?php echo isset($_GET['status']) && $_GET['status'] === 'published' ? ' selected' : ''; ?>>已发布</option>
            <option value="trashed" <?php echo isset($_GET['status']) && $_GET['status'] === 'trashed' ? ' selected' : ''; ?>>回收站</option>
          </select>

          <button class="btn btn-success btn-sm">筛选</button>
        </form>

        <div style="float:right;">&nbsp;
          <span>
            共
            <?php echo $max_page; ?>页，跳转到&nbsp;
            <input type="text" style="width:50px;" id="jump_value">&nbsp;页&nbsp;
            <!-- 把状态和分类存在data-search参数里,用于JS获取 -->
            <a href="" data-search="<?php echo $search; ?>" class="btn btn-success btn-sm"
              id="jump">>></a>
          </span>
        </div>

        <ul class="pagination pagination-sm pull-right">

          <!-- http传递的参数都是字符串,要转为整型数据 -->
          <li>
            <a href="?page=<?php echo((int)$page - 1) < 1 ? 1 .$search :(int)$page - 1 .$search; ?>">上一页</a>
          </li>

          <?php if ($start > 1): ?>
          <li><a href="#">...</a></li>
          <?php endif; ?>

          <!-- 知道开始和结束的循环就用for循环 -->
          <?php for ($i = $start; $i <= $end; $i++): ?>
          <!-- 如果当前遍历的按钮值是当前的页码, 显示高亮 -->
          <li class="<?php echo $i === $page? 'active':''; ?>">
            <a href="?page=<?php echo $i. $search; ?>">
              <?php echo $i; ?></a>
          </li>
          <?php endfor; ?>

          <?php if ($end < $max_page): ?>
          <li><a href="#">...</a></li>
          <?php endif; ?>

          <li>
            <a href="?page=<?php echo((int)$page + 1) > $max_page ? $max_page .$search : (int)$page + 1 .$search; ?>">下一页</a>
          </li>

        </ul>

      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40">
              <input type="checkbox" id='checkAll'>
            </th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center">
              <input type="checkbox" data-id="<?php echo $item['id']; ?>">
            </td>

            <td>
              <?php echo $item['title']; ?>
            </td>

            <td>
              <?php echo $item['nickname']; ?>
            </td>

            <td>
              <?php echo $item['name']; ?>
            </td>

            <td class="text-center">
              <?php echo convert_date($item['created']); ?>
            </td>

            <td class="text-center">
              <?php echo convert_status($item['status']); ?>
            </td>

            <td class="text-center">
              <a href="/admin/posts-edit.php?id=<?php echo $item['id']; ?>"
                class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts-delete.php?id=<?php echo $item['id']; ?>"
                class="btn btn-danger btn-xs">删除</a>
            </td>

          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include "inc/asibar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"> </script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js">
  </script>
  <script>
    $(function() {

      $('#jump').on('click', function() {
        // 获取存放在data-search里的参数
        var search = $(this).data('search');
        // 拼接传参
        $(this).prop('search', 'page=' + $('#jump_value').val() + search);
      });


      var checkBox = $('tbody input');
      var btnDelete = $('#batch_delete');

      // 保存所有选中状态的checkbox
      var allCheckeds = [];

      // 监听所有表单项发生的变化
      checkBox.on('change', function() {
        var id = $(this).data('id');
        if ($(this).prop('checked')) {
          // 选中状态的加入数组
          allCheckeds.push(id);
        } else {
          allCheckeds.splice(allCheckeds.indexOf(id), 1);
        }

        allCheckeds.length ? btnDelete.fadeIn() : btnDelete.fadeOut();
        // 集体传参
        btnDelete.prop('search', "?id=" + allCheckeds);
      });

      // 监听全选框
      $('#checkAll').on('change', function() {
        // 清除参数重复
        allCheckeds.splice(0, allCheckeds.length);
        var checked = $(this).prop('checked');
        checkBox.prop('checked', checked).trigger('change');


      });
    });
  </script>
  <script>
    NProgress.done()
  </script>
</body>

</html>