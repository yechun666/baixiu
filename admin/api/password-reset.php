<?php

require_once '../../functions.php';

function get_back()
{
    if (empty($_GET['id'])) {
        exit('<h1>无效参数</h1>');
    }
    
    // 获取旧密码
    $GLOBALS['password'] = xiu_fetch_one("SELECT * FROM `users` WHERE `id` = '{$_GET["id"]}' LIMIT 1;")['password'];
}


function post_back()
{
    if (empty($_POST['id'])) {
        exit('<h1>无效参数</h1>');
    }

    // 修改密码
    $row = xiu_execute("UPDATE `users` SET `password` = '{$_POST["password"]}' WHERE `id` = '{$_POST["id"]}';");

    $GLOBALS['flag'] = $row > 0;
}

$data = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    get_back();
    $data['password'] = $password;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    post_back();
    $data['flag'] = $flag;
}

header('Content-Type: application/json');

echo json_encode($data);
