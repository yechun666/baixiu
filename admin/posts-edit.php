<?php

require_once '../functions.php';

xiu_get_current_user();

// 日期转换
function convert_date($date)
{
    $time = strtotime($date);
    return date('Y-m-d\TH:i', $time);
}

// 分类ID转换
function convert_categor($categor)
{
    $dict = array(
        '未分类' => '1',
        '奇趣事' => '2',
        '爱生活' => '3',
        '爱旅行' => '4',
    );
    return isset($dict[$categor]) ? $dict[$categor] : null;
}

// 验证编辑
function verify()
{
    if (empty($_POST['title'])) {
        $GLOBALS['message'] = '请填写标题';
        $GLOBALS['flag'] = false;
        return;
    }

    if (empty($_POST['content'])) {
        $GLOBALS['message'] = '请填写内容';
        $GLOBALS['flag'] = false;
        return;
    }

    if (empty($_POST['slug'])) {
        $GLOBALS['message'] = '请填写别名';
        $GLOBALS['flag'] = false;
        return;
    }

    // 验证文件域
    if (empty($_FILES['feature']) || $_FILES['feature']['error'] !== UPLOAD_ERR_OK) {
        $GLOBALS['message'] = '请正确提交图片';
        $GLOBALS['flag'] = false;
        return;
    }

    // 移动文件
    $ext = pathinfo($_FILES['feature']['name'], PATHINFO_EXTENSION);
    $target = '../static/uploads/' . uniqid() . '.' . $ext;
    if (!move_uploaded_file($_FILES['feature']['tmp_name'], $target)) {
        $GLOBALS['message'] = '上传图片失败';
        $GLOBALS['flag'] = false;
        return;
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $slug = $_POST['slug'];
    $status = $_POST['status'];

    // 格式化成数据库时间格式(datetime)
    $created = str_replace("T", " ", $_POST['created']);

    // 转换分类ID === 1/2/3/4
    $category = convert_categor($_POST['category']);

    // 联表更新
    // UPDATE ... INNER JOIN ... ON ... SET ... WHERE
    $row = xiu_execute(
        "UPDATE `posts` SET
    posts.slug = '{$slug}', 
    posts.title = '{$title}',
    posts.feature = '{$target}',
    posts.created = '{$created}',
    posts.content = '{$content}',
    posts.status = '{$status}',
    posts.category_id = '{$category}'
    WHERE posts.id = '{$_GET["id"]}'
    ;"
    );

    $GLOBALS['flag'] = $row > 0;
    $GLOBALS['message'] = $row <= 0 ? '修改失败' : '修改成功';
}

if (empty($_GET['id'])) {
    // 确保页面是在有ID的前提下加载进来
    exit('<h1>无效的参数</h1>');
} else {
    // 表单提交
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify();
    }
}

// 获取数据功能最后执行
// 获取编辑的数据
$edit_posts = xiu_fetch_one("SELECT
    posts.id,
    posts.title,
    posts.content,
    posts.slug,
    posts.feature,
    posts.created,
    posts.status,
    categories.name
 FROM `posts` 
 INNER JOIN categories ON posts.category_id = categories.id
 WHERE posts.id = '{$_GET["id"]}'
 ;");

// 获取分类列表
$categories = xiu_fetch_all('SELECT * FROM `categories`');

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <title>Edit post &laquo; Admin</title>
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
                <h1>编辑 “ <?php echo $edit_posts['title']; ?>
                    ”</h1>
            </div>

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

            <!-- 提交的表单要把ID也提交上去, 否则下面表单项显示报错 -->
            <form class="row" action="<?php $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>"
                method='post' autocomplete='off' enctype='multipart/form-data'>
                <div class="col-md-9">
                    <!-- 标题 -->
                    <div class="form-group">
                        <label for="title">标题</label>
                        <input id="title" class="form-control input-lg" name="title" type="text" placeholder="<?php echo $edit_posts['title']; ?>">
                    </div>
                    <!-- 内容 -->
                    <div class="form-group">
                        <label for="content">内容</label>
                        <script id="container" name="content" type="text/plain"><?php echo $edit_posts['content']; ?></script>
                    </div>
                </div>

                <div class="col-md-3">
                    <!-- 别名 -->
                    <div class="form-group">
                        <label for="slug">别名</label>
                        <input id="slug" class="form-control" name="slug" type="text" placeholder="<?php echo $edit_posts['slug']; ?>">
                        <p class="help-block">https://zce.me/post/<strong>slug</strong></p>
                    </div>
                    <!-- 图像 -->
                    <div class="form-group">
                        <label for="feature">特色图像</label>

                        <img class="help-block thumbnail" style="<?php echo empty($edit_posts['feature']) ? 'display:none;' : 'display:block;'; ?>"
                            src=" <?php echo $edit_posts['feature']; ?>">

                        <input id="feature" class="form-control" name="feature" type="file">
                    </div>
                    <!-- 分类 -->
                    <div class="form-group">
                        <label for="category">所属分类</label>
                        <select id="category" class="form-control" name="category">
                            <?php foreach ($categories as $item): ?>
                            <!-- 判断显示 -->
                            <option value="<?php echo $item['name']; ?>"
                                <?php echo $item['name'] == $edit_posts['name'] ? ' selected' : ''; ?>>
                                <?php echo $item['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- 时间 -->
                    <div class="form-group">
                        <label for="created">发布时间</label>
                        <input id="created" class="form-control" name="created" type="datetime-local" value="<?php echo convert_date($edit_posts['created']); ?>">
                    </div>
                    <!-- 状态 -->
                    <div class="
                            form-group">
                        <label for="status">状态</label>
                        <select id="status" class="form-control" name="status">
                            <option value="drafted" <?php echo $edit_posts['status'] === 'drafted' ? ' selected' : '';?>>草稿</option>
                            <option value="published" <?php echo $edit_posts['status'] === 'published' ? ' selected' : '';?>>已发布</option>
                            <option value="trashed" <?php echo $edit_posts['status'] === 'trashed' ? ' selected' : '';?>>回收站</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include "inc/asibar.php" ?>

    <script src="/static/assets/vendors/jquery/jquery.js"></script>
    <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
    <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
    <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
    <script>
        UE.getEditor('container', {
            // 富文本编辑器 - 初始化高度 (宽度默认跟随父元素)
            initialFrameHeight: 350,
            // 关闭自动增长高度
            autoHeightEnabled: false
        });
    </script>
    <script>
        NProgress.done()
    </script>
</body>

</html>