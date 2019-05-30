<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<div class="infoBox">
    <img src="{{ asset('images/icon.png') }}" alt="">
    <div data-show="true" id="boxer" class="ant-alert ant-alert-success">{{ $msg }}</div>
</div>
</body>
<style>
    .infoBox{
        text-align: center;
        margin:140px 200px ;
        padding: 20px;
    }
    .infoBox img{
        width: 250px;
    }
    .ant-alert {
        position: relative;
          padding: 58px 48px 58px 38px;
        border-radius: 4px;
        margin: 0 auto;
        color: rgba(0, 0, 0, .65);
        font-size: 18px;
        line-height: 16px;
        width: 550px;
    }
    .ant-alert-success {
    }
</style>
</html>