<?php

require_once '../functions.php';

if (empty($_GET['id'])) {
    exit('<h1>必须传递有效参数</h1>');
}

$delete_id = $_GET['id'];

xiu_execute("DELETE FROM `posts` WHERE `id` IN({$delete_id});");

// 跳转回当前页码页面
header('Location: ' .$_SERVER['HTTP_REFERER']);
