<?php

require_once '../../config.php';


if (!$_GET['email']) {
    exit('<h1>必须传递有效参数</h1>');
}

$email = $_GET['email'];

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
    exit('<h1>连接数据库失败</h1>');
}

$query = mysqli_query($conn, "select * from `users` where `email` = '{$email}' limit 1;");

if (!$query) {
    exit('<h1>查询数据库失败</h1>');
}

$row = mysqli_fetch_assoc($query);

if (!$row['avatar']) {
    exit('<h1>获取数据失败</h1>');
}

echo $row['avatar'];
