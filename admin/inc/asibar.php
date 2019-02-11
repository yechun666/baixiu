<?php

require_once '../functions.php';

// 确保 $current_page 存在
$current_page = isset($current_page) ? $current_page : '';

$user = xiu_get_current_user();

?>
<div class="aside">
    <div class="profile">
        <a href="/admin/profile.php">
            <img class="avatar" src="<?php echo $user['avatar'] ?>">
        </a>
        <h3 class="name"><?php echo $user['nickname'] ?>
        </h3>
    </div>
    <ul class="nav">

        <!-- 判断标记设置高亮 -->
        <li <?php echo $current_page === 'index'? 'class=active':'' ?>>
            <a href="/admin/index.php">
                <i class="fa fa-dashboard"></i>仪表盘
            </a>
        </li>

        <!-- 数组保存标记 -->
        <?php $menu_post = array('posts','post_add','categories') ?>
        <!-- 遍历数组判断设置高亮 -->
        <li <?php echo in_array($current_page, $menu_post)?'class=active':'' ?>>
            <!-- 遍历数组判断设置箭头 -->
            <a href="#menu-posts" data-toggle="collapse" <?php echo in_array($current_page, $menu_post)?'':'class=collapsed' ?>>
                <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
            </a>
            <!-- 遍历数组判断设置展开 -->
            <ul id="menu-posts" class="collapse<?php echo in_array($current_page, $menu_post)?' in':'' ?>">
                <li <?php echo $current_page === 'posts'? 'class=active':'' ?>>
                    <a href="/admin/posts.php">所有文章</a>
                </li>
                <li <?php echo $current_page === 'post_add'? 'class=active':'' ?>>
                    <a href="/admin/posts-new.php">写文章</a>
                </li>
                <li <?php echo $current_page === 'categories'? 'class=active':'' ?>>
                    <a href="/admin/categories.php">分类目录</a>
                </li>
            </ul>
        </li>

        <!-- 判断标记设置高亮 -->
        <li <?php echo $current_page === 'comments'? 'class=active':'' ?>>
            <a href="/admin/comments.php">
                <i class="fa fa-comments"></i>评论
            </a>
        </li>

        <!-- 判断标记设置高亮 -->
        <li <?php echo $current_page === 'users'? 'class=active':'' ?>>
            <a href="/admin/users.php">
                <i class="fa fa-users"></i>用户
            </a>
        </li>

        <!-- 数组保存标记 -->
        <?php $menu_setting = array('nav_menus','slides','settings') ?>
        <!-- 遍历数组判断设置高亮 -->
        <li <?php echo in_array($current_page, $menu_setting)?'class=active':'' ?>>
            <!-- 遍历数组判断设置箭头 -->
            <a href="#menu-settings" data-toggle="collapse" <?php echo in_array($current_page, $menu_setting)?'':'class=collapsed' ?>>
                <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
            </a>
            <!-- 遍历数组判断设置展开 -->
            <ul id="menu-settings" class="collapse<?php echo in_array($current_page, $menu_setting)?' in':'' ?>">
                <li <?php echo $current_page === 'nav_menus'? 'class=active':'' ?>>
                    <a href="/admin/nav-menus.php">导航菜单</a>
                </li>
                <li <?php echo $current_page === 'slides'? 'class=active':'' ?>>
                    <a href="/admin/slides.php">图片轮播</a>
                </li>
                <li <?php echo $current_page === 'settings'? 'class=active':'' ?>>
                    <a href="/admin/settings.php">网站设置</a>
                </li>
            </ul>
        </li>
    </ul>
</div>