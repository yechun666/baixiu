<?php

require_once '../../functions.php';

if (empty($_GET['id'])) {
    exit('<h1>无效参数</h1>');
}

$row = xiu_execute("DELETE FROM `users` WHERE `id` IN ({$_GET["id"]});");

$flag = $row > 0;

header('Content-Type: application/json');

echo json_encode($flag);
