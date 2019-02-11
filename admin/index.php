<?php

require_once '../functions.php';

xiu_get_current_user();

$posts_count = xiu_fetch_one('select count(1) as num from `posts`;');
$categories_count = xiu_fetch_one('select count(1) as num from `categories`;');
$comments_count = xiu_fetch_one('select count(1) as num from `comments`;');

$drafted_count = xiu_fetch_one('select count(1) as num from `posts` where `status` = "drafted";');
$held_count = xiu_fetch_one('select count(1) as num from `comments` where `status` = "held";');

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <title>Dashboard &laquo; Admin</title>
    <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="/static/assets/css/admin.css">
    <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>

<body>
    <script>
        NProgress.start()
    </script>

    <div class="main">
        <?php include "inc/navbar.php" ?>
        <div class="container-fluid">
            <div class="jumbotron text-center">
                <h1>POSTS</h1>
                <p>Thoughts, stories and ideas.</p>
                <p><a class="btn btn-primary btn-lg" href="posts-new.php" role="button">发表文章</a></p>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">站点内容统计：</h3>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong><?php echo $posts_count['num'] ?></strong>篇文章（<strong><?php echo $drafted_count['num'] ?></strong>篇草稿）</li>
                            <li class="list-group-item"><strong><?php echo $categories_count['num'] ?></strong>个分类</li>
                            <li class="list-group-item"><strong><?php echo $comments_count['num'] ?></strong>条评论（<strong><?php echo $held_count['num'] ?></strong>条待审核）</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4" id="myChart" style="height:400px">
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </div>

    <?php $current_page = 'index'; ?>
    <?php include "inc/asibar.php" ?>

    <script src="/static/assets/vendors/jquery/jquery.js"></script>
    <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
    <script src="/static/assets/vendors/echarts/echarts.js"></script>
    <script>
        // 初始化echarts图表
        var myChart = echarts.init(document.getElementById('myChart'));

        // 设置选项
        var option = {
            // 标题
            title: {
                text: '报表统计',
                x: 'center'
            },
            // 说明
            legend: {
                orient: 'vertical',
                x: 'left',
                data: ['文章', '分类', '评论']
            },
            // 提示
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            // 设置
            series: [{
                type: 'pie',
                radius: '55%',
                center: ['50%', '40%'],
                data: [{
                        value: <?php echo $posts_count['num'] ?> ,
                        name: '文章'
                    },
                    {
                        value: <?php echo $categories_count['num'] ?> ,
                        name: '分类'
                    },
                    {
                        value: <?php echo $comments_count['num'] ?> ,
                        name: '评论'
                    }
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }]
        };

        // 为echarts对象加载数据 
        myChart.setOption(option);
    </script>
    <script>
        NProgress.done()
    </script>
</body>

</html>