<?php

require_once '../../functions.php';

if (empty($_GET['id'])) {
    exit('<h1>无效参数</h1>');
}

$id = $_GET['id'];

$row = xiu_execute("DELETE FROM `comments` WHERE `id` IN ({$id})");

// jquery会自动根据返回的type类型自动转为js需要的类型
// json对象 => js对象
// json数组 => js数组
// json布尔值 => js布尔值
header('Content-Type: application/json');

$json = json_encode($row > 0);

echo $json;
