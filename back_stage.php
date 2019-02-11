<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h1>后台管理系统</h1>
    <a href="/admin/">127.0.0.1</a>
</body>
<script>
    document.onkeydown = function(e) {
        e = e || window.e;
        if (e.keyCode === 13) {
            location.assign('/admin/');
        }
    };
</script>

</html>