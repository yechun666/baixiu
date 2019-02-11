<?php

require_once 'config.php';

session_start();

// 用户登录信息
function xiu_get_current_user()
{
    if (empty($_SESSION['current_login_user'])) {
        header('Location: /admin/login.php');
        exit();
    }
    return $_SESSION['current_login_user'];
}

// 获取全部数据
function xiu_fetch_all($sql)
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conn) {
        exit('<h1>连接数据库失败</h1>');
    }
    mysqli_set_charset($conn, 'utf8');
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        exit('<h1>查询数据失败</h1>');
    }
    $data = null;
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }
    mysqli_free_result($query);
    mysqli_close($conn);
    return $data;
}

// 获取一行数据
function xiu_fetch_one($sql)
{
    $result = xiu_fetch_all($sql);
    // 涉及到关联数组的操作,都要先判断该元素是否存在,不存在的话PHP会报错
    return isset($result[0]) ? $result[0] : null;
}

// 增删改
function xiu_execute($sql)
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conn) {
        exit('<h1>连接数据库失败</h1>');
    }
    mysqli_set_charset($conn, 'utf8');
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        exit('<h1>查询失败</h1>');
    }
    $rows = mysqli_affected_rows($conn);
    mysqli_close($conn);
    return $rows;
}
