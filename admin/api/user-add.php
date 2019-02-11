<?php

require_once '../../functions.php';

function add_user()
{
    if (empty($_POST['email'])) {
        $GLOBALS['message'] = '请输入邮箱';
        $GLOBALS['flag'] = false;
        return;
    }

    if (empty($_POST['slug'])) {
        $GLOBALS['message'] = '请输入别名';
        $GLOBALS['flag'] = false;
        return;
    }

    if (empty($_POST['nickname'])) {
        $GLOBALS['message'] = '请输入昵称';
        $GLOBALS['flag'] = false;
        return;
    }

    if (empty($_POST['password'])) {
        $GLOBALS['message'] = '请输入密码';
        $GLOBALS['flag'] = false;
        return;
    }

    $row = xiu_execute("INSERT INTO `users` VALUES 
    (NULL,
    '{$_POST["slug"]}',
    '{$_POST["email"]}',
    '{$_POST["password"]}',
    '{$_POST["nickname"]}',
    '/static/assets/img/default.png',
    NULL,
    'activated');");

    $GLOBALS['flag'] = $row > 0;
    $GLOBALS['message'] = empty($GLOBALS['flag']) ? '添加失败' : '添加成功';
}

function get_users()
{
    $GLOBALS['users'] = xiu_fetch_all('SELECT * FROM `users`;');
}

$data = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_user();
    $data['flag'] = $flag;
    $data['message'] = $message;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    get_users();
    $data['users'] = $users;
}

header('Content-Type: application/json');

echo json_encode($data);
