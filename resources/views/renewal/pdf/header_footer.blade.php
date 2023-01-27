<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
    html {
        margin: 0;
    }
    body {
        margin: 0;
        font-size: 12px;
    }
    .wrapper {
        margin: 0;
        width: 100%;
    }
    .left,
    .center,
    .right {
        display: inline-block;
        margin: 0;
    }
    .left {
        text-align: left;
        width: 32%;
        margin-right: 1%;
    }
    .center {
        text-align: center;
        width: 32%;
    }
    .right {
        text-align: right;
        width: 32%;
        margin-left: 1%;
    }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="left">
            {{ isset($left) ? $left : '' }}
        </div>
        <div class="center">
            {{ isset($center) ? $center : '' }}
        </div>
        <div class="right">
            {{ isset($right) ? $right : '' }}
        </div>
    </div>
    <br><br>
</body>
</html>