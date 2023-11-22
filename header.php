<?php

echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>Audit</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style>
    body {
        width: 70em;
        margin: 0 auto;
        font-family: Arial, Tahoma, Verdana, sans-serif;
    }
    table tr:nth-child(odd){
        background:#F4F4F4;
    }
</style>
</head>

<body>
<table border=0 style="width: 100%; table-layout:fixed;">
<tr style="height: 29pt; font-size: 16pt; font-weight: bold;">
<th><a href="index.php">使用者列表</a></th>
<th><a href="import.php">匯入與重整</a></th>
<th><a href="detail.php">弱掃結果</a></th>
<th><a href="sum.php">資料彙總</a></th>
<th><a href="check.php">關於</a></th>
</tr>
</table>
<hr>
HTML;
