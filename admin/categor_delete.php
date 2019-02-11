<?php

require_once '../functions.php';

if (empty($_GET['id'])) {
    exit('<h1>必须传递有效参数</h1>');
}

$delete_id = $_GET['id'];

xiu_execute("delete from `categories` where `id` in ({$delete_id});");

header('Location: /admin/categories.php');
