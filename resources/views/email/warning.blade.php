<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
-----------------------------------------<br>
<br>
<p>名称： {{ $name }}</p>
<p>错误类型： {{ $type_name }} - {{ $type }}</p>
@if(!empty($data))
    <p>数据：</p>
    @foreach($data as $key => $value)
        <p>&nbsp;&nbsp;&nbsp;&nbsp;{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}</p>
    @endforeach
@endif

<p>错误原因：</p>
<p><pre>{{ $msg }}</pre></p>
<br>
-----------------------------------------<br>
</body>
</html>