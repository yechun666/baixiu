<?php

require_once '../functions.php';

xiu_get_current_user();

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <style>
    .loadBox{
      display:none;
      position:fixed;
      top:0;
      bottom:0;
      left:0;
      right:0;
      background-color:rgba(0,0,0,0.2);
      z-index:9999;
    }
    .boxLoading {  
      width: 50px;
      height: 50px;
      margin: auto;
      position: absolute;
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
    }
    .boxLoading:before {
      content: '';
      width: 50px;
      height: 5px;
      background: #000;
      opacity: 0.1;
      position: absolute;
      top: 59px;
      left: 0;
      border-radius: 50%;
      animation: box-loading-shadow 0.5s linear infinite;
    }
    .boxLoading:after {
      content: '';
      width: 50px;
      height: 50px;
      background: yellow;
      animation: box-loading-animate 0.5s linear infinite;
      position: absolute;
      top: 0;
      left: 0;
      border-radius: 3px;
    }
    @keyframes box-loading-animate {
      17% {
        border-bottom-right-radius: 3px;
      }
      25% {
        transform: translateY(9px) rotate(22.5deg);
      }
      50% {
        transform: translateY(18px) scale(1, .9) rotate(45deg);
        border-bottom-right-radius: 40px;
      }
      75% {
        transform: translateY(9px) rotate(67.5deg);
      }
      100% {
        transform: translateY(0) rotate(90deg);
      }
    }
    @keyframes box-loading-shadow {
      0%, 100% {
        transform: scale(1, 1);
      }
      50% {
        transform: scale(1.2, 1);
      }
    }
  </style>
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>

<body>
  <div class="main">
    <?php include "inc/navbar.php" ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right" id='page-box'></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <div class="loadBox">
    <div class="boxLoading"></div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include "inc/asibar.php" ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/template-web.js"></script>
  <script src="/static/assets/vendors/Pagination/jquery.twbsPagination.js"></script>
  <script>
    // Ajax全局事件
    $(function() {
      $(document).ajaxStart(function() {
        NProgress.start();
        $('.loadBox').fadeIn();
      });
      $(document).ajaxStop(function() {
        NProgress.done();
        $('.loadBox').fadeOut();
      });
    });

    // 当前页码
    var currentPage = 1;

    // Ajax请求渲染
    function loadData(page) {
      $.getJSON('/admin/api/comments.php', {
        page: page
      }, function(res) {
        //  ===渲染页码===
        // 3.删除数据后, 如果当前页码大于服务端返回的最大页码, 则跳到最大页码
        if (page > res.max_page) {
          loadData(res.max_page);
          return;
        }
        // 分页组件不会自动渲染页码, 导致页码不同步数据, 每次Ajax请求页码时:
        // 1.先销毁之前的页码
        $('#page-box').twbsPagination('destroy');
        // 2.再重新渲染页码
        $('#page-box').twbsPagination({
          totalPages: res.max_page,
          visiablePages: 5,
          startPage: page, // 初始化当前页码
          initiateStartPageClick: false, // 阻止默认初始化(外部已经加载第一页)
          first: "首页",
          prev: "上一页",
          next: "下一页",
          last: "未页",
          onPageClick: function(e, page) {
            loadData(page);
          }
        });
        currentPage = page;

        //  ===渲染页面===
        $('tbody').empty();
        var status = '';
        var className = '';
        var success = '';
        var rejected = '';
        res.comments.forEach(function(ele) {
          switch (ele.status) {
            case 'approved':
              status = '已批准';
              className = 'success';
              success = '';
              rejected = "<a href='#' class='btn btn-warning btn-xs'> 拒绝 </a> ";
              break;
            case 'rejected':
              status = '已拒绝';
              className = 'danger';
              success = "<a href='#' class='btn btn-success btn-xs'> 批准 </a> ";
              rejected = '';
              break;
            case 'held':
              status = '待审核';
              className = 'warning';
              success = "<a href='#' class = 'btn btn-success btn-xs'> 批准 </a> ";
              rejected = "<a href='#' class = 'btn btn-warning btn-xs'> 拒绝 </a> ";
              break;
            case 'trashed':
              status = '回收站';
              className = 'success';
              success = '';
              rejected = '';
              break;
          }
          var tr = $("<tr class = " + className + ">" +
            "<td class = 'text-center'><input type = 'checkbox' data-id = " + ele.id + "></td>" +
            "<td width = '60'>" + ele.author + "</td >" +
            "<td>" + ele.content + "</td>" +
            "<td width = '180'>《" + ele.posts_title + "》</td>" +
            "<td width = '160'>" + ele.created + "</td>" +
            "<td class = 'text-center' width = '80'>" + status + "</td>" +
            "<td class = 'text-center' width = '150'>" +
            success + rejected +
            "<a href = '#' class = 'btn btn-danger btn-xs btn-delete' data-id = " + ele.id + "> 删除 </a>" +
            "</td></tr>"
          );
          $('tbody').append(tr);
        });
        $('thead input').prop('checked', false);
        visiable.css('display', 'none');
      });
    }
    // 加载页码(默认加载第一页)
    loadData(currentPage);

    // 删除点击
    $('tbody').on('click', '.btn-delete', function() {
      var id = $(this).data('id');
      $.get('/admin/api/comments-delete.php', {
        id: id
      }, function(res) {
        if (!res) return;
        // 重新加载当页
        loadData(currentPage);
      });
    });

    var checkedArr = []; //选中状态数组
    var visiable = $('.page-action>.btn-batch'); //显示的盒子
    var batchDelete = visiable.find('.btn-danger'); //批量删除按钮

    // 复选框点击
    $('tbody').on('change', 'input', function() {
      var id = $(this).data('id');
      var checked = $(this).prop('checked');
      if (checked) {
        checkedArr.push(id);
      } else {
        checkedArr.splice(checkedArr.indexOf(id), 1);
      }
      batchDelete.data('id', checkedArr);
      checkedArr.length ? visiable.fadeIn() : visiable.fadeOut();
    });

    // 全选框点击
    $('thead input').on('change', function() {
      var checked = $(this).prop('checked');
      checkedArr.splice(0, checkedArr.length);
      $('tbody input').prop('checked', checked).trigger('change');
    });

    // 批量删除
    batchDelete.on('click', function() {
      $.get('/admin/api/comments-delete.php', {
        // 数组与字符串相加 => 字符串数组(字符串在底层也是数组)
        id: "" + $(this).data('id')
      }, function(res) {
        if (!res) return;
        // 重新加载当页
        loadData(currentPage);

      });
    });
  </script>
</body>

</html>