<?php
include('config.php');
include('header.php');

$temp_str = php_ini_scanned_files();
if(str_contains($temp_str, 'mysqli.ini')) {
   $c1 = "green";
   $s1 = "checked";
} else {
   $c1 = "black";
   $s1 = "";
}

if(str_contains($temp_str, 'mbstring.ini')) {
   $c2 = "green";
   $s2 = "checked";
} else {
   $c2 = "black";
   $s2 = "";
}

echo <<<HTML
<h1>需安裝模組</h1><BR />
HTML;

echo "<span style=\"color: $c1; font-size: 14pt;\"><input type=\"checkbox\" $s1> php-mysql & mysqli.ini</span><BR /><BR />\n";
echo "<span style=\"color: $c2; font-size: 14pt;\"><input type=\"checkbox\" $s2> php-mbstring</span><BR /><BR />\n";

echo <<<HTML
<h2>版本：1.4</h2><BR />
HTML;
