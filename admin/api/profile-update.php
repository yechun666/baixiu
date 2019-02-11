<?php

require_once '../../functions.php';

function get_back()
{
    if (empty($_GET['id'])) {
        exit('<h1>无效参数</h1>');
    }
    
    $GLOBALS['data'] = xiu_fetch_one("SELECT * FROM `users` WHERE `id` = '{$_GET["id"]}';");
    $GLOBALS['flag'] = empty($data);
}

function post_back()
{
    if (empty($_POST['id'])) {
        exit('<h1>无效参数</h1>');
    }

    if (empty($_POST['avatar'])) {
        $GLOBALS['message'] = '请选择头像';
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

    if (empty($_POST['bio'])) {
        $GLOBALS['message'] = '请输入简介';
        $GLOBALS['flag'] = false;
        return;
    }

    $slug = $_POST['slug'];
    $avatar = $_POST['avatar'];
    $nickname = $_POST['nickname'];
    $bio = $_POST['bio'];
    $id = $_POST['id'];
    
    // slug这里不能用字符串模板拼接...很奇怪
    $row = xiu_execute("UPDATE `users` SET 
    `slug` = ' ".$slug." ', 
    `avatar` = '{$avatar}', 
    `nickname` = '{$nickname}',
    `bio` = '{$bio}'
    WHERE `id` = '{$id}';");

    $GLOBALS['flag'] = $row > 0;
    $GLOBALS['message'] = $row > 0 ? '修改成功' : '修改失败';
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    get_back();
    $json = json_encode(array(
        'data' => $data,
        'flag' => $flag
    ));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    post_back();
    $json = json_encode(array(
        'message' => $message,
        'flag' => $flag
    ));
}

header('Content-Type: application/json');

echo $json;
