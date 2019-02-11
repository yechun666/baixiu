<?php

require_once '../../functions.php';

// 每页显示的行数(自定义)
$length = 20;

// 总行数
$total_count = xiu_fetch_one("SELECT COUNT(1) AS count FROM `comments` 
INNER JOIN `posts` ON comments.post_id = posts.id
;")['count'];

// 最大页码
$max_page = (int)ceil($total_count / $length);

// 接收参数
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

// 越过的总行数
$skip = ($page - 1) * $length;

/**
 * 联合查询
 * comments.* === 获取comments表所有的列
 *
 */
$data = xiu_fetch_all("SELECT 
comments.*, 
posts.title AS posts_title
FROM comments 
INNER JOIN posts ON comments.post_id = posts.id 
ORDER BY comments.created DESC
LIMIT {$skip}, {$length}
;");

/**
 * 序列化
 * 服务端接口想要返回多项数据,可以把数据放到一个关联数组中去
 * */
$json = json_encode(array(
    'max_page' => $max_page,
    'comments' => $data
));

// 接口返回的数据最好要声明JSON
header('Content-Type: application/json');

echo $json;
